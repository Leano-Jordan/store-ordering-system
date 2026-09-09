<?php

declare(strict_types=1);

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once 'includes/logger.php';
require_once 'includes/db.php';
require_once 'includes/helpers.php';
require_once 'includes/audit.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: products.php');
    exit();
}

require_once 'includes/csrf.php';
verifyCsrfToken();

$idRaw = $_POST['id'] ?? null;
$name = trim((string) ($_POST['name'] ?? ''));
$description = trim((string) ($_POST['description'] ?? ''));
$priceRaw = $_POST['price'] ?? null;
$category = trim((string) ($_POST['category'] ?? ''));

$id = filter_var($idRaw, FILTER_VALIDATE_INT);
$price = filter_var($priceRaw, FILTER_VALIDATE_FLOAT);

if ($id === false || $id <= 0) {
    exit('Invalid product ID.');
}

if ($price === false || $price < 0) {
    exit('Invalid price.');
}

    if ($name === '' || $category === '') {
        exit('Name and category cannot be empty.');
    }

$currentProductStmt = $conn->prepare('SELECT name, description, price, image, category FROM products WHERE id = ?');

if (!$currentProductStmt) {
    error_log('update_product.php: Failed to prepare current product lookup: '.$conn->error);
    exit('Unable to load product.');
}

if (!$currentProductStmt->bind_param('i', $id)) {
    error_log('update_product.php: Failed to bind parameters for current product lookup: '.$currentProductStmt->error);
    $currentProductStmt->close();
    exit('Unable to load product.');
}

if (!$currentProductStmt->execute()) {
    error_log('update_product.php: Failed to execute current product lookup: '.$currentProductStmt->error);
    $currentProductStmt->close();
    exit('Unable to load product.');
}

$currentProductResult = $currentProductStmt->get_result();

if (!$currentProductResult) {
    error_log('update_product.php: Failed to retrieve current product lookup: '.$currentProductStmt->error);
    $currentProductStmt->close();
    exit('Unable to load product.');
}

$currentProduct = $currentProductResult->fetch_assoc();
$currentProductStmt->close();

if (!$currentProduct) {
    exit('Product not found.');
}

$currentImage = basename((string) ($currentProduct['image'] ?? ''));
$image = $currentImage;

$newImageUploaded = false;
$newImagePath = null;

if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        error_log('update_product.php: Product image File upload error: '.$_FILES['image']['error']);
        header('Location: edit_product.php?id='.$id.'&error=upload_failed.');
        exit();
    }

    if (!is_uploaded_file($_FILES['image']['tmp_name'])) {
        $_SESSION['error'] = 'Invalid image upload.';
        header('Location: edit_product.php?id='.$id);
        exit();
    }

    $maxImageSize = 2 * 1024 * 1024; // 2MB

    if ($_FILES['image']['size'] > $maxImageSize) {
        header('Location: edit_product.php?id='.$id.'&error=Image_size_exceeds_2MB.');
        exit();
    }

    $info = new finfo(FILEINFO_MIME_TYPE);
    $fileMimeType = $info->file($_FILES['image']['tmp_name']);

    if ($fileMimeType === false) {
        $_SESSION['error'] = 'Invalid to validate image format.';
        header('Location: edit_product.php?id='.$id);
        exit();
    }

        if (@getimagesize($_FILES['image']['tmp_name']) === false) {
        $_SESSION['error'] = 'Uploaded file is not a valid image.';
        header('Location: edit_product.php?id='.$id);
        exit();
    }

    if (!validateImageDimensions($_FILES['image']['tmp_name'])) {
        $_SESSION['error'] = 'Image dimensions are too large.';
        header('Location: edit_product.php?id='.$id);
        exit();
    }

    $allowedMimeTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png'];

    if (!isset($allowedMimeTypes[$fileMimeType])) {
        header('Location: edit_product.php?id='.$id.'&error=Invalid_image_format.');
        exit();
    }

    $fileExtensions = $allowedMimeTypes[$fileMimeType];

    $image = bin2hex(random_bytes(16)).'.'.$fileExtensions;
    $newImagePath = __DIR__.'/assets/images/products/'.$image;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $newImagePath)) {
        error_log('update_product.php: Failed to store new product image.');
        header('Location: edit_product.php?id='.$id.'&error=Failed_to_upload_image.');
        exit();
    }

    $newImageUploaded = true;
}

