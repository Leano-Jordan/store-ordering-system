<?php
require_once "includes/auth.php";
require_once "includes/permissions.php";
requireRole([ROLE_ADMIN, ROLE_MANAGER, ROLE_CASHIER, ROLE_KITCHEN]);
require_once "includes/db.php";
include "includes/header.php";

$id = (int)($_GET["id"] ?? 0);

if ($id <= 0) {
    die("Invalid Order.");
}

$sql = "SELECT * FROM orders WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $id);

$stmt->execute();

$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("Order not found");
}

$sql = "SELECT p.name, oi.quantity, oi.price FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$items = $stmt->get_result();
?>

<div class="page-header">

    <h2>Order Details</h2>
    <div>
        <button onclick="window.print()" class="action-btn">
            🖨 Print Receipt
        </button>
    </div>

    <a href="orders.php" class="action-btn">⬅ Back to Orders</a>
</div>

<div class="dashboard-grid">

    <div class="dashboard-card">
        <h3>Order #</h3>
        <p>
            <?php echo htmlspecialchars($order["order_number"]); ?>
        </p>
    </div>

    <div class="dashboard-card">
        <h3>Customer</h3>
        <p>
            <?php echo htmlspecialchars($order["customer_name"]); ?>
        </p>
    </div>

    <div class="dashboard-card">
        <h3>Status</h3>
        <p>
            <span class="status <?php echo strtolower($order["status"]) ?>">
                <?php echo htmlspecialchars($order["status"]); ?>
            </span>
        </p>
    </div>

    <div class="dashboard-card">
        <h3>Total</h3>
        <p>R
            <?php echo number_format($order["total"], 2); ?>
        </p>
    </div>

    <div class="dashboard-card">
        <h3>Time Ordered</h3>
        <p><?php echo htmlspecialchars($order["created_at"]); ?></p>
    </div>

</div>

<table class="orders-table">

    <thead>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Line Total</th>
        </tr>
    </thead>

    <tbody>

        <?php while ($item = $items->fetch_assoc()) { ?>

            <tr>
                <td>
                    <?php echo htmlspecialchars($item["name"]); ?>
                </td>

                <td>
                    <?php echo (int)$item["quantity"]; ?>
                </td>

                <td>R
                    <?php echo number_format($item["price"], 2); ?>
                </td>

                <td>
                    <?php echo number_format($item["price"] * $item["quantity"], 2); ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>

</table>

<div class="page-header">

    <?php if ($order["status"] === "Pending") { ?>

        <form action="update_status.php" method="POST" class="inline-form">
            <input type="hidden" name="id" value="<?php echo (int)$order["id"] ?>">
            <input type="hidden" name="status" value="Preparing">

            <button type="submit" class="action-btn preparing-btn">
                Prepare Order
            </button>
        </form>

        <form action="update_status.php" method="POST" class="inline-form">
            <input type="hidden" name="id" value="<?php echo (int)$order["id"]; ?>">

            <input type="hidden" name="status" value="Cancelled">

            <button type="submit" class="action-btn delete-btn" onclick="return confirm('Cancel this order?');">
                Cancel Order
            </button>
        </form>

    <?php } elseif ($order["status"] === "Preparing") { ?>

        <form action="update_status.php" method="POST" class="inline-form">

            <input type="hidden" name="id" value="<?php echo (int)$order["id"] ?>">
            <input type="hidden" name="status" value="Ready">

            <button type="submit" class="action-btn ready-btn">
                Mark Ready
            </button>
        </form>

        <form action="update_status.php" method="POST" class="inline-form">
            <input type="hidden" name="id" value="<?php echo (int)$order["id"]; ?>">

            <input type="hidden" name="status" value="Cancelled">

            <button type="submit" class="action-btn delete-btn" onclick="return confirm('Cancel this order?');">
                Cancel Order
            </button>
        </form>

    <?php } elseif ($order["status"] === "Ready") { ?>

        <form action="update_status.php" method="POST" class="inline-form">
            <input type="hidden" name="id" value="<?php echo (int)$order["id"] ?>">
            <input type="hidden" name="status" value="Collected">

            <button type="submit" class="action-btn pending-btn">
                Mark Collected
            </button>
        </form>

        <form action="update_status.php" method="POST" class="inline-form">
            <input type="hidden" name="id" value="<?php echo (int)$order["id"]; ?>">

            <input type="hidden" name="status" value="Cancelled">

            <button type="submit" class="action-btn delete-btn" onclick="return confirm('Cancel this order?');">
                Cancel Order
            </button>
        </form>

    <?php } elseif ($order["status"] === "Collected") { ?>
        <p><strong>Order Complete
            </strong></p>

    <?php } elseif ($order["status"] === "Cancelled") { ?>

    <?php } ?>
</div>

<?php include "includes/footer.php" ?>