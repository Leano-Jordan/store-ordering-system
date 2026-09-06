<?php
require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN]);
require_once 'includes/db.php';

$id = (int) $_GET['id'];

$sql = 'SELECT * FROM users WHERE id = ?';

$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log('edit_user.php: Failed to prepare user query: '.$conn->error);
    header('Location: users.php');
    exit();
}

if (!$stmt->bind_param('i', $id)) {
    error_log('edit_user.php: Failed to bind user ID: '.$stmt->error);

    $stmt->close();

    header('Location: users.php');
    exit();
}

if (!$stmt->execute()) {
    error_log('edit_user.php: Failed to execute user query: '.$stmt->error);

    $stmt->close();

    header('Location: users.php');
    exit();
}

$result = $stmt->get_result();

if (!$result) {
    error_log('edit_user.php: Failed to retrieve user result: '.$stmt->error);

    $stmt->close();

    header('Location: users.php');
    exit();
}
$user = $result->fetch_assoc();

if (!$user) {
    header('Location: users.php');
    exit();
}
include 'includes/header.php';
?>

<?php require 'includes/shared/flash_message.php'; ?>

<h2>Edit User</h2>

<div class="form-container">
    <form action="update_user.php" 
        method="POST" 
        enctype="multipart/form-data" 
        autocomplete="off">

        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES); ?>">


        <input type="hidden" name="id" value="<?php echo (int) $user['id']; ?>">
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text"
                name="full_name"
                value="<?php echo htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8'); ?>"
                required
                autocomplete="off">
            <br><br>

            <label for="username">Username</label>
            <input type="text"
                name="username"
                value="<?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>"
                required
                autocomplete="off">
            <br><br>

            <label>Role</label><br>
            <select name="role" required>

                <option value="<?php echo ROLE_ADMIN; ?>"
                    <?php if ($user['role'] === ROLE_ADMIN) {
    echo 'selected';
} ?>>
                    <?php echo ROLE_ADMIN; ?>
                </option>

                <option value="<?php echo ROLE_MANAGER; ?>"
                    <?php if ($user['role'] === ROLE_MANAGER) {
    echo 'selected';
} ?>>
                    <?php echo ROLE_MANAGER; ?>
                </option>

                <option value="<?php echo ROLE_CASHIER; ?>"
                    <?php if ($user['role'] === ROLE_CASHIER) {
    echo 'selected';
} ?>>
                    <?php echo ROLE_CASHIER; ?>
                </option>

                <option value="<?php echo ROLE_KITCHEN; ?>"
                    <?php if ($user['role'] === ROLE_KITCHEN) {
    echo 'selected';
} ?>>
                    <?php echo ROLE_KITCHEN; ?>
                </option>

            </select>

            <br><br>

            <label for="status">Status</label><br>
            <select name="status" required>
                <option value="Active" <?php if ($user['status'] === 'Active') {
    echo 'selected';
} ?>>Active</option>
                <option value="Inactive" <?php if ($user['status'] === 'Inactive') {
    echo 'selected';
} ?>>Inactive</option>
            </select>
            <br><br>


            <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($user['profile_image'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

            <label>Current Profile Picture</label><br>
            <?php $image = !empty($user['profile_image']) ?
            'assets/images/profiles/'.$user['profile_image'] :
            'assets/images/profiles/default-avatar.png';
            ?>

            <img src="<?php echo htmlspecialchars($image, ENT_QUOTES, 'UTF-8'); ?>" 
                class="nav-avatar" 
                alt="Profile"
                style="margin-bottom: 15px;">
            <br>

            <label for="profile_image">New Profile Picture</label><br>
            <input type="file" 
                name="profile_image" 
                accept=".jpg, .jpeg, .png, .webp">
<br><br>

        </div>

        <div class="form-actions">
            <button type="submit" class="action-btn">Update User</button>
        </div>
        <a href="users.php" class="action-btn">Cancel/Back to Users</a>

    </form>

    <?php include 'includes/footer.php'; ?>