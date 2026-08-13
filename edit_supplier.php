<?php
require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once 'includes/db.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $conn->prepare('
SELECT *
FROM suppliers
WHERE id = ?
');

$stmt->bind_param('i', $id);
$stmt->execute();

$result = $stmt->get_result();
$supplier = $result->fetch_assoc();

if (!$supplier) {
    exit('Supplier not found.');
}

include 'includes/header.php';
?>

<div class="page-header">

<h2>Edit Supplier</h2>

<a href="suppliers.php" class="action-btn">
← Back
</a>
</div>

<div class="form-container">

<form action="update_supplier.php" method="POST" autocomplete="off">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
<div class="form-group">

<input
Type="hidden"
Name="id"
Value="<?php echo $supplier['id']; ?>">

<label>Company Name *</label>
<input
Type="text"
Name="company_name"
Required
autocomplete="off"
Value="<?php echo htmlspecialchars($supplier['company_name']); ?>">

<br><br>

<label>Contact Person *</label>
<input
Type="text"
Name="contact_person"
autocomplete="off"
Required
Value="<?php echo htmlspecialchars($supplier['contact_person']); ?>">

<br><br>

<label>Phone *</label>
<input
Type="text"
Name="phone"
autocomplete="off"
Required
Value="<?php echo htmlspecialchars($supplier['phone']); ?>">
</div>

<br><br>

<div class="form-group">
<label>Email</label>

<input
Type="email"
Name="email"
Value="<?php echo htmlspecialchars($supplier['email']); ?>">

<br><br>

<label>Address</label>

<textarea name="address"><?php echo htmlspecialchars($supplier['address']); ?></textarea>

<br><br>

<label>Notes</label>
<textarea name="notes"><?php echo htmlspecialchars($supplier['notes']); ?></textarea>

<br><br>

<label>Status</label>
<select name="status" Required>

<option value="Active" <?php if ($supplier['status'] == 'Active') {
    echo 'selected';
} ?>>
Active
</option>

<option value="Inactive" <?php if ($supplier['status'] == 'Inactive') {
    echo 'selected';
} ?>>
Inactive
</option>

</select>

</div>

<div class="form-actions">

<button type="submit">
Update Supplier
</button>

<a href="suppliers.php" class="action-btn">
Cancel
</a>
</div>

</form>

</div>

<?php include 'includes/footer.php'; ?>

