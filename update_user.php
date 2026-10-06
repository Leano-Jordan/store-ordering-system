<?php

declare(strict_types=1);

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN]);

require_once 'includes/db.php';
require_once 'includes/helpers.php';
require_once 'includes/user_update_helpers.php';
require_once 'includes/audit.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: users.php');
    exit();
}

require_once 'includes/csrf.php';
verifyCsrfToken();

$idRaw = $_POST['id'] ?? null;
$fullNameRaw = $_POST['full_name'] ?? null;
$usernameRaw = $_POST['username'] ?? null;
$roleRaw = $_POST['role'] ?? null;
$statusRaw = $_POST['status'] ?? null;

if (
    !is_string($idRaw)
    || !is_string($fullNameRaw)
    || !is_string($usernameRaw)
    || !is_string($roleRaw)
    || !is_string($statusRaw)
) {
    $_SESSION['error'] = 'Invalid user data.';
    header('Location: users.php');
    exit();
}

$id = filter_var($idRaw, FILTER_VALIDATE_INT);
$fullName = trim($fullNameRaw);
$username = trim($usernameRaw);
$role = trim($roleRaw);
$status = trim($statusRaw);

$allowedRoles = [ROLE_ADMIN, ROLE_MANAGER, ROLE_CASHIER, ROLE_KITCHEN];
$allowedStatuses = ['Active', 'Inactive'];

if ($id === false || $id <= 0) {
    $_SESSION['error'] = 'Invalid user ID.';
    header('Location: users.php');
    exit();
}

if ($fullName === '' || $username === '') {
    $_SESSION['error'] = 'Please complete all required fields.';
    header('Location: users.php');
    exit();
}

if (!in_array($role, $allowedRoles, true)) {
    $_SESSION['error'] = 'Invalid role selected.';
    header('Location: users.php');
    exit();
}

if (!in_array($status, $allowedStatuses, true)) {
    $_SESSION['error'] = 'Invalid user status.';
    header('Location: users.php');
    exit();
}

$check = $conn->prepare(
    'SELECT id FROM users
    WHERE username = ? AND id != ?
    LIMIT 1'
);

if (!$check) {
    error_log('update_user.php: Failed to prepare username check: '.$conn->error);
    $_SESSION['error'] = 'Unable to validate username.';
    header('Location: users.php');
    exit();
}

if (
    !$check->bind_param('si', $username, $id)
    || !$check->execute()
) {
    error_log('update_user.php: Failed to validate username: '.$check->error);
    $check->close();
    $_SESSION['error'] = 'Unable to validate username.';
    header('Location: users.php');
    exit();
}

$usernameResult = $check->get_result();

if (!$usernameResult) {
    error_log('update_user.php: Failed to retrieve username check: '.$check->error);
    $check->close();
    $_SESSION['error'] = 'Unable to validate username.';
    header('Location: users.php');
    exit();
}

if ($usernameResult->num_rows > 0) {
    $usernameResult->free();
    $check->close();
    $_SESSION['error'] = 'Username already exists.';
    header('Location: users.php');
    exit();
}

$usernameResult->free();
$check->close();

$newProfileImageUploaded = false;
$newProfileImagePath = null;
$profileImage = '';

try {
    $upload = prepareProfileImageUpload(
        isset($_FILES['profile_image']) && is_array($_FILES['profile_image'])
            ? $_FILES['profile_image']
            : null
    );

    $newProfileImageUploaded = $upload['uploaded'];
    $newProfileImagePath = $upload['path'];
    $profileImage = $upload['filename'];

    if (!$conn->begin_transaction()) {
        throw new RuntimeException(
            'Unable to begin user update transaction.'
        );
    }

    $transactionStarted = true;

    $userContext = loadUserUpdateContext(
        $conn,
        $id,
        $role,
        $status,
        (int) $_SESSION['user_id']
    );

    $currentImage = basename($userContext['profile_image']);

    if (!$newProfileImageUploaded) {
        $profileImage = $currentImage;
    }

    $updateStmt = $conn->prepare(
        'UPDATE users SET
            full_name = ?,
            username = ?,
            profile_image = ?,
            role = ?,
            status = ?
        WHERE id = ?'
    );

    if (!$updateStmt) {
        throw new RuntimeException('Unable to update user.');
    }

    if (
        !$updateStmt->bind_param(
            'sssssi',
            $fullName,
            $username,
            $profileImage,
            $role,
            $status,
            $id
        )
        || !$updateStmt->execute()
    ) {
        $updateError = $updateStmt->error;
        $updateStmt->close();

        if (stripos($updateError, 'username') !== false) {
            throw new RuntimeException('Username already exists.');
        }

        error_log(
            'update_user.php: User update failed: '.$updateError
        );

        throw new RuntimeException('Unable to update user.');
    }

    $updateStmt->close();

    $changes = [];

    if ($userContext['role'] !== $role) {
        $changes['role'] = [$userContext['role'], $role];
    }

    if ($userContext['status'] !== $status) {
        $changes['status'] = [$userContext['status'], $status];
    }

    if ($userContext['profile_image'] !== $profileImage) {
        $changes['profile_image'] = ['[changed]', '[changed]'];
    }

    if ($changes !== []) {
        recordAudit(
            $conn,
            (int) $_SESSION['user_id'],
            'user',
            $id,
            'UPDATE',
            $changes
        );
    }

    if (!$conn->commit()) {
        throw new RuntimeException(
            'Commit failed for user ID '.$id.'.'
        );
    }

    $transactionStarted = false;
} catch (Throwable $e) {
    if (isset($transactionStarted) && $transactionStarted) {
        $conn->rollback();
    }

    if (
        $newProfileImageUploaded
        && $newProfileImagePath !== null
        && is_file($newProfileImagePath)
        && !unlink($newProfileImagePath)
    ) {
        error_log(
            'update_user.php: Failed to clean up profile image after rollback.'
        );
    }

    error_log('update_user.php: '.$e->getMessage());

    $_SESSION['error'] =
        'Unable to complete user update. Please try again.';

    header('Location: edit_user.php?id='.$id);
    exit();
}

if (
    $newProfileImageUploaded
    && $currentImage !== ''
    && $currentImage !== 'default-profile.png'
) {
    $oldImagePath =
        __DIR__.'/assets/images/profiles/'.$currentImage;

    if (is_file($oldImagePath) && !unlink($oldImagePath)) {
        error_log(
            'update_user.php: Failed to delete old profile image: '
            .$currentImage
        );
    }
}

if (
    isset($_SESSION['user_id'])
    && (int) $_SESSION['user_id'] === $id
) {
    $_SESSION['full_name'] = $fullName;
    $_SESSION['role'] = $role;
    $_SESSION['profile_image'] = $profileImage;
}

$_SESSION['success'] = 'User Updated Successfully.';
header('Location: users.php');
exit();
