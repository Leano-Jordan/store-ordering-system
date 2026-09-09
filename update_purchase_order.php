<?php

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once 'includes/db.php';
require_once 'includes/logger.php';
require_once 'includes/audit.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: purchase_orders.php');
    exit();
}

require_once 'includes/csrf.php';
verifyCsrfToken();

$purchaseOrderIdRaw = $_POST['purchase_order_id'] ?? null;
$supplierIdRaw = $_POST['supplier_id'] ?? null;
$notesRaw = $_POST['notes'] ?? '';
$productIds = $_POST['product_id'] ?? null;
$quantities = $_POST['quantity'] ?? null;
$prices = $_POST['price'] ?? null;

if (
    !is_string($purchaseOrderIdRaw) ||
    !is_string($supplierIdRaw) ||
    !is_string($notesRaw) ||
    !is_array($productIds) ||
    !is_array($quantities) ||
    !is_array($prices)
    ) {
    error_log('update_purchase_orders.php: Invalid purchase order input structure.');
    exit('Invalid Purchase Order data.');
}

$purchaseOrderId = filter_var($purchaseOrderIdRaw, FILTER_VALIDATE_INT);
$supplierId = filter_var($supplierIdRaw, FILTER_VALIDATE_INT);
$notes = trim($notesRaw);

if ($purchaseOrderId === false || $supplierId === false) {
    exit('Invalid Purchase Order data.');
}

if (count($productIds) !== count($quantities) || count($productIds) !== count($prices)) {
    exit('Purchase order item data is invalid.');
}

if (count($productIds) !== count(array_unique($productIds, SORT_REGULAR))) {
    exit('Duplicate products are not allowed on purchase order.');
}

if (!$conn->begin_transaction()) {
    error_log('update_purchase_order.php: Failed to begin transaction: '.$conn->error);

    exit('Unable to start Purchase Order update. Please try again.');
}

if ($purchaseOrderId <= 0) {
    $conn->rollback();
    exit('Invalid Purchase Order.');
}

if ($supplierId <= 0) {
    $conn->rollback();
    exit('Please select a supplier.');
}

$supplierCheckStmt = $conn->prepare('SELECT id FROM suppliers WHERE id = ? AND status = "Active"');

if (!$supplierCheckStmt) {
    $conn->rollback();
    error_log('update_purchase_order.php: Failed to prepare supplier validation: '.$conn->error);
    exit('Failed to validate supplier. Please try again.');
}

if (!$supplierCheckStmt->bind_param('i', $supplierId)) {
    $conn->rollback();
    error_log('update_purchase_order.php: Failed to bind supplier validation: '.$supplierCheckStmt->error);
    exit('Failed to validate supplier. Please try again.');
}

if (!$supplierCheckStmt->execute()) {
    $conn->rollback();
    error_log('update_purchase_order.php: Failed to execute supplier validation: '.$supplierCheckStmt->error);
    exit('Failed to validate supplier. Please try again.');
}

$supplierResult = $supplierCheckStmt->get_result();

if (!$supplierResult) {
    $conn->rollback();
    error_log('update_purchase_order.php: Failed to get supplier validation result: '.$supplierCheckStmt->error);
    exit('Failed to validate supplier. Please try again.');
}

if (!$supplierResult->fetch_assoc()) {
    $conn->rollback();
    error_log('update_purchase_order.php: Supplier ID '.$supplierId.' is not active or does not exist.');
    exit('Selected supplier is unavailable.');
}

$supplierCheckStmt->close();

$productCheckStmt = $conn->prepare('SELECT id FROM products WHERE id = ? AND status = "Active"');

if (!$productCheckStmt) {
    $conn->rollback();
    error_log('update_purchase_order.php: Failed to prepare product validation: '.$conn->error);
    exit('Failed to validate Purchase Order products. Please try again.');
}

