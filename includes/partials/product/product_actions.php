
<a href="edit_product.php?id=<?php echo (int) $row['id']; ?>" 
class="action-btn edit-btn">
    🖋 Edit
</a>
                <?php if ($row['status'] === 'Active') { ?>
                    <a href="delete_product.php?id=<?php echo (int) $row['id']; ?>" 
                    class="action-btn delete-btn">
                        ❌ Deactivate
                    </a>
                <?php } else { ?>

                    <form method="POST" action="reactivate_product.php" style="display:inline">
                        <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">

                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                    
                    <button type="submit" class="action-btn reactivate-btn">
                        ✔ Reactivate
                </button>
                </form>
                    <?php } ?>
