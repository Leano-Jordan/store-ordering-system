<?php
require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole(
    [
        ROLE_ADMIN,
        ROLE_MANAGER,
        ROLE_CASHIER,
        ROLE_KITCHEN,
    ]
);
require_once 'includes/db.php';
require_once 'includes/partials/order/order_actions.php';
$loadScript = true;
require_once 'includes/queries/orders_query.php';

if ($result === false) {
    $_SESSION['error'] = 'Unable to load orders. Please try again';
    header('Location: dashboard.php');
    exit();
}

include 'includes/header.php';
?>

<?php require 'includes/shared/flash_message.php'; ?>

<?php include 'includes/partials/order/order_toolbar.php'; ?>
<?php include 'includes/partials/order/order_table.php'; ?>
<?php include 'includes/partials/order/order_pagination.php'; ?>



<script src="assets/js/orders.js"></script>

<?php include 'includes/footer.php'; ?>