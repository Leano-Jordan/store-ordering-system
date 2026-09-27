<?php
require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER, ROLE_CASHIER]);
require_once 'includes/db.php';
require_once 'includes/dashboard_context.php';

$loadScript = true;
$loadChart = false;

//                                                        TOTAL PRODUCTS

$sql = 'SELECT COUNT(*) AS totalProducts 
FROM products';

$result = $conn->query($sql);

if (!$result) {
    error_log(
        'dashboard.php: Failed to load total product count: '
        .$conn->error
    );
    exit('Unable to load dashboard product data.');
}

    $row = $result->fetch_assoc();
    $totalProducts = (int) ($row['totalProducts'] ?? 0);

    $result->free();

//                                                          ORDERS TODAY

$todayStart = (new DateTimeImmutable('today'))
->format('Y-m-d H:i:s');

$tomorrowStart = (new DateTimeImmutable('tomorrow'))
->format('Y-m-d H:i:s');

$sql = "SELECT COUNT(*) AS todayOrders 
FROM orders 
WHERE created_at >= ? 
AND created_at < ?
AND status != 'Cancelled'";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log('dashboard.php: Failed to prepare today orders query: '.$conn->error);
    exit('Unable to load dashboard data.');
}

if (!$stmt->bind_param('ss', $todayStart, $tomorrowStart)) {
    error_log('dashboard.php: Failed to bind today orders query: '.$stmt->error);

    $stmt->close();

    exit('Unable to load dashboard data.');
}

if (!$stmt->execute()) {
    error_log('dashboard.php: Failed to execute today orders query: '.$stmt->error);

    $stmt->close();

    exit('Unable to load dashboard data.');
}

$result = $stmt->get_result();

if (!$result) {
    $stmt->close();

    error_log('dashboard.php: Failed to retrieve today orders result: '.$conn->error);

    exit('Unable to load dashboard data.');
}

$row = $result->fetch_assoc();
$result->free();
$stmt->close();

$todayOrders = (int) ($row['todayOrders'] ?? 0);

//                                                        PENDING ORDERS

$sql = "SELECT COUNT(*) AS pendingOrders FROM orders WHERE status = 'Pending'";

$result = $conn->query($sql); if (!$result) {
    error_log(
        'dashboard.php: Failed to load pending orders: '
        .$conn->error
    );
    exit('Unable to load dashboard order data.');
}
        $row = $result->fetch_assoc();
        $pendingOrders = (int) ($row['pendingOrders'] ?? 0);

        $result->free();

//                                                       TODAY'S REVENUE

$sql = "SELECT SUM(total) AS todayRevenue
FROM orders WHERE created_at >= ?
AND created_at < ?
AND status = 'Collected'";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log('dashboard.php: Failed to prepare today revenue query: '.$conn->error);
    exit('Unable to load dashboard revenue.');
}

if (!$stmt->bind_param('ss', $todayStart, $tomorrowStart)) {
    error_log('dashboard.php: Failed to bind today revenue query: '.$stmt->error);

    $stmt->close();

    exit('Unable to load dashboard revenue.');
}

if (!$stmt->execute()) {
    error_log('dashboard.php: Failed to execute today revenue query: '.$stmt->error);

    $stmt->close();

    exit('Unable to load dashboard revenue.');
}

$result = $stmt->get_result();

if (!$result) {
    $stmt->close();

    error_log(
        'dashboard.php: Failed to retrieve today revenue result: '
        .$stmt->error
    );

    exit('Unable to load dashboard revenue.');
}

$row = $result->fetch_assoc();

$todayRevenue = (float) ($row['todayRevenue'] ?? 0);

$result->free();

$stmt->close();

//                                                       AVERAGE ORDER VALUE

$averageOrder = 0.00;

//                                                          THIS MONTH'S ORDERS

$monthStart = (new DateTimeImmutable('First day of this month.'))
->setTime(0, 0)
->format('Y-m-d H:i:s');

$nextMonthStart = (new DateTimeImmutable('First day of next month.'))
->setTime(0, 0)
->format('Y-m-d H:i:s');

$monthRevenue = 0.00;

//                                                     LOW STOCK PRODUCTS               //

$sql = "SELECT COUNT(*) AS lowStock 
FROM products 
WHERE status = 'Active' 
AND stock > 0 
AND stock <= 10";

$result = $conn->query($sql);

if (!$result) {
    error_log('dashboard.php: Low-stock query failed: '.$conn->error);

    exit('Unable to load dashboard inventory data.');
}

$row = $result->fetch_assoc();

if (!is_array($row) || !isset($row['lowStock'])) {
    error_log('dashboard.php: Low-stock query returned an invalid result.');

    $result->free();

    exit('Unable to load dashboard inventory data.');
}

$lowStock = (int) $row['lowStock'];

$result->free();

