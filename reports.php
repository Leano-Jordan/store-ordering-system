<?php
require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once 'includes/db.php';
require_once 'includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Location: dashboard.php');
    exit();
}

$loadChart = true;
$loadScript = true;

$allowedRanges = ['7', '30', 'month', 'year'];

$range = $_GET['range'] ?? '7';

if (!in_array($range, $allowedRanges, true)) {
    $range = '7';
}

switch ($range) {
    case '30':
    $where = 'created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)';
    break;

case 'month':
    $where = 'YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())';
    break;

case 'year':
    $where = 'YEAR(created_at) = YEAR(CURDATE())';
    break;

default:
    $where = 'created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)';
    break;
}

$status = 'Collected';

function reportScalar(
    mysqli $conn,
    string $sql,
    string $column
): float {
    $result = $conn->query($sql);

    if (!$result) {
        error_log('reports.php: Query failed. '.$conn->error);

        exit('Unable to generate report.');
    }

    $row = $result->fetch_assoc();

    if (!$row) {
        return 0.00;
    }

    return (float) ($row[$column] ?? 0);
}

$revenueStmt = $conn->prepare(
    "SELECT SUM(total) AS revenue
    FROM orders WHERE $where
    AND status = ?"
);

if (!$revenueStmt) {
    error_log('reports.php: Failed to prepare revenue query. MySQL error: '.$conn->error);
    exit('Unable to generate report. Please try again later.');
}

if (!$revenueStmt->bind_param('s', $status)) {
    error_log('reports.php: Revenue query parameter binding failed: '.$revenueStmt->error);

    $revenueStmt->close();

    exit('Unable to generate report. Please try again later.');
}

if (!$revenueStmt->execute()) {
    error_log('reports.php: Revenue query execution failed: '.$revenueStmt->error);
    $revenueStmt->close();
    exit('Unable to to generate report. Please try again later.');
}

$revenueResult = $revenueStmt->get_result();

if (!$revenueResult) {
    error_log('reports.php: Revenue query result failed: '.$revenueStmt->error);
    $revenueStmt->close();
    exit('Unable to to generate report. Please try again later.');
}

$revenueRow = $revenueResult->fetch_assoc();

$totalRevenue = (float) ($revenueRow['revenue'] ?? 0);

$revenueStmt->close();

$totalOrders = reportScalar($conn, "SELECT COUNT(*) 
AS total FROM orders WHERE $where", 'total');

$completedOrders = reportScalar($conn, "SELECT COUNT(*) 
AS total FROM orders WHERE $where AND status = 'Collected'", 'total');

$cancelledOrders = reportScalar($conn, "SELECT COUNT(*) 
AS total FROM orders WHERE $where AND status = 'Cancelled'", 'total');

$averageOrder = reportScalar($conn, "SELECT AVG(total) 
AS avg FROM orders WHERE $where AND status = 'Collected'", 'avg');

$dailySales = $conn->query("SELECT DATE(created_At) 
AS sale_date, COUNT(*) AS order_count, SUM(total) AS daily_total FROM orders WHERE $where 
AND status = 'Collected' GROUP BY DATE(created_at) ORDER BY sale_date DESC");

if (!$dailySales) {
    error_log('reports.php: Daily sales query failed: '.$conn->error);
    exit('Unable to to generate report. Please try again later.');
}

$chartResult = $conn->query("SELECT DATE(created_At) 
AS sale_date, SUM(total) AS daily_total FROM orders WHERE $where 
AND status = 'Collected' GROUP BY DATE(created_at) ORDER BY sale_date ASC");

if (!$chartResult) {
    error_log('reports.php: Failed to load chart data: '.$conn->error);

    exit('Unable to generate report.');
}

$chartLabels = [];
$chartData = [];

$topProducts = $conn->query("SELECT p.name, 
SUM(oi.quantity) AS quantity_sold FROM order_items oi
LEFT JOIN products p ON oi.product_id = p.id JOIN orders o ON oi.order_id = o.id
WHERE $where AND o.status = 'Collected' GROUP BY oi.product_id
ORDER BY quantity_sold DESC LIMIT 5");

if (!$topProducts) {
    error_log('reports.php: Top-products query failed: '.$conn->error);
    exit('Unable to to generate report.');
}

$topCustomers = $conn->query("SELECT customer_name, 
COUNT(*) AS orders, SUM(total) AS spent FROM orders 
WHERE $where AND status = 'Collected' GROUP BY customer_name ORDER BY spent DESC LIMIT 5");

if (!$topCustomers) {
    error_log('reports.php: Top-customers query failed: '.$conn->error);
    exit('Unable to to generate report.');
}

$topCategories = $conn->query("SELECT p.category, 
SUM(oi.quantity) AS quantity_sold FROM order_items oi
LEFT JOIN products p ON oi.product_id = p.id JOIN orders o ON oi.order_id = o.id
WHERE $where AND o.status = 'Collected' GROUP BY p.category
ORDER BY quantity_sold DESC");

if (!$topCategories) {
    error_log('reports.php: Top-categories query failed: '.$conn->error);
    exit('Unable to to generate report.');
}

while ($chart = $chartResult->fetch_assoc()) {
    $chartLabels[] = date('d M', strtotime($chart['sale_date']));
    $chartData[] = $chart['daily_total'];
}

require_once 'includes/header.php';

?>

<div class="page-header">
    <h2>Sales Report</h2>
</div>
<br>

<?php require 'includes/shared/flash_message.php'; ?>

<?php require 'includes/partials/reports/report_toolbar.php'; ?>

<?php require 'includes/partials/reports/report_summary_cards.php'; ?>

<?php require 'includes/partials/reports/report_chart.php'; ?>

<br>

<?php require 'includes/partials/reports/report_daily_breakdown.php'; ?>

<br>

<?php require 'includes/partials/reports/report_top_products.php'; ?>

<br>

<?php require 'includes/partials/reports/report_top_customers.php'; ?>

<br>

<?php require 'includes/partials/reports/report_top_categories.php'; ?>

<?php require_once 'includes/footer.php'; ?>