$transactionStarted = false;

if (!$conn->begin_transaction()) {
    error_log('update_product.php: Failed to begin product update transaction: '.$conn->error);

    if ($newImageUploaded && $newImagePath !== null && is_file($newImagePath)) {
        if (!unlink($newImagePath)) {
            error_log('update_product.php: Failed to clean up new product image after transaction start failure: '.$newImagePath);
        }
    }

    $_SESSION['error'] = 'Unable to update product. Please try again.';
    header('Location: edit_product.php?id='.$id);
    exit();
}

$transactionStarted = true;

$sql = 'UPDATE products SET 
name=?, description=?, 
price=?, image=?, 
category=? WHERE id=?';

$updatedSucceeded = executeStatement(
    $conn,
    $sql,
    'ssdssi',
    [
        $name,
        $description,
        $price,
        $image,
        $category,
        $id,
    ]
);

if (!$updatedSucceeded) {
    if ($transactionStarted) {
        $conn->rollback();
    }

    if ($newImageUploaded && $newImagePath !== null && is_file($newImagePath)) {
        if (!unlink($newImagePath)) {
            error_log('update_product.php: Failed to clean up new product image after database failure: '.$newImagePath);
        }
    }
    error_log('update_product.php: Failed to update product ID: '.$id);

    $_SESSION['error'] = 'Unable to update product. Please try again.';
    header('Location: edit_product.php?id='.$id);
    exit();
}

$changes = [];

if ((string) ($currentProduct['name'] ?? '') !== $name) {
    $changes['name'] = [
        (string) ($currentProduct['name'] ?? ''),
        $name,
    ];
}

if ((string) ($currentProduct['description'] ?? '') !== $description) {
    $changes['description'] = [
        (string) ($currentProduct['description'] ?? ''),
        $description,
    ];
}

if ((string) ($currentProduct['price'] ?? '') !== (string) $price) {
    $changes['price'] = [
        (string) ($currentProduct['price'] ?? ''),
        (string) $price,
    ];
}

if ((string) ($currentProduct['category'] ?? '') !== $category) {
    $changes['category'] = [
        (string) ($currentProduct['category'] ?? ''),
        $category,
    ];
}

if ($currentImage !== $image) {
    $changes['image'] = [
        $currentImage === '' ? null : 'IMAGE',
        $image === '' ? null : 'IMAGE',
    ];
}

try {
    if ($changes !== []) {
        recordAudit(
            $conn,
            (int) $_SESSION['user_id'],
            'product',
            $id,
            'UPDATE',
            $changes
        );
    }

    if (!logActivity($conn, (int) $_SESSION['user_id'], 'Updated Product: '.$name)
            ) {
        throw new RuntimeException('Product activity logging failed.');
    }

    if (!$conn->commit()) {
        throw new RuntimeException('Product update transaction commit failed.');
    }
} catch (\Throwable $exception) {
    if ($transactionStarted) {
        $conn->rollback();
    }

    error_log('update_product.php: Audit transaction failed for product ID '.$id.': '.$exception->getMessage());

    if ($newImageUploaded && $newImagePath !== null && is_file($newImagePath)
    ) {
        if (!unlink($newImagePath)) {
            error_log('update_product.php: Failed to clean up new product image after audit failure: '.$newImagePath);
        }
    }

    $_SESSION['error'] = 'Unable to complete product update. Please try again.';

    header('Location: edit_product.php?id='.$id);
    exit();
}

if ($newImageUploaded && $currentImage !== '' && $currentImage !== 'no-image.png') {
    $oldImagePath = __DIR__.'/assets/images/products/'.$currentImage;

    if (is_file($oldImagePath)) {
        if (!unlink($oldImagePath)) {
            error_log('update_product.php: Failed to delete old product image: '.$oldImagePath);
        }
    }
}

header('Location: products.php');
exit();