//                                                     OUT OF STOCK PRODUCTS          //

$sql = "SELECT COUNT(*) AS outOfStock 
FROM products 
WHERE status = 'Active' 
AND stock = 0";

$result = $conn->query($sql);
if (!$result) {
    error_log('dashboard.php: Failed to load Out-of-stock query failed: '.$conn->error);
    exit('Unable to load dashboard inventory data.');
}

$row = $result->fetch_assoc();
$outOfStock = (int) ($row['outOfStock'] ?? 0);
$result->free();

//                                                            INVENTORY VALUE                                     //

$result = $conn->query("SELECT SUM(price * stock) 
    AS inventoryValue 
    FROM products 
    WHERE status = 'Active'
        ");

if (!$result) {
    error_log('dashboard.php: Failed to load inventory value: '.$conn->error);
    exit('Unable to load dashboard inventory data.');
}

$row = $result->fetch_assoc();
$inventoryValue = (float) ($row['inventoryValue'] ?? 0);
$result->free();

//                                                             PENDING PURCHASE ORDERS                               //

$result = $conn->query("SELECT COUNT(*) AS pendingPOs 
    FROM purchase_orders WHERE status = 'Pending'
    ");

    if (!$result) {
        error_log('dashboard.php: Failed to load pending purchase orders: '.$conn->error);
        exit('Unable to load dashboard purchase order data.');
    }

    $row = $result->fetch_assoc();
    $pendingPOs = (int) ($row['pendingPOs'] ?? 0);
    $result->free();

//                                                                ACTIVE SUPPLIERS                                                       //

$result = $conn->query("SELECT COUNT(*) AS totalSuppliers 
        FROM suppliers WHERE status = 'Active'");

if (!$result) {
    error_log('dashboard.php: Failed to load Supplier count: '.$conn->error);
    exit('Unable to load dashboard supplier data.');
}

$row = $result->fetch_assoc();
$totalSuppliers = (int) ($row['totalSuppliers'] ?? 0);
$result->free();

//                                                     LOW STOCK PRODUCT LIST (FOR THE ALERT WIDGETS)

$lowStockProducts = null;

if ($dashboardContext['showLowStockAlert']) {
    $lowStockProducts = $conn->query(
        "SELECT name, stock
         FROM products
         WHERE status = 'Active'
         AND stock > 0
         AND stock <= 10
         ORDER BY stock ASC
         LIMIT 5"
    );

    if (!$lowStockProducts) {
        error_log('dashboard.php: Failed to load low-stock products: '.$conn->error);
        exit('Unable to load dashboard inventory alerts.');
    }
}

//                                                                  RECENT ORDERS                                                                 //

$sql = 'SELECT id, order_number, customer_name, total, status, created_at 
        FROM orders ORDER BY created_at DESC LIMIT 10';

$recentOrders = $conn->query($sql);

if (!$recentOrders) {
    error_log('dashboard.php: Failed to load recent orders: '.$conn->error);
    exit('Unable to load recent orders.');
}

//                                                COLLECTED ORDER HISTORY

$result = $conn->query(
    "SELECT COUNT(*) AS collectedOrders
     FROM orders
     WHERE status = 'Collected'"
);

if (!$result) {
    error_log('dashboard.php: Failed to load collected order count: '.$conn->error);
    exit('Unable to load dashboard sales history.');
}

$row = $result->fetch_assoc();
$collectedOrders = (int) ($row['collectedOrders'] ?? 0);
$result->free();

//                                                ADAPTIVE DASHBOARD CONTEXT

$dashboardContext = buildDashboardContext(
    (string) ($_SESSION['role'] ?? ''),
    [
        'totalProducts' => $totalProducts,
        'todayOrders' => $todayOrders,
        'pendingOrders' => $pendingOrders,
        'collectedOrders' => $collectedOrders,
        'lowStock' => $lowStock,
        'outOfStock' => $outOfStock,
        'totalSuppliers' => $totalSuppliers,
        'pendingPOs' => $pendingPOs,
        'hasRecentOrders' => $recentOrders->num_rows > 0,
    ]
);

if ($dashboardContext['showAverageOrder']) {
    $sql = "SELECT AVG(total) AS averageOrder
            FROM orders
            WHERE status = 'Collected'";

    $result = $conn->query($sql);

    if (!$result) {
        error_log('dashboard.php: Failed to load average order value: '.$conn->error);
        exit('Unable to load dashboard sales data.');
    }

    $row = $result->fetch_assoc();
    $averageOrder = (float) ($row['averageOrder'] ?? 0);
    $result->free();
}

if ($dashboardContext['showMonthRevenueMetrics']) {
    $sql = "SELECT SUM(total) AS monthRevenue
            FROM orders
            WHERE created_at >= ?
            AND created_at < ?
            AND status = 'Collected'";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log('dashboard.php: Failed to prepare monthly revenue query: '.$conn->error);
        exit('Unable to load monthly revenue.');
    }

    if (!$stmt->bind_param('ss', $monthStart, $nextMonthStart)) {
        error_log('dashboard.php: Failed to bind monthly revenue query: '.$stmt->error);
        $stmt->close();
        exit('Unable to load monthly revenue.');
    }

    if (!$stmt->execute()) {
        error_log('dashboard.php: Failed to execute monthly revenue query: '.$stmt->error);
        $stmt->close();
        exit('Unable to load monthly revenue.');
    }

    $result = $stmt->get_result();

    if (!$result) {
        error_log('dashboard.php: Failed to retrieve monthly revenue result: '.$stmt->error);
        $stmt->close();
        exit('Unable to load monthly revenue.');
    }

    $row = $result->fetch_assoc();
    $monthRevenue = (float) ($row['monthRevenue'] ?? 0);

    $result->free();
    $stmt->close();
}



//                                                    SALES FOR THE LAST 7 DAYS - CHART ANALYTICS

$chartLabels = [];
$chartData = [];

if ($dashboardContext['showSalesChart']) {
    $range = $_GET['range'] ?? '7';
    $allowedRanges = ['7', '30', 'month'];

    if (!in_array($range, $allowedRanges, true)) {
        $range = '7';
    }

    $now = new DateTimeImmutable('now');

    switch ($range) {
        case '30':
            $rangeStart = $now
            ->modify('-30 days')
            ->setTime(0, 0)
            ->format('Y-m-d H:i:s');

            $rangeEnd = $tomorrowStart;
            break;

        case 'month':
            $rangeStart = $monthStart;
            $rangeEnd = $nextMonthStart;
            break;

        default:
            $rangeStart = $now
            ->modify('-6 days')
            ->setTime(0, 0)
            ->format('Y-m-d H:i:s');

            $rangeEnd = $tomorrowStart;
            break;
    }

    $sql = "SELECT DATE(created_at) AS sale_date,
            SUM(total) AS daily_total
            FROM orders
            WHERE created_at >= ?
            AND created_at < ?
            AND status = 'Collected'
            GROUP BY DATE(created_at)
            ORDER BY sale_date";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log('dashboard.php: Failed to prepare sales chart query: '.$conn->error);
        exit('Unable to load dashboard sales chart.');
    }

    if (!$stmt->bind_param('ss', $rangeStart, $rangeEnd)) {
        error_log('dashboard.php: Failed to bind sales chart query: '.$stmt->error);
        $stmt->close();
        exit('Unable to load dashboard sales chart.');
    }

    if (!$stmt->execute()) {
        error_log('dashboard.php: Failed to execute sales chart query: '.$stmt->error);
        $stmt->close();
        exit('Unable to load dashboard sales chart.');
    }

    $chartResult = $stmt->get_result();
    $stmt->close();

    if (!$chartResult) {
        error_log('dashboard.php: Failed to load sales chart data: '.$conn->error);
        exit('Unable to load dashboard sales chart data.');
    }

    while ($chart = $chartResult->fetch_assoc()) {
        $chartLabels[] = date('D', strtotime($chart['sale_date']));
        $chartData[] = $chart['daily_total'];
    }

    $chartResult->free();
    $loadChart = true;
}

//                                                         THE TOP 5 SELLING PRODUCTS

$topProducts = null;

if ($dashboardContext['showTopSelling']) {
    $sql = 'SELECT io.product_name_at_sale AS name,
            SUM(io.quantity) AS totalSold FROM order_items io
            JOIN orders o ON io.order_id = o.id
            WHERE o.status = "Collected"
            GROUP BY io.product_id, io.product_name_at_sale
            ORDER BY totalSold DESC LIMIT 5';

    $topProducts = $conn->query($sql);

    if (!$topProducts) {
        error_log('dashboard.php: Failed to load top-selling products: '.$conn->error);
        exit('Unable to load dashboard sales data.');
    }
}

require_once 'includes/header.php';

require_once 'includes/header.php';
?>

<div class="page-header">
    <h2>Dashboard</h2>
</div>
<?php require 'includes/shared/flash_message.php'; ?>

<?php include 'includes/partials/dashboard/dashboard_get_started.php'; ?>

<?php include 'includes/partials/dashboard/dashboard_stats.php'; ?>

<?php require 'includes/partials/dashboard/dashboard_chart.php'; ?>

<?php require 'includes/partials/dashboard/dashboard_cards.php'; ?>

<?php require 'includes/partials/dashboard/dashboard_quick_actions.php'; ?>

<?php require 'includes/partials/dashboard/dashboard_recent_orders.php'; ?>

<?php require 'includes/partials/dashboard/dashboard_top_selling.php'; ?>

<?php

require_once 'includes/footer.php';

?>