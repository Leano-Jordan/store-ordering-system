<?php
require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once 'includes/db.php';

include 'includes/header.php';
?>

<?php require 'includes/shared/flash_message.php'; ?>

<div class="page-header">
    <h2>Add Supplier</h2>

    <a href="suppliers.php" class="action-btn">
        ← Back
    </a>
</div>

<div class="form-container">

<form action="save_supplier.php" method="POST" autocomplete="off">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">

<div class="form-group">

<label>Company Name *</label>
<input
Type="text"
Name="company_name"
autocomplete="off"
Required>

<br><br>

<label>Contact Person *</label>
<input
Type="text"
Name="contact_person"
autocomplete="off"
Required>

<br><br>

<label>Phone *</label>
<input
Type="text"
Name="phone"
autocomplete="off"
Required>

<br><br>

<label>Email</label>
<input
Type="email"
Name="email"
autocomplete="off"
>

<br><br>

<label>Address</label>
<textarea
Name="address" autocomplete="off"></textarea>

<br><br>

<label>Notes</label>
<textarea
Name="notes" autocomplete="off"></textarea>

<br><br>

<label>Status</label>
<select name="status" Required>

<option value="Active">
Active
</option>

<option value="Inactive">
Inactive
</option>

</select>
</div>

<div class="form-actions">

<button type="submit">
Save Supplier
</button>

<a href="suppliers.php" class="action-btn">
Cancel
</a>

</div>

</form>

</div>

<?php include 'includes/footer.php';
?>

