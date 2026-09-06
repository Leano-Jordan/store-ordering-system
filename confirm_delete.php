<?php

declare(strict_types=1);

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN]);
require_once 'includes/db.php';
require_once 'includes/logger.php';
require_once 'includes/csrf.php';
require_once 'includes/audit.php';
verifyCsrfToken();

/************          **************  CONFIRM DELETE PRODUCT  ***********            **************/

$idRaw = $_POST['id'] ?? null;

if (!is_string($idRaw)) {
    $_SESSION['error'] = 'Invalid product ID.';
    header('Location: products.php');
    exit();
}

$id = filter_var($idRaw, FILTER_VALIDATE_INT);

if ($id === false || $id <= 0) {
    header('Location: products.php');
    exit();
}

$productSql = 'SELECT name, image FROM products WHERE id=?';
$imageStmt = $conn->prepare($productSql);

if (!$imageStmt) {
    error_log('confirm_delete.php: Failed to prepare product lookup: '.$conn->error);
    $_SESSION['error'] = 'Unable to load the product. Please try again.';
    header('Location: products.php');
    exit();
}

if (!$imageStmt->bind_param('i', $id)) {
    error_log('confirm_delete.php: Failed to bind product ID: '.$imageStmt->error);
    $imageStmt->close();
    $_SESSION['error'] = 'Unable to load the product. Please try again.';
    header('Location: products.php');
    exit();
}

if (!$imageStmt->execute()) {
    error_log('confirm_delete.php: Failed to execute product lookup: '.$imageStmt->error);
    $imageStmt->close();
    $_SESSION['error'] = 'Unable to load the product. Please try again.';
    header('Location: products.php');
    exit();
}

$result = $imageStmt->get_result();

if (!$result) {
    error_log('confirm_delete.php: Failed to retrieve product result: '.$imageStmt->error);
    $imageStmt->close();
    $_SESSION['error'] = 'Unable to load the product. Please try again.';
    header('Location: products.php');
    exit();
}

$product = $result->fetch_assoc();

if (!$product) {
    $imageStmt->close();
    header('Location: products.php');
    exit();
}

$productName = $product['name'];

$imageStmt->close();

$conn->begin_transaction();

try {
    $sql = "UPDATE products SET status = 'Inactive' WHERE id=? AND status = 'Active'";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new RuntimeException('Failed to prepare product deactivation.');
    }

    if (!$stmt->bind_param('i', $id)) {
        $stmt->close();

        throw new RuntimeException('Failed to bind product deactivation.');
    }

    if (!$stmt->execute()) {
        $error = $stmt->error;

        $stmt->close();

        throw new RuntimeException('Failed to execute product deactivation: '.$error);
    }

    if ($stmt->affected_rows !== 1) {
        $stmt->close();

        throw new RuntimeException('Product was not active or could not be deactivated.');
    }

    $stmt->close();

    if (!logActivity(
        $conn,
        (int) $_SESSION['user_id'],
        'Deactivated product: '.$productName
    )) {
        throw new RuntimeException('Failed to record product activity.');
    }

    recordAudit(
        $conn,
        (int) $_SESSION['user_id'],
        'product',
        $id,
        'DEACTIVATE',
        [
        'status' => ['Active', 'Inactive'],
]
    );

    if (!$conn->commit()) {
        throw new RuntimeException('Product deactivation commit failed.');
    }

    $_SESSION['success'] = 'Product deactivated successfully.';

    header('Location: products.php');
    exit();
} catch (Throwable $e) {
    $conn->rollback();

    error_log('confirm_delete.php: '.$e->getMessage());

    $_SESSION['error'] = 'Unable to deactivate the product. Please try again.';

    header('Location: products.php');
    exit();
}
