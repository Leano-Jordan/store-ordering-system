
<h2>Activity Logs</h2>

<form method="GET" class="search-form">
<div class="search-area">
    <input type="text" 
        id="search"
        name="search"
        placeholder="🔍 Search activity..."
        value="<?php echo htmlspecialchars($search); ?>">

    <button type="submit" class="action-btn">
        Submit
    </button>

    <a href="activity_logs.php" class="action-btn clear-btn">Clear</a>
    </div>
</form>
<div class="activity-toolbar">
    <div class="chart-filter">
        <a href="?range=7&search=<?php echo urlencode($search); ?>" class="action-btn <?php echo $range === '7' ? 'active-nav' : ''; ?>">7 Days</a>
        <a href="?range=30&search=<?php echo urlencode($search); ?>" class="action-btn <?php echo $range === '30' ? 'active-nav' : ''; ?>">30 Days</a>
        <a href="?range=month&search=<?php echo urlencode($search); ?>" class="action-btn <?php echo $range === 'month' ? 'active-nav' : ''; ?>">This Month</a>
        <a href="?range=year&search=<?php echo urlencode($search); ?>" class="action-btn <?php echo $range === 'year' ? 'active-nav' : ''; ?>">This Year</a>
    </div>

    <div class="activity-legend">

    <div class="product-status">
        <span class="status ready">
            🟢 Added Product
        </span>

        <span class="status preparing">
            🔵 Updated Product
        </span>

        <span class="status pending">
            🟠 Order Changes
        </span>

        <span class="status cancelled">
            🔴 Deactivated Product
        </span>
    </div>

    <div class="PO-status">   
        <span class="status ready">
            🟢 PO Received
        </span>

        <span class="status preparing">
            🔵 PO Created
        </span>

        <span class="status cancelled">
            🔴 PO Cancelled
        </span>
    </div>



    </div>
</div>