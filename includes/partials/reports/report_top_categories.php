<h2>Best Selling Categories</h2>

<table class="orders-table reports-table">

    <thead>
        <tr>
            <th>Category</th>
            <th>Items Sold</th>
        </tr>
    </thead>

    <tbody>
        <?php
        while ($category = $topCategories->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($category['category']); ?></td>
                <td><?php echo (int) $category['quantity_sold']; ?></td>
            </tr>
        <?php } ?>

    </tbody>
</table>