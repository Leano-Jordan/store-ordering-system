<div class="dashboard-grid">

    <div class="dashboard-card">
        <h3>Total Revenue</h3>
        <p>R<?php echo number_format($totalRevenue, 2); ?></p>
    </div>

    <div class="dashboard-card">
        <h3>Total Orders</h3>
        <p><?php echo $totalOrders; ?></p>
    </div>

    <div class="dashboard-card">
        <h3>Completed</h3>
        <p><?php echo $completedOrders; ?></p>
    </div>

    <div class="dashboard-card">
        <h3>Cancelled</h3>
        <p><?php echo $cancelledOrders; ?></p>
    </div>

    <div class="dashboard-card">
        <h3>Average Orders</h3>
        <p>R<?php echo number_format($averageOrder, 2); ?></p>
    </div>

</div>