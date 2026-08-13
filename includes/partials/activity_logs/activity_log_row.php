<tr>
                <td>
                    <?php echo date('d F Y H:i', strtotime($row['created_at'])); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['username']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['role']); ?>
                </td>

                <td>

                    <?php

                    $badgeClass = 'status-pending';

    if (strpos($row['action'], 'Added') === 0) {
        $badgeClass = 'ready';
    } elseif (strpos($row['action'], 'Updated') === 0) {
        $badgeClass = 'preparing';
    } elseif (strpos($row['action'], 'Changed') === 0) {
        $badgeClass = 'pending';
    } elseif (strpos($row['action'], 'Deactivated') === 0) {
        $badgeClass = 'cancelled';
    } elseif (strpos($row['action'], 'Deleted') === 0) {
        $badgeClass = 'cancelled';
    } elseif (strpos($row['action'], 'Draft') === 0) {
        $badgeClass = 'draft';
    } elseif (strpos($row['action'], 'Received') === 0) {
        $badgeClass = 'ready';
    } elseif (strpos($row['action'], 'Created') === 0) {
        $badgeClass = 'preparing';
    } elseif (strpos($row['action'], 'Cancelled') === 0) {
        $badgeClass = 'cancelled';
    } else {
        $badgeClass = 'pending';
    } ?>

                    <span class="status <?php echo $badgeClass; ?>">
                        <?php echo htmlspecialchars($row['action']); ?>
                    </span>
                </td>
            </tr>