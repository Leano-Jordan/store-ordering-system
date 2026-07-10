<?php
/*require_once "includes/auth.php";
require_once "includes/permissions.php";
requireRole([ROLE_ADMIN, ROLE_MANAGER]);*/
require_once "includes/db.php";

$loadChart = true;
$loadScript = true;

$range = $_GET["range"] ?? "7";

if ($range === "30") {

    $where = "created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
} elseif ($range === "month") {
    $where = "YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())";
} elseif ($range === "year") {
    $where = "YEAR(created_at) = YEAR(CURDATE())";
} else {
    $where = "created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
}

$result = $conn->query("SELECT SUM(total) AS revenue FROM orders 
WHERE $where AND status <> 'Cancelled'");

if (!$result) {
    die($conn->error);
}

$totalRevenue = $result->fetch_assoc()["revenue"] ?? 0;

$totalOrders = $conn->query("SELECT COUNT(*) 
AS total FROM orders WHERE $where")->fetch_assoc()["total"] ?? 0;

$completedOrders = $conn->query("SELECT COUNT(*) 
AS total FROM orders WHERE $where AND status = 'Collected'")->fetch_assoc()["total"] ?? 0;

$cancelledOrders = $conn->query("SELECT COUNT(*) 
AS total FROM orders WHERE $where AND status = 'Cancelled'")->fetch_assoc()["total"] ?? 0;

$averageOrder = $conn->query("SELECT AVG(total) 
AS avg FROM orders WHERE $where AND status<> 'Cancelled'")->fetch_assoc()["avg"] ?? 0;

$dailySales = $conn->query("SELECT DATE(created_At) 
AS sale_date, COUNT(*) AS order_count, SUM(total) AS daily_total FROM orders WHERE $where 
AND status <> 'Cancelled' GROUP BY DATE(created_at) ORDER BY sale_date DESC");

$chartResult = $conn->query("SELECT DATE(created_At) 
AS sale_date, SUM(total) AS daily_total FROM orders WHERE $where 
AND status <> 'Cancelled' GROUP BY DATE(created_at) ORDER BY sale_date ASC");

$chartLabels = [];
$chartData = [];

$topProducts = $conn->query("SELECT p.name, 
SUM(oi.quantity) AS quantity_sold FROM order_items oi
JOIN products p ON oi.product_id = p.id JOIN orders o ON oi.order_id = o.id
WHERE $where AND o.status <> 'Cancelled' GROUP BY oi.product_id
ORDER BY quantity_sold DESC LIMIT 5");

$topCustomers = $conn->query("SELECT customer_name, 
COUNT(*) AS orders, SUM(total) AS spent FROM orders 
WHERE $where AND status <> 'Cancelled' GROUP BY customer_name ORDER BY spent DESC LIMIT 5");

$topCategories = $conn->query("SELECT p.category, 
SUM(oi.quantity) AS quantity_sold FROM order_items oi
JOIN products p ON oi.product_id = p.id JOIN orders o ON oi.order_id = o.id
WHERE $where AND o.status <> 'Cancelled' GROUP BY p.category
ORDER BY quantity_sold DESC");

while ($chart = $chartResult->fetch_assoc()) {
    $chartLabels[] = date("d M", strtotime($chart["sale_date"]));
    $chartData[] = $chart["daily_total"];
}

require_once "includes/header.php";
?>

<div class="page-header">
    <h2>Sales Report</h2>
</div>

<div class="report-actions">
    <div class="chart-filter">
        <a href="?range=7" class="action-btn <?php echo $range === '7' ? 'active-nav' : ''; ?>">7 Days</a>
        <a href="?range=30" class="action-btn <?php echo $range === '30' ? 'active-nav' : ''; ?>">30 Days</a>
        <a href="?range=month" class="action-btn <?php echo $range === 'month' ? 'active-nav' : ''; ?>">This Month</a>
        <a href="?range=year" class="action-btn <?php echo $range === 'year' ? 'active-nav' : ''; ?>">This Year</a>
    </div>

    <div class="report-buttons">

        <a href="export_report_csv.php?range=<?php echo urlencode($range); ?>" class="action-btn">
            Export CSV
        </a>

        <a href="export_report_pdf.php?range=<?php echo urlencode($range); ?>"
            target="_blank"
            class="action-btn">
            Export PDF
        </a>

        <a href="#" onclick="window.print();" class="action-btn">
            Print
        </a>

    </div>
</div>
<br>

<div class="dashboard-grid">

    <div class="dashboard-card">
        <h3>Total Revenue</h3>
        <p>R<?php echo number_format($totalRevenue, 2); ?></p>
    </div>

    <div class="dashboard-card">
        <h3>Total Orders</h3>
        <p><?php echo $totalOrders; ?></p>
    </div>

    <div class="dashboard-card">
        <h3>Completed</h3>
        <p><?php echo $completedOrders; ?></p>
    </div>

    <div class="dashboard-card">
        <h3>Cancelled</h3>
        <p><?php echo $cancelledOrders; ?></p>
    </div>

    <div class="dashboard-card">
        <h3>Average Orders</h3>
        <p>R<?php echo number_format($averageOrder, 2); ?></p>
    </div>

</div>

<div class="dashboard-chart">
    <h2>Revenue Over Time</h2>
    <canvas id="salesChart"></canvas>
</div>

<br>

<h2>Daily Breakdown</h2>

<table class="orders-table reports-table">

    <thead>
        <tr>
            <th>Date</th>
            <th>Orders</th>
            <th>Revenue</th>
        </tr>
    </thead>

    <tbody>
        <?php while ($row = $dailySales->fetch_assoc()) { ?>

            <tr>
                <td><?php echo date("d M Y", strtotime($row["sale_date"])); ?></td>
                <td><?php echo (int)$row["order_count"]; ?></td>
                <td><?php echo number_format($row["daily_total"], 2); ?></td>
            </tr>
        <?php } ?>

    </tbody>
</table>

<br>
<h2>Top Selling Products</h2>

<table class="orders-table reports-table">

    <thead>
        <tr>
            <th>Product</th>
            <th>Quantity Sold</th>
        </tr>
    </thead>

    <tbody>
        <?php
        while ($product = $topProducts->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($product["name"]); ?></td>
                <td><?php echo (int)$product["quantity_sold"]; ?></td>
            </tr>
        <?php } ?>

    </tbody>
</table>

<br>
<h2>Top Customers</h2>
<table class="orders-table reports-table">

    <thead>
        <tr>
            <th>Customer</th>
            <th>Orders</th>
            <th>Total Spent</th>
        </tr>
    </thead>

    <tbody>
        <?php
        while ($customer = $topCustomers->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($customer["customer_name"]); ?></td>
                <td><?php echo (int)$customer["orders"]; ?></td>
                <td><?php echo number_format($customer["spent"], 2); ?></td>
            </tr>
        <?php } ?>

    </tbody>
</table>

<br>
<h2>Best Selling Categories</h2>

<table class="orders-table reports-table">

    <thead>
        <tr>
            <th>Category</th>
            <th>Items Sold</th>
        </tr>
    </thead>

    <tbody>
        <?php
        while ($category = $topCategories->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($category["category"]); ?></td>
                <td><?php echo (int)$category["quantity_sold"]; ?></td>
            </tr>
        <?php } ?>

    </tbody>
</table>



<?php require_once "includes/footer.php"; ?>