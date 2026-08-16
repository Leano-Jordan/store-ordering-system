<?php
require_once 'includes/auth.php';
require_once 'includes/permissions.php';
require_once 'includes/db.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER, ROLE_CASHIER]);

$vatEnabled = false;
$vatRate = 0.00;

$vatStmt = $conn->prepare(
    'SELECT vat_enabled, vat_rate 
    FROM business_settings 
    ORDER BY id ASC 
    LIMIT 1'
);

if ($vatStmt) {
    if ($vatStmt->execute()) {
        $vatResult = $vatStmt->get_result();

        if ($vatResult) {
            $vatSettings = $vatResult->fetch_assoc();

            if ($vatSettings) {
                $vatEnabled = (int) $vatSettings['vat_enabled'] === 1;
                $vatRate = max(
                    0.00,
                    (float) $vatSettings['vat_rate']
                );
            }
        }
    } else {
        error_log(
            'index.php: Failed to load VAT settings: '.$vatStmt->error
        );
    }

    $vatStmt->close();
} else {
    error_log(
        'index.php: Failed to prepare VAT settings lookup: '.$conn->error
    );
}

$loadScript = true;

$sql = "SELECT id, name, description, price, image, category, stock FROM products 
WHERE status ='Active' 
ORDER BY category ASC,
stock = 0 ASC,
name ASC
";

$result = $conn->query($sql);

$catResult = $conn->query(
    "SELECT DISTINCT category 
    FROM products 
    WHERE status = 'Active' 
    ORDER BY category 
    ASC"
);

$categories = [];
while ($cat = $catResult->fetch_assoc()) {
    $categories[] = $cat['category'];
}
include 'includes/header.php';
?>

<h1>
    SwiftOrder POS
</h1>

<div class="main-container">
    <div class="left-panel">

        <div class="search-area">

            <input type="text" 
                id="search"
                placeholder="🔍 Search for a product..."
                autocomplete="off"
                onkeyup="searchProducts()">
            <button id="clear-search"
            class="action-btn clear-btn" 
            onclick="clearSearch()">Clear</button>

        </div>

        <div class="category-filter" id="category-filter">
            <button class="category-btn active" onclick="filterProducts('All', this)">
                📃 All
            </button>
            <?php foreach ($categories as $cat) { ?>
                <button class="category-btn" onclick="filterProducts('<?php echo htmlspecialchars($cat, ENT_QUOTES); ?>', this)">
                    <?php echo htmlspecialchars($cat); ?>
                </button>
            <?php } ?>
        </div>

        <div class="products">

            <?php while ($row = $result->fetch_assoc()) { ?>

                <div class="product" data-category="<?php echo htmlspecialchars($row['category']); ?>">

                    <img src="./assets/images/products/<?php echo htmlspecialchars($row['image']); ?>"
                        alt="<?php echo htmlspecialchars($row['name']); ?>" class="product-image">
                
                <div class="product-info">
                    <h2><?php echo htmlspecialchars($row['name']); ?></h2>
                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                </div>

                <div class="product-footer">
                    <p>R<?php echo number_format($row['price'], 2); ?></p>

                    <?php if ($row['stock'] > 0) { ?>
                        <button class="add-to-cart" onclick="addToCart(
                    <?php echo $row['id']; ?>, 
                    '<?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?>', 
                    <?php echo $row['price']; ?>,
                    '<?php echo htmlspecialchars($row['image'], ENT_QUOTES); ?>')">
                            + Add
                        </button>

                    <?php } else { ?>

                        <button class="out-of-stock-btn" disabled>
                            🔴 Out of Stock
                        </button>

                    <?php } ?>

                </div>
                </div>

            <?php } ?>
        </div>
    </div>
    
    <!-- =================== CART PANEL =================== -->
    <div class="cart-section">

<div class="payment-method-tabs">
    <button class="payment-method-btn active" onclick="setPaymentMethod('cash_pmt', this)">Cash</button>

    <button class="payment-method-btn" onclick="setPaymentMethod('card_pmt', this)">Card</button>

    <button class="payment-method-btn" onclick="setPaymentMethod('eft_pmt', this)">EFT</button>
</div>
<input type="hidden" id="payment-method" value="cash_pmt">

<div class="cart-body">
    <div id="cart-items"></div>
</div>

<div class="cart-footer">

    <div class="cart-summary">
        
        <div class="summary-row">
            <span>Cart:</span>
        <strong>
        <span id="cart-badge">0</span>
        </div>
    </strong>

        <div class="summary-row">
                <span>
                    VAT Incl.
                    <?php if ($vatEnabled) { ?>
                        (
                            <?php echo number_format($vatRate, 2); ?>%
                            ):
                    <?php } else { ?>
                            :
                    <?php } ?>
                </span>
            <strong>

            <span id="vat"
                data-vat-enabled="<?php echo $vatEnabled ? '1' : 0; ?>"
                data-vat-rate="<?php echo htmlspecialchars(number_format($vatRate, 2, '-', ''), ENT_QUOTES, 'UTF-8');
                ?>">R0.00</span>

    </strong>
        </div>

        <div class="summary-row summary-total">
            <span>Total:</span>
            <span id="total">R0.00</span>
        </div>

        </div>
    </div>

    <input type="text" id="customer" placeholder="Customer name" autocomplete="off">
<div class="cart-actions">
    <button id="placeOrderBtn" class="place-order-btn" onclick="placeOrder()">
        Place Order
    </button>
    <button class="clear-cart-btn" onclick="clearCart()">
        Clear Cart
    </button>
    </div>
</div>

<!-- =================== / CART PANEL =================== -->
</div>
</div>
<input type="hidden" id="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES); ?>">
<?php include 'includes/footer.php'; ?>