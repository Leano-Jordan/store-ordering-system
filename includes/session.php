<?php

declare(strict_types=1);

require_once __DIR__.'/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');

    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_trans_sid', '0');

    $isProduction = defined('APP_ENV') && APP_ENV === 'production';

    if ($isProduction) {
        if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
            http_response_code(400);
            exit('Secure connection required.');
        }
    }

    session_set_cookie_params(
        ['lifetime' => 0,
        'path' => '/',
        'secure' => $isProduction,
        'httponly' => true,
        'samesite' => 'Lax',
        ]
    );

    session_start();
}
