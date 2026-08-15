<?php

require_once 'vendor/autoload.php';

use Dompdf\Dompdf;

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);

require_once 'includes/db.php';

$dompdf = new Dompdf();

$range = $_GET['range'] ?? '7';

if ($range === '30') {
    $where = 'created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)';
} elseif ($range === 'month') {
    $where = 'YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())';
} elseif ($range === 'year') {
    $where = 'YEAR(created_at) = YEAR(CURDATE())';
} else {
    $where = 'created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)';
}

$totalRevenue = $conn->query("SELECT SUM(total) 
AS revenue FROM orders WHERE $where AND status = 'Collected'")->fetch_assoc()['revenue'] ?? 0;

$totalOrders = $conn->query("SELECT COUNT(*) 
AS total FROM orders WHERE $where")->fetch_assoc()['total'] ?? 0;

$completedOrders = $conn->query("SELECT COUNT(*) 
AS total FROM orders WHERE $where AND status = 'Collected'")->fetch_assoc()['total'] ?? 0;

$cancelledOrders = $conn->query("SELECT COUNT(*) 
AS total FROM orders WHERE $where AND status = 'Cancelled'")->fetch_assoc()['total'] ?? 0;

$averageOrder = $conn->query("SELECT AVG(total) 
AS avg FROM orders WHERE $where AND status = 'Collected'")->fetch_assoc()['avg'] ?? 0;

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
