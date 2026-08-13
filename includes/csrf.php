<?php

require_once __DIR__.'/../config.php';
require_once __DIR__.'/session.php';

function csrfToken()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(): void
{
    if (defined('APP_ENV') && APP_ENV === 'development') {
        return;
    }

    if (
        empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) ||
        !hash_equals(
            $_SESSION['csrf_token'],
            $_POST['csrf_token']
        )) {
        exit('Invalid CSRF Token.');
    }
}
