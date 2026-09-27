<?php if ($dashboardContext['showSalesChart']) { ?>
<div class="dashboard-chart">
    <div class="chart-header">
        <h2>Sales Overview</h2>

        <div class="chart-filter">
            <a href="?range=7" class="action-btn">7 Days</a>
            <a href="?range=30" class="action-btn">30 Days</a>
            <a href="?range=month" class="action-btn">This Month</a>
        </div>
    </div>

    <canvas id="salesChart"></canvas>
</div>
<?php } ?>