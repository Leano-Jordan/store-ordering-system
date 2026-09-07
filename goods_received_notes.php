<?php
require_once 'includes/auth.php';
require_once 'includes/permissions.php';
require_once 'includes/logger.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);

require_once 'includes/db.php';

$limit = 10;

$page = max(1, (int) ($_GET['page'] ?? 1));

$offset = ($page - 1) * $limit;

$searchRaw = $_GET['search'] ?? '';

if (!is_string($searchRaw)) {
    $searchRaw = '';
}

$search = trim($searchRaw);

if ($search !== '') {
    $searchTerm = "%{$search}%";

    $stmt = $conn->prepare(
        'SELECT grn.*,
            Po.po_number, s.company_name, u.username
        FROM goods_received_notes grn
        INNER JOIN purchase_orders po
            ON grn.purchase_order_id = po.id
        INNER JOIN suppliers s
            ON grn.supplier_id = s.id
        INNER JOIN users u
            ON grn.received_by = u.id
        WHERE grn.grn_number LIKE ?
            OR po.po_number LIKE ?
            OR s.company_name LIKE ?
        ORDER BY grn.received_at DESC
        LIMIT ? OFFSET ?
    '
    );

    if (!$stmt) {
        error_log('goods_received_notes.php: Failed to prepare search query: '.$conn->error);
        exit('Unable to load goods received notes.');
    }

    if (!$stmt->bind_param(
        'sssii',
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $limit,
        $offset
    )) {
        error_log('goods_received_notes.php: Failed to bind search parameters: '.$stmt->error);

        $stmt->close();

        exit('Unable to load goods received notes.');
    }

    if (!$stmt->execute()) {
        error_log('goods_received_notes.php: Failed to execute search query: '.$stmt->error);
        exit('Unable to load goods received notes.');
    }

    $result = $stmt->get_result();

    if (!$result) {
        error_log('goods_received_notes.php: Failed to get search result: '.$stmt->error);
        exit('Unable to load goods received notes.');
    }

    $countStmt = $conn->prepare('SELECT COUNT(*) total
        FROM goods_received_notes grn
        INNER JOIN purchase_orders po
            ON grn.purchase_order_id = po.id
        INNER JOIN suppliers s
            ON grn.supplier_id = s.id
        WHERE grn.grn_number LIKE ?
            OR po.po_number LIKE ?
            OR s.company_name LIKE ?
    ');

    if (!$countStmt) {
        error_log('goods_received_notes.php: Failed to prepare count query: '.$conn->error);
        exit('Unable to load goods received notes.');
    }

    if (!$countStmt->bind_param(
        'sss',
        $searchTerm,
        $searchTerm,
        $searchTerm
    )) {
        error_log('goods_received_notes.php: Failed to bind count parameters: '.$countStmt->error);

        $countStmt->close();
        $stmt->close();

        exit('Unable to load goods received notes.');
    }

    if (!$countStmt->execute()) {
        error_log('goods_received_notes.php: Failed to execute count query: '.$countStmt->error);
        exit('Unable to load goods received notes.');
    }

    $countResult = $countStmt->get_result();

    if (!$countResult) {
        error_log('goods_received_notes.php: Failed to get count result: '.$countStmt->error);
        exit('Unable to load goods received notes.');
    }

    $countRow = $countResult->fetch_assoc();
    $countResult->free();
    $countStmt->close();

    $totalRows = $countRow['total'] ?? 0;
} else {
    $result = $conn->query("SELECT
            grn.*,
            Po.po_number,
            s.company_name,
            u.username
        FROM goods_received_notes grn
        INNER JOIN purchase_orders po
            ON grn.purchase_order_id = po.id
        INNER JOIN suppliers s
            ON grn.supplier_id = s.id
        INNER JOIN users u
            ON grn.received_by = u.id
        ORDER BY grn.received_at DESC
        LIMIT $limit OFFSET $offset
    ");

    if (!$result) {
        error_log('goods_received_notes.php: Failed to load GRN list: '.$conn->error);
        exit('Unable to load goods received notes.');
    }

    $countResult = $conn->query('SELECT COUNT(*) total FROM goods_received_notes');

    if (!$countResult) {
        error_log('goods_received_notes.php: Failed to count GRN records: '.$conn->error);
        exit('Unable to load goods received notes.');
    }

    $totalRows = $countResult->fetch_assoc()['total'] ?? 0;
}

$totalPages = ceil($totalRows / $limit);

//      SUMMARY CARDS
$totalGrnsResult = $conn->query('SELECT COUNT(*) total FROM goods_received_notes');

if (!$totalGrnsResult) {
    error_log('goods_received_notes.php: Failed to count total GRNs: '.$conn->error);
    exit('Unable to load goods received notes.');
}

$totalGrns = $totalGrnsResult->fetch_assoc()['total'] ?? 0;
$totalGrnsResult->free();

$totalValueResult = $conn->query('SELECT COALESCE(SUM(total),0) total FROM goods_received_notes');

if (!$totalValueResult) {
    error_log('goods_received_notes.php: Failed to calculate total value of GRNs: '.$conn->error);
    exit('Unable to load goods received notes.');
}

$totalValue = $totalValueResult->fetch_assoc()['total'] ?? 0;
$totalValueResult->free();

$todayResult = $conn->query('SELECT COUNT(*) total FROM goods_received_notes WHERE DATE(received_at) = CURDATE()');

if (!$todayResult) {
    error_log('goods_received_notes.php: Failed to count today\'s GRNs: '.$conn->error);
    exit('Unable to load goods received notes.');
}

$todayGrns = $todayResult->fetch_assoc()['total'] ?? 0;

include 'includes/header.php';
?>

<div class="page-header">

    <h2>Goods Received Notes</h2>

    <a href="purchase_orders.php" class="action-btn">
        ← Purchase Orders
    </a>

</div>

<?php require 'includes/shared/flash_message.php'; ?>

<?php include 'includes/partials/grn/grn_toolbar.php'; ?>

<?php include 'includes/partials/grn/grn_summary_cards.php'; ?>

<?php include 'includes/partials/grn/grn_table.php'; ?>

<?php include 'includes/partials/grn/grn_pagination.php'; ?>

<?php include 'includes/footer.php'; ?>