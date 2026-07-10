<?php
require_once "includes/auth.php";
require_once "includes/permissions.php";
requireRole([ROLE_ADMIN]);
require_once "includes/db.php";

/************ ************** DEACTIVATE USERS *********** *****************/

$id = (int)$_GET["id"];

/*********                *******  PREVENT DEACTIVATION MYSELF *******       ****************/

if ($id === (int)$_SESSION["user_id"]) {

    header("Location: users.php?error=self_deactivate");
    exit();
}

/*********         *********  MAKING SURE AT LEAST 1 ACTIVE ADMIN STAYS   **********       *********/

$check = $conn->prepare("SELECT COUNT(*) AS total FROM users WHERE role = ? AND status = 'Active'");

$adminRole = ROLE_ADMIN;
$check->bind_param("s", $adminRole);
$check->execute();

$totalAdmins = $check->get_result()->fetch_assoc()["total"];

/************************************    CHECK IF THIS USER IS ACTIVE ADMIN    ***************************************/

$stmt = $conn->prepare("SELECT role, status FROM users WHERE id = ?");

$stmt->bind_param("i", $id);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

if (
    $user &&
    $user["role"] === ROLE_ADMIN &&
    $user["status"] === "Active" &&
    $totalAdmins <= 1
) {

    header("Location: users.php?error=last_admin");
    exit();
}

/*************************  DEACTIVATE USER  *********************/

$stmt = $conn->prepare(
    "UPDATE users 
    SET status = 'Inactive' 
    WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: users.php?success=user_deactivated");
exit();
