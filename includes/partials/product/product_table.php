<table class="orders-table">
    <thead>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Description</th>
            <th>Status</th>
            <th>Stock</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
    
        <?php while ($row = $result->fetch_assoc()) { ?>

            <?php include __DIR__.'/product_row.php'; ?>

        <?php } ?>
    </tbody>
</table>