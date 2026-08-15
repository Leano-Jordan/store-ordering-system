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

$status = 'Cancelled';

$revenueStmt = $conn->prepare("SELECT SUM(total) AS revenue
    FROM orders
    WHERE $where
    AND status = ?
");

if (!$revenueStmt) {
    error_log('reports.php: Failed to prepare revenue query. MySQL error: '.$conn->error);
    exit('Unable to generate report. Please try again later.');
}

$revenueStmt->bind_param('s', $status);
$revenueStmt->execute();

$totalRevenue = $revenueStmt->get_result()->fetch_assoc()['revenue'] ?? 0;

$revenueStmt->close();

$totalOrders = $conn->query("SELECT COUNT(*) 
AS total FROM orders WHERE $where")->fetch_assoc()['total'] ?? 0;

$completedOrders = $conn->query("SELECT COUNT(*) 
AS total FROM orders WHERE $where AND status = 'Collected'")->fetch_assoc()['total'] ?? 0;

$cancelledOrders = $conn->query("SELECT COUNT(*) 
AS total FROM orders WHERE $where AND status = 'Cancelled'")->fetch_assoc()['total'] ?? 0;

$averageOrder = $conn->query("SELECT AVG(total) 
AS avg FROM orders WHERE $where AND status = 'Collected'")->fetch_assoc()['avg'] ?? 0;

$dailySales = $conn->query("SELECT DATE(created_At) 
AS sale_date, COUNT(*) AS order_count, SUM(total) AS daily_total FROM orders WHERE $where 
AND status = 'Collected' GROUP BY DATE(created_at) ORDER BY sale_date DESC");

$chartResult = $conn->query("SELECT DATE(created_At) 
AS sale_date, SUM(total) AS daily_total FROM orders WHERE $where 
AND status = 'Collected' GROUP BY DATE(created_at) ORDER BY sale_date ASC");

$chartLabels = [];
$chartData = [];

$topProducts = $conn->query("SELECT p.name, 
SUM(oi.quantity) AS quantity_sold FROM order_items oi
LEFT JOIN products p ON oi.product_id = p.id JOIN orders o ON oi.order_id = o.id
WHERE $where AND o.status = 'Collected' GROUP BY oi.product_id
ORDER BY quantity_sold DESC LIMIT 5");

$topCustomers = $conn->query("SELECT customer_name, 
COUNT(*) AS orders, SUM(total) AS spent FROM orders 
WHERE $where AND status = 'Collected' GROUP BY customer_name ORDER BY spent DESC LIMIT 5");

$topCategories = $conn->query("SELECT p.category, 
SUM(oi.quantity) AS quantity_sold FROM order_items oi
LEFT JOIN products p ON oi.product_id = p.id JOIN orders o ON oi.order_id = o.id
WHERE $where AND o.status = 'Collected' GROUP BY p.category
ORDER BY quantity_sold DESC");

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