<?php
require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once 'includes/csrf.php';
require_once 'includes/db.php';

$isEdit = false;
$purchaseOrder = null;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $isEdit = true;
    $id = (int) $_GET['id'];

    $stmt = $conn->prepare('SELECT * FROM purchase_orders 
        WHERE id = ?');

    if (!$stmt) {
        error_log('add_purchase_order.php: Failed to prepare purchase order lookup: '.$conn->error);
        exit('Unable to load purchase order details.');
    }

    if (!$stmt->bind_param('i', $id)) {
        error_log('add_purchase_order.php: Failed to bind purchase order lookup: '.$stmt->error);

        $stmt->close();

        exit('Unable to load purchase order details.');
    }

    if (!$stmt->execute()) {
        error_log('add_purchase_order.php: Failed to execute purchase order lookup: '.$stmt->error);

        $stmt->close();

        exit('Unable to load purchase order details.');
    }

    $purchaseOrderResult = $stmt->get_result();

    if (!$purchaseOrderResult) {
        error_log('add_purchase_order.php: Failed to retrieve purchase order lookup result: '.$stmt->error);

        $stmt->close();

        exit('Unable to load purchase order details.');
    }

    $purchaseOrder = $purchaseOrderResult->fetch_assoc();

    $stmt->close();

    if (!$purchaseOrder) {
        exit('Purchase order not found.');
    }
}

$suppliers = $conn->query("SELECT id, company_name
FROM suppliers
WHERE status='Active'
ORDER BY company_name ASC
");

if (!$suppliers) {
    error_log('add_purchase_order.php: Failed to load active suppliers: '.$conn->error);
    exit('Unable to load suppliers.');
}

$productsResult = $conn->query("SELECT id, 
name FROM products 
WHERE status='Active' 
ORDER BY name ASC
");

if (!$productsResult) {
    error_log('add_purchase_order.php: Failed to load active products: '.$conn->error);
    exit('Unable to load products.');
}

$productList = [];

while ($product = $productsResult->fetch_assoc()) {
    $productList[] = [
        'id' => $product['id'],
        'name' => $product['name'],
    ];
}

$purchaseOrderItems = [];

if ($isEdit) {
    $stmt = $conn->prepare('SELECT * FROM purchase_order_items 
    WHERE purchase_order_id = ? ORDER BY id ASC');

    if (!$stmt) {
        error_log('add_purchase_order.php: Failed to prepare purchase order items lookup: '.$conn->error);
        exit('Unable to load purchase order items.');
    }

    $stmt->bind_param('i', $id);
    $stmt->execute();

    $purchaseOrderItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$loadScript = true;
include 'includes/header.php';
?>

<div class="page-header">
    <h2><?php echo $isEdit ? 'Edit Purchase Order' : 'New Purchase Order'; ?></h2>

    <a href="purchase_orders.php" class="action-btn delete-btn">
        ← Back / Cancel
    </a>
</div>

<div class="form-container">

<form action="<?php echo $isEdit
    ? 'update_purchase_order.php' :
    'save_purchase_order.php'; ?>" 
    method="POST" autocomplete="off">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">

    <?php if ($isEdit) { ?>

        <input type="hidden" 
            name="purchase_order_id" value="<?php echo $purchaseOrder['id']; ?>">

    <?php } ?>

<div class="form-group">
    <label>Supplier *</label>

    <?php if ($suppliers->num_rows > 0) { ?>

    <select name="supplier_id" required>

        <option value="">-- Choose Supplier --</option>

        <?php while ($supplier = $suppliers->fetch_assoc()) { ?>

            <option value="<?php echo $supplier['id']; ?>"
            <?php if ($isEdit && $supplier['id'] ==
                    $purchaseOrder['supplier_id']) {
        echo 'selected';
    }
    ?>>

            <?php echo htmlspecialchars($supplier['company_name']); ?>
        </option>
        <?php } ?>
    </select>

    <?php } else { ?>

        <p>No active suppliers found.</p>

        <a href="add_supplier.php" class="action-btn">
            + Add Supplier
        </a>
        <?php } ?>
</div>

<div class="form-group">

<div class="page-header">
    <h3>Purchase Order Items</h3>

    <br>

    <button type="button"
            class="action-btn"
            onclick="addPurchaseOrderRow()">
                + Add Item
    </button>
</div>

<div class="table-container">
    <table id="purchase-order-items" class="orders-table data-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Unit Cost</th>
                <th>Total</th>
                <th>Action</th>
            </tr>

        </thead>
        <tbody>

        </tbody>
    </table>
</div>

</div>

<div class="po-total">
    <strong>Purchase Order Total:</strong>
    <span id="purchase-order-total">R0.00</span>
</div>

<br><br>

<div class="form-group">

<label>
    Notes
</label>

    <textarea name="notes" rows="4" maxlength="1000"><?php echo $isEdit ? htmlspecialchars($purchaseOrder['notes']) : ''; ?></textarea>
</div>

<div class="form-actions">

    <?php if (!$isEdit) { ?>

    <button type="submit" name="status" value="Draft" class="action-btn">
        Save as Draft
    </button>

    <button type="submit" name="status" value="Pending" class="action-btn">
        Create Purchase Order
    </button>

    <?php } else { ?>

        <button type="submit" name="status" value="Draft" class="action-btn">
            Save P/O Draft
        </button>

        <?php if ($purchaseOrder['status'] === 'Draft') { ?>

        <button type="submit" name="status" value="Pending" class="action-btn">
            Submit Purchase Order
        </button>
        <?php } ?>

    <?php } ?>

</div>

</form>

</div>

<script>
window.poProducts = <?php echo json_encode(
        $productList,
        JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    );
        ?>;
        
window.poItems = <?php echo json_encode(
            $purchaseOrderItems,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        ); ?>;

</script>

<?php include 'includes/footer.php'; ?>

