<?php
/** @var mysqli_result $result */
?>
<div class="table-container">
    <table class="orders-table data-table">

    <thead>
        <tr>
            <th>Order#</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Status</th>
            <th>Payment</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody id="orders-body">

        <?php while ($row = $result->fetch_assoc()) { ?>

        <?php include __DIR__.'/order_row.php'; ?>

        <?php } ?>
    </tbody>
    </table>
</div>