for ($i = 0; $i < count($productIds); ++$i) {
    $productIdRaw = $productIds[$i] ?? null;
    $productId = filter_var($productIdRaw, FILTER_VALIDATE_INT);

    if ($productId === false || $productId <= 0) {
        $productCheckStmt->close();
        $conn->rollback();
        exit('Invalid product ID.');
    }

    if (!$productCheckStmt->bind_param('i', $productId)) {
        $productCheckStmt->close();
        $conn->rollback();
        error_log('update_purchase_order.php: Failed to bind product validation for product ID '.$productId.': '.$productCheckStmt->error);
        exit('Failed to validate Purchase Order products. Please try again.');
    }

    if (!$productCheckStmt->execute()) {
        $productCheckStmt->close();
        $conn->rollback();
        error_log('update_purchase_order.php: Failed to execute product validation for product ID '.$productId.': '.$productCheckStmt->error);
        exit('Failed to validate Purchase Order products. Please try again.');
    }

    $productResult = $productCheckStmt->get_result();

    if (!$productResult) {
        $productCheckStmt->close();
        $conn->rollback();

        error_log('update_purchase_order.php: Failed to get product validation result for product ID '.$productId.': '.$productCheckStmt->error);
        exit('Failed to validate Purchase Order products. Please try again.');
    }

    if (!$productResult->fetch_assoc()) {
        $productCheckStmt->close();
        $conn->rollback();

        error_log('update_purchase_order.php: Product ID '.$productId.' is not active or does not exist.');
        exit('One or more selected products are unavailable.');
    }
}

$productCheckStmt->close();

$grandTotal = 0;

for ($i = 0; $i < count($productIds); ++$i) {
    $qtyRaw = $quantities[$i] ?? null;
    $priceRaw = $prices[$i] ?? null;

    $qty = filter_var($qtyRaw, FILTER_VALIDATE_INT);
    $price = filter_var($priceRaw, FILTER_VALIDATE_FLOAT);

    if ($qty === false || $price === false) {
        $conn->rollback();
        exit('Invalid quantity or price.');
    }

    if ($qty <= 0 || $price <= 0) {
        $conn->rollback();
        exit('Invalid quantity or price.');
    }

    $grandTotal += round(
    (float) $qty * (float) $price, 2);
}

/* CHECK IF PURCHASE ORDER IS EDIT-ABLE */

$checkStmt = $conn->prepare('SELECT supplier_id, status, total FROM purchase_orders WHERE id = ? FOR UPDATE');
if (!$checkStmt) {
    $conn->rollback();
    error_log('update_purchase_order.php: Failed to prepare purchase order check query: '.$conn->error);
    exit('Failed to load Purchase Order. Please try again.');
}

$checkStmt->bind_param('i', $purchaseOrderId);

if (!$checkStmt->execute()) {
    $conn->rollback();
    error_log('update_purchase_order.php: Failed to execute purchase order check query: '.$checkStmt->error);
    exit('Failed to load Purchase Order. Please try again.');
}

$checkResult = $checkStmt->get_result();

if (!$checkResult) {
    $conn->rollback();
    error_log('update_purchase_order.php: Failed to get purchase order check result: '.$checkStmt->error);
    exit('Failed to load Purchase Order. Please try again.');
}

$currentPO = $checkResult->fetch_assoc();

$checkStmt->close();

if (!$currentPO) {
    $conn->rollback();

    error_log(
        'update_purchase_order.php: Purchase order not found for ID '
        .$purchaseOrderId
    );

    exit('Purchase order not found.');
}

$originalSupplierId = (int) $currentPO['supplier_id'];
$originalStatus = (string) $currentPO['status'];
$originalTotal = (float) $currentPO['total'];

$statusRaw = $_POST['status'] ?? $currentPO['status'];

if (!is_string($statusRaw)) {
    $conn->rollback();

    error_log('update_purchase_order.php: Invalid status input structure for Purchase order ID '.$purchaseOrderId);
    exit('Invalid Purchase Order Status.');
}

$status = $statusRaw;

if (!in_array($status, ['Draft', 'Pending'], true)) {
    $conn->rollback();
    error_log('update_purchase_order.php: Invalid status provided for purchase order ID '.$purchaseOrderId.': '.$status);
    exit('Invalid Purchase Order Status.');
}

if (!in_array($currentPO['status'], ['Draft', 'Pending'])) {
    $conn->rollback();
    error_log('update_purchase_order.php: Attempt to edit a non-editable purchase order ID '.$purchaseOrderId.' with status '.$currentPO['status']);
    exit('Cannot edit a received or cancelled purchase order.');
}

/* UPDATE THE PURCHASE ORDER */

