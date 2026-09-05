<tr>
    <td><?php echo htmlspecialchars($row['po_number'], ENT_QUOTES, 'UTF-8'); ?></td>
    <td><?php echo htmlspecialchars($row['company_name'], ENT_QUOTES, 'UTF-8'); ?></td>
    <td>R <?php echo number_format($row['total'], 2); ?></td>

<td>
<?php

switch ($row['status']) {
    case 'Draft':
        echo '<span class="status draft">Draft</span>';
        break;
    case 'Pending':
        echo '<span class="status pending">Pending</span>';
        break;
    case 'Received':
        echo '<span class="status success">Received</span>';
        break;
    case 'Cancelled':
        echo '<span class="status danger">Cancelled</span>';
        break;
    default:
        echo '<span class="status info">Unknown</span>';
        break;
} ?>
</td>
<td>

<?php echo date('Y-m-d', strtotime($row['created_at'])); ?>

    </td>

    <?php include __DIR__.'/purchase_order_actions.php'; ?>
    
    </tr>