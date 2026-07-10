<?php
require_once "includes/auth.php";
require_once "includes/permissions.php";
requireRole([ROLE_ADMIN, ROLE_MANAGER, ROLE_CASHIER, ROLE_KITCHEN]);
require_once "includes/db.php";

$loadScript = true;

$limit = 15;
$page = max(1, (int)($_GET["page"] ?? 1));

$offset = ($page - 1) * $limit;

$where = "";

if (!empty($_GET["order"])) {

    $order = trim($_GET["order"]);
    $where = "WHERE order_number = ?";
}

if ($where === "") {
    $result = $conn->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
    $totalResult = $conn->query("SELECT COUNT(*) AS total FROM orders");
} else {
    $stmt = $conn->prepare("SELECT * FROM orders $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
    $param = trim($_GET["order"]);
    $stmt->bind_param("s", $param);
    $stmt->execute();
    $result = $stmt->get_result();

    $countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM orders $where");

    $countParam = trim($_GET["order"]);
    $countStmt->bind_param("s", $countParam);
    $countStmt->execute();
    $totalResult = $countStmt->get_result();
}

$totalRows = $totalResult->fetch_assoc()["total"];
$totalPages = ceil($totalRows / $limit);

include "includes/header.php";
?>

<h2>Orders</h2>

<input type="text"
    id="orderSearch"
    placeholder="Search Customer Name or Order Number..."
    value="<?php echo htmlspecialchars($_GET['order'] ?? ''); ?>"
    onkeyup="searchOrders()">

<select id="orderSort" onchange="sortOrders()">

    <option value="">Sort Orders</option>
    <option value="status"
        <?php if (($_GET['status'] ?? '') === 'Pending') echo 'selected'; ?>>Status</option>
    <option value="newest">Newest First</option>
    <option value="oldest">Oldest First</option>
    <option value="highest">Highest Total</option>
    <option value="lowest">Lowest Total</option>
</select>

<br><br>

<table class="orders-table">

    <thead>
        <tr>
            <th>Order #</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Status</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>

        <?php while ($row = $result->fetch_assoc()) { ?>

            <tr>
                <td>
                    <a href="order_details.php?id=<?php echo (int)$row["id"]; ?>" class="order-link">
                        <?php echo htmlspecialchars($row["order_number"], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </td>

                <td>
                    <?php echo htmlspecialchars($row["customer_name"], ENT_QUOTES, 'UTF-8'); ?>
                </td>

                <td>R
                    <?php echo number_format($row["total"], 2); ?>
                </td>

                <td>
                    <span class="status <?php echo
                                        strtolower($row["status"]); ?>">

                        <?php echo htmlspecialchars($row["status"], ENT_QUOTES, "UTF-8"); ?>

                    </span>
                </td>

                <td>
                    <?php echo date("d F Y H:i", strtotime($row["created_at"])); ?>
                </td>

                <td>
                    <?php

                    if (
                        $row["status"] === "Pending" &&
                        in_array($_SESSION["role"], [ROLE_ADMIN, ROLE_MANAGER, ROLE_KITCHEN])
                    ) { ?>

                        <form action="update_status.php" method="POST" class="inline-form">
                            <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
                            <input type="hidden" name="status" value="Preparing">
                            <input type="hidden" name="return_to" value="orders">

                            <button type="submit" class="action-btn pending-btn">
                                Prepare
                            </button>
                        </form>

                    <?php
                    } elseif (
                        $row["status"] === "Preparing" &&
                        in_array($_SESSION["role"], [ROLE_ADMIN, ROLE_MANAGER, ROLE_KITCHEN])
                    ) {
                    ?>

                        <form action="update_status.php" method="POST" class="inline-form">
                            <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
                            <input type="hidden" name="status" value="Ready">
                            <input type="hidden" name="return_to" value="orders">

                            <button type="submit" class="action-btn preparing-btn">
                                Ready
                            </button>
                        </form>

                    <?php
                    } elseif (
                        $row["status"] === "Ready" &&
                        in_array($_SESSION["role"], [ROLE_ADMIN, ROLE_MANAGER, ROLE_CASHIER])
                    ) {
                    ?>

                        <form action="update_status.php" method="POST" class="inline-form">
                            <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
                            <input type="hidden" name="status" value="Collected">
                            <input type="hidden" name="return_to" value="orders">

                            <button type="submit" class="action-btn ready-btn">
                                Collect
                            </button>
                        </form>

                    <?php

                    } else {

                        echo "_";
                    }

                    ?>
                </td>

            </tr>

        <?php } ?>

    </tbody>
</table>

<div class="pagination">
    <?php if ($page > 1) { ?>

        <a href="?page=<?php echo $page - 1; ?>" class="action-btn">
            ⬅ Previous
        </a>
    <?php } ?>

    <span>
        Page <?php echo $page; ?> of <?php echo $totalPages; ?>
    </span>

    <?php if ($page < $totalPages) { ?>

        <a href="?page=<?php echo $page + 1; ?>" class="action-btn">
            Next ➡
        </a>
    <?php } ?>

</div>

<?php include "includes/footer.php"; ?>