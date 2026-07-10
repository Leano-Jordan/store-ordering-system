<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {

    $_SESSION["redirect_after_login"] = $_SERVER["REQUEST_URI"];

    header("Location: login.php");
    exit();
}
