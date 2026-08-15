<?php

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once 'includes/db.php';

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

$result = $conn->query("SELECT DATE(created_at) AS sale_date, 
COUNT(*) AS orders, SUM(total) AS revenue FROM orders WHERE $where 
AND status = 'Cancelled' GROUP BY DATE(created_at) ORDER BY sale_date DESC");

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename=sales_report.csv');

$output = fopen('php://output', 'w');

fputcsv($output, ['Date', 'Orders', 'Revenue']);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        date('d F Y', strtotime($row['sale_date'])),
        $row['orders'],
        number_format($row['revenue'], 2),
    ]);
}

fclose($output);
exit();
