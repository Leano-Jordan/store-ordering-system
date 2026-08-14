<?php

const ROLE_ADMIN = 'Admin';
const ROLE_MANAGER = 'Manager';
const ROLE_CASHIER = 'Cashier';
const ROLE_KITCHEN = 'Kitchen';

function requireRole(array $allowedRoles): void
{
    if (
        !isset($_SESSION['user_id'],
        $_SESSION['role']) ||
        !is_int($_SESSION['user_id']) ||
        $_SESSION['user_id'] < 1 ||
        !is_string($_SESSION['role']) || $_SESSION['role'] === ''
        ) {
        header('Location: login.php');
        exit();
    }

    if (!in_array($_SESSION['role'], $allowedRoles, true)) {
        http_response_code(403);
        header('Location: 403.php');
        exit();
    }
}
