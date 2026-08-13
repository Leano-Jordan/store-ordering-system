<div class="filter-buttons">
        <a href="?filter=" class="action-btn <?php echo $filter === '' ? 'active-nav' : ''; ?>">All</a>
        <a href="?filter=increase" class="action-btn <?php echo $filter === 'increase' ? 'active-nav' : ''; ?>">🟢 Increases</a>
        <a href="?filter=decrease" class="action-btn <?php echo $filter === 'decrease' ? 'active-nav' : ''; ?>">🔴 Decreases</a>
        <a href="?filter=po" class="action-btn <?php echo $filter === 'po' ? 'active-nav' : ''; ?>">📦 PO Receipts</a>
        <a href="?filter=order" class="action-btn <?php echo $filter === 'order' ? 'active-nav' : ''; ?>">🛒 Order Sales</a>
        <a href="?filter=manual" class="action-btn <?php echo $filter === 'manual' ? 'active-nav' : ''; ?>">📖 Manual</a>
        <a href="?filter=today" class="action-btn <?php echo $filter === 'today' ? 'active-nav' : ''; ?>">📆 Today</a>
    </div>