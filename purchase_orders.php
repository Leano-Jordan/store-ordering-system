<?php
require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once 'includes/db.php';

$limit = 10;

$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$searchRaw = $_GET['search'] ?? '';

if (!is_string($searchRaw)) {
    $searchRaw = '';
}

$search = trim($searchRaw);

if (!empty($search)) {
    $searchTerm = '%'.$search.'%';

    $stmt = $conn->prepare(
        "SELECT purchase_orders.*, 
    suppliers.company_name
    FROM purchase_orders
    INNER JOIN suppliers
    ON purchase_orders.supplier_id = suppliers.id
    WHERE purchase_orders.po_number LIKE ? OR suppliers.company_name LIKE ? 
    ORDER BY purchase_orders.created_at DESC
    LIMIT $limit OFFSET $offset"
    );

    if (!$stmt) {
        error_log('purchase_orders.php: Failed to prepare search query: '.$conn->error);
        exit('Unable to load purchase orders.');
    }

    if (!$stmt->bind_param('ss', $searchTerm, $searchTerm)) {
        error_log('purchase_orders.php: Failed to bind search parameters: '.$stmt->error);
        $stmt->close();
        exit('Unable to load Purchase Order.');
    }

    if (!$stmt->execute()) {
        error_log('purchase_orders.php: Failed to execute Purchase Order search: '.$stmt->error);
        $stmt->close();
        exit('Unable to load Purchase Order.');
    }

    $result = $stmt->get_result();

    if (!$result) {
        error_log('purchase_orders.php: Failed to retrieve Purchase Order search result: '.$stmt->error);
        $stmt->close();
        exit('Unable to load Purchase Order.');
    }

    $countStmt = $conn->prepare('SELECT COUNT(*) AS total 
        FROM purchase_orders 
        INNER JOIN suppliers ON purchase_orders.supplier_id = suppliers.id 
        WHERE purchase_orders.po_number LIKE ? OR suppliers.company_name LIKE ?
        ');

    if (!$countStmt) {
        error_log('purchase_orders.php: Failed to prepare purchase order count query: '.$conn->error);

        $stmt->close();

        exit('Unable to load purchase orders count.');
    }

    if (!$countStmt->bind_param('ss', $searchTerm, $searchTerm)) {
        error_log('purchase_orders.php: Failed to bind count parameters: '.$countStmt->error);

        $countStmt->close();

        exit('Unable to load Purchase Order count.');
    }

    if (!$countStmt->execute()) {
        error_log('purchase_orders.php: Failed to execute Purchase Order count: '.$countStmt->error);

        $countStmt->close();
        $stmt->close();

        exit('Unable to load Purchase Order count.');
    }

    $countResult = $countStmt->get_result();

    if (!$countResult) {
        error_log('purchase_orders.php: Failed to retrieve Purchase Order count: '.$countStmt->error);
        $countStmt->close();
        exit('Unable to load Purchase Order count.');
    }

    $totalRow = $countResult->fetch_assoc();

    if (!is_array($totalRow) || !isset($totalRow['total'])) {
        error_log('purchase_orders.php: Purchase Order count query '.'returned an invalid result.');

        $countResult->free();
        $countStmt->close();
        $stmt->close();

        exit('Unable to load Purchase Order count.');
    }

    $totalRows = (int) $totalRow['total'];

    $countResult->free();
    $countStmt->close();
    $stmt->close();
} else {
    $result = $conn->query("SELECT purchase_orders.*, 
    suppliers.company_name
    FROM purchase_orders
    INNER JOIN suppliers
    ON purchase_orders.supplier_id = suppliers.id
    ORDER BY purchase_orders.created_at DESC
    LIMIT $limit OFFSET $offset");

    if (!$result) {
        error_log('purchase_orders.php: Failed to load purchase orders: '.$conn->error);
        exit('Unable to load purchase orders.');
    }

    $countResult = $conn->query('SELECT COUNT(*) AS total FROM purchase_orders INNER JOIN suppliers ON purchase_orders.supplier_id = suppliers.id');

    if (!$countResult) {
        error_log('purchase_orders.php: Failed to count purchase orders: '.$conn->error);
        exit('Unable to load purchase orders count.');
    }

    $totalRow = $countResult->fetch_assoc();

    if (!is_array($totalRow) || !isset($totalRow['total'])) {
        error_log('purchase_orders.php: Purchase order count query '.'returned an invalid result.');

        exit('Unable to load purchase orders count.');
    }

    $totalRows = (int) $totalRow['total'];
}

$totalPages = ceil($totalRows / $limit);

include 'includes/header.php';
?>

<div class="page-header">

<h2>Purchase Orders</h2>

<a href="add_purchase_order.php" class="action-btn">
    + Create a New Purchase Order
</a>
</div>

<?php require 'includes/shared/flash_message.php'; ?>

<?php include 'includes/partials/purchase_order/purchase_order_toolbar.php'; ?>

<?php include 'includes/partials/purchase_order/purchase_order_table.php'; ?>

<?php include 'includes/partials/purchase_order/purchase_order_pagination.php'; ?>

<?php include 'includes/footer.php'; ?>
