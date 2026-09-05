<div class="table-container">
    <table class="orders-table">

        <thead>
            <tr>
                <th>Date & Time</th>
                <th>User</th>
                <th>Role</th>
                <th>Activity</th>
            </tr>
        </thead>

        <tbody>

            <?php while ($row = $result->fetch_assoc()) {
        require 'activity_log_row.php';
    } ?>

        </tbody>

    </table>
</div>