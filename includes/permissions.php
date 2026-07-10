<?php

const ROLE_ADMIN = "Admin";
const ROLE_MANAGER = "Manager";
const ROLE_CASHIER = "Cashier";
const ROLE_KITCHEN = "Kitchen";

function requireRole(array $allowedRoles): void
{
    if (!isset($_SESSION["role"])) {
        header("Location: login.php");
        exit();
    }

    if (!in_array($_SESSION["role"], $allowedRoles, true)) {
        header("Location: 403.php");
        exit();
    }
}
