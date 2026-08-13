<h2>Top Selling Products</h2>

<table class="orders-table reports-table">

    <thead>
        <tr>
            <th>Product</th>
            <th>Quantity Sold</th>
        </tr>
    </thead>

    <tbody>
        <?php
        while ($product = $topProducts->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo (int) $product['quantity_sold']; ?></td>
            </tr>
        <?php } ?>

    </tbody>
</table>