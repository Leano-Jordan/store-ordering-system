<?php
require_once "includes/auth.php";
require_once "includes/permissions.php";
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once "includes/db.php";

include "includes/header.php";

?>

<h2>Add Product</h2>

<form action="save_product.php"
    method="POST"
    enctype="multipart/form-data"
    autocomplete="off">

    <label for="name">Product Name</label>
    <input type="text" id="name" name="name" required><br><br>

    <label for="category">Category</label><br>
    <select name="category" id="category" required>

        <option value="Meals">Meals</option>
        <option value="Sides">Sides</option>
        <option value="Snacks">Snacks</option>
        <option value="Drinks">Drinks</option>
        <option value="Other">Other</option>

    </select><br><br>

    <label for="price">Price</label><br>
    <input type="number" name="price" step="0.01" required><br><br>

    <label for="image">Product Image</label><br>
    <input type="file" name="image" required accept="image/*"><br><br>

    <?php if (isset($_GET["error"]) && $_GET["error"] === "invalid_image") {
        echo '<p class="error-message">
                Only JPG, JPEG and PNG images are allowed.
            </p>';
    }
    ?>

    <label for="description">Description</label><br>
    <textarea name="description" rows="4" required></textarea>

    <br><br>

    <input type="submit" value="Save Product" class="action-btn">

</form>
<?php include "includes/footer.php" ?>