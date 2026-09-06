<?php

declare(strict_types=1);

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN]);

require_once 'includes/csrf.php';
require_once 'includes/db.php';
require_once 'includes/helpers.php';
require_once 'includes/audit.php';
require_once 'includes/upload_helpers.php';

verifyCsrfToken();

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

$allowedRoles = [
    ROLE_ADMIN,
    ROLE_MANAGER,
    ROLE_CASHIER,
    ROLE_KITCHEN,
];

if (!in_array($role, $allowedRoles, true)) {
    $_SESSION['error'] = 'Invalid role selected.';
    header('Location: add_user.php');
    exit();
}

$allowedStatuses = [
    'Active',
    'Inactive',
];

if (!in_array($status, $allowedStatuses, true)) {
    $_SESSION['error'] = 'Invalid status selected.';
    header('Location: add_user.php');
    exit();
}

$profileImage = null;
$destination = null;

if (
    isset($_FILES['profile_image']) && is_array($_FILES['profile_image']) &&
    ($_FILES['profile_image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE
) {
    $uploadError = $_FILES['profile_image']['error'] ?? null;
    $temporaryPath = $_FILES['profile_image']['tmp_name'] ?? null;
    $fileSize = $_FILES['profile_image']['size'] ?? null;

    if (
        !is_int($uploadError) || $uploadError !== UPLOAD_ERR_OK ||
        !is_string($temporaryPath) || !is_int($fileSize)
    ) {
        $_SESSION['error'] = 'Error uploading profile image.';

        header('Location: add_user.php');
        exit();
    }

    $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    $maxFileSize = 2 * 1024 * 1024;

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($temporaryPath);

    if (
        $mimeType === false || !in_array($mimeType, $allowedMimeTypes, true)
    ) {
        $_SESSION['error'] = 'Invalid profile image format.';

        header('Location: add_user.php');
        exit();
    }

    if ($fileSize > $maxFileSize) {
        $_SESSION['error'] =
            'Profile image exceeds 2MB.';

        header('Location: add_user.php');
        exit();
    }

    if (!validateImageDimensions($temporaryPath)) {
        $_SESSION['error'] =
            'Profile image dimensions are too large.';

        header('Location: add_user.php');
        exit();
    }

    $allowedExtensions = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    try {
        $profileImage =
            bin2hex(random_bytes(16)).'.'.$allowedExtensions[$mimeType];
    } catch (Throwable $exception) {
        error_log(
            'save_user.php: Failed to generate profile image name: '.$exception->getMessage()
        );

        $_SESSION['error'] = 'Unable to process profile image.';

        header('Location: add_user.php');
        exit();
    }

    $destination = __DIR__.'/assets/images/profiles/'.$profileImage;

    if (
        !move_uploaded_file($temporaryPath, $destination)
    ) {
        $_SESSION['error'] = 'Failed to upload profile image.';

        header('Location: add_user.php');
        exit();
    }
}

$transactionStarted = false;

try {
    if (!$conn->begin_transaction()) {
        throw new RuntimeException('Failed to begin user creation transaction: '.$conn->error);
    }

    $transactionStarted = true;

    $check = $conn->prepare(
        'SELECT id
        FROM users
        WHERE username = ?
        LIMIT 1
        FOR UPDATE'
    );

    if (!$check) {
        throw new RuntimeException('Unable to check username.');
    }

    if (!$check->bind_param('s', $username)) {
        $check->close();

        throw new RuntimeException('Unable to bind username check.');
    }

    if (!$check->execute()) {
        $error = $check->error;
        $check->close();

        throw new RuntimeException('Unable to check username: '.$error);
    }

    $existingUser = $check->get_result();

    if ($existingUser !== false && $existingUser->num_rows > 0
    ) {
        $check->close();

        throw new InvalidArgumentException('Username already exists.');
    }

    $check->close();

    $hashedPassword = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    if ($hashedPassword === false) {
        throw new RuntimeException('Unable to secure user password.');
    }

    $sql = 'INSERT INTO users (
                full_name,
                username,
                password,
                profile_image,
                role,
                status
            ) VALUES (?, ?, ?, ?, ?, ?)';

    if (!executeStatement(
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
        throw new RuntimeException('Unable to save user.');
    }

    $userId = (int) $conn->insert_id;

    if ($userId < 1) {
        throw new RuntimeException('User ID was not generated.');
    }

    recordAudit(
        $conn,
        (int) $_SESSION['user_id'],
        'user',
        $userId,
        'CREATE',
        [
            'full_name' => [
                null, '[created]',
            ],
            'username' => [
                null, '[created]',
            ],
            'role' => [
                null, $role,
            ],
            'status' => [
                null, $status,
            ],
            'profile_image' => [
                null, $profileImage !== null
                    ? '[uploaded]' : null,
            ],
        ]
    );

    if (!$conn->commit()) {
        $error = $conn->error;

        throw new RuntimeException('User creation commit failed: '.$error);
    }

    $_SESSION['success'] = 'User added successfully.';

    header('Location: users.php');
    exit();
} catch (InvalidArgumentException $exception) {
    if ($transactionStarted) {
        $conn->rollback();
    }

    if (
        $destination !== null && is_file($destination) && !unlink($destination)
    ) {
        error_log(
            'save_user.php: Failed to remove profile image after validation failure: '.$destination
        );
    }

    $_SESSION['error'] = $exception->getMessage();

    header('Location: add_user.php');
    exit();
} catch (Throwable $exception) {
    if ($transactionStarted) {
        $conn->rollback();
    }

    if ($destination !== null && is_file($destination) && !unlink($destination)
    ) {
        error_log('save_user.php: Failed to remove profile image after rollback: '.$destination);
    }

    error_log('save_user.php: '.$exception->getMessage());

    $_SESSION['error'] = 'Unable to save user. Please try again.';

    header('Location: add_user.php');
    exit();
}
