<?php

require_once __DIR__.'/session.php';

$currentPage = basename($_SERVER['PHP_SELF']);

require_once 'includes/permissions.php';
require_once 'includes/helpers.php';
require_once 'includes/csrf.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SwiftOrder POS</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
</head>

<body>

    <header class="top-nav">
        <h1>SwiftOrder</h1>
        
        <nav>
            

            <?php
            if (isset($_SESSION['role']) && $_SESSION['role'] === ROLE_ADMIN) { ?>
                <a href="index.php" class="<?php echo $currentPage === 'index.php' ? 'active-nav' : ''; ?>">Home</a>
                <a href="dashboard.php" class="<?php echo $currentPage === 'dashboard.php' ? 'active-nav' : ''; ?>">Dashboard</a>
                <a href="orders.php" class="<?php echo $currentPage === 'orders.php' ? 'active-nav' : ''; ?>">Orders</a>
                <a href="products.php" class="<?php echo $currentPage === 'products.php' ? 'active-nav' : ''; ?>">Products</a>
                <a href="stock_history.php" class="<?php echo $currentPage === 'stock_history.php' ? 'active-nav' : ''; ?>">Stock History</a>
                <a href="suppliers.php" class="<?php echo $currentPage === 'suppliers.php' ? 'active-nav' : ''; ?>">Suppliers</a>
                <a href="purchase_orders.php" class="<?php echo $currentPage === 'purchase_orders.php' ? 'active-nav' : ''; ?>">Purchase Orders</a>
                <a href="goods_received_notes.php" class="<?php echo $currentPage === 'goods_received_notes.php' ? 'active-nav' : ''; ?>">GRNs</a>
                <a href="users.php" class="<?php echo $currentPage === 'users.php' ? 'active-nav' : ''; ?>">Users</a>
                <a href="reports.php" class="<?php echo $currentPage === 'reports.php' ? 'active-nav' : ''; ?>">Reports</a>
                <a href="activity_logs.php" class="<?php echo $currentPage === 'activity_logs.php' ? 'active-nav' : ''; ?>">Activity Logs</a>
                <a href="about.php">About</a>
                <a href="logout.php" class="<?php echo $currentPage === 'logout.php' ? 'active-nav' : ''; ?>">Logout</a>

            <?php } elseif (isset($_SESSION['role']) && $_SESSION['role'] === ROLE_MANAGER) { ?>
                <a href="index.php" class="<?php echo $currentPage === 'index.php' ? 'active-nav' : ''; ?>">Home</a>
                <a href="dashboard.php" class="<?php echo $currentPage === 'dashboard.php' ? 'active-nav' : ''; ?>">Dashboard</a>
                <a href="orders.php" class="<?php echo $currentPage === 'orders.php' ? 'active-nav' : ''; ?>">Orders</a>
                <a href="products.php" class="<?php echo $currentPage === 'products.php' ? 'active-nav' : ''; ?>">Products</a>
                <a href="stock_history.php" class="<?php echo $currentPage === 'stock_history.php' ? 'active-nav' : ''; ?>">Stock History</a>
                <a href="suppliers.php" class="<?php echo $currentPage === 'suppliers.php' ? 'active-nav' : ''; ?>">Suppliers</a>
                <a href="purchase_orders.php" class="<?php echo $currentPage === 'purchase_orders.php' ? 'active-nav' : ''; ?>">Purchase Orders</a>
                <a href="goods_received_notes.php" class="<?php echo $currentPage === 'goods_received_notes.php' ? 'active-nav' : ''; ?>">GRNs</a>
                <a href="reports.php" class="<?php echo $currentPage === 'reports.php' ? 'active-nav' : ''; ?>">Reports</a>
                <a href="about.php">About</a>
                <a href="logout.php">Logout</a>
            <?php }

            if (isset($_SESSION['role']) && $_SESSION['role'] === ROLE_CASHIER) { ?>
                <a href="index.php" class="<?php echo $currentPage === 'index.php' ? 'active-nav' : ''; ?>">Home</a>
                <a href="dashboard.php" class="<?php echo $currentPage === 'dashboard.php' ? 'active-nav' : ''; ?>">Dashboard</a>
                <a href="orders.php" class="<?php echo $currentPage === 'orders.php' ? 'active-nav' : ''; ?>">Orders</a>
                <a href="about.php">About</a>
                <a href="logout.php">Logout</a>
            <?php }

            if (isset($_SESSION['role']) && $_SESSION['role'] === ROLE_KITCHEN) { ?>
                <a href="orders.php" class="<?php echo $currentPage === 'orders.php' ? 'active-nav' : ''; ?>">Orders</a>
                <a href="about.php">About</a>
                <a href="logout.php">Logout</a>

            <?php } ?>

        </nav>

        <?php

        if (isset($_SESSION['full_name'])) { ?>

    <div class="nav-user">

        <div class="user-details">

        <div id="system-clock" class="system-clock"></div>

                <div class="user-name">
                    <?php echo htmlspecialchars($_SESSION['full_name']); ?>
                </div>

                <div class="user-role">
                    (<?php echo htmlspecialchars($_SESSION['role']); ?>)
                </div>

            </div>

            <div class="user-profile">
                <?php $avatar = !empty($_SESSION['profile_image'])
                    ? 'assets/images/profiles/'.$_SESSION['profile_image']
                    : 'assets/images/profiles/default-avatar.png';
                ?>
                <img src="<?php echo htmlspecialchars($avatar); ?>" 
                    class="nav-avatar" 
                    alt="Profile">
            </div>
    </div>

<?php } ?>
        
</header>