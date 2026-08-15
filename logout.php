<?php

declare(strict_types=1);

require_once __DIR__.'/includes/session.php';
require_once __DIR__.'/includes/db.php';
require_once __DIR__.'/includes/session_tracker.php';
require_once __DIR__.'/includes/csrf.php';

verifyCsrfToken();

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
        'LOGGED_OUT'
    );

    if (!$sessionClosed) {
        error_log(
            'SwiftOrder logout could not close tracked session. '
            .'Session record ID: '.$_SESSION['session_log_id']
        );
    }
}

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        [
            'expires' => time() - 42000,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => $params['samesite'] ?? 'Lax',
        ]
    );
}

session_unset();
session_destroy();

header('Location: login.php');
exit();
