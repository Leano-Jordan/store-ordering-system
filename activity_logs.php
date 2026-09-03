<?php
require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once 'includes/db.php';

$searchRaw = $_GET['search'] ?? '';

if (!is_string($searchRaw)) {
    $searchRaw = '';
}

$search = trim($searchRaw);
$range = $_GET['range'] ?? '7';

$limit = 50;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

if ($range === '30') {
    $where = 'activity_logs.created_at 
    >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)';
} elseif ($range === 'month') {
    $where = 'YEAR(activity_logs.created_at) = 
    YEAR(CURDATE()) AND MONTH(activity_logs.created_at) = MONTH(CURDATE())';
} elseif ($range === 'year') {
    $where = 'YEAR(activity_logs.created_at) = 
    YEAR(CURDATE())';
} else {
    $where = 'activity_logs.created_at >= 
    DATE_SUB(CURDATE(), INTERVAL 7 DAY)';
}

if ($search !== '') {
    $term = '%'.$search.'%';

    $stmt = $conn->prepare(
        "SELECT 
            activity_logs.action, 
            activity_logs.created_at,
            users.username, users.role 
        FROM activity_logs 
        JOIN users 
            ON activity_logs.user_id = users.id 
        WHERE ($where) AND (users.username LIKE ?
            OR activity_logs.action LIKE ?)
        ORDER BY activity_logs.created_at DESC LIMIT $limit OFFSET $offset"
    );

    if (!$stmt->bind_param('ss', $term, $term)) {
        error_log('activity_logs.php: Failed to bind search parameters: '.$stmt->error);
        $stmt->close();
        exit('Unable to load activity logs.');
    }

    if (!$stmt->execute()) {
        error_log('activity_logs.php: Failed to execute search parameters: '.$stmt->error);
        $stmt->close();
        exit('Unable to load activity logs.');
    }

    $result = $stmt->get_result();

    if (!$result) {
        error_log('activity_logs.php: Failed to retrieve search parameters: '.$stmt->error);
        $stmt->close();
        exit('Unable to load activity logs.');
    }
} else {
    $result = $conn->query(
        "SELECT activity_logs.action, 
        activity_logs.created_at,
        users.username,
        users.role FROM activity_logs 
        JOIN users 
        ON activity_logs.user_id = users.id WHERE $where
        ORDER BY activity_logs.created_at 
        DESC LIMIT $limit OFFSET $offset"
    );
}

if (!$result) {
    error_log('SwiftOrder activity logs query failed. '.$conn->error);

    $_SESSION['error'] =
        'Unable to load activity logs. Please try again.';

    header('Location: dashboard.php');
    exit();
}

include 'includes/header.php';
?>
<?php require 'includes/shared/flash_message.php'; ?>
<?php require 'includes/partials/activity_logs/activity_log_toolbar.php'; ?>
<?php require 'includes/partials/activity_logs/activity_log_table.php'; ?>
<?php require 'includes/partials/activity_logs/activity_log_pagination.php'; ?>