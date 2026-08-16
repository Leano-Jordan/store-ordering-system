<?php while ($row =
        $result->fetch_assoc()
    ) { ?>

        <tr>

        <td>
            <?php if (!empty($row['profile_image'])) { ?>
                <img src="assets/images/profiles/<?php echo htmlspecialchars($row['profile_image']); ?>" class="user-avatar" alt="Profile">
            <?php } else { ?>
                <img src="assets/images/profiles/default.png" class="user-avatar" alt="Profile">
            <?php } ?>
        </td>

            <td>
                <?php echo htmlspecialchars($row['full_name'], ENT_QUOTES, 'UTF-8'); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row['role'], ENT_QUOTES, 'UTF-8'); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8'); ?>
            </td>

            <td>
                <?php echo date('d M Y', strtotime($row['created_at'])); ?>
            </td>

            <td>
                <a href="edit_user.php?id=<?php echo (int) $row['id']; ?>" class="action-btn edit-btn">
                    Edit User
                </a>

                <?php if ($row['status'] === 'Active') { ?>

                    <form method="POST" 
                        action="deactivate_user.php" 
                        style="display: inline"
                        onsubmit="return confirm('Deactivate this user?')
                        ">

                    <input 
                        type="hidden" 
                        name="csrf_token" 
                        value="<?php echo htmlspecialchars(
        csrfToken(),
        ENT_QUOTES,
        'UTF-8'
    ); ?>">

                <button type="submit" class="action-btn delete-btn">
                        Deactivate User
                </button>
            </form>

                <?php } else { ?>

                    <form method="POST" action="reactivate_user.php" style="display:inline">
                        <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">

                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                    
                    <button class="action-btn">
                        Reactivate
                </button>
                </form>
                <?php } ?>
            </td>

        </tr>

        <?php } ?>