<?php

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once 'includes/db.php';
require_once 'includes/csrf.php';
require_once 'includes/audit.php';
require_once 'includes/logger.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: purchase_orders.php');
    exit();
}

verifyCsrfToken();

$supplierId = filter_input(INPUT_POST, 'supplier_id', FILTER_VALIDATE_INT);

if ($supplierId === false || $supplierId <= 0) {
    $_SESSION['error'] = 'Invalid supplier selected.';
    header('Location: add_purchase_order.php');
    exit();
}

$notesInput = $_POST['notes'] ?? '';

if (!is_string($notesInput)) {
    $_SESSION['error'] = 'Invalid purchase order notes.';
    header('Location: add_purchase_order.php');
    exit();
}

$notes = trim($notesInput);

$statusInput = $_POST['status'] ?? '';

$status = (is_string($statusInput) && in_array($statusInput, ['Draft', 'Pending'], true)) ? $statusInput : 'Pending';

$productIds = $_POST['product_id'] ?? [];
$quantities = $_POST['quantity'] ?? [];
$prices = $_POST['price'] ?? [];

if (
    !is_array($productIds) ||
    !is_array($quantities) ||
    !is_array($prices) ||
    empty($productIds) ||
    count($productIds) !== count($quantities) ||
    count($productIds) !== count($prices)
    ) {
    $_SESSION['error'] = 'Invalid purchase order item data.';
    header('Location: add_purchase_order.php');
    exit();
}

$supplierStmt = $conn->prepare('SELECT id FROM suppliers WHERE id = ? AND status = "Active"');

if (!$supplierStmt) {
    error_log('save_purchase_order.php: Failed to prepare supplier lookup: '.$conn->error);
    exit('Unable to validate supplier.');
}

if (!$supplierStmt->bind_param('i', $supplierId)) {
    error_log('save_purchase_order.php: Failed to bind supplier lookup: '.$supplierStmt->error);
    $supplierStmt->close();
    exit('Unable to validate supplier.');
}

if (!$supplierStmt->execute()) {
    error_log('save_purchase_order.php: Failed to execute supplier lookup: '.$supplierStmt->error);
    $supplierStmt->close();
    exit('Unable to validate supplier.');
}

$supplierResult = $supplierStmt->get_result();

if (!$supplierResult) {
    error_log('save_purchase_order.php: Failed to retrieve supplier lookup result: '.$supplierStmt->error);

    $supplierStmt->close();

    exit('Unable to validate supplier.');
}

if ($supplierResult->num_rows !== 1) {
    $supplierStmt->close();
    exit('Selected supplier is not available.');
}

$supplierStmt->close();

$validateProducts = [];

$productStmt = $conn->prepare('SELECT id FROM products WHERE id = ? AND status = "Active"');

if (!$productStmt) {
    error_log('save_purchase_order.php: Failed to prepare product lookup: '.$conn->error);
    exit('Unable to validate purchase order products.');
}

for ($i = 0; $i < count($productIds); ++$i) {
    $productId = (int) $productIds[$i];

    if (isset($validateProducts[$productId])) {
        $productStmt->close();
        exit('Duplicate products are not allowed in a purchase order.');
    }

    if ($productId <= 0) {
        $productStmt->close();
        exit('Invalid product selected.');
    }

    if (!$productStmt->bind_param('i', $productId)) {
        error_log('save_purchase_order.php: Failed to bind product lookup: '.$productStmt->error);

        $productStmt->close();

        exit('Unable to validate purchase order products.');
    }

    if (!$productStmt->execute()) {
        error_log('save_purchase_order.php: Failed to execute product lookup: '.$productStmt->error);

        $productStmt->close();

        exit('Unable to validate purchase order products.');
    }

    $productResult = $productStmt->get_result();

    if (!$productResult) {
        error_log('save_purchase_order.php: Failed to retrieve product lookup result: '.$productStmt->error);

        $productStmt->close();

        exit('Unable to validate purchase order products.');
    }

    if ($productResult->num_rows !== 1) {
        $productStmt->close();
        exit('One or more selected products are not available.');
    }

    $validateProducts[$productId] = true;
}

$productStmt->close();

$grandTotal = 0;

