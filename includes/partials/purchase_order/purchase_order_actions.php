<td>

<?php

$grnStmt = $conn->prepare('SELECT id FROM goods_received_notes WHERE purchase_order_id = ? LIMIT 1');

if (!$grnStmt) {
    error_log('purchase_order_actions.php: Failed to prepare GRN lookup: '.$conn->error);
    exit('Unable to load purchase order actions.');
}

$grnStmt->bind_param('i', $row['id']);

if (!$grnStmt->execute()) {
    error_log('purchase_order_actions.php: Failed to execute GRN for purchase order ID '.(int) $row['id'].': '.$grnStmt->error);
    $grnStmt->close();

    exit('Unable to load purchase order actions.');
}

$grn = $grnStmt->get_result()->fetch_assoc();
$grnStmt->close();

?>

<?php if ($row['status'] === 'Draft' || $row['status'] === 'Pending') { ?>
    <a href="add_purchase_order.php?id=<?php echo (int) $row['id']; ?>" class="action-btn edit-btn">
        Edit P/O
    </a>

    <?php if ($row['status'] === 'Pending') { ?>
        <form action="receive_purchase_order.php" method="POST" class="inline-form" style="display:inline;">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
            <button type="submit" class="action-btn ready-btn" onclick="return confirm('Mark this PO as Received and update stock');">
                ✔ Receive PO
            </button>
        </form>
    <?php } ?>

        <form action="cancel_purchase_order.php" method="POST" class="inline-form" style="display:inline;">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
            <button type="submit" class="action-btn delete-btn" onclick="return confirm('Cancel this Purchase Order?');">
                ❌ Cancel PO
            </button>
        </form>
    <?php } ?>

    <?php if ($grn) { ?>
        <a href="view_grn.php?id=
        <?php echo (int) $grn['id']; ?>" 
        class="action-btn">
            View GRN
        </a>
    <?php } ?>
</td>