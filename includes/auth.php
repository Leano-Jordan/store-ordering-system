<?php

declare(strict_types=1);

require_once __DIR__.'/session.php';
require_once __DIR__.'/db.php';
require_once __DIR__.'/session_tracker.php';

const SESSION_ABSOLUTE_TIMEOUT = 28800; // 8 hours.

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
$sessionStartedAt = filter_var($_SESSION['session_started_at'] ?? null, FILTER_VALIDATE_INT);

if (
    $sessionStartedAt === false ||
    $sessionStartedAt < 1 ||
    (
        $currentTime - $sessionStartedAt
    ) > SESSION_ABSOLUTE_TIMEOUT) {
    if (
        isset($_SESSION['session_log_id'],
        $_SESSION['user_id']
        )
        && is_int($_SESSION['session_log_id'])
        && $_SESSION['session_log_id'] > 0
        && is_int($_SESSION['user_id'])
        && $_SESSION['user_id'] > 0
        ) {
        $sessionClosed = closeSessionRecord(
            $conn,
            $_SESSION['session_log_id'],
            $_SESSION['user_id'],
            'TIMED_OUT'
        );

        if (!$sessionClosed) {
            error_log('SwiftOrder absolute-timeout session could not be closed');
        }
    }

    $_SESSION = [];
    session_destroy();

    header('Location: login.php');
    exit();
}

if (
    isset(
        $_SESSION['last_activity'])
        && (!is_int($_SESSION['last_activity'])
        || ($currentTime - $_SESSION['last_activity']) > SESSION_ABSOLUTE_TIMEOUT)
) {
    if (
        isset(
            $_SESSION['session_log_id'], $_SESSION['user_id'])
            && is_int($_SESSION['session_log_id'])
            && $_SESSION['session_log_id'] > 0
            && is_int($_SESSION['user_id'])
            && $_SESSION['user_id'] > 0
            ) {
        $sessionClosed = closeSessionRecord(
            $conn,
            $_SESSION['session_log_id'],
            $_SESSION['user_id'],
            'TIMED_OUT'
        );

        if (!$sessionClosed) {
            error_log(
                'SwiftOrder timed-out session could not be closed. '
                .'Session record ID: '.$_SESSION['session_log_id']
            );
        }
    }

    $_SESSION = [];
    session_destroy();

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

if (!isset($_SESSION['session_started_at']) || !is_int($_SESSION['session_started_at'])) {
    $_SESSION['session_started_at'] = $currentTime;
}

$_SESSION['last_activity'] = $currentTime;

if (
    !isset($_SESSION['session_log_id'])
    || !is_int($_SESSION['session_log_id'])
    || $_SESSION['session_log_id'] < 1
    ) {
    $_SESSION = [];
    session_destroy();

    header('Location: login.php');
    exit();
}

    $sessionUpdated = touchSessionRecord(
        $conn,
        $_SESSION['session_log_id'],
        (int) $user['id']
    );

    if (!$sessionUpdated) {
        error_log(
            'SwiftOrder authenticated session missing valid session record. '.'User ID: '.$userId
        );

        $_SESSION = [];
        session_destroy();

        header('Location: login.php');
        exit();
    }
