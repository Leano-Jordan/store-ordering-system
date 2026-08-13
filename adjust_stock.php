<?php

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once 'includes/db.php';
require_once 'includes/csrf.php';
verifyCsrfToken();

$products = $conn->query("SELECT id, name, stock 
FROM products 
WHERE status='Active' 
ORDER BY category, name");

if (!$products) {
    error_log('adjust_stock.php: Failed to load active products: '.$conn->error);
    exit('Failed to load products. Please try again later.');
}

include 'includes/header.php';
?>

<div class="page-header">
    <h2>Adjust Stock</h2>
</div>

<div class="form-container">
<form action="save_stock_adjustments.php" 
method="POST" 
class="form-card">
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES); ?>">

<div class="form-group">
    <label for="product">Product</label>
    <select name="product_id" required>

<option value="">Select Product</option>

<?php while ($product =
$products->fetch_assoc()) { ?>

    <option value="<?php echo $product['id']; ?>">
        <?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>
        (Current Stock: <?php echo (int) $product['stock']; ?>)
    </option>

<?php } ?>
    </select>
    
    <br><br>

<label for="adjustment_type">Adjustment Type</label>
<select name="adjustment_type" required>
    <option value="Increase">Increase</option>
    <option value="Decrease">Decrease</option>
</select>

<br><br>

<label for="quantity">Quantity</label>
<input type="number" 
        name="quantity" 
        min="1" 
        required>

<br><br>

        <label for="reason">Reason</label>
        <select name="reason" required>

            <option value="">-- Select Reason --</option>
            <option value="New Delivery">New Delivery</option>
            <option value="Damaged">Damaged</option>
            <option value="Expired">Expired</option>
            <option value="Stock Count">Stock Count</option>
            <option value="Correction">Correction</option>
            <option value="Other (Specify on *notes)">Other (Specify on *notes)</option>

        </select>

        <br><br>

        <label for="notes">Notes</label>
        <textarea name="notes" rows="4">
        </textarea>

        <br>
        </div>
        <div class="form-actions">
        <button type="submit" class="action-btn">
            Save Adjustment
        </button>
        </div>
</form>
</div>

<?php include 'includes/footer.php'; ?>