<?php
require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN]);
$loadScript = true;
require_once 'includes/db.php';

$limit = 10;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$stmt = $conn->prepare(
    'SELECT * FROM users 
    ORDER BY created_at 
    DESC LIMIT ?, ?'
);
$stmt->bind_param('ii', $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

$totalResult = $conn->query('SELECT COUNT(*) AS total FROM users');
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

include 'includes/header.php';
?>

<?php require 'includes/shared/flash_message.php'; ?>

<?php if (isset($_GET['error']) && $_GET['error'] === 'self_deactivate') { ?>

    <div class="error-message">
        You cannot deactivate your own account while you are logged in.
    </div>
<?php } ?>

<?php if (isset($_GET['error']) && $_GET['error'] === 'last_admin') { ?>

    <div class="error-message">
        You cannot deactivate the last active Administrator.
    </div>
<?php } ?>

<script>
    if (window.history.replaceState) {
        window.history.replaceState({}, document.title, "users.php");
    }
</script>

<?php if (isset($_GET['success']) && $_GET['success'] === 'user_added') { ?>
    <div class="success-message">
        User added successfully.
    </div>
<?php } ?>

<?php if (isset($_GET['success']) && $_GET['success'] === 'user_updated') { ?>
    <div class="success-message">
        User updated successfully.
    </div>
<?php } ?>

<?php if (isset($_GET['success']) && $_GET['success'] === 'user_deactivated') { ?>
    <div class="success-message">
        User deactivated successfully.
    </div>
<?php } ?>

<?php if (isset($_GET['success']) && $_GET['success'] === 'user_activated') { ?>
    <div class="success-message">
        User reactivated successfully.
    </div>
<?php } ?>

<?php include 'includes/partials/users/user_toolbar.php'; ?>

<?php include 'includes/partials/users/user_table.php'; ?>

<?php while ($row = $result->fetch_assoc()) {
    include 'includes/partials/users/user_row.php';
} ?>

<?php include 'includes/partials/users/user_pagination.php'; ?>

<?php include 'includes/footer.php'; ?>