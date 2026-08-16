<?php

require_once '../includes/auth.php';
require_once '../includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER,
ROLE_CASHIER, ROLE_KITCHEN, ]);
require_once '../includes/db.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $conn->prepare('SELECT status FROM orders WHERE id = ?');

if (!$stmt->bind_param('i', $id)) {
    error_log('order_status_refresh.php: Failed to bind order ID: '.$stmt->error);
    $stmt->close();
    exit();
}

if (!$stmt->execute()) {
    error_log('order_status_refresh.php: Failed to execute order status query: '.$stmt->error);
    $stmt->close();
    exit();
}

$order = $stmt->get_result()->fetch_assoc();

$stmt->close();

if (!$order) {
    exit();
}

echo json_encode($order);
