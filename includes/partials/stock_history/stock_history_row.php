
        <tr>
            <td><?php echo date('d M Y H:i', strtotime($row['created_at'])); ?></td>
            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
            <td>
                <?php if ($row['adjustment_type'] === 'Increase') { ?>
                    <span style="color:#2a9d5c; font-weight:bold;">
                        +<?php echo (int) $row['quantity']; ?>
                    </span>
                <?php } else { ?>
                    <span style="color:#d84554; font-weight:bold;">
                        -<?php echo (int) $row['quantity']; ?>
                    </span>
                <?php } ?>
            </td>
            <td><?php echo (int) $row['available_stock']; ?></td>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td><?php echo htmlspecialchars($row['reason']); ?></td>
            <td><?php echo htmlspecialchars($row['notes']); ?></td>
        </tr>