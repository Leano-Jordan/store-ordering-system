<?php
require_once "includes/auth.php";
require_once "includes/permissions.php";
requireRole([ROLE_ADMIN]);
require_once "includes/db.php";

$limit = 10;

$page = max(1, (int)($_GET["page"] ?? 1));

$offset = ($page - 1) * $limit;

$stockFilter = $_GET["stock"] ?? "";

$search = trim($_GET["search"] ?? "");

if ($stockFilter === "low") {

    $sql = "SELECT * FROM products 
    WHERE status = 'Active' 
    AND stock > 0 
    AND stock <= 10 
    ORDER BY category ASC, stock DESC, name ASC 
    LIMIT $limit OFFSET $offset";

    $totalResult = $conn->query("SELECT COUNT(*) AS total 
    FROM products 
    WHERE status = 'Active' 
    AND stock > 0 
    AND stock <= 10");
} elseif ($stockFilter === "out") {

    $sql = "SELECT * FROM products 
    WHERE status = 'Active' 
    AND stock = 0 
    ORDER BY category, name 
    LIMIT $limit OFFSET $offset";

    $totalResult = $conn->query(
        "SELECT COUNT(*) AS total 
    FROM products 
    WHERE status = 'Active' 
    AND stock = 0"
    );
} else {

    if ($search !== "") {

        $safeSearch = $conn->real_escape_string($search);

        $sql = "SELECT * FROM products 
        WHERE status = 'Active' AND (name LIKE '%$safeSearch%' OR category LIKE '%$safeSearch%') 
        ORDER BY status = 'Inactive', category ASC, 
        CASE 
        WHEN stock = 0 THEN 2 
        WHEN stock <= 10 THEN 1 
        ELSE 0 
        END, 
        stock DESC, name ASC 
        LIMIT $limit OFFSET $offset";

        $totalResult = $conn->query("SELECT COUNT(*) AS total 
        FROM products 
        WHERE status = 'Active' AND (name LIKE '%$safeSearch%' OR category LIKE '%$safeSearch%')");
    } else {

        $sql = "SELECT * FROM products ORDER BY status = 'Inactive', 
        category ASC,
        CASE 
WHEN stock = 0 THEN 2 
WHEN stock <= 10 THEN 1 
ELSE 0 
END, stock DESC, name ASC LIMIT $limit OFFSET $offset";
        $totalResult = $conn->query("SELECT COUNT(*) AS total FROM products");
    }
}

$result = $conn->query($sql);

if (!$result) {
    die($conn->error);
}

$totalRows = $totalResult->fetch_assoc()["total"];

$totalPages = ceil($totalRows / $limit);

include "includes/header.php";

?>

<div class="page-header">

    <form method="GET" class="search-form">

        <input type="text"
            name="search"
            placeholder="Search Products..."
            value="<?php echo htmlspecialchars($search); ?>">

        <?php if ($stockFilter !== "") { ?>

            <input type="hidden" name="stock" value="<?php echo htmlspecialchars($stockFilter); ?>">
        <?php } ?>

        <button type="submit" class="action-btn">
            Search
        </button>

        <a href="products.php" class="action-btn">Clear</a>

    </form>

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
        <th>Stock</th>
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
                <?php

                if ($row["stock"] == 0) {

                    echo '🔴 Out of Stock';
                } elseif ($row["stock"] <= 10) {

                    echo "🟠 Low Stock (" . (int)$row["stock"] . ")";
                } else {
                    echo "🟢 " . (int)$row["stock"] . " in Stock";
                }
                ?>
            </td>
            <td>

                <a href="edit_product.php?id=<?php echo (int)$row["id"]; ?>" class="action-btn edit-btn">
                    🖋 Edit
                </a>
                <?php if ($row["status"] === "Active") { ?>
                    <a href="delete_product.php?id=<?php echo (int)$row["id"]; ?>" class="action-btn delete-btn">
                        ❌ Deactivate
                    </a>
                <?php } else { ?><a href="reactivate_product.php?id=<?php echo (int)$row["id"]; ?>" class="action-btn reactivate-btn">
                        ✔ Reactivate
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