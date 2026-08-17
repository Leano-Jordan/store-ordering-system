<?php

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN]);
require_once 'includes/csrf.php';
verifyCsrfToken();
require_once 'includes/db.php';
require_once 'includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: users.php');
    exit();
}

$fullNameRaw = $_POST['full_name'] ?? null;
$usernameRaw = $_POST['username'] ?? null;
$passwordRaw = $_POST['password'] ?? null;
$roleRaw = $_POST['role'] ?? null;
$statusRaw = $_POST['status'] ?? null;

if (
    !is_string($fullNameRaw) ||
    !is_string($usernameRaw) ||
    !is_string($passwordRaw) ||
    !is_string($roleRaw) ||
    !is_string($statusRaw)
) {
    $_SESSION['error'] = 'Invalid user data.';
    header('Location: add_user.php');
    exit();
}

$fullName = trim($fullNameRaw);
$username = trim($usernameRaw);
$password = $passwordRaw;
$role = trim($roleRaw);
$status = trim($statusRaw);

if (
    $fullName === '' ||
    $username === '' ||
    $password === '' ||
    $role === '' ||
    $status === ''
) {
    $_SESSION['error'] = 'Please complete all required fields.';

    header('Location: add_user.php');
    exit();
}

$allowedRoles = [ROLE_ADMIN, ROLE_MANAGER, ROLE_CASHIER, ROLE_KITCHEN];

if (!in_array($role, $allowedRoles, true)) {
    $_SESSION['error'] = 'Invalid role selected.';
    header('Location: add_user.php');
    exit();
}

$profileImage = null;

if (
    isset($_FILES['profile_image']) &&
    $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE
) {
    if ($_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
        error_log('Error uploading profile image with error code: '.$_FILES['profile_image']['error']);

        $_SESSION['error'] = 'Error uploading profile image.';
        header('Location: add_user.php');
        exit();
    }

    $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    $maxFileSize = 2 * 1024 * 1024; // 2MB

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($_FILES['profile_image']['tmp_name']);

    if ($mimeType === false || !in_array($mimeType, $allowedMimeTypes, true)) {
        $_SESSION['error'] = 'Invalid profile image format.';
        header('Location: add_user.php');
        exit();
    }

    if ($_FILES['profile_image']['size'] > $maxFileSize) {
        $_SESSION['error'] = 'Profile image exceeds 2MB.';
        header('Location: add_user.php');
        exit();
    }

    if (!validateImageDimensions($_FILES['profile_image']['tmp_name'])) {
        $_SESSION['error'] = 'Profile image dimensions are too large.';
        header('Location: add_user.php');
        exit();
    }

    $allowedExtensions = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    $profileImage = bin2hex(random_bytes(16)).'.'.$allowedExtensions[$mimeType];

    $destination = __DIR__.'/assets/images/profiles/'.$profileImage;

    if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $destination)) {
        $_SESSION['error'] = 'Failed to upload profile image.';
        header('Location: add_user.php');
        exit();
    }
}

$check = $conn->prepare('SELECT id FROM users WHERE username = ?');
$check->bind_param('s', $username);
$check->execute();
$existingUser = $check->get_result();

if ($existingUser->num_rows > 0) {
    $check->close();

    if (
        $profileImage !== null
        && isset($destination)
        && is_file($destination)
        && !unlink($destination)
        ) {
        error_log('save_user.php: Failed to remove orphan profile image: '.$destination);
    }

    $_SESSION['error'] = 'Username already exists.';
    header('Location: add_user.php');
    exit();
}

$check->close();

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$sql = 'INSERT INTO users (full_name, username, password, profile_image, role, status) VALUES(?, ?, ?, ?, ?, ?)';

if (executeStatement(
    $conn,
    $sql,
    'ssssss',
    [
        $fullName,
        $username,
        $hashedPassword,
        $profileImage,
        $role,
        $status,
    ]
)) {
    logActivity(
        $conn,
        $_SESSION['user_id'],
        'Added user: '.$username
    );

    $_SESSION['success'] = 'User Added Successfully.';
    header('Location: users.php');
    exit();
}
    if ($profileImage !== null &&
    isset($destination) &&
    is_file($destination)
    ) {
        if (!unlink($destination)) {
            error_log('save_user.php: Failed to remove profile image after database failure: '.$destination);
        }
    }

    error_log('save_user.php: Failed to save user.');

    $_SESSION['error'] = 'Unable to save user. Please try again.';
    header('Location: add_user.php');
    exit();
