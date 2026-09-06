<?php
require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once 'includes/db.php';

$limit = 20;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$searchRaw = $_GET['search'] ?? '';

if (!is_string($searchRaw)) {
    $searchRaw = '';
}

$search = trim($searchRaw);
$filter = $_GET['filter'] ?? '';

$whereConditions = [];
$bindTypes = '';
$bindValues = [];

$todayStart = (new DateTimeImmutable('today'))
->format('Y-m-d H:i:s');

$tomorrowStart = (new DateTimeImmutable('tomorrow'))
->format('Y-m-d H:i:s');

if ($search !== '') {
    $searchParam = '%'.$search.'%';

    $whereConditions[] =
    '(p.name LIKE ? OR u.username 
    LIKE ? OR sa.reason LIKE ?)';

    $bindTypes .= 'sss';

    $bindValues[] = $searchParam;
    $bindValues[] = $searchParam;
    $bindValues[] = $searchParam;
}

if ($filter === 'increase') {
    $whereConditions[] = "sa.adjustment_type = 'Increase'";
} elseif ($filter === 'decrease') {
    $whereConditions[] = "sa.adjustment_type = 'Decrease'";
} elseif ($filter === 'po') {
    $whereConditions[] = "sa.reason = 'Purchase Order Receipt'";
} elseif ($filter === 'order') {
    $whereConditions[] = "sa.reason = 'Order Collected'";
} elseif ($filter === 'manual') {
    $whereConditions[] = "sa.reason NOT IN ('Purchase Order Receipt', 'Order Collected')";
} elseif ($filter === 'today') {
    $whereConditions[] = 'sa.created_at >= ? AND sa.created_at < ?';

    $bindTypes .= 'ss';

    $bindValues[] = $todayStart;
    $bindValues[] = $tomorrowStart;
}

$where = !empty($whereConditions) ? 'WHERE '.implode(' AND ', $whereConditions) : '';

$stmt = $conn->prepare(
    "SELECT sa.created_at, p.name AS product_name,
    sa.adjustment_type, sa.quantity,
    sa.available_stock, sa.reason,
    sa.notes, u.username
    FROM stock_adjustments sa
    INNER JOIN products p ON sa.product_id = p.id
    INNER JOIN users u ON sa.user_id = u.id $where
    ORDER BY sa.created_at DESC LIMIT $limit OFFSET $offset"
);

if (!$stmt) {
    error_log('stock_history.php: Failed to prepare stock-history query: '.$conn->error);

    exit('Unable to load stock history.');
}

if ($bindTypes !== '') {
    if (!$stmt->bind_param($bindTypes, ...$bindValues)
        ) {
        error_log('stock_history.php: Failed to bind stock-history parameters: '.$stmt->error);

        $stmt->close();

        exit('Unable to load stock history.');
    }
}

if (!$stmt->execute()) {
    error_log('stock_history.php: Failed to load stock history: '.$stmt->error);
    $stmt->close();

    exit('Unable to load stock history.');
}

$result = $stmt->get_result();

if (!$result) {
    error_log('stock_history.php: Failed to retrieve stock-history result: '.$stmt->error);

    $stmt->close();

    exit('Unable to load stock history.');
}

$countStmt = $conn->prepare(
    "SELECT COUNT(*) AS total 
    FROM stock_adjustments sa 
    INNER JOIN products p ON sa.product_id = p.id 
    INNER JOIN users u ON sa.user_id = u.id $where
"
);

if (!$countStmt) {
    error_log('stock_history.php: Failed to prepare stock-history count query: '.$conn->error);
    $stmt->close();
    exit('Unable to load stock history.');
}

if ($bindTypes !== '') {
    if (!$countStmt->bind_param(
        $bindTypes,
        ...$bindValues
    )) {
        error_log(
            'stock_history.php: Failed to bind stock-history count parameters: '
            .$countStmt->error
        );

        $countStmt->close();
        $stmt->close();

        exit('Unable to load stock history.');
    }
}

if (!$countStmt->execute()) {
    error_log(
        'stock_history.php: Failed to count stock history: '
        .$countStmt->error
    );

    $countStmt->close();
    $stmt->close();

    exit('Unable to load stock history.');
}

$totalResult = $countStmt->get_result();

if (!$totalResult) {
    error_log(
        'stock_history.php: Failed to retrieve stock-history count result: '
        .$countStmt->error
    );

    $countStmt->close();
    $stmt->close();

    exit('Unable to load stock history.');
}

$countStmt->close();

include 'includes/header.php';
?>
<?php require 'includes/shared/flash_message.php'; ?>
<?php include 'includes/partials/stock_history/stock_history_toolbar.php'; ?>

<div class="inventory-toolbar">

    <form method="GET" class="search-form">
        <div class="form-group">
<div class="search-area">
        <input
            type="text"
            name="search"
            placeholder="🔍 Search Product, User or Reason. . ."
            value="<?php echo htmlspecialchars($search); ?>">

        <button type="submit" class="action-btn">Search</button>
        <a href="stock_history.php" class="action-btn clear-btn">Clear</a>
        </div>

    </div>
    </form>

    <?php include 'includes/partials/stock_history/stock_history_filters.php'; ?>
    
</div>

<?php include 'includes/partials/stock_history/stock_history_table.php'; ?>

<?php include 'includes/partials/stock_history/stock_history_pagination.php'; ?>



<?php include 'includes/footer.php'; ?>
