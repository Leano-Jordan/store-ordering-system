<?php

require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/csrf.php';
verifyCsrfToken();
require_once 'includes/permissions.php';
require_once 'includes/helpers.php';
require_once 'includes/shared/errors.php';
require_once 'includes/audit.php';
require_once 'includes/logger.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER, ROLE_CASHIER]);

if (
    $_SERVER['REQUEST_METHOD'] !==
    'POST'
) {
    jsonError('Invalid request.');
}

$orderNumber = 'SW'
    .date('ymdHis')
    .str_pad(
        (string) random_int(1, 9999),
        4,
        '0',
        STR_PAD_LEFT
    );

$requestIdInput = $_POST['request_id'] ?? null;

if (!is_string($requestIdInput)) {
    jsonError('Invalid order request.');
}

$requestId = trim($requestIdInput);

if ($requestId === '' ||
!preg_match(
    '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/',
    $requestId
)
    ) {
    jsonError('Invalid order request.');
}

$existingStmt = $conn->prepare(
    'SELECT order_number
    FROM orders
    WHERE request_id = ?
    LIMIT 1'
);

if (!$existingStmt) {
    error_log(
        'place_order.php: Failed to check request ID: '
        .$conn->error
    );

    jsonError('Unable to process the order. Please try again.');
}

if (!$existingStmt->bind_param('s', $requestId)) {
    $existingStmt->close();

    error_log(
        'place_order.php: Failed to bind request ID: '
        .$existingStmt->error
    );

    jsonError('Unable to process the order. Please try again.');
}

if (!$existingStmt->execute()) {
    $existingStmt->close();

    error_log(
        'place_order.php: Failed to execute request ID check: '
        .$existingStmt->error
    );

    jsonError('Unable to process the order. Please try again.');
}

$existingResult = $existingStmt->get_result();

if ($existingResult && $existingResult->num_rows === 1) {
    $existingOrder = $existingResult->fetch_assoc();
    $existingStmt->close();

    header('Content-Type: application/json');

    echo json_encode([
        'success' => true,
        'message' => 'Order already placed.',
        'order_number' => $existingOrder['order_number'],
    ]);

    exit();
}

$existingStmt->close();

$customerInput = $_POST['customer'] ?? '';

if (!is_string($customerInput)) {
    $_SESSION['error'] = 'Invalid customer information.';
    header('Location: index.php');
    exit();
}

$customer = trim($customerInput);

$cartRaw = $_POST['cart'] ?? '[]';

if (!is_string($cartRaw)) {
    jsonError('Invalid cart data.');
}

$cart = json_decode($cartRaw, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    jsonError('Invalid cart data. Please try again.');
}

    $paymentMethodInput = $_POST['payment_method'] ?? 'cash_pmt';

if (!is_string($paymentMethodInput)) {
    $_SESSION['error'] = 'Invalid payment method.';
    header('Location: index.php');
    exit();
}

$paymentMethod = trim($paymentMethodInput);

if (!in_array($paymentMethod, ['cash_pmt', 'card_pmt', 'eft_pmt'], true)) {
    jsonError('Invalid payment method.');
}

if (!is_array($cart) || empty($cart)) {
    jsonError('Cart is empty.');
}

// COLLECT ALL THE PRODUCT IDS FROM THE CART

$productIds = [];

foreach ($cart as $item) {
    if (!is_array($item)) {
        jsonError('Invalid cart item format.');
    }

    if (!array_key_exists('id', $item) || !array_key_exists('quantity', $item)) {
        jsonError('Cart item is missing required fields.');
    }

    $productId = filter_var($item['id'], FILTER_VALIDATE_INT);
    $quantity = filter_var($item['quantity'], FILTER_VALIDATE_INT);

    if ($productId === false || $productId <= 0 || $quantity === false || $quantity <= 0) {
        jsonError('Invalid product ID or quantity in cart.');
    }

    $productIds[] = $productId;
}

