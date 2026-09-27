<?php if ($dashboardContext['showStarterPanel']) { ?>
<section class="dashboard-card dashboard-start-panel" aria-labelledby="dashboard-start-title">
    <div>
        <p class="dashboard-start-kicker">LET'S GET STARTED</p>
        <h2 id="dashboard-start-title">Set up the essentials first</h2>
        <p>
            Zazu keeps a new business lean. Add the information you need now,
            then the dashboard will grow with your activity.
        </p>
    </div>

    <div class="dashboard-start-actions">
        <?php if ($dashboardContext['isAdmin']) { ?>
            <a href="business_settings.php" class="action-btn">
                Business details
            </a>
        <?php } ?>

        <?php if ($dashboardContext['canManageProducts']) { ?>
            <a href="add_product.php" class="action-btn">
                Add your first product
            </a>
        <?php } ?>

        <a href="orders.php" class="action-btn edit-btn">
            View orders
        </a>
    </div>
</section>
<?php } ?>