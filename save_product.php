<?php

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once 'includes/csrf.php';
verifyCsrfToken();
require_once 'includes/db.php';
require_once 'includes/logger.php';
require_once 'includes/helpers.php';
require_once 'includes/upload_helpers.php';
require_once 'includes/audit.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: products.php');
    exit();
}

$nameRaw = $_POST['name'] ?? null;
$descriptionRaw = $_POST['description'] ?? '';
$priceRaw = $_POST['price'] ?? null;
$categoryRaw = $_POST['category'] ?? null;
$stockRaw = $_POST['stock'] ?? null;

if (
    !is_string($nameRaw) ||
    !is_string($descriptionRaw) ||
    !is_string($priceRaw) ||
    !is_string($categoryRaw) ||
    !is_string($stockRaw)
    ) {
    $_SESSION['error'] = 'Invalid input data.';
    header('Location: add_product.php');
    exit();
}

$name = trim($nameRaw);
$description = trim($descriptionRaw);
$category = trim($categoryRaw);

$price = filter_var($priceRaw, FILTER_VALIDATE_FLOAT);
$stock = filter_var($stockRaw, FILTER_VALIDATE_INT);

if ($name === '' || $category === '') {
    $_SESSION['error'] = 'Name and category cannot be empty.';
    header('Location: add_product.php');
    exit();
}

if ($price === false || $price <= 0) {
    $_SESSION['error'] = 'Please enter valid price.';
    header('Location: add_product.php');
    exit();
}

if ($stock === false || $stock < 0) {
    $_SESSION['error'] = 'Please enter valid stock quantity.';
    header('Location: add_product.php');
    exit();
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = 'Please upload a valid product image.';
    header('Location: add_product.php');
    exit();
}

if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
    $_SESSION['error'] = 'Image must be 2 MB or smaller.';
    header('Location: add_product.php');
    exit();
}

$tempName = $_FILES['image']['tmp_name'];

if (!is_uploaded_file($tempName)) {
    $_SESSION['error'] = 'Invalid image upload.';
    header('Location: add_product.php');
    exit();
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$fileMimeType = $finfo->file($tempName);

$allowedMimeTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png'];

if ($fileMimeType === false || !isset($allowedMimeTypes[$fileMimeType])
) {
    $_SESSION['error'] = 'Invalid image format.';
    header('Location: add_product.php');
    exit();
}

if (@getimagesize($tempName) === false) {
    $_SESSION['error'] = 'Uploaded file is not a valid image.';
    header('Location: add_product.php');
    exit();
}

if (!validateImageDimensions($tempName)) {
    $_SESSION['error'] = 'Image dimensions are too large.';
    header('Location: add_product.php');
    exit();
}

$fileExtensions = $allowedMimeTypes[$fileMimeType];
$image = bin2hex(random_bytes(16)).'.'.$fileExtensions;
$imagePath = __DIR__.'/assets/images/products/'.$image;

if (!move_uploaded_file($tempName, $imagePath)) {
    error_log('save_product.php: Failed to move uploaded image.');
    $_SESSION['error'] = 'Failed to save product image. Please try again.';
    header('Location: add_product.php');
    exit();
}

$sql = 'INSERT INTO products (name, 
    description, 
    price, 
    image, 
    category, 
    stock) 
    VALUES(?, ?, ?, ?, ?, ?)';

$transactionStarted = false;

try {
    if (!$conn->begin_transaction()) {
        throw new RuntimeException('Failed to begin product creation transaction: '.$conn->error);
    }

    $transactionStarted = true;

    if (!executeStatement(
        $conn,
        $sql,
        'ssdssi',
        [
            $name,
            $description,
            $price,
            $image,
            $category,
            $stock,
        ]
    )) {
        throw new RuntimeException('Failed to save product.');
    }

    $productId = (int) $conn->insert_id;

    if ($productId < 1) {
        throw new RuntimeException('Product creation returned an invalid ID.');
    }

    recordAudit(
        $conn,
        (int) $_SESSION['user_id'],
        'product',
        $productId,
        'CREATE',
        [
            'name' => [null, $name],
            'price' => [
                null,
                number_format((float) $price, 2, '.', ''),
            ],
            'category' => [null, $category],
            'stock' => [null, (string) $stock],
        ]
    );

    if (!$conn->commit()) {
        $commitError = $conn->error;

        if ($transactionStarted) {
            $conn->rollback();
        }

        throw new RuntimeException('Product creation commit failed: '.$commitError);
    }

    $transactionStarted = false;

    header('Location: products.php');

    exit();
} catch (Throwable $e) {
    if ($transactionStarted) {
        $conn->rollback();
    }

    if (is_file($imagePath) && !unlink($imagePath)) {
        error_log(
            'save_product.php: Failed to remove product image after rollback: '.$imagePath
        );
    }

    error_log(
        'save_product.php: '.$e->getMessage()
    );

    $_SESSION['error'] = 'Unable to save product. Please try again.';

    header('Location: add_product.php');
    exit();
}
