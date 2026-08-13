<?php
function renderOrderAction(array $row): void
{
    if ($row['status'] === 'Pending' && in_array($_SESSION['role'], [ROLE_ADMIN, ROLE_MANAGER, ROLE_KITCHEN])) {?>
    
    <form action="<?php echo defined('AJAX_REQUEST') ?
    '../update_status.php' :
    'update_status.php'; ?>" 
    method="POST" 
    class="inline-form">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES); ?>">

        
        <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
        <input type="hidden" name="status" value="Preparing">
        <input type="hidden" name="return_to" value="orders">
        
        <button type="submit" class="action-btn pending-btn">
            Prepare
            </button>
    </form>
<?php
    } elseif ($row['status'] === 'Preparing' && in_array($_SESSION['role'], [ROLE_ADMIN, ROLE_MANAGER, ROLE_KITCHEN])) {
        ?>

    <form action="<?php echo defined('AJAX_REQUEST') ?
        '../update_status.php' :
        'update_status.php'; ?>" 
        method="POST" 
        class="inline-form">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES); ?>">

            
            <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
            <input type="hidden" name="status" value="Ready">
            <input type="hidden" name="return_to" value="orders">
        <button type="submit" class="action-btn preparing-btn">
            Ready
        </button>
    </form>
    
    <?php
    } elseif ($row['status'] === 'Ready' && in_array($_SESSION['role'], [ROLE_ADMIN, ROLE_MANAGER, ROLE_CASHIER])) { ?>
    
    <form action="<?php echo defined('AJAX_REQUEST') ?
        '../update_status.php' :
        'update_status.php'; ?>" 
        method="POST" 
        class="inline-form">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES); ?>">
            
            <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
            <input type="hidden" name="status" value="Collected">
            <input type="hidden" name="return_to" value="orders">
    
        <button type="submit" class="action-btn ready-btn">
            Collect
        </button>
    </form>
            <?php
    } else {
        echo '_';
    }
}
