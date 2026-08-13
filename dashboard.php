<?php
require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER, ROLE_CASHIER]);
require_once 'includes/db.php';

$loadScript = true;
$loadChart = true;

//                                                        TOTAL PRODUCTS

$sql = 'SELECT COUNT(*) AS totalProducts FROM products';
$result = $conn->query($sql);
$row = $result->fetch_assoc();

$totalProducts = $row['totalProducts'];

//                                                          ORDERS TODAY

$sql = 'SELECT COUNT(*) AS todayOrders FROM orders 
WHERE DATE(created_at) = CURDATE()';
$result = $conn->query($sql);
$row = $result->fetch_assoc();

$todayOrders = $row['todayOrders'];

//                                                        PENDING ORDERS

$sql = "SELECT COUNT(*) AS pendingOrders FROM orders WHERE status = 'Pending'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

$pendingOrders = $row['pendingOrders'];

//                                                       TODAY'S REVENUE

$sql = "SELECT SUM(total) AS todayRevenue FROM orders WHERE DATE(created_at) = 
CURDATE() AND status = 'Collected'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

$todayRevenue = $row['todayRevenue'] ?? 0;

//                                                       AVERAGE ORDER VALUE

$sql = "SELECT AVG(total) AS averageOrder FROM orders WHERE status = 'Collected'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

$averageOrder = $row['averageOrder'] ?? 0;

//                                                          THIS MONTH'S ORDERS

$sql = "SELECT SUM(total) AS monthRevenue FROM orders 
WHERE YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE()) AND status = 'Collected'";

$result = $conn->query($sql);
$row = $result->fetch_assoc();

$monthRevenue = $row['monthRevenue'] ?? 0;

//                                                     LOW STOCK PRODUCTS               //

$sql = "SELECT COUNT(*) AS lowStock 
FROM products 
WHERE status = 'Active' 
AND stock > 0 
AND stock <= 10";

$result = $conn->query($sql);
$row = $result->fetch_assoc();
$lowStock = $row['lowStock'];

//                                                     OUT OF STOCK PRODUCTS               //

$sql = "SELECT COUNT(*) AS outOfStock 
FROM products 
WHERE status = 'Active' 
AND stock = 0";

$result = $conn->query($sql);
$row = $result->fetch_assoc();
$outOfStock = $row['outOfStock'];

//                                                            INVENTORY VALUE                                                //

$result = $conn->query("SELECT SUM(price * stock) 
    AS inventoryValue 
    FROM products 
    WHERE status = 'Active'
        ");
    $inventoryValue = $result->fetch_assoc()['inventoryValue'] ?? 0;

//                                                             PENDING PURCHASE ORDERS                                                      //

$result = $conn->query("SELECT COUNT(*) 
    AS pendingPOs 
    FROM purchase_orders 
    WHERE status = 'Pending'");
$pendingPOs = $result->fetch_assoc()['pendingPOs'];

//                                                                ACTIVE SUPPLIERS                                                       //

$result = $conn->query("SELECT COUNT(*) 
        AS totalSuppliers 
        FROM suppliers 
        WHERE status = 'Active'");
$totalSuppliers = $result->fetch_assoc()['totalSuppliers'];

//                                                     LOW STOCK PRODUCT LIST (FOR THE ALERT WIDGETS)                                      //

$lowStockProducts = $conn->query("SELECT 
    name, 
    stock FROM products 
    WHERE status = 'Active' AND stock > 0 AND stock <= 10 ORDER BY stock ASC LIMIT 5");

//                                                                  RECENT ORDERS                                                                 //

$sql = 'SELECT id, order_number, customer_name, total, status, created_at 
        FROM orders ORDER BY created_at DESC LIMIT 10';

$recentOrders = $conn->query($sql);

//                                                    SALES FOR THE LAST 7 DAYS - CHART ANALYTICS

$range = $_GET['range'] ?? '7';

if ($range === '30') {
    $where = 'created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)';
} elseif ($range === 'month') {
    $where = 'YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())';
} else {
    $where = 'created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)';
}

$sql = "SELECT DATE(created_at) AS 
sale_date, 
SUM(total) AS daily_total 
FROM orders 
WHERE $where GROUP BY DATE(created_at) 
ORDER BY sale_date";

$chartResult = $conn->query($sql);

if (!$chartResult) {
    error_log('dashboard.php: Failed to load sales chart data: '.$conn->error);
    exit('Unable to load dashboard sales chart data.');
}

$chartLabels = [];
$chartData = [];

while ($chart = $chartResult->fetch_assoc()) {
    $chartLabels[] = date('D', strtotime($chart['sale_date']));
    $chartData[] = $chart['daily_total'];
}

//                                                         THE TOP 5 SELLING PRODUCTS

$sql = 'SELECT p.name, SUM(io.quantity) AS totalSold FROM order_items io
        JOIN products p ON io.product_id = p.id
        GROUP BY io.product_id
        ORDER BY totalSold DESC
        LIMIT 5';

$topProducts = $conn->query($sql);

require_once 'includes/header.php';
?>

<div class="page-header">
    <h2>Dashboard
    </h2>
</div>
<?php require 'includes/shared/flash_message.php'; ?>

<?php include 'includes/partials/dashboard/dashboard_stats.php'; ?>

<?php require 'includes/partials/dashboard/dashboard_chart.php'; ?>

<?php require 'includes/partials/dashboard/dashboard_cards.php'; ?>

<?php require 'includes/partials/dashboard/dashboard_quick_actions.php'; ?>

<?php require 'includes/partials/dashboard/dashboard_recent_orders.php'; ?>

<?php require 'includes/partials/dashboard/dashboard_top_selling.php'; ?>

<?php

require_once 'includes/footer.php';

?>