<?php
require_once "includes/auth.php";
require_once "includes/permissions.php";
requireRole([ROLE_ADMIN, ROLE_MANAGER, ROLE_CASHIER]);
require_once "includes/db.php";

$loadScript = true;
$loadChart = true;

require_once "includes/header.php";

?>

<?php

//                                                        TOTAL PRODUCTS

$sql = "SELECT COUNT(*) AS totalProducts FROM products";

$result = $conn->query($sql);

$row = $result->fetch_assoc();

$totalProducts = $row["totalProducts"];

//                                                          ORDERS TODAY

$sql = "SELECT COUNT(*) AS todayOrders FROM orders 
WHERE DATE(created_at) = CURDATE()";

$result = $conn->query($sql);

$row = $result->fetch_assoc();

$todayOrders = $row["todayOrders"];

//                                                        PENDING ORDERS

$sql = "SELECT COUNT(*) AS pendingOrders FROM orders WHERE status = 'Pending'";

$result = $conn->query($sql);

$row = $result->fetch_assoc();

$pendingOrders = $row["pendingOrders"];

//                                                       TODAY'S REVENUE

$sql = "SELECT SUM(total) AS todayRevenue FROM orders WHERE DATE(created_at) = 
CURDATE() AND status <> 'Cancelled'";

$result = $conn->query($sql);

$row = $result->fetch_assoc();

$todayRevenue = $row["todayRevenue"] ?? 0;


//                                                       AVERAGE ORDER VALUE

$sql = "SELECT AVG(total) AS averageOrder FROM orders WHERE status <> 'Cancelled'";

$result = $conn->query($sql);

$row = $result->fetch_assoc();

$averageOrder = $row["averageOrder"] ?? 0;

//                                                          THIS MONTH'S ORDERS

$sql = "SELECT SUM(total) AS monthRevenue FROM orders WHERE YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE()) AND status <> 'Cancelled'";

$result = $conn->query($sql);

$row = $result->fetch_assoc();

$monthRevenue = $row["monthRevenue"] ?? 0;

//                                                     LOW STOCK PRODUCTS               //

$sql = "SELECT COUNT(*) AS lowStock 
FROM products 
WHERE status = 'Active' 
AND stock > 0 
AND stock <= 10";

$result = $conn->query($sql);
$row = $result->fetch_assoc();
$lowStock = $row["lowStock"];

//                                                     OUT OF STOCK PRODUCTS               //

$sql = "SELECT COUNT(*) AS outOfStock 
FROM products 
WHERE status = 'Active' 
AND stock > 0 
AND stock = 0";

$result = $conn->query($sql);
$row = $result->fetch_assoc();
$outOfStock = $row["outOfStock"];

//                                                          RECENT ORDERS                           //

$sql = "SELECT id, order_number, customer_name, total, status, created_at 
        FROM orders ORDER BY created_at DESC LIMIT 10";

$recentOrders = $conn->query($sql);

//                                               SALES FOR THE LAST 7 DAYS - CHART ANALYTICS

$range = $_GET["range"] ?? "7";

if ($range === "30") {

    $where = "created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
} elseif ($range === "month") {

    $where = "YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())";
} else {

    $where = "created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
}

$sql = "SELECT DATE(created_at) AS 
sale_date, 
SUM(total) AS daily_total 
FROM orders 
WHERE $where GROUP BY DATE(created_at) 
ORDER BY sale_date";



$chartResult = $conn->query($sql);

if (!$chartResult) {
    die($conn->error);
}

$chartLabels = [];
$chartData = [];

while ($chart = $chartResult->fetch_assoc()) {

    $chartLabels[] = date("D", strtotime($chart["sale_date"]));
    $chartData[] = $chart["daily_total"];
}

//                                                         THE TOP 5 SELLING PRODUCTS

$sql = "SELECT p.name, SUM(io.quantity) AS totalSold FROM order_items io
        JOIN products p ON io.product_id = p.id
        GROUP BY io.product_id
        ORDER BY totalSold DESC
        LIMIT 5";

$topProducts = $conn->query($sql);

?>

<?php


?>


<div class="page-header">
    <h2>Dashboard
    </h2>
</div>

