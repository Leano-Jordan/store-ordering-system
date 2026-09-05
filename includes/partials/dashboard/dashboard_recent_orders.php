<?php
/** @var mysqli_result $recentOrders */
?>

<h2>Recent Orders</h2>

    <table class="dashboard-table data-table data-table--compact">
    <thead>
        <tr>
            <th>Order #</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Status</th>
            <th>Time</th>
        </tr>
    </thead>

    <tbody id="orders-body">

        <?php while ($order = $recentOrders->fetch_assoc()) { ?>

        <?php include 'dashboard_recent_orders_row.php'; ?>

        <?php } ?>
    </tbody>
    </table>