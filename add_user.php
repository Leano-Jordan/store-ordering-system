<?php
require_once "includes/auth.php";
require_once "includes/permissions.php";
requireRole([ROLE_ADMIN]);
require_once "includes/db.php";
include "includes/header.php";

?>

<h2>Add User</h2>

<div class="form-container">
    <form action="save_user.php"
        method="POST"
        autocomplete="off">

        <div class="form-group">
            <label for="full_name">Full Name</label><br>
            <input type="text" name="full_name" required><br><br>

            <label for="username">Username</label><br>
            <input type="text" name="username" required><br><br>

            <label for="password">Password</label><br>
            <input type="password" name="password" required><br><br>

            <label for="role">Role</label><br>
            <select name="role" required>
                <option value="<?php echo ROLE_ADMIN; ?>"><?php echo ROLE_ADMIN ?></option>
                <option value="<?php echo ROLE_MANAGER; ?>"><?php echo ROLE_MANAGER ?></option>
                <option value="<?php echo ROLE_CASHIER; ?>"><?php echo ROLE_CASHIER ?></option>
                <option value="<?php echo ROLE_KITCHEN; ?>"><?php echo ROLE_KITCHEN ?></option>
            </select><br><br>

            <label for="status">Status</label><br>
            <select name="status" required>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select><br><br>
        </div>

        <div class="form-actions">
            <button type="submit" class="action-btn">Save User</button>
        </div>
    </form>
</div>