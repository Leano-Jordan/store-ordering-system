<?php
require_once "includes/auth.php";
require_once "includes/permissions.php";
requireRole([ROLE_ADMIN]);
$loadScript = true;
require_once "includes/db.php";

$limit = 10;
$page = max(1, (int)($_GET["page"] ?? 1));
$offset = ($page - 1) * $limit;

$stmt = $conn->prepare(
    "SELECT * FROM users 
    ORDER BY created_at 
    DESC LIMIT ?, ?"
);
$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

$totalResult = $conn->query("SELECT COUNT(*) AS total FROM users");
$totalRows = $totalResult->fetch_assoc()["total"];
$totalPages = ceil($totalRows / $limit);

include "includes/header.php";

?>

<?php if (isset($_GET["error"]) && $_GET["error"] === "self_deactivate") { ?>

    <div class="error-message">
        You cannot deactivate your own account while you are logged in.
    </div>
<?php } ?>

<?php if (isset($_GET["error"]) && $_GET["error"] === "last_admin") { ?>

    <div class="error-message">
        You cannot deactivate the last active Administrator.
    </div>
<?php } ?>

<script>
    if (window.history.replaceState) {
        window.history.replaceState({}, document.title, "users.php");
    }
</script>

<?php if (isset($_GET["success"]) && $_GET["success"] === "user_added") { ?>
    <div class="success-message">
        User added successfully.
    </div>
<?php } ?>

<?php if (isset($_GET["success"]) && $_GET["success"] === "user_updated") { ?>
    <div class="success-message">
        User updated successfully.
    </div>
<?php } ?>

<?php if (isset($_GET["success"]) && $_GET["success"] === "user_deactivated") { ?>
    <div class="success-message">
        User deactivated successfully.
    </div>
<?php } ?>

<?php if (isset($_GET["success"]) && $_GET["success"] === "user_activated") { ?>
    <div class="success-message">
        User reactivated successfully.
    </div>
<?php } ?>

<div class="page-header">

    <h2>Users</h2>

    <a href="add_user.php" class="action-btn">+ Add User</a>

</div>

<table class="orders-table">
    <tr>
        <th>Full Name</th>
        <th>Username</th>
        <th>Role</th>
        <th>Status</th>
        <th>Created</th>
        <th>Actions</th>
    </tr>

    <?php while ($row =
        $result->fetch_assoc()
    ) { ?>

        <tr>

            <td>
                <?php echo htmlspecialchars($row["full_name"], ENT_QUOTES, "UTF-8") ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["username"], ENT_QUOTES, "UTF-8") ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["role"], ENT_QUOTES, "UTF-8") ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["status"], ENT_QUOTES, "UTF-8") ?>
            </td>

            <td>
                <?php echo date("d M Y", strtotime($row["created_at"])); ?>
            </td>

            <td>
                <a href="edit_user.php?id=<?php echo (int)$row["id"]; ?>" class="action-btn edit-btn">
                    Edit User
                </a>

                <?php if ($row["status"] === "Active") { ?>

                    <a href="deactivate_user.php?id=<?php echo (int)$row["id"]; ?>" class="action-btn delete-btn">
                        Deactivate User
                    </a>
                <?php } else { ?>
                    <a href="reactivate_user.php?id=<?php echo (int)$row["id"]; ?>" class="action-btn">
                        Reactivate
                    </a>
                <?php } ?>
            </td>

        </tr>

    <?php } ?>
</table>

<div class="pagination">
    <?php if ($page > 1) { ?>

        <a href="?page=<?php echo $page - 1; ?>" class="action-btn">
            ⬅ Previous
        </a>
    <?php } ?>

    <span>
        Page <?php echo $page; ?> of <?php echo $totalPages; ?>
    </span>

    <?php if ($page < $totalPages) { ?>

        <a href="?page=<?php echo $page + 1; ?>" class="action-btn">
            Next ➡
        </a>

    <?php } ?>
</div>

<?php include "includes/footer.php"; ?>