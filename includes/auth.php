<?php

declare(strict_types=1);

require_once __DIR__.'/session.php';
require_once __DIR__.'/db.php';

const SESSION_IDLE_TIMEOUT = 1800;

if (!isset($_SESSION['user_id'])) {
    $requestUri = $_SERVER['REQUEST_URI'] ?? 'dashboard.php';

    if (
        !is_string($requestUri)
        || $requestUri === ''
        || $requestUri[0] !== '/'
        || strpos($requestUri, '//') === 0
        ) {
        $requestUri = 'dashboard.php';
    }

    $_SESSION['redirect_after_login'] = $requestUri;

    header('Location: login.php');
    exit();
}

$userId = filter_var($_SESSION['user_id'], FILTER_VALIDATE_INT);

if ($userId === false || $userId < 1) {
    $_SESSION = [];

    header('Location: login.php');
    exit();
}

$currentTime = time();

if (
    isset($_SESSION['last_activity']) && (!is_int($_SESSION['last_activity']) || ($currentTime - $_SESSION['last_activity']) > SESSION_IDLE_TIMEOUT)
) {
    $_SESSION = [];

    header('Location: login.php');
    exit();
}

$stmt = $conn->prepare('SELECT id, full_name, role, profile_image, status FROM users WHERE id = ? LIMIT 1');

if (!$stmt) {
    error_log('SwiftOrder auth prepare failed: '.$conn->error);
    http_response_code(500);
    exit('An unexpected error occurred.');
}

$stmt->bind_param('i', $userId);

if (!$stmt->execute()) {
    error_log('SwiftOrder auth execute failed: '.$stmt->error);
    $stmt->close();
    http_response_code(500);
    exit('An unexpected error occurred.');
}

$result = $stmt->get_result();

if (!$result || $result->num_rows !== 1) {
    $stmt->close();
    $_SESSION = [];

    header('Location: login.php');
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();

if (($user['status'] ?? '') !== 'Active') {
    $_SESSION = [];

    header('Location: login.php');
    exit();
}

$_SESSION['user_id'] = (int) $user['id'];
$_SESSION['full_name'] = (string) ($user['full_name'] ?? '');
$_SESSION['role'] = (string) ($user['role'] ?? '');
$_SESSION['profile_image'] = (string) ($user['profile_image'] ?? '');
$_SESSION['last_activity'] = $currentTime;
