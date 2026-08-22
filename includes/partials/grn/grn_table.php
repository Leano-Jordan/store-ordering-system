<?php

require_once __DIR__.'/../../auth.php';
require_once __DIR__.'/../../permissions.php';

require_once __DIR__.'/../../logger.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);

require_once __DIR__.'/../../db.php';
?>

<div class="table-container">
    
    <table class="grn-table">
    
        <thead>
            <tr>
                <th>GRN Number</th>
                <th>PO Number</th>
                <th>Supplier</th>
                <th>Total</th>
                <th>Received</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php if ($result && $result->num_rows > 0) { ?>

            <?php while ($row = $result->fetch_assoc()) { ?>

            <tr>
                <td>
                    <?php echo htmlspecialchars($row['grn_number'], ENT_QUOTES, 'UTF-8'); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['po_number'], ENT_QUOTES, 'UTF-8'); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['company_name'], ENT_QUOTES, 'UTF-8'); ?>
                </td>

                <td>R 
                    <?php echo number_format($row['total'], 2); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'); ?>
                </td>

                <td>
                    <?php echo date('d M Y H:i', strtotime($row['received_at'])); ?>
                </td>
                
                <td>
                    <a href="view_grn.php?id=<?php echo (int) $row['id']; ?>" class="action-btn">
                        View GRN
                    </a>
                </td>
            </tr>

            <?php } ?>

            <?php } else { ?>

            <tr>
                <td colspan="7">
                    No Goods Received Notes found.
                </td>
            </tr>

            <?php } ?>
            
        </tbody>

    </table>
</div>