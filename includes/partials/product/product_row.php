
        <tr>
            <td>
                <?php $image = !empty($row['image']) ? $row['image'] : 'no-image.png'; ?>

                <img src="./assets/images/products/<?php echo $image; ?>"
                    alt="Product Image" class="product-thumb">
            </td>

            <td><?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($row['category'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td>R <?php echo number_format($row['price'], 2); ?></td>
            <td><?php echo htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
            <td>
                <?php

                if ($row['stock'] == 0) {
                    echo '🔴 Out of Stock';
                } elseif ($row['stock'] <= 10) {
                    echo '🟠 Low Stock ('.(int) $row['stock'].')';
                } else {
                    echo '🟢 '.(int) $row['stock'].' in Stock';
                }
                ?>
            </td>
            <td>
                <?php include __DIR__.'/product_actions.php'; ?>
            </td>
        </tr>
