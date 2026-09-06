<?php

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
require_once 'includes/logger.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);

require_once 'includes/db.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $conn->prepare(
    'SELECT grn.*, 
    po.po_number, 
    s.company_name, 
    s.contact_person, 
    s.phone, 
    s.email, 
    u.full_name FROM goods_received_notes grn 
        INNER JOIN purchase_orders po 
        ON grn.purchase_order_id = po.id 
        INNER JOIN suppliers s 
        ON grn.supplier_id = s.id 
        INNER JOIN users u 
        ON grn.received_by = u.id 
        WHERE grn.id = ?'
);

if (!$stmt) {
    error_log('view_grn.php: Failed to prepare GRN query: '.$conn->error);
    exit('Unable to load Goods Received Note.');
}

if (!$stmt->bind_param('i', $id)) {
    error_log('view_grn.php: Failed to bind GRN ID: '.$stmt->error);
    $stmt->close();
    exit('Unable to load Goods Received Note.');
}

if (!$stmt->execute()) {
    error_log('view_grn.php: Failed to execute GRN query: '.$stmt->error);
    exit('Unable to load Goods Received Note.');
}

$grnResult = $stmt->get_result();

if (!$grnResult) {
    error_log('view_grn.php: Failed to get GRN result: '.$stmt->error);
    exit('Unable to load Goods Received Note.');
}

$grn = $grnResult->fetch_assoc();

if (!$grn) {
    $_SESSION['error'] = 'Goods Received Note not found.';
    header('Location: goods_received_notes.php');
    exit();
}

$itemStmt = $conn->prepare('SELECT gni.*, 
    gni.quantity, 
    gni.cost_price, 
    gni.line_total, 
    p.name FROM goods_received_note_items gni 
    INNER JOIN products p 
    ON gni.product_id = p.id 
    WHERE gni.grn_id = ? 
    ORDER BY p.name 
    ASC');

if (!$itemStmt) {
    error_log('view_grn.php: Failed to prepare GRN items query: '.$conn->error);
    exit('Unable to load Goods Received Note items.');
}

        if (!$itemStmt->bind_param('i', $id)) {
            error_log('view_grn.php: Failed to bind GRN items ID: '.$itemStmt->error);
            $itemStmt->close();
            exit('Unable to load Goods Received Note items.');
        }

    if (!$itemStmt->execute()) {
        error_log('view_grn.php: Failed to execute GRN items query: '.$itemStmt->error);
        exit('Unable to load Goods Received Note items.');
    }

    $items = $itemStmt->get_result();

    if (!$items) {
        error_log('view_grn.php: Failed to get GRN items result: '.$itemStmt->error);
        exit('Unable to load Goods Received Note items.');
    }

    $itemStmt->close();

    include 'includes/header.php';
?>

<div class="document-header">
    <div class="document-top">

    <div>
        
    <h2>Goods Received Note</h2>

        <p class="document-number">
            <?php echo htmlspecialchars($grn['grn_number'], ENT_QUOTES, 'UTF-8'); ?>
        </p>
    </div>

    <div class="document-actions">

    <button onclick="window.print()" class="action-btn">
        🖨 Print
    </button>

    <a href="purchase_orders.php" class="action-btn">
        Purchase Orders
    </a>

    </div>
    </div>


<div class="document-back-po">

    <a href="goods_received_notes.php" class="action-btn">
        ⬅ Back to GRNs
    </a>
</div><br>

<div class="grn-details">

    <p><strong>Purchase Order:</strong>
        <?php echo htmlspecialchars($grn['po_number']); ?>
    </p>

    <p><strong>Supplier:</strong>
        <?php echo htmlspecialchars($grn['company_name'], ENT_QUOTES, 'UTF-8'); ?>
    </p>

    <p><strong>Contact:</strong>
        <?php echo htmlspecialchars($grn['contact_person'], ENT_QUOTES, 'UTF-8'); ?>
    </p>

    <p><strong>Phone:</strong>
        <?php echo htmlspecialchars($grn['phone'], ENT_QUOTES, 'UTF-8'); ?>
    </p>

    <p><strong>Email:</strong>
        <?php echo htmlspecialchars($grn['email'], ENT_QUOTES, 'UTF-8'); ?>
    </p>

    <p><strong>Received By:</strong>
        <?php echo htmlspecialchars($grn['full_name'], ENT_QUOTES, 'UTF-8'); ?>
    </p>
            
    <p><strong>Total:</strong>R 
        <?php echo number_format($grn['total'], 2); ?>
    </p>

    <p><strong>Received:</strong>
        <?php echo date('d M Y H:i', strtotime($grn['received_at'])); ?>
    </p>

    </div>

    <?php if (!empty($grn['notes'])) { ?>

    <div class="grn-notes">
        <h3>Receiving Notes:</h3>

        <p>
            <?php echo nl2br(htmlspecialchars($grn['notes'])); ?>
        </p>

    </div>

    <?php } ?>

    <div class="table-container">
        <table class="grn-table">

            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Cost</th>
                    <th>Line Total</th>
                </tr>
            </thead>

                        <tbody>

            <?php while ($item = $items->fetch_assoc()) { ?>

            <tr>
                <td>
                    <?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>
                </td>
            
                <td>
                    <?php echo (int) $item['quantity']; ?>
                </td>

                <td>R 
                    <?php echo number_format($item['cost_price'], 2); ?>
                </td>

                <td>R 
                    <?php echo number_format($item['line_total'], 2); ?>
                </td>
            </tr>

            <?php } ?>

            <tr class="grand-total-row">
                <td colspan="3" style="text-align: right;">
                    <strong>Total</strong>
                </td>

                <td>
                    <strong>
                        R <?php echo number_format($grn['total'], 2); ?>
                    </strong>
                </td>
            </tr>

            </tbody>

        </table>
    </div>
</div>
    <?php include 'includes/footer.php'; ?>