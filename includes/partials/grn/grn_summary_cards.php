<?php

require_once __DIR__.'/../../auth.php';
require_once __DIR__.'/../../permissions.php';

require_once __DIR__.'/../../logger.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);

require_once __DIR__.'/../../db.php';
?>

<?php

$totalGrnsResult = $conn->query('SELECT COUNT(*) AS total FROM goods_received_notes');

if (!$totalGrnsResult) {
    error_log('grn_summary_cards.php: Failed to execute total GRNs query: '.$conn->error);
    exit('Unable to load GRN summary.');
}

$totalGrns = $totalGrnsResult->fetch_assoc()['total'] ?? 0;

$todayGrnsResult = $conn->query('SELECT COUNT(*) total
FROM goods_received_notes
WHERE DATE(received_at)=CURDATE()
');

if (!$todayGrnsResult) {
    error_log('grn_summary_cards.php: Failed to execute today\'s GRNs query: '.$conn->error);
    exit('Unable to load GRN summary.');
}

$todayGrns = $todayGrnsResult->fetch_assoc()['total'] ?? 0;

$totalReceivedResult = $conn->query('
SELECT COALESCE(SUM(total),0) AS total
FROM goods_received_notes');

if (!$totalReceivedResult) {
    error_log('grn_summary_cards.php: Failed to calculate total received total: '.$conn->error);
    exit('Unable to load GRN summary.');
}

$totalReceived = $totalReceivedResult->fetch_assoc()['total'] ?? 0;

$totalSuppliersResult = $conn->query('
SELECT COUNT(DISTINCT supplier_id) AS total
FROM goods_received_notes
');

if (!$totalSuppliersResult) {
    error_log('grn_summary_cards.php: Failed to count total suppliers: '.$conn->error);
    exit('Unable to load GRN summary.');
}

$totalSuppliers = $totalSuppliersResult->fetch_assoc()['total'] ?? 0;

?>

<div class="grn_summary">
<div class="dashboard-grid">
    <div class="dashboard-card">
        <h3>Total GRNs</h3>
        <p><?php echo number_format($totalGrns); ?></p>
    </div>

    <div class="dashboard-card">
        <h3>Today's GRNs</h3>
        <p><?php echo number_format($todayGrns); ?></p>
    </div>

    <div class="dashboard-card">
        <h3>Total Received</h3>
        <p><?php echo formatCurrency($totalReceived); ?></p>
    </div>

    <div class="dashboard-card">
        <h3>Suppliers</h3>
        <p><?php echo number_format($totalSuppliers); ?></p>
    </div>

</div>
</div>