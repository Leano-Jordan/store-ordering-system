<?php
require_once "includes/db.php";

$loadScript = true;

include "includes/header.php";

$sql = "SELECT * FROM products 
WHERE status ='Active' 
ORDER BY category ASC,
stock = 0 ASC,
name ASC
";

$result = $conn->query($sql);

?>

<h1>
    SwiftOrder POS
</h1>

<div class="main-container">
    <div class="left-panel">

        <div class="search-area">

            <input type="text" id="search"
                placeholder="Search meals, drinks, sides, snacks or other items"
                onkeyup="searchProducts()">
            <button id="clear-search" onclick="clearSearch()">Clear</button>

        </div>

        <div class="category-filter" id="category-filter">
            <button class="category-btn active" onclick="filterProducts('All', this)">📃All</button>
            <button class="category-btn" onclick="filterProducts('Meals', this)">🍔Meals</button>
            <button class="category-btn" onclick="filterProducts('Drinks', this)">🥤Drinks</button>
            <button class="category-btn" onclick="filterProducts('Sides', this)">🍟Sides</button>
            <button class="category-btn" onclick="filterProducts('Snacks', this)">🍬Snacks</button>
            <button class="category-btn" onclick="filterProducts('Other', this)">👓Other</button>
        </div>

        <div class="products">

            <?php while ($row = $result->fetch_assoc()) { ?>

                <div class="product" data-category="<?= htmlspecialchars($row['category']) ?>">

                    <img src="./assets/images/products/<?= htmlspecialchars($row['image']) ?>"
                        alt="<?= htmlspecialchars($row['name']) ?>" class="product-image">

                    <h2><?= htmlspecialchars($row['name']) ?></h2>
                    <p><?= htmlspecialchars($row['description']) ?></p>
                    <p>R<?= number_format($row['price'], 2) ?></p>

                    <?php if ($row["stock"] > 0) { ?>
                        <button onclick="addToCart(
                    <?= $row['id'] ?>, 
                    '<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>', 
                    <?= $row['price'] ?>)">
                            Order Now
                        </button>

                    <?php } else { ?>

                        <button class="out-of-stock-btn" disabled>
                            🔴 Out of Stock
                        </button>

                    <?php } ?>

                </div>

            <?php } ?>
        </div>
    </div>
    <div class="cart-section">

        <h2 id="cart-title">
            🛒Cart
        </h2>

        <p id="cart">
            Items: 0
        </p>

        <div id="cart-items">

        </div>

        <p id="total">
            Total: R0.00
        </p>
        <br>

        <input type="text" id="customer" placeholder="Your Name">

        <button id="placeOrderBtn" onclick="placeOrder()">Place Order</button>

        <br><br>

        <button onclick="clearCart()">Clear Cart</button>

    </div>
</div>

<?php include "includes/footer.php"; ?>