<?php

if (!isset($limit)) {
    $limit = 15;
}

if (!isset($page)) {
    $page = max(1, (int) ($_GET['page'] ?? 1));
}

$offset = ($page - 1) * $limit;

$where = '';

if (!empty($_GET['order'])) {
    $order = trim($_GET['order']);
    $where = 'WHERE order_number = ? OR customer_name LIKE ?';
}

if ($where === '') {
    $result = $conn->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
    $totalResult = $conn->query('SELECT COUNT(*) AS total FROM orders');
} else {
    $stmt = $conn->prepare("SELECT * FROM orders $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset");

    if ($stmt === false) {
        error_log('SwiftOrder orders query prepare failed: '.$conn->error);

        return false;
    } else {
        $param = trim($_GET['order']);
        $paramLike = "%{$param}%";
        $stmt->bind_param('ss', $param, $paramLike);

        if (!$stmt->execute()) {
            error_log('SwiftOrder orders query execute failed: '.$conn->error);

            return false;
        } else {
            $result = $stmt->get_result();
        }
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM orders $where");

    if ($countStmt === false) {
        error_log('SwiftOrder orders count prepare failed: '.$conn->error);

        $totalResult = false;
    } else {
        $countParam = trim($_GET['order']);
        $countParamLike = "%{$countParam}%";
        $countStmt->bind_param('ss', $countParam, $countParamLike);

        if (!$countStmt->execute()) {
            error_log('SwiftOrder orders count execute failed: '.$conn->error);

            $totalResult = false;
        } else {
            $totalResult = $countStmt->get_result();
        }
    }

    $countStmt->execute();
    $totalResult = $countStmt->get_result();
}

if ($totalResult === false) {
    $totalRows = 0;
} else {
    $totalRows = $totalResult->fetch_assoc()['total'];
}

$totalPages = ceil($totalRows / $limit);
