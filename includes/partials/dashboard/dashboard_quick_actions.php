<div class="quick-actions">
    <h2>Quick Actions</h2>

    <div class="action-grid">
        <a href="orders.php" class="action-card">
            🛒<span>Orders</span>
        </a>

        <?php if ($dashboardContext['canManageProducts']) { ?>
            <a href="products.php" class="action-card">
                📦<span>Products</span>
            </a>

            <a href="add_product.php" class="action-card">
                ➕<span>Add Product</span>
            </a>
        <?php } ?>

        <a href="index.php" class="action-card">
            🏠<span>Customer Menu</span>
        </a>

        <?php if ($dashboardContext['isAdmin']) { ?>
            <a href="business_settings.php" class="action-card">
                ⚙️<span>Business Settings</span>
            </a>
        <?php } ?>
    </div>
</div>