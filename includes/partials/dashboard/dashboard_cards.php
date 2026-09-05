<?php
/** @var mysqli_result $lowStock /
* @var mysqli_result $lowStockProducts **/
?>

<?php if ($lowStock > 0) { ?>
    <div class="dashboard-card dashboard-card--warning">
        <h3>⚠ Low Stock Alert</h3>
        <table class="dashboard-table">
            <tr>
                <th>Product</th>
                <th>Stock</th>
            </tr>
        <?php while ($p = $lowStockProducts->fetch_assoc()) { ?>

                <tr>
                    <td><?php echo htmlspecialchars($p['name']); ?></td>
                    <td style="color: #e9a94a; font-weight: bold;">
                        <?php echo (int) $p['stock']; ?></td>
                </tr>
                <?php } ?>
        </table>
        <a href="purchase_orders.php" 
            class="action-btn" 
            style="margin-top: 10px; display:inline-block;">
            + Create Purchase Order
        </a>
    </div>
<?php } ?>