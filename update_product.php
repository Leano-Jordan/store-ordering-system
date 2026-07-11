<?php
require_once "includes/auth.php";
require_once "includes/permissions.php";
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once "includes/logger.php";
require_once "includes/db.php";

$id = (int)$_POST["id"];
$name = $_POST["name"];
$description = $_POST["description"];
$price = $_POST["price"];
$category = $_POST["category"];
$stock = (int)$_POST["stock"];

$currentImage = $_POST["current_image"];
$image = $currentImage;

if (!empty($_FILES["image"]["name"])) {

    $allowedExtensions = ["jpg", "jpeg", "png"];
    $allowedMimeTypes = ["image/jpeg", "image/png"];
    $fileExtension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
    $fileMimeType = mime_content_type($_FILES["image"]["tmp_name"]);

    if (
        !in_array($fileExtension, $allowedExtensions) ||
        !in_array($fileMimeType, $allowedMimeTypes)
    ) {

        header("Location: edit_product.php?id=" . (int)$id . "&error=invalid_image");
        exit();
    }

    if (!empty($currentImage) && file_exists("assets/images/products/" . $currentImage)) {

        unlink("assets/images/products/" . $currentImage);
    }

    $image = time() . "_" . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], __DIR__ . "/assets/images/products/" . $image);
}


$sql = "UPDATE products 
SET 
name=?, 
description=?, 
price=?, 
image=?, 
category=?, 
stock=? 
WHERE id=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ssdssii",
    $name,
    $description,
    $price,
    $image,
    $category,
    $stock,
    $id
);

if (!$stmt->execute()) {
    die("Execute Error: " . $stmt->error);
}

logActivity(
    $conn,
    $_SESSION["user_id"],
    "Updated product: " . $name
);

header("Location: products.php");
exit();
