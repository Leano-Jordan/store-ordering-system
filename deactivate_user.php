<?php

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN]);
require_once 'includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: users.php');
    exit();
}

verifyCsrfToken();

require_once 'includes/db.php';

/************ ************** DEACTIVATE USERS *********** *****************/

$id = (int) ($_POST['id'] ?? 0);

/*********                *******  PREVENT DEACTIVATION MYSELF *******       ****************/

if ($id === (int) $_SESSION['user_id']) {
    $_SESSION['error'] = 'Cannot Deactivate your own account';
    header('Location: users.php');
    exit();
}

/*********         *********  MAKING SURE AT LEAST 1 ACTIVE ADMIN STAYS   **********       *********/

$check = $conn->prepare("SELECT COUNT(*) AS total FROM users WHERE role = ? AND status = 'Active'");

$adminRole = ROLE_ADMIN;
$check->bind_param('s', $adminRole);
$check->execute();

$totalAdmins = $check->get_result()->fetch_assoc()['total'];

/************************************    CHECK IF THIS USER IS ACTIVE ADMIN    ***************************************/

$stmt = $conn->prepare('SELECT role, status FROM users WHERE id = ?');

$stmt->bind_param('i', $id);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

if (
    $user &&
    $user['role'] === ROLE_ADMIN &&
    $user['status'] === 'Active' &&
    $totalAdmins <= 1
) {
    $_SESSION['error'] = 'Cannot Deactivate Last Admin';
    header('Location: users.php?error=last_admin');
    exit();
}

/*************************  DEACTIVATE USER  *********************/

$stmt = $conn->prepare(
    "UPDATE users 
    SET status = 'Inactive' 
    WHERE id = ?"
);

$stmt->bind_param('i', $id);
$stmt->execute();

$_SESSION['success'] = 'User deactivated successfully.';
header('Location: users.php');
exit();
