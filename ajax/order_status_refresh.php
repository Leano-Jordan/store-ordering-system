<?php

require_once '../includes/auth.php';
require_once '../includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER,
ROLE_CASHIER, ROLE_KITCHEN, ]);
require_once '../includes/db.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $conn->prepare('SELECT status, updated_at FROM orders WHERE id = ?');

$stmt->bind_param('i', $id);
$stmt->execute();

$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    exit();
}

echo json_encode($order);
