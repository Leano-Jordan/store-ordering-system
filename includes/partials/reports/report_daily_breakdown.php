<h2>Daily Breakdown</h2>

<div class="table-container">
    <table class="orders-table reports-table data-table">

        <thead>
            <tr>
                <th>Date</th>
                <th>Orders</th>
                <th>Revenue</th>
            </tr>
        </thead>

        <tbody>
            <?php while ($row = $dailySales->fetch_assoc()) { ?>

                <tr>
                    <td><?php echo date('d M Y', strtotime($row['sale_date'])); ?></td>
                    <td><?php echo (int) $row['order_count']; ?></td>
                    <td>R <?php echo number_format($row['daily_total'], 2); ?></td>
                </tr>
            <?php } ?>

        </tbody>
    </table>
</div>