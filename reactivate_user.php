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
$userCheck = $conn->prepare('SELECT id, status FROM users WHERE id = ? LIMIT 1');

if (!$userCheck) {
    error_log('reactivate_user.php prepare user check failed: '.$conn->error);
    $_SESSION['error'] = 'Unable to reactivate user.';
    header('Location: users.php');
    exit();
}

$userCheck->bind_param('i', $id);

if (!$userCheck->execute()) {
    error_log('reactivate_user.php execute user check failed: '.$userCheck->error);
    $userCheck->close();

    $_SESSION['error'] = 'Unable to reactivate user.';
    header('Location: users.php');
    exit();
}

$userResult = $userCheck->get_result();
$userRecord = $userResult->fetch_assoc();

$userCheck->close();

if (!$userRecord) {
    $_SESSION['error'] = 'User not found.';
    header('Location: users.php');
    exit();
}

$conn->begin_transaction();

try {
    $updateStmt = $conn->prepare(
        "UPDATE users 
    SET status = 'Active' 
    WHERE id = ? AND status = 'Inactive'"
    );

    if (!$updateStmt) {
        throw new RuntimeException('reactivate_user.php update prepare failed. '.$conn->error);
    }

    if (!$updateStmt->bind_param('i', $id)) {
        $updateStmt->close();

        throw new RuntimeException('reactivate_user.php update bind failed. '.$updateStmt->error);
    }

    if (!$updateStmt->execute()) {
        $error = $updateStmt->error;
        $updateStmt->close();

        throw new RuntimeException('reactivate_user.php update execute failed. '.$error);
    }

    if ($updateStmt->affected_rows !== 1) {
        $updateStmt->close();

        throw new RuntimeException('reactivate_user.php user was not reactivated');
    }

    $updateStmt->close();

    recordAudit(
        $conn,
        (int) $_SESSION['user_id'],
        'user',
        $id,
        'REACTIVATE',
        ['status' => [(string) $userRecord['status'], 'Active'],
    ]
    );

    if (!$conn->commit()) {
        throw new RuntimeException('reactivate_user.php commit failed: '.$conn->error);
    }
} catch (\Throwable $exception) {
    $conn->rollback();

    error_log(
        'reactivate_user.php transaction failed: '
        .$exception->getMessage()
    );

    $_SESSION['error'] = 'Unable to reactivate user.';
    header('Location: users.php');
    exit();
}

$_SESSION['success'] = 'User reactivated successfully.';
header('Location: users.php');
exit();
