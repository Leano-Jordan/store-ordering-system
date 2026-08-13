<?php
require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once 'includes/db.php';

$limit = 20;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$search = trim($_GET['search'] ?? '');
$filter = $_GET['filter'] ?? '';

$whereConditions = [];

$searchParam = null;
if ($search !== '') {
    $searchParam = '%'.$search.'%';
    $whereConditions[] = '(p.name LIKE ? OR u.username LIKE ? OR sa.reason LIKE ?)';
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
    $whereConditions[] = 'DATE(sa.created_at)=CURDATE()';
}

$where = !empty($whereConditions) ? 'WHERE '.implode(' AND ', $whereConditions) : '';

$stmt = $conn->prepare("SELECT sa.created_at,
        p.name AS product_name,
        sa.adjustment_type,
        sa.quantity,
        sa.available_stock,
        sa.reason,
        sa.notes,
        u.username
    FROM stock_adjustments sa
    INNER JOIN products p ON sa.product_id = p.id
    INNER JOIN users u ON sa.user_id = u.id
    $where
    ORDER BY sa.created_at DESC
    LIMIT $limit OFFSET $offset
");

if ($searchParam !== null) {
    $stmt->bind_param('sss', $searchParam, $searchParam, $searchParam);
}

$stmt->execute();
$result = $stmt->get_result();

$countStmt = $conn->prepare("SELECT COUNT(*) AS total
    FROM stock_adjustments sa
    INNER JOIN products p ON sa.product_id = p.id
    INNER JOIN users u ON sa.user_id = u.id
    $where
");

if ($searchParam !== null) {
    $countStmt->bind_param('sss', $searchParam, $searchParam, $searchParam);
}
$countStmt->execute();
$totalResult = $countStmt->get_result();

$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

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
