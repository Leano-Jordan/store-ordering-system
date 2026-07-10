<?php
require_once "includes/auth.php";
require_once "includes/permissions.php";
requireRole([ROLE_ADMIN]);
require_once "includes/db.php";

$limit = 10;

$page = max(1, (int)($_GET["page"] ?? 1));

$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM products ORDER BY category, name LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);

$totalResult = $conn->query("SELECT COUNT(*) AS total FROM products");

$totalRows = $totalResult->fetch_assoc()["total"];

$totalPages = ceil($totalRows / $limit);

include "includes/header.php";

?>

<div class="page-header">

    <h2>Products</h2>

    <a href="add_product.php" class="action-btn">+ Add Product</a>

</div>

<table class="orders-table">
    <tr>
        <th>Image</th>
        <th>Name</th>
        <th>Category</th>
        <th>Price</th>
        <th>Description</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>

    <?php while ($row =
        $result->fetch_assoc()
    ) { ?>

        <tr>
            <td>
                <?php $image = !empty($row["image"]) ? $row["image"] : "no-image.png"; ?>

                <img src="./assets/images/products/<?php echo $image; ?>"
                    alt="Product Image" class="product-thumb">
            </td>

            <td><?php echo htmlspecialchars($row["name"], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($row["category"], ENT_QUOTES, 'UTF-8'); ?></td>
            <td>R<?php echo number_format($row["price"], 2); ?></td>
            <td><?php echo htmlspecialchars($row["description"], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($row["status"]); ?></td>
            <td>

                <a href="edit_product.php?id=<?php echo (int)$row["id"]; ?>" class="action-btn edit-btn">
                    🖋 Edit
                </a>
                <?php if ($row["status"] === "Active") { ?>
                    <a href="delete_product.php?id=<?php echo (int)$row["id"]; ?>" class="action-btn delete-btn">
                        🗑 Deactivate
                    </a>
                <?php } else { ?><a href="reactivate_product.php?id=<?php echo (int)$row["id"]; ?>" class="action-btn">
                        ✅ Reactivate
                    </a><?php } ?>

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