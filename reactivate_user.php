<?php
require_once "includes/auth.php";
require_once "includes/permissions.php";
requireRole([ROLE_ADMIN]);
require_once "includes/db.php";

/************ *       ************* REACTIVATE USERS ***********              *****************/

$id = (int)$_GET["id"];

/*********                *******  PREVENT DEACTIVATION MYSELF *******       ****************/

if ($id === (int)$_SESSION["user_id"]) {

    header("Location: users.php");
    exit();
}

/*************************  REACTIVATE USER  *********************/

$stmt = $conn->prepare(
    "UPDATE users 
    SET status = 'Active' 
    WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: users.php?success=user_activated");
exit();
