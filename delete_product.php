<?php
require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN]);
require_once 'includes/db.php';
require_once 'includes/logger.php';
require_once 'includes/csrf.php';

/************ ************** DELETE PRODUCT *********** *****************/

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: products.php');
    exit();
}

$sql = 'SELECT * FROM products WHERE id=?';

$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log('delete_product.php: Failed to prepare product lookup: '.$conn->error);
    $_SESSION['error'] = 'Unable to load the product. Please try again.';
    header('Location: products.php');
    exit();
}

if (!$stmt->bind_param('i', $id)) {
    error_log('delete_product.php: Failed to bind product ID: '.$stmt->error);
    $stmt->close();
    $_SESSION['error'] = 'Unable to load the product. Please try again.';
    header('Location: products.php');
    exit();
}

if (!$stmt->execute()) {
    error_log('delete_product.php: Failed to execute product lookup: '.$stmt->error);
    $stmt->close();
    $_SESSION['error'] = 'Unable to load the product. Please try again.';
    header('Location: products.php');
    exit();
}

$result = $stmt->get_result();

if (!$result) {
    error_log('delete_product.php: Failed to retrieve product result: '.$stmt->error);
    $stmt->close();
    $_SESSION['error'] = 'Unable to load the product. Please try again.';
    header('Location: products.php');
    exit();
}

$product = $result->fetch_assoc();

$stmt->close();

if (!$product) {
    header('Location: products.php');
    exit();
}

include 'includes/header.php'; ?>

<h2>
    Deactivate Product
</h2>

<p>
    Are you sure you want to deactivate
    <strong>
        <?php echo htmlspecialchars($product['name']); ?>
    </strong>?
</p>

<form action="confirm_delete.php" method="POST">
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES); ?>">

    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

    <button type="submit" class="action-btn delete-btn">
        🗑 Yes, Deactivate
    </button>

    <a href="products.php" class="action-btn">
        Cancel
    </a>
</form>

<?php include 'includes/footer.php'; ?>