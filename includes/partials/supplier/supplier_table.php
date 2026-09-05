<div class="table-container">
    <table class="orders-table">
        <thead>
        <tr>
            <th>Company</th>
            <th>Contact Person</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>

        <?php while ($row = $result->fetch_assoc()) { ?>

            <?php include 'includes/partials/supplier/supplier_row.php'; ?>

            <?php } ?>

        </tbody>
    </table>
</div>