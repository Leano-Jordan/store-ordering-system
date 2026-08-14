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
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        exit('Method Not Allowed.');
    }

    $sessionToken = $_SESSION['csrf_token'] ?? null;
    $submittedToken = $_POST['csrf_token'] ?? null;

    if (
        !is_string($sessionToken)
        || $sessionToken === ''
        || !is_string($submittedToken)
        || $submittedToken === ''
        || !hash_equals($sessionToken, $submittedToken)) {
        http_response_code(403);
        exit('Forbidden.');
    }
}
