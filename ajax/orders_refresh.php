<?php

define('AJAX_REQUEST', true);

require_once '../includes/auth.php';
require_once '../includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER, ROLE_CASHIER, ROLE_KITCHEN]);
require_once '../includes/db.php';
require_once '../includes/csrf.php';
require_once '../includes/partials/order/order_actions.php';

$limit = 15;
$page = max(1, (int) ($_GET['page'] ?? 1));

require_once '../includes/queries/orders_query.php';

    while ($row = $result->fetch_assoc()) {
        include '../includes/partials/order/order_row.php';
    }