for ($i = 0; $i < count($productIds); ++$i) {
    $productId = (int) $productIds[$i];

    if (!isset($validateProducts[$productId])) {
        $_SESSION['error'] = 'One or more selected products are not available.';
        header('Location: add_purchase_order.php');
        exit();
    }

    $qty = filter_var($quantities[$i], FILTER_VALIDATE_INT);
    $price = (float) $prices[$i];

    if ($qty <= 0 || $price <= 0) {
        $_SESSION['error'] = 'Invalid quantity or unit cost.';
        header('Location: add_purchase_order.php');
        exit();
    }

    $grandTotal += ($qty * $price);
}

$poNumber = 'PO-'.date('YmdHis').'-'.str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);

$transactionStarted = false;

try {
    if (!$conn->begin_transaction()) {
        throw new RuntimeException('Failed to begin purchase order transaction: '.$conn->error);
    }

    $transactionStarted = true;

    $stmt = $conn->prepare(
        'INSERT INTO purchase_orders
        (supplier_id, 
        po_number, 
        total,
        status,
        notes
        ) VALUES(?, ?, ?, ?, ?)
'
    );

    if (!$stmt) {
        throw new Exception('Failed to prepare purchase order insert: '.$conn->error);
    }

    if (!$stmt->bind_param(
        'isdss',
        $supplierId,
        $poNumber,
        $grandTotal,
        $status,
        $notes
    )) {
        $error = $stmt->error;
        $stmt->close();

        throw new RuntimeException('Failed to bind purchase order insert: '.$error);
    }

    if (!$stmt->execute()) {
        throw new Exception('Failed to execute purchase order insert: '.$stmt->error);
    }

    $purchaseOrderId = $conn->insert_id;
    $stmt->close();

    for ($i = 0; $i < count($productIds); ++$i) {
        $productId = filter_var($productIds[$i], FILTER_VALIDATE_INT);
        $qty = filter_var($quantities[$i], FILTER_VALIDATE_INT);
        $price = filter_var($prices[$i], FILTER_VALIDATE_FLOAT);

        if ($productId === false || $productId <= 0 || $qty === false || $qty <= 0 || $price === false || $price <= 0) {
            throw new Exception('Invalid purchase order item data.');
        }

        $lineTotal = $qty * $price;

        $itemStmt = $conn->prepare(
            'INSERT INTO purchase_order_items(purchase_order_id,product_id, quantity, cost_price, line_total) 
            VALUES (?, ?, ?, ?, ?)'
        );

        if (!$itemStmt) {
            throw new Exception('Failed to prepare purchase order item insert: '.$conn->error);
        }

        if (!$itemStmt->bind_param(
            'iiddd',
            $purchaseOrderId,
            $productId,
            $qty,
            $price,
            $lineTotal
        )) {
            $error = $itemStmt->error;
            $itemStmt->close();

            throw new RuntimeException('Failed to bind purchase order item insert: '.$error);
        }

        if (!$itemStmt->execute()) {
            throw new Exception('Failed to execute purchase order item insert: '.$itemStmt->error);
        }

        $itemStmt->close();
    }

    recordAudit(
        $conn,
        (int) $_SESSION['user_id'],
        'purchase_order',
        (int) $purchaseOrderId,
        'CREATE',
        [
            'supplier_id' => [null, (string) $supplierId],
            'status' => [null, $status],
            'total' => [null, number_format($grandTotal, 2, '.', '')],
            'po_number' => [null, $poNumber],
]
    );

    if (!logActivity(
        $conn,
        (int) $_SESSION['user_id'],
        'Created Purchase Order. '.$poNumber
    )
    ) {
        throw new RuntimeException('Failed to record purchase order activity.');
    }

    if (!$conn->commit()) {
        throw new Exception('Failed to commit purchase order transaction: '.$conn->error);
    }
} catch (Throwable $e) {
    if ($transactionStarted) {
        $conn->rollback();
    }

    error_log('save_purchase_order.php: Transaction failed: '.$e->getMessage());

    $_SESSION['error'] = 'Unable to save purchase order. Please try again.';

    header('Location: add_purchase_order.php');
    exit();
}

header('Location: purchase_orders.php');
exit();
