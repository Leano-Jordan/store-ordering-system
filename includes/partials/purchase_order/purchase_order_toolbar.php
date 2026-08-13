<div class="report-actions">

    <form method="GET" class="search-form">
        <div class="form-group">
<div class="search-area">
        <input
            type="text"
            name="search"
            placeholder="Search PO Number or Supplier..."
            value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">


    <button type="submit" class="action-btn">
    🔍 Search
    </button>

            <a href="purchase_orders.php" class="action-btn clear-btn">
                Clear
            </a>
            </div>
        </div>
    </form>
</div>