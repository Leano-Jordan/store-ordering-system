<?php
require_once "includes/auth.php";
require_once "includes/db.php";
require_once "includes/permissions.php";
requireRole([ROLE_ADMIN, ROLE_MANAGER, ROLE_CASHIER, ROLE_KITCHEN]);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request.");
}

$id = intval($_POST["id"] ?? 0);
if ($id <= 0) {
    die("Invalid order.");
}

$status = $_POST["status"] ?? "";

$sql = "SELECT status FROM orders WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$currentOrder = $result->fetch_assoc();

if (!$currentOrder) {
    die("Order not found.");
}

$currentStatus = $currentOrder["status"];

$stmt->close();

if ($currentStatus === "Collected" || $currentStatus === "Cancelled") {
    die("This order can no longer be modified");
}


$allowedStatuses = ["Pending", "Preparing", "Ready", "Collected", "Cancelled"];

if (!in_array($status, $allowedStatuses)) {
    die("Invalid status.");
}

/***********         ************* ROLE BASED STATUS**********     *********/

$role = $_SESSION["role"];

if ($role === ROLE_KITCHEN) {

    if (
        !(($currentStatus === "Pending" && $status === "Preparing") ||
            ($currentStatus === "Preparing" && $status === "Ready"))
    ) {
        die("Staff cannot perform this action.");
    }
}

if ($role === ROLE_CASHIER) {

    if (!(($currentStatus === "Ready" && $status === "Collected"))) {
        die("Cashiers can only collect ready orders.");
    }
}



$sql = "UPDATE orders SET 
status=? WHERE id=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {

    if (($_POST["return_to"] ?? "") === "orders") {
        header("Location: orders.php");
    } else {
        header("Location: order_details.php?id=" . (int)$id);
    }
    exit();
} else {
    die("Failed to update order status.");
}
