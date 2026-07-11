<?php
require_once "includes/auth.php";
require_once "includes/permissions.php";
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once "includes/db.php";

$search = trim($_GET["search"] ?? "");
$range = $_GET["range"] ?? "7";

if ($range === "30") {

    $where = "activity_logs.created_at 
    >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
} elseif ($range === "month") {

    $where = "YEAR(activity_logs.created_at) = 
    YEAR(CURDATE()) AND MONTH(activity_logs.created_at) = MONTH(CURDATE())";
} elseif ($range === "year") {

    $where = "YEAR(activity_logs.created_at) = 
    YEAR(CURDATE())";
} else {

    $where = "activity_logs.created_at >= 
    DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
}

if ($search !== "") {

    $stmt = $conn->prepare(
        "SELECT 
            activity_logs.action, 
            activity_logs.created_at,
            users.username, users.role 
        FROM activity_logs 
        JOIN users 
            ON activity_logs.user_id = users.id 
        WHERE users.username LIKE ?
            OR activity_logs.action LIKE ?
        ORDER BY activity_logs.created_at DESC"
    );

    $term = "%$search%";

    $stmt->bind_param("ss", $term, $term);
    $stmt->execute();

    $result = $stmt->get_result();
} else {

    $result = $conn->query("SELECT activity_logs.action, 
activity_logs.created_at,
users.username, users.role FROM activity_logs 
JOIN users ON activity_logs.user_id = users.id WHERE $where
ORDER BY activity_logs.created_at DESC");
}

if (!$result) {
    die("$conn->error");
}

include "includes/header.php";
?>

<h2>Activity Logs</h2>

<form method="GET" class="search-form">

    <input type="text"
        name="search"
        placeholder="Search activity..."
        value="<?php echo htmlspecialchars($search); ?>">

    <button type="submit" class="action-btn">
        Submit
    </button>

    <a href="activity_logs.php" class="action-btn">Clear</a>

</form>
<div class="activity-toolbar">
    <div class="chart-filter">
        <a href="?range=7&search=<?php echo urlencode($search); ?>" class="action-btn <?php echo $range === '7' ? 'active-nav' : ''; ?>">7 Days</a>
        <a href="?range=30&search=<?php echo urlencode($search); ?>" class="action-btn <?php echo $range === '30' ? 'active-nav' : ''; ?>">30 Days</a>
        <a href="?range=month&search=<?php echo urlencode($search); ?>" class="action-btn <?php echo $range === 'month' ? 'active-nav' : ''; ?>">This Month</a>
        <a href="?range=year&search=<?php echo urlencode($search); ?>" class="action-btn <?php echo $range === 'year' ? 'active-nav' : ''; ?>">This Year</a>
    </div>

    <div class="activity-legend">

        <span class="status ready">
            🟢 Added Product
        </span>

        <span class="status ready">
            🔵 Updated Product
        </span>

        <span class="status ready">
            🟠 Order Changes
        </span>

        <span class="status ready">
            🔴 Deactivated Product
        </span>
    </div>
</div>

<table class="orders-table">

    <thead>
        <tr>
            <th>Date & Time</th>
            <th>User</th>
            <th>Role</th>
            <th>Activity</th>
        </tr>
    </thead>

    <tbody>

        <?php while ($row = $result->fetch_assoc()) { ?>

            <tr>
                <td>
                    <?php echo date("d F Y H:i", strtotime($row["created_at"])); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row["username"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row["role"]); ?>
                </td>

                <td>

                    <?php

                    $badgeClass = "status-pending";

                    if (strpos($row["action"], "Added") === 0) {
                        $badgeClass = "ready";
                    } elseif (
                        strpos($row["action"], "Updated")
                        === 0 || strpos($row["action"], "Changed") === 0
                    ) {
                        $badgeClass = "preparing";
                    } elseif (strpos($row["action"], "Deactivated") === 0) {
                        $badgeClass = "cancelled";
                    } elseif (strpos($row["action"], "Deleted") === 0) {
                        $badgeClass = "cancelled";
                    } else {
                        $badgeClass = "pending";
                    }

                    ?>

                    <span class="status <?php echo $badgeClass; ?>">
                        <?php echo htmlspecialchars($row["action"]); ?>
                    </span>
                </td>
            </tr>

        <?php } ?>

    </tbody>

</table>

<?php include "includes/footer.php"; ?>