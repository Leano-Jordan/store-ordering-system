<tr>

            <td>
                <a href="order_details.php?id=<?php echo (int) $order['id']; ?>" class="order-link">
                    <?php echo htmlspecialchars($order['order_number']); ?>
                </a>
            </td>

            <td>
                <?php echo htmlspecialchars($order['customer_name']); ?>
            </td>

            <td>R <?php echo number_format($order['total'], 2); ?>
            </td>

            <td>
                <span class="status <?php echo strtolower($order['status']); ?>">
                    <?php echo htmlspecialchars($order['status']); ?>
                </span>
            </td>

            <td>
                <?php echo date('H:i', strtotime($order['created_at'])); ?>
            </td>

        </tr>