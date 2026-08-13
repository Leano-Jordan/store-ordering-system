<?php

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN]);
require_once 'includes/db.php';
require_once 'includes/logger.php';
require_once 'includes/csrf.php';
verifyCsrfToken();

/************          **************  CONFIRM DELETE PRODUCT  ***********            **************/

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: products.php');
    exit();
}

$productSql = 'SELECT name, image FROM products WHERE id=?';
$imageStmt = $conn->prepare($productSql);
$imageStmt->bind_param('i', $id);
$imageStmt->execute();

$result = $imageStmt->get_result();
$product = $result->fetch_assoc();
if (!$product) {
    header('Location: products.php');
    exit();
}

$productName = $product['name'];

$imageStmt->close();

$sql = "UPDATE products SET status = 'Inactive' WHERE id=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    logActivity(
        $conn,
        $_SESSION['user_id'],
        'Deactivated product: '.$productName
    );

    header('Location: products.php');
    exit();
} else {
    error_log('SwiftOrder product deactivation failed. Product ID: '.$id);
    $_SESSION['error'] = 'Unable to deactivate the product. Please try again.';
    header('Location: products.php');
    exit();
}
