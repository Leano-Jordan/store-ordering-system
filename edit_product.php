<?php
require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once 'includes/db.php';

$id = (int) $_GET['id'];

$sql = 'SELECT * FROM products WHERE id=?';

$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log('edit_product.php: Failed to prepare product query: '.$conn->error);
    header('Location: products.php');
    exit();
}

if (!$stmt->bind_param('i', $id)) {
    error_log('edit_product.php: Failed to bind product ID: '.$stmt->error);
    $stmt->close();
    header('Location: products.php');
    exit();
}

if (!$stmt->execute()) {
    error_log('edit_product.php: Failed to execute product query: '.$stmt->error);
    $stmt->close();
    header('Location: products.php');
    exit();
}

$result = $stmt->get_result();

if (!$result) {
    error_log('edit_product.php: Failed to retrieve product result: '.$stmt->error);
    $stmt->close();
    header('Location: products.php');
    exit();
}

$product = $result->fetch_assoc();

$stmt->close();

if (!$product) {
    header('Location: products.php');
    exit();
}
include 'includes/header.php';
?>

<h2>Edit Product</h2>

<?php if (isset($_GET['error']) && $_GET['error'] === 'invalid_image') { ?>
    <p class="error-message">Only JPG, JPEG and PNG images are allowed.</p>
<?php } ?>
<div class="form-container">
    <form action="update_product.php" method="POST" enctype="multipart/form-data" autocomplete="off">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES); ?>">


        <div class="form-group">
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
            <input type="hidden" name="current_image" value="<?php echo htmlspecialchars((string) $product['image'], ENT_QUOTES, 'UTF-8'); ?>">

            <p>
                Current Image: <?php echo htmlspecialchars((string) $product['image'], ENT_QUOTES, 'UTF-8'); ?>
            </p>

            <label for="name">Product Name</label><br>
            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" autocomplete="off" required><br><br>

            <label for="category">Category</label><br>

            <select name="category">
                <option value="Meals" <?php if ($product['category'] == 'Meals') {
    echo 'selected';
} ?>>
                    Meals
                </option>

                <option value="Sides" <?php if ($product['category'] == 'Sides') {
    echo 'selected';
} ?>>
                    Sides
                </option>

                <option value="Snacks" <?php if ($product['category'] == 'Snacks') {
    echo 'selected';
} ?>>
                    Snacks
                </option>

                <option value="Drinks" <?php if ($product['category'] == 'Drinks') {
    echo 'selected';
} ?>>
                    Drinks
                </option>

                <option value="Other" <?php if ($product['category'] == 'Other') {
    echo 'selected';
} ?>>
                    Other
                </option>
            </select>
            <br><br>

            <label for="stock">Stock Quantity</label>
            <input
                type="number"
                id="stock"
                name="stock"
                min="0"
                value="<?php echo (int) $product['stock']; ?>"
                placeholder="Enter Stock Quantity"
                required>
            <br>

            <label for="price">Price</label><br>
            <input type="number" step="0.01" name="price"
                value="<?php echo $product['price']; ?>" required>

            <br><br>

            <label for="image">Current Image</label><br>

            <?php
            if (!empty($product['image'])) { ?>
                <img src="./assets/images/products/<?php echo rawurlencode((string) $product['image']); ?>"
                    alt="Product Image" width="120">

                <br><br>

                <small>
                    <?php echo htmlspecialchars((string) $product['image'], ENT_QUOTES, 'UTF-8'); ?>
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

            <label for="description">Description</label><br>
            <textarea name="description" rows="4"><?php echo htmlspecialchars($product['description']); ?>
    </textarea>

            <br><br>
            <div class="form-actions">
                <input type="submit" value="Update Product" class="action-btn">
                <a href="delete_product.php?id=<?php echo (int) $product['id']; ?>" class="action-btn delete-btn">
                    ❌ Deactivate</a>
                <a href="products.php" class="action-btn delete-btn">Cancel</a>
            </div>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>