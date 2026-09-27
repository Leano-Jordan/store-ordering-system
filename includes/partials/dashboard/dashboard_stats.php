<div class="dashboard-grid">

    <?php if ($dashboardContext['showProducts']) { ?>
        <div class="dashboard-card">
            <h3>Total Products</h3>
            <p>
                <a href="products.php" class="dashboard-link">
                    <?php echo $totalProducts; ?>
                </a>
            </p>
        </div>
    <?php } ?>

    <?php if ($dashboardContext['showOrdersToday']) { ?>
        <div class="dashboard-card">
            <h3>Orders Today</h3>
            <p>
                <a href="orders.php" class="dashboard-link">
                    <?php echo $todayOrders; ?>
                </a>
            </p>
        </div>
    <?php } ?>

    <?php if ($dashboardContext['showPendingOrders']) { ?>
        <div class="dashboard-card">
            <h3>Pending Orders</h3>
            <p>
                <a href="orders.php?status=Pending" class="dashboard-link">
                    <?php echo $pendingOrders; ?>
                </a>
            </p>
        </div>
    <?php } ?>

    <?php if ($dashboardContext['showRevenueMetrics']) { ?>
        <div class="dashboard-card">
            <h3>Today's Revenue</h3>
            <p>R<?php echo number_format($todayRevenue, 2); ?></p>
        </div>
    <?php } ?>

    <?php if ($dashboardContext['showAverageOrder']) { ?>
        <div class="dashboard-card">
            <h3>Average Order</h3>
            <p>R<?php echo number_format($averageOrder, 2); ?></p>
        </div>
    <?php } ?>

    <?php if ($dashboardContext['showMonthRevenueMetrics']) { ?>
        <div class="dashboard-card">
            <h3>This Month's Revenue</h3>
            <p>R<?php echo number_format($monthRevenue, 2); ?></p>
        </div>
    <?php } ?>

    <?php if ($dashboardContext['showLowStockAlert']) { ?>
        <div class="dashboard-card">
            <h3>⚠ Low Stock</h3>
            <p>
                <a href="products.php?stock=low" class="dashboard-link">
                    <?php echo $lowStock; ?>
                </a>
            </p>
        </div>
    <?php } ?>

    <?php if ($dashboardContext['showOutOfStockAlert']) { ?>
        <div class="dashboard-card">
            <h3>🔴 Out of Stock</h3>
            <p>
                <a href="products.php?stock=out" class="dashboard-link">
                    <?php echo $outOfStock; ?>
                </a>
            </p>
        </div>
    <?php } ?>

    <?php if ($dashboardContext['showInventoryValue']) { ?>
        <div class="dashboard-card">
            <h3>📦 Inventory Value</h3>
            <p>R<?php echo number_format($inventoryValue, 2); ?></p>
        </div>
    <?php } ?>

    <?php if ($dashboardContext['showPendingPOMetric']) { ?>
        <div class="dashboard-card">
            <h3>🛒 Pending Purchase Orders</h3>
            <p>
                <a href="purchase_orders.php" class="dashboard-link">
                    <?php echo $pendingPOs; ?>
                </a>
            </p>
        </div>
    <?php } ?>

    <?php if ($dashboardContext['showSupplierMetric']) { ?>
        <div class="dashboard-card">
            <h3>🏭 Active Suppliers</h3>
            <p>
                <a href="suppliers.php" class="dashboard-link">
                    <?php echo $totalSuppliers; ?>
                </a>
            </p>
        </div>
    <?php } ?>

</div>
