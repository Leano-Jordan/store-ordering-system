<table class="orders-table">
    <thead>
        <tr>
            <th>Date & Time</th>
            <th>Product</th>
            <th>Change</th>
            <th>Total Quantity</th>
            <th>By</th>
            <th>Reason</th>
            <th>Additional Notes</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) {?>

            <?php include __DIR__.'/stock_history_row.php'; ?>

            <?php } ?>
    </tbody>
</table>