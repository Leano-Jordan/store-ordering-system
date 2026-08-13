<div class="pagination">
    <?php if ($page > 1) { ?>
        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&filter=<?php echo urlencode($filter); ?>" class="action-btn">
            ⬅ Previous
        </a>
    <?php } ?>

    <span>Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>

    <?php if ($page < $totalPages) { ?>
        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&filter=<?php echo urlencode($filter); ?>" class="action-btn">
            Next ➡
        </a>
    <?php } ?>
</div>