<?php

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN]);

require_once 'includes/db.php';
require_once 'includes/helpers.php';
require_once 'includes/upload_helpers.php';

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
    !is_string($idRaw) ||
    !is_string($fullNameRaw) ||
    !is_string($usernameRaw) ||
    !is_string($roleRaw) ||
    !is_string($statusRaw)
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

$currentUserStmt = $conn->prepare('SELECT profile_image, role, status FROM users WHERE id = ?');

if (!$currentUserStmt) {
    error_log('update_user.php: Failed to prepare current user lookup: '.$conn->error);
    $_SESSION['error'] = 'Unable to load user.';
    header('Location: users.php');
    exit();
}

if (!$currentUserStmt->bind_param('i', $id)) {
    error_log('update_user.php: Failed to bind parameters for current user lookup: '.$currentUserStmt->error);
    $currentUserStmt->close();
    $_SESSION['error'] = 'Unable to load user.';
    header('Location: users.php');
    exit();
}

if (!$currentUserStmt->execute()) {
    error_log('update_user.php: Failed to execute current user lookup: '.$currentUserStmt->error);
    $currentUserStmt->close();
    $_SESSION['error'] = 'Unable to load user.';
    header('Location: users.php');
    exit();
}

$currentUserResult = $currentUserStmt->get_result();

if (!$currentUserResult) {
    error_log('update_user.php: Failed to retrieve current user: '.$currentUserStmt->error);
    $currentUserStmt->close();
    $_SESSION['error'] = 'Unable to load user.';
    header('Location: users.php');
    exit();
}

$currentUser = $currentUserResult->fetch_assoc();
$currentUserStmt->close();

if (!$currentUser) {
    $_SESSION['error'] = 'User not found.';
    header('Location: users.php');
    exit();
}

if ($id === (int) $_SESSION['user_id'] && ($role !== ROLE_ADMIN || $status !== 'Active')) {
    $_SESSION['error'] = 'You cannot remove administrator access from your own account';
    header('Location: users.php');
    exit();
}

if ($currentUser['role'] === ROLE_ADMIN && $currentUser['status'] === 'Active' && ($role !== ROLE_ADMIN || $status !== 'Active')) {
    $adminCheck = $conn->prepare("SELECT COUNT(*) AS total FROM users WHERE role = ? AND status = 'Active' AND id != ?");

    if (!$adminCheck) {
        error_log('update_user.php: Failed to prepare active admin check: '.$conn->error);
        $_SESSION['error'] = 'Unable to validate administrator access.';
        header('Location: users.php');
        exit();
    }

    $adminRole = ROLE_ADMIN;

    if (!$adminCheck->bind_param('si', $adminRole, $id)) {
        error_log('update_user.php: Failed to bind active admin check: '.$adminCheck->error);
        $adminCheck->close();
        $_SESSION['error'] = 'Unable to validate administrator access.';
        header('Location: users.php');
        exit();
    }

    if (!$adminCheck->execute()) {
        error_log('update_user.php: Failed to execute admin check: '.$adminCheck->error);
        $adminCheck->close();
        $_SESSION['error'] = 'Unable to validate administrator access.';
        header('Location: users.php');
        exit();
    }

    $adminResult = $adminCheck->get_result();
    $activeAdminCount = (int) $adminResult->fetch_assoc()['total'];

    $adminCheck->close();

    if ($activeAdminCount === 0) {
        $_SESSION['error'] = 'At least one active administrator must remain.';
        header('Location: users.php');
        exit();
    }
}

$currentImage = basename((string) ($currentUser['profile_image'] ?? ''));

$profileImage = $currentImage;
$newProfileImageUploaded = false;
$newProfileImagePath = null;

$check = $conn->prepare('SELECT id FROM users WHERE username = ? AND id != ?');

if (!$check) {
    error_log('update_user.php: Failed to prepare username check: '.$conn->error);
    $_SESSION['error'] = 'Unable to validate username.';
    header('Location: users.php');
    exit('Unable to check username.');
}

if (!$check->bind_param('si', $username, $id)) {
    error_log('update_user.php: Failed to bind parameters for username check: '.$check->error);
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

if ($usernameResult->num_rows > 0) {
    $check->close();
    $_SESSION['error'] = 'Username already exists.';
    header('Location: users.php');
    exit();
}

$check->close();

if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
        error_log('update_user.php: Profile image file upload error: '.$_FILES['profile_image']['error']);
        $_SESSION['error'] = 'Profile image upload failed.';
        header('Location: users.php');
        exit();
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($_FILES['profile_image']['tmp_name']);

    $allowedMimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if ($mimeType === false || !isset($allowedMimeTypes[$mimeType])) {
        $_SESSION['error'] = 'Invalid profile image format. Allowed formats: JPEG, PNG, WEBP.';
        header('Location: edit_user.php');
        exit();
    }

    if (getimagesize($_FILES['profile_image']['tmp_name']) === false) {
        $_SESSION['error'] = 'Uploaded file is not a valid image.';
        header('Location: edit_user.php?id='.$id);
        exit();
    }

    if (!validateImageDimensions($_FILES['profile_image']['tmp_name'])) {
        $_SESSION['error'] = 'Profile image dimensions exceed the allowed limit of 2000x2000 pixels.';
        header('Location: edit_user.php?id='.$id);
        exit();
    }

    $newProfileImage = bin2hex(random_bytes(16)).'.'.$allowedMimeTypes[$mimeType];

    $newProfileImagePath = __DIR__.'/assets/images/profiles/'.$newProfileImage;

    if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $newProfileImagePath)) {
        error_log('update_user.php: Failed to store new profile image.');
        $_SESSION['error'] = 'Failed to upload profile image.';
        header('Location: edit_user.php?id='.$id);
        exit();
    }

    $profileImage = $newProfileImage;
    $newProfileImageUploaded = true;
}

    $sql = 'UPDATE users
            SET full_name = ?,
                username = ?,
                profile_image = ?,
                role = ?,
                status = ?
            WHERE id = ?';

    $success = executeStatement(
        $conn,
        $sql,
        'sssssi',
        [
            $fullName,
            $username,
            $profileImage,
            $role,
            $status,
            $id,
            ]
    );

if (!$success) {
    if ($newProfileImageUploaded && $newProfileImagePath !== null && is_file($newProfileImagePath)) {
        if (!unlink($newProfileImagePath)) {
            error_log('update_user.php: Failed to clean up new profile image after database failure: '.$newProfileImagePath);
        }
    }

    error_log('update_user.php: Failed to update user ID: '.$id);

    $_SESSION['error'] = 'Unable to update user. Please try again.';
    header('Location: edit_user.php?id='.$id);
    exit();
}

if ($newProfileImageUploaded && $currentImage !== '' && $currentImage !== 'default-profile.png') {
    $oldImagePath = __DIR__.'/assets/images/profiles/'.$currentImage;

    if (is_file($oldImagePath)) {
        if (!unlink($oldImagePath)) {
            error_log('update_user.php: Failed to delete old profile image: '.$currentImage);
        }
    }
}

if (isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] === $id) {
    $_SESSION['full_name'] = $fullName;
    $_SESSION['role'] = $role;
    $_SESSION['profile_image'] = $profileImage;
}

$_SESSION['success'] = 'User Updated Successfully.';
header('Location: users.php');
exit();
