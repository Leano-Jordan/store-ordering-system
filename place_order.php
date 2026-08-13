<?php

require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/csrf.php';
verifyCsrfToken();
require_once 'includes/permissions.php';
require_once 'includes/helpers.php';
require_once 'includes/shared/errors.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER, ROLE_CASHIER]);

if (
    $_SERVER['REQUEST_METHOD'] !==
    'POST'
) {
    jsonError('Invalid request.');
}

$maxOrderNumberAttempts = 20;
$orderNumberAttempts = 0;

do {
    ++$orderNumberAttempts;

    if ($orderNumberAttempts > $maxOrderNumberAttempts) {
        jsonError('Unable to generate a unique order number. Please try again');
    }

    $orderNumber = 'SW'.str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);

    $check = $conn->prepare('SELECT id FROM orders WHERE order_number = ?');
    if (!$check) {
        jsonError('Failed to generate unique order number.');
    }

    $check->bind_param('s', $orderNumber);

    if (!$check->execute()) {
        error_log('place_order.php: Failed to check order number uniqueness: '.$check->error);
        jsonError('Failed to generate a unique order number. Please try again.');
    }

    $result = $check->get_result();
} while ($result->num_rows > 0);

$check->close();

$customer = trim($_POST['customer'] ??
    '');
if ($customer === '') {
    jsonError('Customer name is required');
}

$cart = json_decode($_POST['cart'] ?? '[]', true);

$paymentMethod = $_POST['payment_method'] ?? 'cash_pmt';

if (!in_array($paymentMethod, ['cash_pmt', 'card_pmt', 'eft_pmt'])) {
    jsonError('Invalid payment method.');
}

if (!is_array($cart) || empty($cart)) {
    jsonError('Cart is empty.');
}

// COLLECT ALL THE PRODUCT IDS FROM THE CART

$productIds = array_map(fn ($item) => (int) $item['id'], $cart);
$placeholders = implode(',', array_fill(0, count($productIds), '?'));
$types = str_repeat('i', count($productIds));

$conn->begin_transaction();

$productStmt = $conn->prepare("SELECT id, name, price, stock FROM products WHERE id IN ($placeholders) AND status = 'Active' FOR UPDATE");

if (!$productStmt) {
    $conn->rollback();
    error_log('place_order.php: Failed to prepare product query: '.$conn->error);
    jsonError('Failed to load products.');
}

if (!$productStmt->bind_param($types, ...$productIds)) {
    $conn->rollback();
    error_log('place_order.php: Failed to bind product query parameters: '.$productStmt->error);
    jsonError('Failed to load products.');
}

if (!$productStmt->execute()) {
    $conn->rollback();
    error_log('place_order.php: Failed to execute product query: '.$productStmt->error);
    jsonError('Failed to load products.');
}

$result = $productStmt->get_result();

if (!$result) {
    $conn->rollback();
    error_log('place_order.php: Failed to get product result: '.$productStmt->error);
    jsonError('Failed to load products.');
}

$productMap = [];
while ($p = $result->fetch_assoc()) {
    $productMap[$p['id']] = $p;
}

$productStmt->close();

$items = '';
$total = 0;
$dbPrices = [];

foreach ($cart as $item) {
    $productId = (int) $item['id'];
    $quantity = (int) $item['quantity'];

    if ($productId <= 0 || $quantity <= 0) {
        $conn->rollback();
        jsonError('Invalid cart item.');
    }

    if (!isset($productMap[$productId])) {
        $conn->rollback();
        jsonError("Product ID $productId not found.");
    }

    $product = $productMap[$productId];

    if ($quantity > $product['stock']) {
        $conn->rollback();
        header('Content-Type: application/json');
        echo json_encode(['success' => false,
        'message' => 'Only '.$product['stock'].' '.$product['name'].'(s) available in stock.', ]);
        exit();
    }

    $total += $product['price'] * $quantity;
    $dbPrices[$productId] = $product['price'];
    $items .= $product['name'].' x '.$quantity."\n";
}

$items = trim($items);

$status = 'Pending';

$paymentMethod = trim($_POST['payment_method'] ?? 'cash_pmt');

$sql = 'INSERT INTO orders (
    order_number, 
    customer_name, 
    items, 
    total, 
    status, 
    payment_method) VALUES (?, ?, ?, ?, ?, ?)';

$stmt = $conn->prepare($sql);
if (!$stmt) {
    $conn->rollback();
    error_log('place_order.php: Failed to prepare order insert: '.$conn->error);
    jsonError('Failed to place the order. Please try again.');
}

$stmt->bind_param(
    'sssdss',
    $orderNumber,
    $customer,
    $items,
    $total,
    $status,
    $paymentMethod
);

if ($stmt->execute()) {
    $orderId = $conn->insert_id;

    $itemStmt = $conn->prepare(
        'INSERT INTO order_items(order_id, product_id, quantity, price) 
    VALUES (?, ?, ?, ?)'
    );

    if (!$itemStmt) {
        $conn->rollback();
        error_log('place_order.php: Failed to prepare order items insert: '.$conn->error);
        jsonError('Failed to save order items. Please try again.');
    }

    foreach ($cart as $item) {
        $productId = (int) $item['id'];
        $quantity = (int) $item['quantity'];
        $price = $dbPrices[(int) $item['id']];

        $itemStmt->bind_param('iiid', $orderId, $productId, $quantity, $price);

        if (!$itemStmt->execute()) {
            $conn->rollback();
            error_log('place_order.php: Failed to insert order item: '.$itemStmt->error);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Failed to save order items. Please try again.',
            ]);
            exit();
        }
    }

    $itemStmt->close();

    if (!$conn->commit()) {
        $conn->rollback();
        error_log('place_order.php: Failed to commit transaction: '.$conn->error);
        jsonError('Failed to place the order. Please try again.');
    }

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully.',
        'order_number' => $orderNumber,
    ]);

    exit();
} else {
    $conn->rollback();
    error_log('place_order.php: Failed to insert order: '.$stmt->error);

    header('Content-Type: application/json');

    echo json_encode([
        'success' => false,
        'message' => 'Failed to place the order.',
    ]);

    exit();
}
