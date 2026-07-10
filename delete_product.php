<?php
require_once "includes/auth.php";
require_once "includes/permissions.php";
requireRole([ROLE_ADMIN]);
require_once "includes/db.php";

/************ ************** DELETE PRODUCT *********** *****************/

$id = (int)$_GET["id"];
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

include "includes/header.php"; ?>

<h2>
    Delete Product
</h2>

<p>
    Are you sure you want to deactivate
    <strong>
        <?php echo htmlspecialchars($product["name"]); ?>
    </strong>?
</p>

<form action="confirm_delete.php" method="POST">
    <input type="hidden" name="id" value="<?php echo $product["id"]; ?>">

    <button type="submit" class="action-btn delete-btn">
        🗑 Yes, Deactivate
    </button>

    <a href="products.php" class="action-btn">
        Cancel
    </a>
</form>

<?php include "includes/footer.php" ?>