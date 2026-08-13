<?php
// Initialize variables with default values
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$stockFilter = isset($_GET['stock']) ? $_GET['stock'] : '';
$totalPages = 10; // Example value, replace with actual total pages logic
?>

<div class="pagination">
    <?php if ($page > 1) { ?>

        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&stock=<?php echo urlencode($stockFilter); ?>" class="action-btn">
            ⬅ Previous
        </a>
    <?php } ?>

    <span>
        Page <?php echo $page; ?> of <?php echo $totalPages; ?>
    </span>

    <?php if ($page < $totalPages) { ?>

        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&stock=<?php echo urlencode($stockFilter); ?>" class="action-btn">
            Next ➡
        </a>

    <?php } ?>
</div>