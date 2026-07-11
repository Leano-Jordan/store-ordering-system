<?php

function logActivity(mysqli $conn, int $userId, string $action): void
{

    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
    $stmt->bind_param("is", $userId, $action);
    $stmt->execute();
    $stmt->close();
}
