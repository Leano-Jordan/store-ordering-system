<?php
$orderQueryRaw = $_GET['order'] ?? '';
$orderQuery = is_string($orderQueryRaw)
    ? htmlspecialchars(
        urlencode($orderQueryRaw),
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    )
    : '';
?>
<div class="pagination">
    <?php if ($page > 1) { ?>

        <a href="?page=<?php echo $page - 1; ?>&order=<?php echo $orderQuery; ?>" class="action-btn">
            ⬅ Previous
        </a>
    <?php } ?>

    <span>
        Page <?php echo $page; ?> of <?php echo $totalPages; ?>
    </span>

    <?php if ($page < $totalPages) { ?>

        <a href="?page=<?php echo $page + 1; ?>&order=<?php echo $orderQuery; ?>" class="action-btn">
            Next ➡
        </a>
    <?php } ?>

</div>
