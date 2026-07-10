<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentPage = basename($_SERVER["PHP_SELF"]);
require_once "includes/permissions.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SwiftOrder POS</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <header class="top-nav">
        <h1>SwiftOrder POS</h1>

        <nav>

            <?php
            if (isset($_SESSION["role"]) && $_SESSION["role"] === ROLE_ADMIN) { ?>
                <a href="index.php" class="<?php echo $currentPage === 'index.php' ? 'active-nav' : ''; ?>">POS</a>
                <a href="dashboard.php" class="<?php echo $currentPage === 'dashboard.php' ? 'active-nav' : ''; ?>">Dashboard</a>
                <a href="orders.php" class="<?php echo $currentPage === 'orders.php' ? 'active-nav' : ''; ?>">Orders</a>
                <a href="products.php" class="<?php echo $currentPage === 'products.php' ? 'active-nav' : ''; ?>">Products</a>
                <a href="users.php" class="<?php echo $currentPage === 'users.php' ? 'active-nav' : ''; ?>">Staff</a>
                <a href="reports.php" class="<?php echo $currentPage === 'reports.php' ? 'active-nav' : ''; ?>">Reports</a>
                <a href="docs/CHANGELOG.md">About</a>
                <a href="logout.php" class="<?php echo $currentPage === 'logout.php' ? 'active-nav' : ''; ?>">Logout</a>
            <?php } elseif (isset($_SESSION["role"]) && $_SESSION["role"] === ROLE_MANAGER) { ?>
                <a href="index.php" class="<?php echo $currentPage === 'index.php' ? 'active-nav' : ''; ?>">POS</a>
                <a href="dashboard.php">Dashboard</a>
                <a href="orders.php">Orders</a>
                <a href="products.php">Products</a>
                <a href="reports.php">Reports</a>
                <a href="docs/CHANGELOG.md">About</a>
                <a href="logout.php">Logout</a>
            <?php }

            if (isset($_SESSION["role"]) && $_SESSION["role"] === ROLE_CASHIER) { ?>
                <a href="index.php" class="<?php echo $currentPage === 'index.php' ? 'active-nav' : ''; ?>">POS</a>
                <a href="dashboard.php">Dashboard</a>
                <a href="orders.php">Orders</a>
                <a href="docs/CHANGELOG.md">About</a>
                <a href="logout.php">Logout</a>
            <?php }

            if (isset($_SESSION["role"]) && $_SESSION["role"] === ROLE_KITCHEN) { ?>
                <a href="orders.php">Orders</a>
                <a href="docs/CHANGELOG.md">About</a>
                <a href="logout.php">Logout</a>

            <?php } ?>

        </nav>

        <?php
        if (isset($_SESSION["full_name"])) { ?>
            <div class="nav-user">
                <?php echo htmlspecialchars($_SESSION["full_name"]); ?>
                (<?php echo htmlspecialchars($_SESSION["role"]); ?>)
            </div>
        <?php } ?>

    </header>