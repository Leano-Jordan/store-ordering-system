<div class="pagination">
    <?php if ($page > 1) { ?>

        <a href="?page=<?php echo $page - 1; ?>&range=<?php echo $range; ?>&search=<?php echo urlencode($search); ?>" class="action-btn">
            ⬅ Previous
        </a>
    <?php } ?>

    <span>
        Page <?php echo $page; ?><?php echo $page; ?>
    </span>

    <?php if ($result->num_rows === $limit) { ?>

        <a href="?page=<?php echo $page + 1; ?>&range=<?php echo $range; ?>&search=<?php echo urlencode($search); ?>" class="action-btn">
            Next ➡
        </a>
    <?php } ?>

</div>