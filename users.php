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

if (!$stmt) {
    error_log('users.php: Failed to prepare user list query: '.$conn->error);
    $_SESSION['error'] = 'Unable to load users. Please try again.';
    header('Location: users.php');
    exit();
}

if (!$stmt->bind_param('ii', $offset, $limit)) {
    error_log('users.php: Failed to bind user list parameters: '.$stmt->error);
    $stmt->close();
    $_SESSION['error'] = 'Unable to load users. Please try again.';
    header('Location: users.php');
    exit();
}

if (!$stmt->execute()) {
    error_log('users.php: Failed to execute user list query: '.$stmt->error);
    $stmt->close();
    $_SESSION['error'] = 'Unable to load users. Please try again.';
    header('Location: users.php');
    exit();
}

$result = $stmt->get_result();

if (!$result) {
    error_log('users.php: Failed to retrieve user list result: '.$stmt->error);
    $stmt->close();
    $_SESSION['error'] = 'Unable to load users. Please try again.';
    header('Location: users.php');
    exit();
}

$totalResult = $conn->query('SELECT COUNT(*) AS total FROM users');

if (!$totalResult) {
    error_log('users.php: Failed to count users: '.$conn->error);
    $stmt->close();
    $_SESSION['error'] = 'Unable to load users. Please try again.';
    header('Location: users.php');
    exit();
}

$totalRow = $totalResult->fetch_assoc();

if (!is_array($totalRow) || !isset($totalRow['total'])) {
    error_log('users.php: User count query returned an invalid result.');
    $stmt->close();
    $_SESSION['error'] = 'Unable to load users. Please try again.';
    header('Location: users.php');
    exit();
}

$totalRows = (int) $totalRow['total'];
$totalPages = (int) ceil($totalRows / $limit);

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

<?php include 'includes/partials/users/user_pagination.php'; ?>

<?php include 'includes/footer.php'; ?>