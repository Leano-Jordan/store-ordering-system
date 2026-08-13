<?php

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once 'includes/db.php';
require_once 'includes/logger.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: purchase_orders.php');
    exit();
}

require_once 'includes/csrf.php';
verifyCsrfToken();

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    header('Location: purchase_orders.php');
    exit();
}

    $stmt = $conn->prepare('SELECT status FROM purchase_orders WHERE id = ?');
    if (!$stmt) {
        error_log('cancel_purchase_order.php: Failed to prepare purchase order status lookup: '.$conn->error);
        exit('Unable to cancel purchase order.');
    }

    $stmt->bind_param('i', $id);
    $stmt->execute();
    $po = $stmt->get_result()->fetch_assoc();

    if (!$po) {
        header('Location: purchase_orders.php');
        exit();
    }

    if ($po['status'] !== 'Pending' && $po['status'] !== 'Draft') {
        header('Location: purchase_orders.php');
        exit();
    }

    $updateStmt = $conn->prepare("UPDATE purchase_orders SET status = 'Cancelled' WHERE id = ? AND status IN ('Pending', 'Draft')");
    if (!$updateStmt) {
        error_log('cancel_purchase_order.php: Failed to prepare purchase order cancellation: '.$conn->error);
        exit('Unable to cancel purchase order.');
    }

    $updateStmt->bind_param('i', $id);

    if (!$updateStmt->execute()) {
        error_log('cancel_purchase_order.php: Failed to execute purchase order cancellation: '.$updateStmt->error);
        exit('Unable to cancel purchase order.');
    }

    if ($updateStmt->affected_rows === 0) {
        exit('Purchase order could not be cancelled. It may have already been processed or cancelled.');
    }

    logActivity($conn, $_SESSION['user_id'], 'Cancelled Purchase Order ID '.$id);

    header('Location: purchase_orders.php');
    exit();
