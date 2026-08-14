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

$conn->begin_transaction();

/************ ************** DEACTIVATE USERS *********** *****************/

$id = (int) ($_POST['id'] ?? 0);

/*********                *******  PREVENT DEACTIVATION MYSELF *******       ****************/

if ($id === (int) $_SESSION['user_id']) {
    $_SESSION['error'] = 'Cannot Deactivate your own account';
    header('Location: users.php');
    exit();
}

/*********         *********  MAKING SURE AT LEAST 1 ACTIVE ADMIN STAYS   **********       *********/

$targetStmt = $conn->prepare('SELECT role, status FROM users WHERE id = ? FOR UPDATE');

if (!$targetStmt) {
    $conn->rollback();
    error_log('deactivate_user.php target lookup prepare failed. '.$conn->error);
    $_SESSION['error'] = 'Unable to deactivate user.';
    header('Location: users.php');
    exit();
}

$targetStmt->bind_param('i', $id);

if (!$targetStmt->execute()) {
    $conn->rollback();
    error_log('deactivate_user.php target lookup execute failed. '.$targetStmt->error);
    $targetStmt->close();
    $_SESSION['error'] = 'Unable to deactivate user.';
    header('Location: users.php');
    exit();
}

$userResult = $targetStmt->get_result();
$user = $userResult->fetch_assoc();
$targetStmt->close();

if (!$user) {
    $conn->rollback();
    $_SESSION['error'] = 'User not found.';
    header('Location: users.php');
    exit();
}

/************************************    CHECK IF THIS USER IS ACTIVE ADMIN    ***************************************/

if ($user['role'] === ROLE_ADMIN && $user['status'] === 'Active') {
    $adminCheck = $conn->prepare("SELECT COUNT(*) AS total FROM users WHERE role = ? AND status ='Active'");

    if (!$adminCheck) {
        $conn->rollback();
        error_log('deactivate_user.php active admin count prepare failed. '.$conn->error);
        $_SESSION['error'] = 'Unable to validate administrator access.';
        header('Location: users.php');
        exit();
    }

    $adminRole = ROLE_ADMIN;
    $adminCheck->bind_param('s', $adminRole);

    if (!$adminCheck->execute()) {
        $conn->rollback();
        error_log('deactivate_user.php active admin count execute failed. '.$adminCheck->error);
        $adminCheck->close();
        $_SESSION['error'] = 'Unable to validate administrator access.';
        header('Location: users.php');
        exit();
    }

    $adminResult = $adminCheck->get_result();
    $activeAdminCount = (int) $adminResult->fetch_assoc()['total'];
    $adminCheck->close();

    if ($activeAdminCount <= 1) {
        $conn->rollback();
        $_SESSION['error'] = 'Cannot Deactivate last admin.';
        header('Location: users.php?error=last_admin');
        exit();
    }
}

/*************************  DEACTIVATE USER  *********************/

$updateStmt = $conn->prepare(
    "UPDATE users 
    SET status = 'Inactive' 
    WHERE id = ?"
);

if (!$updateStmt) {
    $conn->rollback();
    error_log('deactivate_user.php update prepare failed. '.$conn->error);
    $_SESSION['error'] = 'Unable to Deactivate user.';
    header('Location: users.php');
    exit();
}

$updateStmt->bind_param('i', $id);

if (!$updateStmt->execute()) {
    $conn->rollback();
    error_log('deactivate_user.php update execute failed. '.$updateStmt->error);
    $updateStmt->close();
    $_SESSION['error'] = 'Unable to Deactivate user.';
    header('Location: users.php');
    exit();
}

if ($updateStmt->affected_rows !== 1) {
    $updateStmt->close();
    $conn->rollback();
    $_SESSION['error'] = 'User was not deactivated.';
    header('Location: users.php');
    exit();
}

$updateStmt->close();

if (!$conn->commit()) {
    $conn->rollback();
    error_log('deactivate_user.php commit failed. ');
    $_SESSION['error'] = 'Unable to complete user deactivation.';
    header('Location: users.php');
    exit();
}

$_SESSION['success'] = 'User deactivated successfully.';
header('Location: users.php');
exit();
