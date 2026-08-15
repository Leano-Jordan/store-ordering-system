<?php

declare(strict_types=1);

require_once __DIR__.'/includes/auth.php';
require_once __DIR__.'/includes/db.php';
require_once __DIR__.'/includes/permissions.php';
require_once __DIR__.'/includes/csrf.php';
require_once __DIR__.'/includes/session_tracker.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Method not Allowed.');
}

verifyCsrfToken();

requireRole([ROLE_ADMIN]);

$sessionId = filter_input(INPUT_POST, 'session_id', FILTER_VALIDATE_INT);

$currentSessionId = $_SESSION['session_log_id'] ?? null;

if (is_int($currentSessionId) && $currentSessionId === $sessionId) {
    http_response_code(400);
    exit('You cannot terminate your current session.');
}

$stmt = $conn->prepare('SELECT id, user_id FROM user_sessions WHERE id = ? AND status =\'ACTIVE\' LIMIT 1');

if (!$stmt) {
    error_log('SwiftOrder session termination prepare failed: '.$conn->error);
    http_response_code(500);
    exit('An unexpected error occurred.');
}

$stmt->bind_param('i', $sessionId);

if (!$stmt->execute()) {
    error_log('SwiftOrder session termination execute failed: '.$stmt->error);
    $stmt->close();
    http_response_code(500);
    exit('An unexpected error occurred.');
}

$result = $stmt->get_result();

if (!$result || $result->num_rows !== 1) {
    $stmt->close();
    http_response_code(400);
    exit('Active session not found.');
}

$session = $result->fetch_assoc();
$stmt->close();

$targetUserId = filter_var($session['user_id'] ?? null, FILTER_VALIDATE_INT);

if ($targetUserId === false || $targetUserId < 1) {
    http_response_code(500);
    exit('Invalid session owner.');
}

if (!closeSessionRecord($conn, $sessionId, $targetUserId, 'TERMINATED')) {
    error_log('SwiftOrder failed to terminate session ID '.$sessionId);

    http_response_code(500);
    exit('Unable to terminate session.');
}

header('Location: users.php');
exit();
