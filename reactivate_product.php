<?php

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN]);
require_once 'includes/db.php';
require_once 'includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: products.php');
    exit();
}

verifyCsrfToken();

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    header('Location: products.php');
    exit();
}

$stmt = $conn->prepare("UPDATE products SET status = 'Active' WHERE id = ?");

if (!$stmt) {
    error_log('reactivate_product.php prepare failed: '.$conn->error);
    $_SESSION['error'] = 'Unable to reactivate product.';
    header('Location: products.php');
    exit();
}

$stmt->bind_param('i', $id);

if (!$stmt->execute()) {
    error_log('reactivate_product.php execute failed: '.$stmt->error);
    $_SESSION['error'] = 'Unable to reactivate product.';
    header('Location: products.php');
    exit();
}

$_SESSION['success'] = 'Product reactivated successfully.';
header('Location: products.php');
exit();
