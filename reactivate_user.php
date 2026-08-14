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

$updateStmt = $conn->prepare(
    "UPDATE users 
    SET status = 'Active' 
    WHERE id = ? AND status = 'Inactive'"
);

if (!$stmt) {
    $conn->rollback();
    error_log('reactivate_user.php update prepare failed. '.$conn->error);
    $_SESSION['error'] = 'Unable to Reactivate user.';
    header('Location: users.php');
    exit();
}

$stmt->bind_param('i', $id);

if (!$stmt->execute()) {
    $conn->rollback();
    error_log('reactivate_user.php update execute failed. '.$stmt->error);
    $stmt->close();
    $_SESSION['error'] = 'Unable to Reactivate user.';
    header('Location: users.php');
    exit();
}

if ($stmt->affected_rows !== 1) {
    $stmt->close();
    $_SESSION['error'] = 'User was not reactivated.';
    header('Location: users.php');
    exit();
}

$stmt->close();

$_SESSION['success'] = 'User reactivated successfully.';
header('Location: users.php');
exit();
