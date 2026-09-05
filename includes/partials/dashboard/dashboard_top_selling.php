<div class="dashboard-card">

    <h2>
        Top Selling Products
    </h2>

        <table class="dashboard-table data-table data-table--compact">

        <tr>
            <th>Product</th>
            <th>Sold</th>
        </tr>

        <?php while ($product = $topProducts->fetch_assoc()) { ?>

            <tr>
                <td>
                    <?php echo htmlspecialchars($product['name']); ?>
                </td>

                <td>
                    <?php echo (int) $product['totalSold']; ?>
                </td>
            </tr>

        <?php } ?>

    </table>

</div>