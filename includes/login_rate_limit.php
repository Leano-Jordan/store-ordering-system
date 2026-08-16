<?php

declare(strict_types=1);

function loginRateLimitKey(string $username): string
{
    return hash('sha256', strtolower(trim($username)));
}

function getLoginRateLimit(
    mysqli $conn,
    string $username
): ?array {
    $usernameHash = loginRateLimitKey($username);

    $stmt = $conn->prepare(
        'SELECT username_hash, window_started_at, failed_attempts, locked_until
        FROM login_rate_limits
        WHERE username_hash = ?'
    );

    if (!$stmt) {
        throw new RuntimeException('Unable to prepare login rate limit lookup.');
    }

    $stmt->bind_param('s', $usernameHash);

    if (!$stmt->execute()) {
        $stmt->close();

        throw new RuntimeException('Unable to execute login rate limit lookup.');
    }

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $stmt->close();

    return $row ?: null;
}

function isLoginRateLimited(
    mysqli $conn,
    string $username
): bool {
    $row = getLoginRateLimit($conn, $username);

    if ($row === null || $row['locked_until'] === null) {
        return false;
    }

    return strtotime($row['locked_until']) > time();
}

function recordLoginFailure(
    mysqli $conn,
    string $username
): void {
    $usernameHash = loginRateLimitKey($username);
    $now = date('Y-m-d H:i:s');

    $sql = '
        INSERT INTO login_rate_limits
            (
                username_hash,
                window_started_at,
                failed_attempts,
                locked_until
            )
        VALUES
            (?, ?, 1, NULL)
        ON DUPLICATE KEY UPDATE
            failed_attempts = failed_attempts + 1,
            locked_until = CASE
                WHEN failed_attempts + 1 >= 5
                THEN DATE_ADD(NOW(), INTERVAL 5 MINUTE)
                ELSE locked_until
            END
    ';

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new RuntimeException('Unable to prepare login failure update.');
    }

    $stmt->bind_param(
        'ss',
        $usernameHash,
        $now
    );

    if (!$stmt->execute()) {
        $stmt->close();

        throw new RuntimeException('Unable to record login failure.');
    }

    $stmt->close();
}

function clearLoginFailures(
    mysqli $conn,
    string $username
): void {
    $usernameHash = loginRateLimitKey($username);

    $stmt = $conn->prepare(
        'DELETE FROM login_rate_limits
        WHERE username_hash = ?'
    );

    if (!$stmt) {
        throw new RuntimeException('Unable to prepare login rate limit reset.');
    }

    $stmt->bind_param('s', $usernameHash);

    if (!$stmt->execute()) {
        $stmt->close();

        throw new RuntimeException('Unable to reset login rate limit.');
    }

    $stmt->close();
}
