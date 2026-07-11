<?php
require_once "includes/auth.php";

require_once "includes/permissions.php";
requireRole([ROLE_ADMIN, ROLE_MANAGER, ROLE_CASHIER, ROLE_KITCHEN]);
require_once "includes/db.php";
require_once "includes/logger.php";

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

$orderStmt = $conn->prepare("SELECT order_number, items FROM orders WHERE id = ?");
$orderStmt->bind_param("i", $id);
$orderStmt->execute();

$orderData = $orderStmt->get_result()->fetch_assoc();
$orderNumber = $orderData["order_number"];
$orderItems = $orderData["items"];

$orderStmt->close();

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
        die("User cannot perform this action.");
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

    if ($status === "Collected") {

        $items = explode("\n", trim($orderItems));

        foreach ($items as $item) {

            if (preg_match('/^(.*?)\s+x\s+(\d+)$/', trim($item), $matches)) {

                $productName = trim($matches[1]);
                $quantity = (int)$matches[2];

                $stockStmt = $conn->prepare("UPDATE products 
                SET stock = stock - ? WHERE name = ?");

                $stockStmt->bind_param("is", $quantity, $productName);
                $stockStmt->execute();

                if ($stockStmt->affected_rows == 0) {
                    error_log("SwiftOrder: Product stock deduction failed: " . $productName . " on " . $orderNumber);
                }

                $stockStmt->close();
            }
        }
    }

    logActivity(
        $conn,
        $_SESSION["user_id"],
        "Changed Order " . $orderNumber . " from " . $currentStatus . " to " . $status
    );

    if (($_POST["return_to"] ?? "") === "orders") {
        header("Location: orders.php");
    } else {
        header("Location: order_details.php?id=" . (int)$id);
    }
    exit();
} else {
    die("Failed to update order status.");
}
