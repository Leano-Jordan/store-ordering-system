<?php
require_once "includes/auth.php";
require_once "includes/permissions.php";
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once "includes/db.php";

$id = (int)$_GET['id'];

$sql = "SELECT * FROM products WHERE id=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header("Location: products.php");
    exit();
}
include "includes/header.php";
?>

<h2>Edit Product</h2>

<?php if (isset($_GET["error"]) && $_GET["error"] === "invalid_image") { ?>
    <p class="error-message">Only JPG, JPEG and PNG images are allowed.</p>
<?php } ?>

<form action="update_product.php" method="POST" enctype="multipart/form-data" autocomplete="off">

    <input type="hidden" name="id" value="<?php echo $product["id"]; ?>">
    <input type="hidden" name="current_image" value="<?php echo $product["image"] ?>">

    <p>
        Current Image: <?php echo $currentImage = $product["image"]; ?>
    </p>

    <label for="name">Product Name</label><br>
    <input type="text" name="name" value="<?php echo htmlspecialchars($product["name"]); ?>" required><br><br>

    <label for="category">Category</label><br>

    <select name="category">
        <option value="Meals" <?php if ($product["category"] == "Meals") echo "selected"; ?>>
            Meals
        </option>

        <option value="Sides" <?php if ($product["category"] == "Sides") echo "selected"; ?>>
            Sides
        </option>

        <option value="Snacks" <?php if ($product["category"] == "Snacks") echo "selected"; ?>>
            Snacks
        </option>

        <option value="Drinks" <?php if ($product["category"] == "Drinks") echo "selected"; ?>>
            Drinks
        </option>

        <option value="Other" <?php if ($product["category"] == "Other") echo "selected"; ?>>
            Other
        </option>
    </select>
    <br><br>

    <label for="price">Price</label><br>
    <input type="number" step="0.01" name="price"
        value="<?php echo $product["price"]; ?>" required>

    <br><br>

    <label for="image">Current Image</label><br>

    <?php
    if (!empty($product["image"])) { ?>
        <img src="./assets/images/products/<?php echo $product["image"]; ?>"
            alt="Product Image" width="120">

        <br><br>

        <small>
            <?php echo $product["image"]; ?>
        </small>

    <?php
    } else { ?>

        <p>
            No image uploaded.
        </p>

    <?php } ?>

    <br><br>

    <label>Choose New Image</label><br>
    <input type="file" name="image" accept=".jpg, .jpeg, .png">

    <p>
        <small>
            Leave this empty to keep the current image.
        </small>
    </p>

    <br><br>

    <label for="description">description</label><br>
    <textarea name="description" rows="4"><?php echo htmlspecialchars($product["description"]); ?>
    </textarea>

    <br><br>

    <input type="submit" value="Update Product" class="action-btn">
    <a href="delete_product.php?id=<?php echo (int)$product["id"]; ?>" class="action-btn delete-btn">
        🗑 Delete</a>
    <a href="products.php" class="action-btn">Back to Products</a>

</form>

<?php include "includes/footer.php"; ?>