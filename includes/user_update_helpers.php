<?php

declare(strict_types=1);

require_once __DIR__.'/upload_helpers.php';

function loadUserUpdateContext(
    mysqli $conn,
    int $id,
    string $newRole,
    string $newStatus,
    int $sessionUserId
): array {
    $stmt = $conn->prepare(
        'SELECT profile_image, role, status
        FROM users
        WHERE id = ?
        LIMIT 1
        FOR UPDATE'
    );

    if (!$stmt) {
        error_log('user_update_helpers.php: Failed to prepare user lock: '.$conn->error);
        throw new RuntimeException('Unable to load user.');
    }

    if (!$stmt->bind_param('i', $id) || !$stmt->execute()) {
        error_log('user_update_helpers.php: Failed to load user: '.$stmt->error);
        $stmt->close();
        throw new RuntimeException('Unable to load user.');
    }

    $result = $stmt->get_result();
    $user = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    if (!is_array($user)) {
        throw new RuntimeException('User not found.');
    }

    if (
        $id === $sessionUserId
        && ($newRole !== ROLE_ADMIN || $newStatus !== 'Active')
    ) {
        throw new RuntimeException(
            'You cannot remove administrator access from your own account'
        );
    }

    $currentRole = (string) ($user['role'] ?? '');
    $currentStatus = (string) ($user['status'] ?? '');

    if (
        $currentRole === ROLE_ADMIN
        && $currentStatus === 'Active'
        && ($newRole !== ROLE_ADMIN || $newStatus !== 'Active')
    ) {
        $adminStmt = $conn->prepare(
            "SELECT id
            FROM users
            WHERE role = ?
            AND status = 'Active'
            AND id != ?
            FOR UPDATE"
        );

        if (!$adminStmt) {
            error_log(
                'user_update_helpers.php: Failed to prepare admin lock: '
                .$conn->error
            );
            throw new RuntimeException(
                'Unable to validate administrator access.'
            );
        }

        $adminRole = ROLE_ADMIN;

        if (
            !$adminStmt->bind_param('si', $adminRole, $id)
            || !$adminStmt->execute()
        ) {
            error_log(
                'user_update_helpers.php: Failed to validate admin access: '
                .$adminStmt->error
            );
            $adminStmt->close();
            throw new RuntimeException(
                'Unable to validate administrator access.'
            );
        }

        $adminResult = $adminStmt->get_result();
        $activeAdminCount = $adminResult ? $adminResult->num_rows : -1;

        if ($adminResult) {
            $adminResult->free();
        }

        $adminStmt->close();

        if ($activeAdminCount < 1) {
            throw new RuntimeException(
                'At least one active administrator must remain.'
            );
        }
    }

    return [
        'profile_image' => (string) ($user['profile_image'] ?? ''),
        'role' => $currentRole,
        'status' => $currentStatus,
    ];
}

function prepareProfileImageUpload(?array $file): array
{
    if ($file === null) {
        return [
            'uploaded' => false,
            'filename' => '',
            'path' => null,
        ];
    }

    $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;

    if (!is_int($error) && !is_numeric($error)) {
        throw new RuntimeException('Profile image upload failed.');
    }

    $error = (int) $error;

    if ($error === UPLOAD_ERR_NO_FILE) {
        return [
            'uploaded' => false,
            'filename' => '',
            'path' => null,
        ];
    }

    if ($error !== UPLOAD_ERR_OK) {
        error_log(
            'user_update_helpers.php: Profile image upload error: '.$error
        );
        throw new RuntimeException('Profile image upload failed.');
    }

    $size = $file['size'] ?? null;
    $tmpName = $file['tmp_name'] ?? null;

    if (
        (!is_int($size) && !is_numeric($size))
        || !is_string($tmpName)
        || $tmpName === ''
    ) {
        throw new RuntimeException('Invalid profile image upload.');
    }

    if ((int) $size > 2 * 1024 * 1024) {
        throw new RuntimeException(
            'Profile image must not exceed 2MB.'
        );
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($tmpName);

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

    if (getimagesize($tmpName) === false) {
        throw new RuntimeException('Uploaded file is not a valid image.');
    }

    if (!validateImageDimensions($tmpName)) {
        throw new RuntimeException(
            'Profile image dimensions exceed the allowed limit of 2000x2000 pixels.'
        );
    }

    $filename = bin2hex(random_bytes(16))
        .'.'.$allowedMimeTypes[$mimeType];

    $directory = __DIR__.'/../assets/images/profiles';

    if (
        !is_dir($directory)
        && !mkdir($directory, 0755, true)
        && !is_dir($directory)
    ) {
        throw new RuntimeException(
            'Failed to prepare profile image storage.'
        );
    }

    if (!is_writable($directory)) {
        throw new RuntimeException(
            'Profile image storage is unavailable.'
        );
    }

    $path = $directory.'/'.$filename;

    if (!move_uploaded_file($tmpName, $path)) {
        throw new RuntimeException('Failed to upload profile image.');
    }

    return [
        'uploaded' => true,
        'filename' => $filename,
        'path' => $path,
    ];
}
