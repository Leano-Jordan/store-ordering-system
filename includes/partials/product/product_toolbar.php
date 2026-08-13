<form method="GET" class="search-form">
        <div class="form-group">
<div class="search-area">
        <input type="text"
            name="search"
            id="search"
            placeholder="🔍 Search Products..."
            onkeyup="searchProducts()"
            value="<?php echo htmlspecialchars($search); ?>">

        <?php if ($stockFilter !== '') { ?>

            <input type="hidden" name="stock" value="<?php echo htmlspecialchars($stockFilter); ?>">
        <?php } ?>

        <button type="submit" class="action-btn">
            Search
        </button>

        <a href="products.php" class="action-btn clear-btn">Clear</a>
        </div>
        </div>
    </form>