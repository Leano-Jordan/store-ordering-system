<?php

if (!empty($_SESSION['success'])) { ?>

<div class="success-message">
    <?php echo htmlspecialchars($_SESSION['success']);

    unset($_SESSION['success']); ?>
</div>

<?php }

if (!empty($_SESSION['error'])) { ?>

<div class="error-message">
    <?php echo htmlspecialchars($_SESSION['error']);

    unset($_SESSION['error']); ?>
</div>

<?php }
