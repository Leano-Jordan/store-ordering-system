<?php

require_once 'vendor/autoload.php';

use Dompdf\Dompdf;

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);

require_once 'includes/db.php';

$dompdf = new Dompdf();

$range = $_GET['range'] ?? '7';

$allowedRanges = ['7', '30', 'month', 'year'];

if (!in_array($range, $allowedRanges, true)) {
    $range = '7';
}

if ($range === '30') {
    $where = 'created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)';
} elseif ($range === 'month') {
    $where = 'YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())';
} elseif ($range === 'year') {
    $where = 'YEAR(created_at) = YEAR(CURDATE())';
} else {
    $where = 'created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)';
}

$revenueResult = $conn->query("SELECT SUM(total) 
AS revenue FROM orders WHERE $where AND status = 'Collected'");

if (!$revenueResult) {
    error_log('export_report_pdf.php: Failed to load revenue: '.$conn->error);

    http_response_code(500);

    exit('Unable to export report');
}

$revenueRow = $revenueResult->fetch_assoc();
$revenueResult->free();

$totalRevenue = (float) ($revenueRow['revenue'] ?? 0);

$totalOrdersResult = $conn->query(
    "SELECT COUNT(*) 
    AS total FROM orders 
    WHERE $where"
);

if (!$totalOrdersResult) {
    error_log(
        'export_report_pdf.php: Failed to load total orders: '
        .$conn->error
    );

    http_response_code(500);

    exit('Unable to export report');
}

$totalOrdersRow = $totalOrdersResult->fetch_assoc();
$totalOrders = (int) ($totalOrdersRow['total'] ?? 0);
$totalOrdersResult->free();

$completedOrdersResult = $conn->query(
    "SELECT COUNT(*) AS total
    FROM orders
    WHERE $where AND status = 'Collected'"
);

if (!$completedOrdersResult) {
    error_log(
        'export_report_pdf.php: Failed to load completed orders: '
        .$conn->error
    );

    http_response_code(500);

    exit('Unable to export report');
}

$completedOrdersRow = $completedOrdersResult->fetch_assoc();
$completedOrders = (int) ($completedOrdersRow['total'] ?? 0);
$completedOrdersResult->free();

$cancelledOrdersResult = $conn->query(
    "SELECT COUNT(*) 
    AS total
    FROM orders
    WHERE $where AND status = 'Cancelled'"
);

if (!$cancelledOrdersResult) {
    error_log(
        'export_report_pdf.php: Failed to load cancelled orders: '
        .$conn->error
    );

    http_response_code(500);

    exit('Unable to export report');
}

$cancelledOrdersRow = $cancelledOrdersResult->fetch_assoc();
$cancelledOrders = (int) ($cancelledOrdersRow['total'] ?? 0);
$cancelledOrdersResult->free();

$averageOrderResult = $conn->query(
    "SELECT AVG(total) 
    AS avg
    FROM orders
    WHERE $where 
    AND status = 'Collected'"
);

if (!$averageOrderResult) {
    error_log(
        'export_report_pdf.php: Failed to load average order: '
        .$conn->error
    );

    http_response_code(500);

    exit('Unable to export report');
}

$averageOrderRow = $averageOrderResult->fetch_assoc();
$averageOrder = (float) ($averageOrderRow['avg'] ?? 0);
$averageOrderResult->free();

$html = '
<h1>SwiftOrder POS - Sales Report</h1>
<hr>
<h3>Summary</h3>

<p><strong>Total Revenue:</strong> R'.number_format($totalRevenue, 2)."</p>
<p><strong>Total Orders:</strong>{$totalOrders}</p>
<p><strong>Completed Orders:</strong>{$completedOrders}</p>
<p><strong>Cancelled Orders:</strong>{$cancelledOrders}</p>
<p><strong>Average Order:</strong> R".number_format($averageOrder, 2).'</p>
';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('SwiftOrder_Report.pdf', ['Attachment' => false]);
exit();
