<?php
require_once "includes/auth.php";
require_once "includes/permissions.php";
requireRole([ROLE_ADMIN]);
require_once "includes/db.php";


/************          **************  CONFIRM DELETE PRODUCT  ***********            **************/

$id = $_POST["id"] ?? 0;
if ($id <= 0) {
    header("Location: products.php");
    exit();
}

$imageSql = "SELECT image FROM products WHERE id=?";
$imageStmt = $conn->prepare($imageSql);

$imageStmt->bind_param("i", $id);
$imageStmt->execute();

$result = $imageStmt->get_result();
$product = $result->fetch_assoc();

$imageStmt->close();

$sql = "UPDATE products SET status = 'Inactive' WHERE id=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    header("Location: products.php");
    exit();
} else {

    die("Failed to deactivate product: " . $stmt->error);
}
