<?php

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once 'includes/db.php';

$range = $_GET['range'] ?? '7';

if (!is_string($range)) {
    $range = '7';
}

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

$result = $conn->query("SELECT DATE(created_at) AS sale_date, 
COUNT(*) AS orders, SUM(total) AS revenue FROM orders WHERE $where 
AND status = 'Collected' GROUP BY DATE(created_at) ORDER BY sale_date DESC");

if (!$result) {
    error_log('export_report_csv.php: Failed to generate report query: '.$conn->error);

    http_response_code(500);

    exit('Unable to export report.');
}

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename=sales_report.csv');

$output = fopen('php://output', 'w');

if ($output === false) {
    error_log('export_report_csv.php: Failed to open CSV output stream.');

    $result->free();

    http_response_code(500);

    exit('Unable to export report.');
}

if (fputcsv($output, ['Date', 'Orders', 'Revenue']) === false) {
    error_log('export_report_csv.php: Failed to write CSV header.');

    fclose($output);
    $result->free();

    http_response_code(500);

    exit('Unable to export report.');
}

while ($row = $result->fetch_assoc()) {
    if (fputcsv($output, [
        date('d F Y', strtotime($row['sale_date'])), $row['orders'], number_format($row['revenue'], 2), ]) === false) {
        error_log('export_report_csv.php: Failed to write CSV data row.');

        fclose($output);
        $result->free();

        http_response_code(500);

        exit('Unable to export report.');
    }
}

$result->free();

if (!fclose($output)) {
    error_log('export_report_csv.php: Failed to close CSV output stream.');

    http_response_code(500);

    exit('Unable to export report.');
}

exit();
