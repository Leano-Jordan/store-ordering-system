<?php
require_once "includes/auth.php";
require_once "includes/permissions.php";
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once "includes/db.php";
require_once "includes/logger.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: products.php");
    exit();
}

$name = trim($_POST["name"]);
$description = trim($_POST["description"]);
$price = trim($_POST["price"]);
$category = trim($_POST["category"]);
$stock = (int)$_POST["stock"];

if (
    empty($name) ||
    empty($price) ||
    empty($category) ||
    $stock < 0
) {
    die("Please complete all required fields.");
}

$image = time() . "_" . basename($_FILES["image"]["name"]);

$tempName = $_FILES["image"]["tmp_name"];

$allowedExtensions = ["jpg", "jpeg", "png",];
$allowedMimeTypes = ["image/jpeg", "image/png"];

$fileExtensions = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
$fileMimeType = mime_content_type($tempName);

if (!in_array(
    $fileExtensions,
    $allowedExtensions
) || ! in_array($fileMimeType, $allowedMimeTypes)) {

    header("Location: add_product.php?error=invalid_image");
    exit();
}

move_uploaded_file(
    $tempName,
    __DIR__ . "/assets/images/products/" . $image
);


$sql = "INSERT INTO products (name, description, price, image, category, stock) VALUES(?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ssdssi",
    $name,
    $description,
    $price,
    $image,
    $category,
    $stock
);

if ($stmt->execute()) {

    logActivity(
        $conn,
        $_SESSION["user_id"],
        "Added product: " . $name
    );

    header("Location: products.php");
    exit();
} else {

    header("Content-Type: application/json");

    echo json_encode([
        "success" => false,
        "message" => $stmt->error
    ]);
}

$stmt->close();
$conn->close();
