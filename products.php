<?php
require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once 'includes/db.php';
require_once 'includes/helpers.php';
require_once 'includes/queries/products_query.php';

$limit = 10;

$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;
$stockFilter = $_GET['stock'] ?? '';

$searchRaw = $_GET['search'] ?? '';

if (!is_string($searchRaw)) {
    $searchRaw = '';
}

$search = trim($searchRaw);

if ($stockFilter === 'low') {
    $sql = "SELECT * FROM products 
    WHERE status = 'Active' 
    AND stock > 0 
    AND stock <= 10 
    ORDER BY category ASC, stock DESC, name ASC 
    LIMIT $limit OFFSET $offset";

    $totalResult = $conn->query("SELECT COUNT(*) AS total 
    FROM products 
    WHERE status = 'Active' 
    AND stock > 0 
    AND stock <= 10");
} elseif ($stockFilter === 'out') {
    $sql = "SELECT * FROM products 
    WHERE status = 'Active' 
    AND stock = 0 
    ORDER BY category, name 
    LIMIT $limit OFFSET $offset";

    $totalResult = $conn->query(
        "SELECT COUNT(*) AS total 
    FROM products 
    WHERE status = 'Active' 
    AND stock = 0"
    );
} else {
    if ($search !== '') {
        $totalResult = countSearchProducts($conn, $search);
        $result = searchProducts($conn, $search, $limit, $offset);

        if (!$totalResult) {
            error_log('products.php: Failed to receive filtered product count.');

            exit('Unable to load product pagination.');
        }

        if (!$result) {
            error_log('products.php: Failed to load filtered products.');

            exit('Unable to load products.');
        }

        $countRow = $totalResult->fetch_assoc();

        if (!$countRow) {
            error_log('products.php: Failed to read filtered product count.');

            exit('Unable to load product pagination.');
        }

        $totalRows = (int) ($countRow['total'] ?? 0);
        $totalProducts = ceil($totalRows / $limit);
        $sql = null;
    } else {
        $result = getProducts($conn, $limit, $offset);

        if (!$result) {
            error_log('products.php: Failed to load products.');

            exit('Unable to load products.');
        }

        $totalResult = $conn->query('SELECT COUNT(*) AS total FROM products');
        $sql = null;
    }
}

if (isset($sql) && $sql !== null) {
    $result = $conn->query($sql);

    if (!$result) {
        error_log($conn->error);
        exit('Unable to load products');
    }
}

if (!isset($totalRows)) {
    if (!$totalResult) {
        error_log('products.php: Failed to receive product count: '.$conn->error);

        exit('Unable to load product pagination.');
    }

    $countRow = $totalResult->fetch_assoc();

    if (!$countRow) {
        error_log('products.php: Failed to read product count.');

        exit('Unable to load product pagination.');
    }

    $totalRows = (int) ($countRow['total'] ?? 0);
}

$totalPages = max(1, (int) ceil($totalRows / $limit));

include 'includes/header.php';
?>

<div class="page-header">
    <h2>Products</h2>
    <a href="add_product.php" class="action-btn">+ Add Product</a>
</div>

<?php require 'includes/shared/flash_message.php'; ?>

<div class="inventory-toolbar">
<?php include 'includes/partials/product/product_toolbar.php'; ?>
</div>

<?php include 'includes/partials/product/product_table.php'; ?>

<?php include 'includes/partials/product/product_pagination.php'; ?>

<?php include 'includes/footer.php'; ?>