if (count($productIds) !== count(array_unique($productIds))) {
    jsonError('Duplicate products are not allowed in the cart.');
}

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
    $productId = filter_var($item['id'], FILTER_VALIDATE_INT);
    $quantity = filter_var($item['quantity'], FILTER_VALIDATE_INT);

    if ($productId === false || $quantity === false || $productId <= 0 || $quantity <= 0) {
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
    $items .= $product['name'].' x '.$quantity.', ';
}

$items = trim($items);

$status = 'Pending';

$sql = 'INSERT INTO orders (
    order_number,
    request_id,
    customer_name, 
    items, 
    total, 
    status, 
    payment_method
    ) VALUES (?, ?, ?, ?, ?, ?, ?)';

$stmt = $conn->prepare($sql);
if (!$stmt) {
    $conn->rollback();
    error_log('place_order.php: Failed to prepare order insert: '.$conn->error);
    jsonError('Failed to place the order. Please try again.');
}

if (!$stmt->bind_param(
    'ssssdss',
    $orderNumber,
    $requestId,
    $customer,
    $items,
    $total,
    $status,
    $paymentMethod
)) {
    $conn->rollback();
    error_log('place_order.php: Failed to bind order insert parameters: '.$stmt->error);
    jsonError('Failed to place the order. Please try again.');
}

if (!$stmt->execute()) {
    $insertError = $stmt->error;

    $stmt->close();
    $conn->rollback();

    if (stripos($insertError, 'request_id') !== false || stripos($insertError, 'uq_orders_request_id') !== false) {
        $existingStmt = $conn->prepare(
            'SELECT order_number
            FROM orders
            WHERE request_id = ?
            LIMIT 1'
        );

        if (
            $existingStmt
            && $existingStmt->bind_param('s', $requestId)
            && $existingStmt->execute()
        ) {
            $existingResult = $existingStmt->get_result();
            $existingOrder = $existingResult
                ? $existingResult->fetch_assoc()
                : null;

            $existingStmt->close();

            if ($existingOrder) {
                header('Content-Type: application/json');

                echo json_encode([
                    'success' => true,
                    'message' => 'Order already placed.',
                    'order_number' => $existingOrder['order_number'],
                ]);

                exit();
            }
        }

        if ($existingStmt) {
            $existingStmt->close();
        }
    }

    error_log(
        'place_order.php: Failed to insert order: '.$insertError
    );

    jsonError('Failed to place the order. Please try again.');
}

$stmt->close();

    $orderId = $conn->insert_id;

    recordAudit(
        $conn,
        (int) $_SESSION['user_id'],
        'order',
        (int) $orderId,
        'CREATE',
        [
            'order_number' => [null, $orderNumber],
            'status' => [null, $status],
            'payment_method' => [null,
            $paymentMethod, ],
            'total' => [null,
            number_format((float) $total, 2, '.', ''),
            ],
        ]
    );

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
        $productId = filter_var($item['id'], FILTER_VALIDATE_INT);
        $quantity = filter_var($item['quantity'], FILTER_VALIDATE_INT);

        if ($productId === false || $quantity === false || $productId <= 0 || $quantity <= 0) {
            $conn->rollback();
            jsonError('Invalid cart item.');
        }

        if (!isset($dbPrices[$productId])) {
            $conn->rollback();
            jsonError('Unable to determine product price.');
        }
        $price = $dbPrices[$productId];

        if (!$itemStmt->bind_param('iiid', $orderId, $productId, $quantity, $price)) {
            $conn->rollback();
            error_log('place_order.php: Failed to bind order item parameters: '.$itemStmt->error);
            jsonError('Failed to save order items. Please try again.');
        }

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

        logActivity(
            $conn,
            (int) $_SESSION['user_id'],
            'Created order: '.$orderNumber
        );

    if (!$conn->commit()) {
        $commitError = $conn->error;

        $conn->rollback();

        error_log('place_order.php: Failed to commit transaction: '.$conn->$commitError);

        jsonError('Failed to place the order. Please try again.');
    }

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully.',
        'order_number' => $orderNumber,
    ]);

    exit();
