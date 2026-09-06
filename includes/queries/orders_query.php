<?php

if (!isset($limit)) {
    $limit = 15;
}

if (!isset($page)) {
    $page = max(1, (int) ($_GET['page'] ?? 1));
}

$offset = ($page - 1) * $limit;

$where = '';
$order = '';

$orderRaw = $_GET['order'] ?? '';

if (!is_string($orderRaw)) {
    $orderRaw = '';
}

$order = trim($orderRaw);

if ($order !== '') {
    $where = 'WHERE order_number = ? OR customer_name LIKE ?';
}

if ($where === '') {
    $result = $conn->query(
        "SELECT * FROM orders 
        ORDER BY created_at 
        DESC LIMIT $limit OFFSET $offset"
    );

    $totalResult = $conn->query(
        'SELECT COUNT(*) AS total 
        FROM orders'
    );
} else {
    $stmt = $conn->prepare(
        "SELECT * FROM orders $where 
        ORDER BY created_at 
        DESC LIMIT $limit OFFSET $offset"
    );

    if ($stmt === false) {
        error_log('SwiftOrder orders query prepare failed: '.$conn->error);
        $result = false;
    } else {
        $param = $order;
        $paramLike = "%{$order}%";

        if (!$stmt->bind_param('ss', $param, $paramLike)
            ) {
            error_log('SwiftOrder orders query bind failed: '.$stmt->error);
            $stmt->close();
            $result = false;
        } elseif (!$stmt->execute()) {
            error_log('SwiftOrder orders query execute failed: '.$stmt->error);

            $stmt->close();
            $result = false;
        } else {
            $result = $stmt->get_result();
            $stmt->close();
        }
    }

    $countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM orders $where");

    if ($countStmt === false) {
        error_log('SwiftOrder orders count prepare failed: '.$conn->error);
        $totalResult = false;
    } else {
        $countParam = $order;
        $countParamLike = "%{$order}%";

        if (!$countStmt->bind_param('ss', $countParam, $countParamLike)) {
            error_log('SwiftOrder orders count bind failed: '.$countStmt->error);
            $countStmt->close();
            $totalResult = false;
        } elseif (!$countStmt->execute()) {
            error_log('SwiftOrder orders count execute failed: '.$countStmt->error);
            $countStmt->close();
            $totalResult = false;
        } else {
            $totalResult = $countStmt->get_result();
            $countStmt->close();
        }
    }
}

if ($totalResult === false) {
    $totalRows = 0;
} else {
    $totalRow = $totalResult->fetch_assoc();

    if (!is_array($totalRow) || !isset($totalRow['total'])) {
        error_log('SwiftOrder orders query count result was invalid.');

        $totalRows = 0;
    } else {
        $totalRows = max(0, (int) $totalRow['total']);
    }
}

$totalPages = (int) ceil($totalRows / $limit);
