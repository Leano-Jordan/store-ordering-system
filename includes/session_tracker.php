<?php

declare(strict_types=1);

/**
 * CREATE A DATABASE RECORD FOR NEWLY AUTHENTICATED PHP SESSION.
 *
 * @throws RuntimeException when session created
 */
function createSessionRecord(mysqli $conn, int $userId): int
{
    $stmt = $conn->prepare('INSERT INTO user_sessions (user_id, login_at, last_activity_at, status) VALUES (?, NOW(), NOW(), ?)');

    if (!$stmt) {
        error_log('SwiftOrder session create prepare failed: '.$conn->error);
        throw new RuntimeException('Unable to create session record.');
    }

    $status = 'ACTIVE';

    if (!$stmt->bind_param('is', $userId, $status)) {
        error_log('SwiftOrder session create bind failed: '.$stmt->error);
        $stmt->close();
        throw new RuntimeException('Unable to create session record.');
    }

    if (!$stmt->execute()) {
        error_log('SwiftOrder session create execute failed: '.$stmt->error);
        $stmt->close();
        throw new RuntimeException('Unable to create session record.');
    }

    $sessionId = $stmt->insert_id;
    $stmt->close();

    if ($sessionId < 1) {
        error_log('SwiftOrder session create returned invalid session ID.');
        throw new RuntimeException('Unable to create session record.');
    }

    return $sessionId;
}

/*
 * refresh activity for currently tracked authenticated sessions.
 *
 * @return bool True when the update statement executes successfully.
 */
function touchSessionRecord(mysqli $conn, int $sessionId, int $userId): bool
{
    $stmt = $conn->prepare('UPDATE user_sessions SET last_activity_at = NOW() WHERE id = ? AND user_id = ? AND status = \'ACTIVE\'');

    if (!$stmt) {
        error_log('SwiftOrder session touch prepare failed: '.$conn->error);

        return false;
    }

    if (!$stmt->bind_param('ii', $sessionId, $userId)) {
        error_log('SwiftOrder session touch bind failed: '.$stmt->error);
        $stmt->close();

        return false;
    }

    if (!$stmt->execute()) {
        error_log('SwiftOrder session touch execute failed: '.$stmt->error);
        $stmt->close();

        return false;
    }

    $stmt->close();

    return true;
}

/**
 * Close an active tracked session.
 *
 * $return bool True when the update statement executes successfully.
 */
function closeSessionRecord(mysqli $conn, int $sessionId, int $userId, string $status): bool
{
    $allowedStatuses = [
        'LOGGED_OUT',
        'TIMED_OUT',
        'TERMINATED',
    ];

    if (!in_array($status, $allowedStatuses, true)) {
        error_log('SwiftOrder session close rejected invalid status.');

        return false;
    }

    $stmt = $conn->prepare(
        'UPDATE user_sessions SET logout_at = NOW(), 
        last_activity_at = NOW(), 
        status = ? 
        WHERE id = ? 
        AND user_id = ? 
        AND status = \'ACTIVE\''
    );

    if (!$stmt) {
        error_log('SwiftOrder session close prepare failed: '.$conn->error);

        return false;
    }

    if (!$stmt->bind_param('sii', $status, $sessionId, $userId)) {
        error_log('SwiftOrder session close bind failed: '.$stmt->error);
        $stmt->close();

        return false;
    }

    if (!$stmt->execute()) {
        error_log('SwiftOrder session close execute failed: '.$stmt->error);
        $stmt->close();

        return false;
    }

    $stmt->close();

    return true;
}
