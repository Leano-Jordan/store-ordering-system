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

/************ *       ************* REACTIVATE USERS ***********              *****************/

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    header('Location: users.php');
    exit();
}
/*********                *******  PREVENT DEACTIVATION MYSELF *******       ****************/

if ($id === (int) $_SESSION['user_id']) {
    header('Location: users.php');
    exit();
}

/*************************  REACTIVATE USER  *********************/

$stmt = $conn->prepare("UPDATE users SET status = 'Active' WHERE id = ?");

if (!$stmt) {
    error_log('reactivate_user.php prepare failed: '.$conn->error);
    $_SESSION['error'] = 'Unable to reactivate user.';
    header('Location: users.php');
    exit();
}

$stmt->bind_param('i', $id);

if (!$stmt->execute()) {
    error_log('reactivate_user.php execute failed: '.$stmt->error);
    $_SESSION['error'] = 'Unable to reactivate user.';
    header('Location: users.php');
    exit();
}

$_SESSION['success'] = 'User reactivated successfully.';
header('Location: users.php');
exit();
