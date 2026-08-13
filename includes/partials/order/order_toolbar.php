<h2>Orders</h2>
<div class="form-group">
<div class="search-area">
<input type="text" 
        id="orderSearch" 
        placeholder="Search Customer Name or Order Number..." 
        value="<?php echo htmlspecialchars($_GET['order'] ?? ''); ?>" 
        onkeyup="searchOrders()">

<select id="orderSort" onchange="sortOrders()">

    <option value="">Sort Orders</option>
    <option value="status"
        <?php if (($_GET['status'] ?? '') === 'Pending') {
    echo 'selected';
} ?>>Status</option>
    <option value="newest">Newest First</option>
    <option value="oldest">Oldest First</option>
    <option value="highest">Highest Total</option>
    <option value="lowest">Lowest Total</option>
</select>
</div>
</div>
<br><br>