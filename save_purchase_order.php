<?php

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once 'includes/db.php';
require_once 'includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: purchase_orders.php');
    exit();
}

verifyCsrfToken();

$supplierId = (int) $_POST['supplier_id'];
$notes = trim($_POST['notes']);
$status = in_array($_POST['status'] ?? '', ['Draft', 'Pending']) ? $_POST['status'] : 'Pending';

$productIds = $_POST['product_id'] ?? [];
$quantities = $_POST['quantity'] ?? [];
$prices = $_POST['price'] ?? [];

if ($supplierId <= 0) {
    exit('Please select a supplier.');
}

if (empty($productIds) ||
    empty($quantities) ||
    empty($prices)) {
    exit('Please add at least one purchase order item.');
}

if (count($productIds) !== count($quantities) || count($productIds) !== count($prices)) {
    exit('Purchase order item data is invalid');
}

$supplierStmt = $conn->prepare('SELECT id FROM suppliers WHERE id = ? AND status = "Active"');

if (!$supplierStmt) {
    error_log('save_purchase_order.php: Failed to prepare supplier lookup: '.$conn->error);
    exit('Unable to validate supplier.');
}

$supplierStmt->bind_param('i', $supplierId);
$supplierStmt->execute();

$supplierResult = $supplierStmt->get_result();

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

    $productStmt->bind_param('i', $productId);
    $productStmt->execute();

    $productResult = $productStmt->get_result();

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
        $conn->rollback();
        exit('One or more selected products are not available.');
    }

    $qty = (float) $quantities[$i];
    $price = (float) $prices[$i];

    if ($qty <= 0 || $price <= 0) {
        $conn->rollback();
        exit('Invalid quantity or unit cost.');
    }
    $grandTotal += ($qty * $price);
}

$poNumber = 'PO-'.date('YmdHis').'-'.str_pad(random_int(0, 999), 3, '0', STR_PAD_LEFT);

$conn->begin_transaction();

$stmt = $conn->prepare('INSERT INTO purchase_orders 
(
supplier_id,
po_number,
total,
status, 
notes
) VALUES(?, ?, ?, ?, ?)
');

if (!$stmt) {
    error_log('save_purchase_order.php: Failed to prepare purchase order insert: '.$conn->error);
    exit('Unable to save purchase order.');
}

$stmt->bind_param(
    'isdss',
    $supplierId,
    $poNumber,
    $grandTotal,
    $status,
    $notes
);

if (!$stmt->execute()) {
    $conn->rollback();
    error_log('save_purchase_order.php: Failed to execute purchase order insert: '.$stmt->error);
    exit('Unable to save purchase order.');
}

$purchaseOrderId = $conn->insert_id;
$stmt->close();

for ($i = 0; $i < count($productIds);
++$i) {
    $productId = (int) $productIds[$i];
    $qty = (float) $quantities[$i];
    $price = (float) $prices[$i];
    $lineTotal = $qty * $price;

    $itemStmt = $conn->prepare('INSERT INTO 
    purchase_order_items(purchase_order_id, product_id, quantity, cost_price, line_total) 
    VALUES (?, ?, ?, ?, ?)');

    if (!$itemStmt) {
        $conn->rollback();
        error_log('save_purchase_order.php: Failed to prepare purchase order item insert: '.$conn->error);
        exit('Unable to save purchase order item(s).');
    }

    $itemStmt->bind_param('iiddd', $purchaseOrderId, $productId, $qty, $price, $lineTotal);

    if (!$itemStmt->execute()) {
        $conn->rollback();
        error_log('save_purchase_order.php: Failed to execute purchase order item insert: '.$itemStmt->error);
        exit('Unable to save purchase order item(s).');
    }

    $itemStmt->close();
}

if (!$conn->commit()) {
    error_log('save_purchase_order.php: Failed to commit purchase order transaction: '.$conn->error);
    exit('Unable to complete purchase order.');
}

require_once 'includes/logger.php';
logActivity($conn, $_SESSION['user_id'], 'Created Purchase Order. '.$poNumber);

header('Location: purchase_orders.php');
exit();