<div class="dashboard-grid">

    <div class="dashboard-card">
        <h3>Total Products</h3>
        <p>
            <a href="products.php" class="dashboard-link">
                <?php echo $totalProducts; ?>
            </a>
        </p>
    </div>

    <div class="dashboard-card">
        <h3>Orders Today</h3>
        <p>
            <a href="orders.php" class="dashboard-link">
                <?php echo $todayOrders; ?>
            </a>
        </p>
    </div>

    <div class="dashboard-card">
        <h3>Pending Orders</h3>
        <p>
            <a href="orders.php?status=Pending" class="dashboard-link">
                <?php echo $pendingOrders; ?>
            </a>
        </p>
    </div>

    <div class="dashboard-card">
        <h3>Today's Revenue</h3>
        <p>R<?php echo number_format($todayRevenue, 2); ?></p>
    </div>

    <div class="dashboard-card">

        <h3>Average Order</h3>
        <p>R<?php echo number_format($averageOrder, 2); ?></p>
    </div>

    <div class="dashboard-card">

        <h3>This Month's Revenue</h3>
        <p>R<?php echo number_format($monthRevenue, 2) ?></p>
    </div>

    <div class="dashboard-card">

        <h3>⚠ Low Stock!</h3>
        <p><a href="products.php?stock=low" class="dashboard-link">
                <?php echo $lowStock; ?>
            </a>
        </p>
    </div>

    <div class="dashboard-card">

        <h3>🔴 Out of Stock!</h3>
        <p><a href="products.php?stock=out" class="dashboard-link">
                <?php echo $outOfStock; ?>
            </a>
        </p>
    </div>

</div>

<h2>Recent Orders</h2>

<table class="dashboard-table">

    <tr>
        <th>Order #</th>
        <th>Customer</th>
        <th>Total</th>
        <th>Status</th>
        <th>Time</th>
    </tr>

    <?php

    while ($order = $recentOrders->fetch_assoc()) { ?>

        <tr>

            <td>
                <a href="order_details.php?id=<?php echo (int)$order["id"]; ?>" class="order-link">
                    <?php echo htmlspecialchars($order["order_number"]); ?>
                </a>
            </td>

            <td>
                <?php echo htmlspecialchars($order["customer_name"]); ?>
            </td>

            <td>R
                <?php echo number_format($order["total"], 2); ?>
            </td>

            <td>
                <span class="status <?php echo strtolower($order["status"]); ?>">
                    <?php echo htmlspecialchars($order["status"]); ?>
                </span>
            </td>

            <td>
                <?php echo date("H:i", strtotime($order["created_at"])); ?>
            </td>

        </tr>

    <?php } ?>

</table>

<div class="quick-actions">
    <h2>
        Quick Actions
    </h2>

    <div class="action-grid">
        <a href="add_product.php" class="action-card">
            ➕<span>
                Add Products
            </span>
        </a>

        <a href="products.php" class="action-card">
            📦<span>
                Products
            </span>
        </a>

        <a href="orders.php" class="action-card">
            🛒<span>
                Orders
            </span>
        </a>

        <a href="index.php" class="action-card">
            🏠<span>
                Customer Menu
            </span>
        </a>


    </div>
</div>

<div class="dashboard-chart">

    <div class="chart-header">
        <h2>Sales Overview</h2>

        <div class="chart-filter">

            <a href="?range=7" class="action-btn">7 Days</a>
            <a href="?range=30" class="action-btn">30 Days</a>
            <a href="?range=month" class="action-btn">This Month</a>

        </div>
    </div>

    <canvas id="salesChart"></canvas>

</div>

<div class="dashboard-card">

    <h2>
        Top Selling Products
    </h2>

    <table class="dashboard-table">

        <tr>
            <th>Product</th>
            <th>Sold</th>
        </tr>

        <?php while ($product = $topProducts->fetch_assoc()) { ?>

            <tr>
                <td>
                    <?php echo htmlspecialchars($product["name"]); ?>
                </td>

                <td>
                    <?php echo (int)$product["totalSold"] ?>
                </td>
            </tr>

        <?php } ?>

    </table>

</div>

<?php

require_once "includes/footer.php"

?>