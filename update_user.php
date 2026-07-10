<?php
require_once "includes/auth.php";
require_once "includes/permissions.php";
requireRole([ROLE_ADMIN]);
require_once "includes/db.php";

$id = (int)$_POST["id"];
$fullName = trim($_POST["full_name"]);
$username = trim($_POST["username"]);
$role = trim($_POST["role"]);
$status = trim($_POST["status"]);

if (
    empty($fullName) ||
    empty($username) ||
    empty($role) ||
    empty($status)
) {
    die("Please complete all fields.");
}

$check = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
$check->bind_param("si", $username, $id);
$check->execute();

if ($check->get_result()->num_rows > 0) {
    die("Username already exists.");
}

$sql = "UPDATE users 
SET 
full_name = ?, 
username = ?, 
role = ?, 
status = ? WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ssssi",
    $fullName,
    $username,
    $role,
    $status,
    $id
);

if (!$stmt->execute()) {
    die("Execute Error: " . $stmt->error);
}
header("Location: users.php?success=user_updated");
exit();
