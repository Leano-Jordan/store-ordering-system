<?php
require_once "includes/db.php";

if (
    $_SERVER["REQUEST_METHOD"] !==
    "POST"
) {

    header("Content-Type: application/json");

    echo json_encode([
        "success" => false,
        "message" => "Invalid Request."
    ]);

    exit();
}

do {
    $orderNumber = "SW" . str_pad(random_int(1, 999999), 6, "0", STR_PAD_LEFT);

    $check = $conn->prepare("SELECT id FROM orders WHERE order_number = ?");

    $check->bind_param("s", $orderNumber);

    $check->execute();

    $result = $check->get_result();
} while ($result->num_rows > 0);

$check->close();

$customer = trim($_POST['customer'] ??
    "");
if ($customer === "") {

    header("Content-Type: application/json");

    echo json_encode([
        "success" => false,
        "message" => "Customer name is required."
    ]);

    exit();
}

$cart = json_decode($_POST['cart'] ?? "[]", true);

if (!is_array($cart) || empty($cart)) {

    header("Content-Type: application/json");

    echo json_encode([
        "success" => false,
        "message" => "Cart is empty."
    ]);

    exit();
}

$items = "";
$total = 0;
$dbPrices = [];

foreach ($cart as $item) {

    $productId = (int)$item["id"];
    $quantity = (int)$item["quantity"];

    $productStmt = $conn->prepare("SELECT name, price, stock FROM products WHERE id = ?");
    $productStmt->bind_param("i", $productId);
    $productStmt->execute();

    $productResult = $productStmt->get_result();

    if ($productResult->num_rows === 0) {

        header("Content-Type: application/json");

        echo json_encode([
            "success" => false,
            "message" => "Product with ID $productId not found."
        ]);

        exit();
    }

    $product = $productResult->fetch_assoc();
    if ($quantity > $product["stock"]) {
        header("Content-Type: application/json");

        echo json_encode([
            "success" => false,
            "message" => "Only " . $product["stock"] . " " . $product["name"] . "(s) available in stock."
        ]);

        $productStmt->close();
        exit();
    }

    $total += $product["price"] * $quantity;

    $dbPrices[$productId] = $product["price"];

    $items .= $product["name"] . " x " . $quantity . "\n";

    $productStmt->close();
}

$items = trim($items);

$status = "Pending";

$sql = "INSERT INTO orders (order_number, customer_name, items, total, status) VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param("sssds", $orderNumber, $customer, $items, $total, $status);

if ($stmt->execute()) {

    $orderId = $conn->insert_id;

    $itemStmt = $conn->prepare(
        "INSERT INTO order_items(order_id, product_id, quantity, price) 
    VALUES (?, ?, ?, ?)"
    );

    foreach ($cart as $item) {

        $productId = (int)$item["id"];
        $quantity = (int)$item["quantity"];
        $price = $dbPrices[(int)$item["id"]];

        $itemStmt->bind_param("iiid", $orderId, $productId, $quantity, $price);

        $itemStmt->execute();
    }

    $itemStmt->close();

    header("Content-Type: application/json");

    echo json_encode([
        "success" => true,
        "message" => "Order placed successfully.",
        "order_number" => $orderNumber
    ]);

    exit();
} else {

    header("Content-Type: application/json");

    echo json_encode([
        "success" => false,
        "message" => "Failed to place the order."
    ]);

    exit();
}

$conn->close();
