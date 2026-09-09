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
require_once 'includes/audit.php';

/************ ************** DEACTIVATE USERS *********** *****************/

$idRaw = $_POST['id'] ?? null;

if (!is_string($idRaw)) {
    $_SESSION['error'] = 'Invalid user ID.';
    header('Location: users.php');
    exit();
}

$id = filter_var($idRaw, FILTER_VALIDATE_INT);

if ($id === false || $id <= 0) {
    $_SESSION['error'] = 'Invalid user ID.';
    header('Location: users.php');
    exit();
}

if (!$conn->begin_transaction()) {
    error_log(
        'deactivate_user.php: Failed to begin transaction: '
        .$conn->error
    );

    $_SESSION['error'] = 'Unable to deactivate user. Please try again.';
    header('Location: users.php');
    exit();
}

/*********                *******  PREVENT DEACTIVATION MYSELF *******       ****************/

if ($id === (int)
$_SESSION['user_id']) {
    $conn->rollback();

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

if (!$targetStmt->bind_param('i', $id)) {
    $conn->rollback();

    error_log('deactivate_user.php target lookup bind failed. '.$targetStmt->error);

    $targetStmt->close();

    $_SESSION['error'] = 'Unable to deactivate user.';
    header('Location: users.php');
    exit();
}

if (!$targetStmt->execute()) {
    $conn->rollback();
    error_log('deactivate_user.php target lookup execute failed. '.$targetStmt->error);
    $targetStmt->close();
    $_SESSION['error'] = 'Unable to deactivate user.';
    header('Location: users.php');
    exit();
}

$userResult = $targetStmt->get_result();

if (!$userResult) {
    error_log('deactivate_user.php target lookup result retrieval failed. '.$targetStmt->error);

    $targetStmt->close();
    $conn->rollback();

    $_SESSION['error'] = 'Unable to deactivate user.';
    header('Location: users.php');
    exit();
}

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
    $adminRole = ROLE_ADMIN;

    $adminLock = $conn->prepare(
        "SELECT id FROM users
        WHERE role = ? AND status = 'Active'
        ORDER BY id FOR UPDATE"
    );

    if (!$adminLock) {
        $conn->rollback();

        error_log(
            'deactivate_user.php active admin lock prepare failed. '
            .$conn->error
        );

        $_SESSION['error'] = 'Unable to validate administrator access.';

        header('Location: users.php');
        exit();
    }

    if (!$adminLock->bind_param('s', $adminRole)) {
        $adminLock->close();
        $conn->rollback();

        $_SESSION['error'] = 'Unable to validate administrator access.';

        header('Location: users.php');
        exit();
    }

    if (!$adminLock->execute()) {
        error_log('deactivate_user.php active admin lock execute failed. '.$adminLock->error);

        $adminLock->close();
        $conn->rollback();

        $_SESSION['error'] = 'Unable to validate administrator access.';

        header('Location: users.php');
        exit();
    }

    $adminResult = $adminLock->get_result();
    $activeAdminCount = $adminResult
        ? $adminResult->num_rows
        : 0;

    $adminLock->close();

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

if (!$updateStmt->bind_param('i', $id)) {
    $conn->rollback();

    error_log('deactivate_user.php update bind failed. '.$updateStmt->error);

    $updateStmt->close();

    $_SESSION['error'] = 'Unable to Deactivate user.';
    header('Location: users.php');
    exit();
}

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

try {
    recordAudit(
        $conn,
        (int) $_SESSION['user_id'],
        'user',
        $id,
        'DEACTIVATE',
        [
            'status' => ['Active', 'Inactive'],
        ]
    );

    if (!$conn->commit()) {
        throw new RuntimeException('deactivate_user.php commit failed: '.$conn->error);
    }
} catch (Throwable $exception) {
    $conn->rollback();

    error_log('deactivate_user.php transaction failed: '.$exception->getMessage()
    );

    $_SESSION['error'] = 'Unable to complete user deactivation.';
    header('Location: users.php');
    exit();
}

$_SESSION['success'] = 'User deactivated successfully.';
header('Location: users.php');
exit();
