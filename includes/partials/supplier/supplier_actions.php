<?php /** @var array $row */ ?>

                <a href="edit_supplier.php?id=<?php echo (int) $row['id']; ?>" class="action-btn edit-btn">
                    🖋 Edit
                </a>

            <?php if ($row['status'] === 'Active') { ?>
                <form action="deactivate_supplier.php" method="POST" class="inline-form" style="display: inline;">

                    <input type="hidden" name="csrf_token" value="<?php echo csrfToken(); ?>">
                    <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                    <button type="submit" class="action-btn delete-btn" onclick="return confirm('Deactivate this supplier?')">
                        ❌ Deactivate
                    </button>
                </form>
            <?php } else { ?>

                <form action="reactivate_supplier.php" method="POST" class="inline-form" style="display: inline;">

                    <input type="hidden" name="csrf_token" value="<?php echo csrfToken(); ?>">
                    <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                    <button type="submit" class="action-btn reactivate-btn" onclick="return confirm('Deactivate this supplier?')">
                        ✔ Reactivate
                    </button>
                </form>                       
                <?php } ?>
