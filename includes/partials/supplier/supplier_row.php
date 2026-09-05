
<?php while ($row =
        $result->fetch_assoc()
    ) { ?>

        <tr>
            
            <td><?php echo htmlspecialchars($row['company_name'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($row['contact_person'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($row['phone']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td>
                <?php

                if ($row['status'] === 'Active') {
                    echo '<span class="status success">Active</span>';
                } else {
                    echo '<span class="status danger">Inactive</span>';
                }
                ?>
            </td>

            <td><?php include __DIR__.'/supplier_actions.php'; ?></td>
        </tr>
        
    <?php } ?>