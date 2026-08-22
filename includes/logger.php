<?php

function logActivity(mysqli $conn, int $userId, string $action): bool
{
    $stmt = $conn->prepare('INSERT INTO activity_logs (user_id, action) VALUES (?, ?)');

    if (!$stmt) {
        error_log('SwiftOrder logActivity prepare failed: '.$conn->error);

        return false;
    }
    $stmt->bind_param('is', $userId, $action);

    if (!$stmt->execute()) {
        error_log('SwiftOrder logActivity insert failed: '.$stmt->error);

        $stmt->close();

        return false;
    }

    $stmt->close();

    return true;
}
