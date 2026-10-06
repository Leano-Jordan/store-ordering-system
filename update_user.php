<?php

declare(strict_types=1);

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN]);

require_once 'includes/db.php';
require_once 'includes/helpers.php';
require_once 'includes/upload_helpers.php';
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

/*
 * Check the common username conflict before any file operation.
 * The database unique index remains the final concurrency safeguard.
 */
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

if (!$check->bind_param('si', $username, $id)) {
    error_log('update_user.php: Failed to bind username check: '.$check->error);
    $check->close();
    $_SESSION['error'] = 'Unable to validate username.';
    header('Location: users.php');
    exit();
}

if (!$check->execute()) {
    error_log('update_user.php: Failed to execute username check: '.$check->error);
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
$currentImage = '';

try {
    /*
     * File-system work is deliberately outside the database transaction.
     * A moved upload can be cleaned up if the later database transaction fails.
     */
    if (
        isset($_FILES['profile_image'])
        && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE
    ) {
        if ($_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Profile image upload failed.');
        }

        $maxProfileImageSize = 2 * 1024 * 1024;

        if ($_FILES['profile_image']['size'] > $maxProfileImageSize) {
            throw new RuntimeException('Profile image must not exceed 2MB.');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($_FILES['profile_image']['tmp_name']);

        $allowedMimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        if ($mimeType === false || !isset($allowedMimeTypes[$mimeType])) {
            throw new RuntimeException(
                'Invalid profile image format. Allowed formats: JPEG, PNG, WEBP.'
            );
        }

        if (getimagesize($_FILES['profile_image']['tmp_name']) === false) {
            throw new RuntimeException('Uploaded file is not a valid image.');
        }

        if (!validateImageDimensions($_FILES['profile_image']['tmp_name'])) {
            throw new RuntimeException(
                'Profile image dimensions exceed the allowed limit of 2000x2000 pixels.'
            );
        }

        $newProfileImage = bin2hex(random_bytes(16))
            .'.'.$allowedMimeTypes[$mimeType];

        $profileImageDirectory = __DIR__.'/assets/images/profiles';

        if (!is_dir($profileImageDirectory)) {
            if (
                !mkdir($profileImageDirectory, 0755, true)
                && !is_dir($profileImageDirectory)
            ) {
                throw new RuntimeException(
                    'Failed to prepare profile image storage.'
                );
            }
        }

        if (!is_writable($profileImageDirectory)) {
            throw new RuntimeException(
                'Profile image storage is unavailable.'
            );
        }

        $newProfileImagePath = $profileImageDirectory.'/'.$newProfileImage;

        if (
            !move_uploaded_file(
                $_FILES['profile_image']['tmp_name'],
                $newProfileImagePath
            )
        ) {
            throw new RuntimeException('Failed to upload profile image.');
        }

        $profileImage = $newProfileImage;
        $newProfileImageUploaded = true;
    }

    if (!$conn->begin_transaction()) {
        throw new RuntimeException(
            'Unable to begin user update transaction.'
        );
    }

    $transactionStarted = true;

    /*
     * Lock the target row so last-admin protection is evaluated against
     * the current database state, not an earlier read.
     */
    $currentUserStmt = $conn->prepare(
        'SELECT profile_image, role, status
        FROM users
        WHERE id = ?
        LIMIT 1
        FOR UPDATE'
    );

    if (!$currentUserStmt) {
        throw new RuntimeException('Unable to load user.');
    }

    if (!$currentUserStmt->bind_param('i', $id)) {
        $currentUserStmt->close();
        throw new RuntimeException('Unable to load user.');
    }

    if (!$currentUserStmt->execute()) {
        $currentUserStmt->close();
        throw new RuntimeException('Unable to load user.');
    }

    $currentUserResult = $currentUserStmt->get_result();
    $currentUser = $currentUserResult
        ? $currentUserResult->fetch_assoc()
        : null;

    $currentUserStmt->close();

    if (!$currentUser) {
        throw new RuntimeException('User not found.');
    }

    if (
        $id === (int) $_SESSION['user_id']
        && ($role !== ROLE_ADMIN || $status !== 'Active')
    ) {
        throw new RuntimeException(
            'You cannot remove administrator access from your own account'
        );
    }

    $currentRole = (string) ($currentUser['role'] ?? '');
    $currentStatus = (string) ($currentUser['status'] ?? '');

    if (
        $currentRole === ROLE_ADMIN
        && $currentStatus === 'Active'
        && ($role !== ROLE_ADMIN || $status !== 'Active')
    ) {
        $adminCheck = $conn->prepare(
            "SELECT id
            FROM users
            WHERE role = ?
            AND status = 'Active'
            AND id != ?
            FOR UPDATE"
        );

        if (!$adminCheck) {
            throw new RuntimeException(
                'Unable to validate administrator access.'
            );
        }

        $adminRole = ROLE_ADMIN;

        if (!$adminCheck->bind_param('si', $adminRole, $id)) {
            $adminCheck->close();
            throw new RuntimeException(
                'Unable to validate administrator access.'
            );
        }

        if (!$adminCheck->execute()) {
            $adminCheck->close();
            throw new RuntimeException(
                'Unable to validate administrator access.'
            );
        }

        $adminResult = $adminCheck->get_result();

        if (!$adminResult) {
            $adminCheck->close();
            throw new RuntimeException(
                'Unable to validate administrator access.'
            );
        }

        $activeAdminCount = $adminResult->num_rows;
        $adminResult->free();
        $adminCheck->close();

        if ($activeAdminCount === 0) {
            throw new RuntimeException(
                'At least one active administrator must remain.'
            );
        }
    }

    $currentImage = basename(
        (string) ($currentUser['profile_image'] ?? '')
    );

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
        throw new RuntimeException(
            'Unable to update user.'
        );
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
    ) {
        $updateStmt->close();
        throw new RuntimeException('Unable to update user.');
    }

    if (!$updateStmt->execute()) {
        $updateError = $updateStmt->error;
        $updateStmt->close();

        if (stripos($updateError, 'username') !== false) {
            throw new RuntimeException('Username already exists.');
        }

        error_log(
            'update_user.php: User update failed: '.$updateError
        );

        throw new RuntimeException(
            'Unable to update user.'
        );
    }

    $updateStmt->close();

    $changes = [];

    if ($currentRole !== $role) {
        $changes['role'] = [$currentRole, $role];
    }

    if ($currentStatus !== $status) {
        $changes['status'] = [$currentStatus, $status];
    }

    if (
        (string) ($currentUser['profile_image'] ?? '')
        !== $profileImage
    ) {
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
            'Commit failed for user ID '.$id.': '.$conn->error
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
            'update_user.php: Failed to clean up new profile image after rollback.'
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
            'update_user.php: Failed to delete old profile image: '.$currentImage
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
