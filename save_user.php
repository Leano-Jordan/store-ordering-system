<?php
require_once "includes/auth.php";
require_once "includes/permissions.php";
requireRole([ROLE_ADMIN]);
require_once "includes/db.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: users.php");
    exit();
}

$fullName = trim($_POST["full_name"]);
$username = trim($_POST["username"]);
$password = trim($_POST["password"]);
$role = trim($_POST["role"]);
$status = trim($_POST["status"]);

if (
    empty($fullName) ||
    empty($username) ||
    empty($password) ||
    empty($role) ||
    empty($status)
) {
    die("Please complete all required fields.");
}

$check = $conn->prepare("SELECT id FROM users WHERE username = ?");
$check->bind_param("s", $username);
$check->execute();
$existingUser = $check->get_result();

if ($existingUser->num_rows > 0) {
    die("Username already exists.");
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (full_name, username, password, role, status) VALUES(?, ?, ?, ?, ?)");
$stmt->bind_param(
    "sssss",
    $fullName,
    $username,
    $hashedPassword,
    $role,
    $status
);

if ($stmt->execute()) {
    header("Location: users.php?success=user_added");
    exit();
} else {

    die("Error: " . $stmt->error);
}

$stmt->close();
$conn->close();
