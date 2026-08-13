<?php
require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once 'includes/db.php';

$limit = 10;

$page = max(1, (int) ($_GET['page'] ?? 1));

$offset = ($page - 1) * $limit;

$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $searchTerm = '%'.$search.'%';

    $stmt = $conn->prepare("SELECT id, 
    company_name, 
    contact_person, 
    phone, 
    email, 
    status FROM suppliers 
    WHERE company_name LIKE ? 
    OR contact_person LIKE ? 
    OR phone LIKE ? 
    OR email LIKE ? 
    ORDER BY company_name ASC 
    LIMIT $limit OFFSET $offset");

    if (!$stmt) {
        error_log('SwiftOrder suppliers query failed: '.$conn->error);
        $_SESSION['error'] = 'Unable to load suppliers. Please try again.';
    }

    $stmt->bind_param('ssss', $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $countStmt = $conn->prepare('SELECT COUNT(*) AS total FROM suppliers WHERE company_name LIKE ? 
    OR contact_person LIKE ? 
    OR phone LIKE ? 
    OR email LIKE ? ');

    if (!$countStmt) {
        exit($conn->error);
    }

    $countStmt->bind_param('ssss', $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $countStmt->execute();
    $totalResult = $countStmt->get_result();
} else {
    $result = $conn->query("SELECT id, company_name, contact_person, phone, email, status 
FROM suppliers 
ORDER BY company_name ASC 
LIMIT $limit OFFSET $offset");

    if (!$result) {
        exit($conn->error);
    }

    $totalResult = $conn->query('SELECT COUNT(*) AS total FROM suppliers');
}

$totalRows = $totalResult->fetch_assoc()['total'];

$totalPages = ceil($totalRows / $limit);

include 'includes/header.php';
?>

<div class="page-header">

<h2>Suppliers</h2>

<a href="add_supplier.php" class="action-btn">
    + Add Supplier
</a>
</div>

<?php require 'includes/shared/flash_message.php'; ?>

<?php include 'includes/partials/supplier/supplier_toolbar.php'; ?>

<?php include 'includes/partials/supplier/supplier_table.php'; ?>

<?php include 'includes/partials/supplier/supplier_pagination.php'; ?>



<?php include 'includes/footer.php'; ?>
