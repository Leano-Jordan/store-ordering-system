<div class="table-container">
    <table class="orders-table">
        <thead>
            <tr>
                <th>PO Number</th>
                <th>Supplier</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>

        </thead>
        
        <tbody>

            <?php $activePOs = [];
            $cancelledPOs = [];

            while ($row = $result->fetch_assoc()) {
                if ($row['status'] === 'Cancelled') {
                    $cancelledPOs[] = $row;
                } else {
                    $activePOs[] = $row;
                }
            }

            foreach (array_merge(
                $activePOs,
                $cancelledPOs
            ) as $row) { ?>
            
            <?php include __DIR__.'/purchase_order_row.php'; ?>
            <?php } ?>

    </tbody>
    </table>
</div>