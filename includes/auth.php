<?php

require_once __DIR__.'/session.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];

    header('Location: login.php');
    exit();
}
