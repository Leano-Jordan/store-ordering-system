<?php

require_once '../includes/auth.php';
require_once '../includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER,
ROLE_CASHIER, ROLE_KITCHEN, ]);
require_once '../includes/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id <= 0) {
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(
        ['error' => 'Invalid order ID.'],
        JSON_THROW_ON_ERROR
    );
    exit();
}

$stmt = $conn->prepare('SELECT status FROM orders WHERE id = ?');

if (!$stmt) {
    error_log('order_status_refresh.php: Failed to prepare order status query: '.$conn->error);

    http_response_code(500);
    exit();
}

if (!$stmt->bind_param('i', $id)) {
    error_log(
        'order_status_refresh.php: Failed to bind order ID: '.$stmt->error
    );

    $stmt->close();
    http_response_code(500);
    exit();
}

if (!$stmt->execute()) {
    error_log(
        'order_status_refresh.php: Failed to execute order status query: '.$stmt->error
    );

    $stmt->close();
    http_response_code(500);
    exit();
}

$result = $stmt->get_result();

if (!$result) {
    error_log(
        'order_status_refresh.php: Failed to retrieve order status result: '.$stmt->error
    );

    $stmt->close();
    http_response_code(500);
    exit();
}

$order = $result->fetch_assoc();
$result->free();

$stmt->close();

if (!$order) {
    http_response_code(404);
    exit();
}

header('Content-Type: application/json; charset=utf-8');

echo json_encode($order, JSON_THROW_ON_ERROR);
