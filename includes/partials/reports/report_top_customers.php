<h2>Top Customers</h2>
<div class="table-container">
    <table class="orders-table reports-table data-table">

        <thead>
            <tr>
                <th>Customer</th>
                <th>Orders</th>
                <th>Total Spent</th>
            </tr>
        </thead>

        <tbody>
            <?php
            while ($customer = $topCustomers->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($customer['customer_name']); ?></td>
                    <td><?php echo (int) $customer['orders']; ?></td>
                    <td>R <?php echo number_format($customer['spent'], 2); ?></td>
                </tr>
            <?php } ?>

        </tbody>
    </table>
</div>