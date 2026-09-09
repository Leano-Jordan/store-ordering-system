<?php

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once 'includes/db.php';
require_once 'includes/csrf.php';
require_once 'includes/audit.php';
require_once 'includes/logger.php';
verifyCsrfToken();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: adjust_stock.php');
    exit();
}

$productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

if ($productId === false || $productId === null || $productId <= 0) {
    $_SESSION['error'] = 'Invalid product selected.';
    header('Location: adjust_stock.php');
    exit();
}

$type = $_POST['adjustment_type'] ?? null;

if (
    !is_string($type) ||
    !in_array($type, ['Increase', 'Decrease'], true)
) {
    $_SESSION['error'] = 'Invalid adjustment type.';
    header('Location: adjust_stock.php');
    exit();
}

$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

if ($quantity === false || $quantity === null || $quantity <= 0) {
    $_SESSION['error'] = 'Invalid adjustment quantity specified.';
    header('Location: adjust_stock.php');
    exit();
}

$reasonRaw = $_POST['reason'] ?? null;
$notesRaw = $_POST['notes'] ?? null;

if (!is_string($reasonRaw) || !is_string($notesRaw)) {
    $_SESSION['error'] = 'Invalid adjustment stock adjustment details.';
    header('Location: adjust_stock.php');
    exit();
}

$reason = trim($reasonRaw);
$notes = trim($notesRaw);

if ($reason === '') {
    $_SESSION['error'] = 'Please provide a reason for the stock adjustment.';
    header('Location: adjust_stock.php');
    exit();
}

$userId = $_SESSION['user_id'];

$transactionStarted = false;

try {
    if (!$conn->begin_transaction()) {
        throw new RuntimeException('Failed to begin stock adjustment transaction: '.$conn->error);
    }

    $transactionStarted = true;

    /*                    GET CURRENT STOCK                      */

    $stmt = $conn->prepare(
        'SELECT stock FROM products WHERE id = ? FOR UPDATE'
    );

    if (!$stmt) {
        throw new Exception($conn->error);
    }

    if (!$stmt->bind_param('i', $productId)) {
        $error = $stmt->error;
        $stmt->close();

        throw new RuntimeException('Failed to bind current stock lookup: '.$error);
    }

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    $result = $stmt->get_result();

    if (!$result) {
        throw new Exception('Unable to read current stock.');
    }

    $product = $result->fetch_assoc();
    $stmt->close();

    if (!$product) {
        throw new Exception('Product not found!');
    }

    $currentStock = (int) $product['stock'];

    /*                  CALCULATE CURRENT STOCK                  */

    if ($type === 'Increase') {
        $newStock = $currentStock + $quantity;
    } else {
        if ($currentStock < $quantity) {
            throw new Exception('Cannot reduce stock below zero.');
        }

        $newStock = $currentStock - $quantity;
    }

    /*                   UPDATE PRODUCT STOCK                    */

    $stmt = $conn->prepare('UPDATE products SET stock = ? WHERE id = ?');
    if (!$stmt) {
        throw new Exception($conn->error);
    }

    if (!$stmt->bind_param('ii', $newStock, $productId)) {
    $error = $stmt->error;
    $stmt->close();

    throw new RuntimeException('Failed to bind stock update parameters: '.$error);
}

if (!$stmt->execute()) {
    $error = $stmt->error;
    $stmt->close();

    throw new RuntimeException('Failed to update product stock: '.$error);
}

$stmt->close();

    /*           SAVE ADJUSTMENT HISTORY             */

    $stmt = $conn->prepare('INSERT 
INTO stock_adjustments
(product_id, user_id, adjustment_type, quantity, available_stock, reason, notes) VALUES (?, ?, ?, ?, ?, ?, ?)');
    if (!$stmt) {
        throw new Exception($conn->error);
    }

    $stmt->bind_param(
        'iisiiss',
        $productId,
        $userId,
        $type,
        $quantity,
        $newStock,
        $reason,
        $notes
    );

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    $stmt->close();

    $changes = ['stock' => [(string) $currentStock, (string) $newStock],
];

    recordAudit(
        $conn,
        (int) $userId,
        'product',
        (int) $productId,
        'STOCK_ADJUST',
        $changes
    );

    if (!logActivity(
        $conn,
        (int) $userId,
        'Stock adjustment: '.$type.' '.$quantity
        .' x '.$reason.' (Product ID: '.$productId.')
        '
    )
    ) {
        throw new RuntimeException('Failed to record stock adjustment activity.');
    }

    if (!$conn->commit()) {
        throw new Exception('Commit failed: '.$conn->error);
    }
} catch (Throwable $e) {
    if ($transactionStarted) {
        $conn->rollback();
    }

    error_log('save_stock_adjustments.php: '.$e->getMessage());

    exit('Stock adjustment failed.');
}

header('Location: stock_history.php');
exit();