$stmt = $conn->prepare('UPDATE purchase_orders
SET
supplier_id = ?,
total = ?,
notes = ?, status = ? 
WHERE id = ?
');

if (!$stmt) {
    $conn->rollback();
    error_log('update_purchase_order.php: Failed to prepare purchase order update query: '.$conn->error);
    exit('Failed to update Purchase Order. Please try again.');
}

if (!$stmt->bind_param(
    'idssi',
    $supplierId,
    $grandTotal,
    $notes,
    $status,
    $purchaseOrderId
)) {
    $conn->rollback();
    error_log('update_purchase_order.php: Failed to bind purchase order update parameters: '.$stmt->error);
    exit('Failed to update Purchase Order. Please try again.');
}

if (!$stmt->execute()) {
    $conn->rollback();
    error_log('update_purchase_order.php: Failed to execute purchase order update query: '.$stmt->error);
    exit('Failed to update Purchase Order. Please try again.');
}

$stmt->close();

/* DELETE THE OLD ITEMS BEFORE RE-INSERTING THEM */

$delete = $conn->prepare('DELETE FROM purchase_order_items
WHERE purchase_order_id = ?
');

if (!$delete) {
    $conn->rollback();
    error_log('update_purchase_order.php: Failed to prepare purchase order item deletion: '.$conn->error);
    exit('Failed to to update Purchase Order items. Please try again');
}

if (!$delete->bind_param('i', $purchaseOrderId)) {
    $conn->rollback();
    error_log('update_purchase_order.php: Failed to bind purchase order item deletion: '.$delete->error);
    exit('Failed to update Purchase Order items. Please try again.');
}

if (!$delete->execute()) {
    $conn->rollback();
    error_log('update_purchase_order.php: Failed to delete old purchase order items for purchase order ID '.$purchaseOrderId.': '.$delete->error);
    exit('Failed to update Purchase Order items. Please try again');
}

$delete->close();

/* INSERT UPDATE ITEMS HERE */

$itemStmt = $conn->prepare('INSERT INTO purchase_order_items
(purchase_order_id,
product_id,
quantity,
cost_price,
line_total
)
VALUES
( ?, ?, ?, ?, ?)
');

if (!$itemStmt) {
    $conn->rollback();
    error_log('update_purchase_order.php: Failed to prepare purchase order items insert query: '.$conn->error);
    exit('Failed to save Purchase Order items. Please try again.');
}

$itemCount = count($productIds);

for ($i = 0; $i < $itemCount; ++$i) {
    $productIdRaw = $productIds[$i] ?? null;
    $productId = filter_var($productIdRaw, FILTER_VALIDATE_INT);

    $qtyRaw = $quantities[$i] ?? null;
    $priceRaw = $prices[$i] ?? null;

    $qty = filter_var($qtyRaw, FILTER_VALIDATE_INT);
    $price = filter_var($priceRaw, FILTER_VALIDATE_FLOAT);

    if ($qty === false || $price === false || $qty <= 0 || $price <= 0) {
        $conn->rollback();

        error_log('update_purchase_order.php: Invalid quantity or price during item insert.');
        exit('Invalid quantity or price.');
    }

    if ($productId === false || $productId <= 0) {
        $conn->rollback();

        error_log('update_purchase_order.php: Invalid product ID during item insert.');
        exit('Invalid product ID during item insert.');
    }

    $lineTotal = round(
        (float) $qty * (float) $price, 2);

    if (!$itemStmt->bind_param(
        'iiddd',
        $purchaseOrderId,
        $productId,
        $qty,
        $price,
        $lineTotal
    )) {
        $conn->rollback();
        error_log('update_purchase_order.php: Failed to bind purchase order item parameters for product ID '.$productId.': '.$itemStmt->error);
        exit('Failed to save Purchase Order items. Please try again.');
    }

    if (!$itemStmt->execute()) {
        $conn->rollback();
        error_log('update_purchase_order.php: Failed to insert purchase order item for product ID '.$productId.': '.$itemStmt->error);
        exit('Failed to save Purchase Order items. Please try again.');
    }
}

$itemStmt->close();

try {
    recordAudit(
        $conn,
        (int) $_SESSION['user_id'],
        'purchase_order',
        (int) $purchaseOrderId,
        'UPDATE',
        [
            'supplier_id' => [(string) $originalSupplierId, (string) $supplierId,],
            'status' => [
                $originalStatus, $status,
            ],
            'total' => [
                number_format($originalTotal, 2, '.', ''),
                number_format((float) $grandTotal, 2, '.', ''),
            ],
        ]
    );

    require_once 'includes/logger.php';

    if (!logActivity(
        $conn,
        (int) $_SESSION['user_id'], 'Updated Purchase Order ID '.$purchaseOrderId)
        ) {
        throw new RuntimeException('Failed to record Purchase Order activity.');
    }

    if (!$conn->commit()) {
        throw new RuntimeException('Failed to save Purchase Order changes: '.$conn->error);
    }
} catch (Throwable $e) {
    $conn->rollback();

    error_log('update_purchase_order.php: Transaction failed for purchase order ID '.$purchaseOrderId.': '.$e->getMessage());

    exit('Failed to save Purchase Order changes. Please try again.');
}

header('Location: purchase_orders.php');
exit();
