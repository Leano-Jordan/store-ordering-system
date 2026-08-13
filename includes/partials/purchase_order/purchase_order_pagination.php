<div class="pagination">
<?php if ($page > 1) { ?>
<a href="?page=<?php echo (int) ($page - 1); ?>&search=<?php echo htmlspecialchars($search), ENT_QUOTES, 'UTF-8'; ?>" class="action-btn">
⬅ Previous
</a>
<?php } ?>

<span>
    Page <?php echo (int) $page; ?> of <?php echo (int) $totalPages; ?>
</span>

<?php if ($page < $totalPages) { ?>
    <a href="?page=<?php echo (int) ($page + 1); ?>&search=<?php echo htmlspecialchars($search), ENT_QUOTES, 'UTF-8'; ?>" class="action-btn">
        Next ➡
    </a>
<?php } ?>

</div>