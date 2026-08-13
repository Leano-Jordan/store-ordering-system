<?php

require_once __DIR__.'/../../auth.php';
require_once __DIR__.'/../../permissions.php';

require_once __DIR__.'/../../logger.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);

require_once __DIR__.'/../../db.php';
?>

<div class="grn-toolbar">

    <form method="GET" class="search-form">
        <input
            type="text"
            name="search"
            placeholder="Search GRN Number, PO Number or Supplier..."
            autocomplete="off"
            value="<?php echo htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8'); ?>">


    <button type="submit" class="action-btn">
    🔍 Search
    </button>

    <?php if (!empty($search)) { ?>
            <a href="goods_received_notes.php" class="action-btn">
                Clear
            </a>
            <?php } ?>
    </form>
</div>
