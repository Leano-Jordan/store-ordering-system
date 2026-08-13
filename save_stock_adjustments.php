<?php

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once 'includes/db.php';
require_once 'includes/csrf.php';
verifyCsrfToken();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: adjust_stock.php');
    exit();
}

$productId = (int) $_POST['product_id'];
$type = $_POST['adjustment_type'];
if (!in_array($type, ['Increase', 'Decrease'])) {
    header('Location: adjust_stock.php');
    exit();
}

$quantity = (int) $_POST['quantity'];
if ($quantity <= 0) {
    header('Location: adjust_stock.php');
    exit();
}

$reason = trim($_POST['reason']);
$notes = trim($_POST['notes']);
$userId = $_SESSION['user_id'];
$conn->begin_transaction();

try {
    /*                    GET CURRENT STOCK                      */

    $stmt = $conn->prepare('SELECT stock 
FROM products 
WHERE id = ? FOR UPDATE');
    if (!$stmt) {
        throw new Exception($conn->error);
    }

    $stmt->bind_param('i', $productId);

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

    $stmt->bind_param('ii', $newStock, $productId);
    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
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

    if (!$conn->commit()) {
        throw new Exception('Commit failed: '.$conn->error);
    }
} catch (Throwable $e) {
    $conn->rollback();
    error_log('save_stock_adjustments.php: '.$e->getMessage());
    exit('Stock adjustment failed.');
}

require_once 'includes/logger.php';
logActivity(
    $conn,
    $_SESSION['user_id'],
    'Stock adjustment: '.$type.' '.$quantity.' x '.$reason.' (Product ID: '.$productId.')'
);
header('Location: stock_history.php');
exit();
