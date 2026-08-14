<?php

declare(strict_types=1);

require_once __DIR__.'/includes/session.php';
require_once __DIR__.'/includes/csrf.php';

verifyCsrfToken();

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
