<?php
require_once "includes/auth.php";
require_once "includes/permissions.php";
requireRole([ROLE_ADMIN]);
require_once "includes/db.php";

$id = (int)$_GET['id'];

$sql = "SELECT * FROM users WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    header("Location: users.php");
    exit();
}
include "includes/header.php";
?>

<h2>Edit User</h2>

<div class="form-container">
    <form action="update_user.php" method="POST" autocomplete="off">

        <input type="hidden" name="id" value="<?php echo (int)$user["id"]; ?>">
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text"
                name="full_name"
                value="<?php echo htmlspecialchars($user["full_name"], ENT_QUOTES, "UTF-8"); ?>"
                required>
            <br><br>

            <label for="username">Username</label>
            <input type="text"
                name="username"
                value="<?php echo htmlspecialchars($user["username"], ENT_QUOTES, "UTF-8"); ?>"
                required>
            <br><br>

            <label>Role</label><br>
            <select name="role" required>

                <option value="<?php echo ROLE_ADMIN; ?>"
                    <?php if ($user["role"] === ROLE_ADMIN) echo "selected"; ?>>
                    <?php echo ROLE_ADMIN ?>
                </option>

                <option value="<?php echo ROLE_MANAGER; ?>"
                    <?php if ($user["role"] === ROLE_MANAGER) echo "selected"; ?>>
                    <?php echo ROLE_MANAGER ?>
                </option>

                <option value="<?php echo ROLE_CASHIER; ?>"
                    <?php if ($user["role"] === ROLE_CASHIER) echo "selected"; ?>>
                    <?php echo ROLE_CASHIER ?>
                </option>

                <option value="<?php echo ROLE_KITCHEN; ?>"
                    <?php if ($user["role"] === ROLE_KITCHEN) echo "selected"; ?>>
                    <?php echo ROLE_KITCHEN ?>
                </option>

            </select>

            <br><br>

            <label for="status">Status</label><br>
            <select name="status" required>
                <option value="Active" <?php if ($user["status"] === "Active") echo "selected"; ?>>Active</option>
                <option value="Inactive" <?php if ($user["status"] === "Inactive") echo "selected"; ?>>Inactive</option>
            </select>

            <br><br>
        </div>

        <div class="form-actions">
            <button type="submit" class="action-btn">Update User</button>
        </div>
        <a href="users.php" class="action-btn">Cancel/Back to Users</a>

    </form>

    <?php include "includes/footer.php"; ?>