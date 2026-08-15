<?php

declare(strict_types=1);

require_once __DIR__.'/includes/auth.php';
require_once __DIR__.'/includes/db.php';
require_once __DIR__.'/includes/permissions.php';
require_once __DIR__.'/includes/csrf.php';

requireRole([ROLE_ADMIN, ROLE_MANAGER]);

$stmt = $conn->prepare(
    'SELECT
        us.id,
        us.user_id,
        u.full_name,
        u.username,
        u.role,
        us.login_at,
        us.last_activity_at,
        us.logout_at,
        us.status
    FROM user_sessions AS us
    INNER JOIN users AS u ON u.id = us.user_id
    ORDER BY us.login_at DESC
    LIMIT 100'
);

if (!$stmt) {
    error_log('SwiftOrder sessions query prepare failed: '.$conn->error);
    http_response_code(500);
    exit('An unexpected error occurred.');
}

if (!$stmt->execute()) {
    error_log('SwiftOrder sessions query execute failed: '.$stmt->error);
    $stmt->close();
    http_response_code(500);
    exit('An unexpected error occurred.');
}

$result = $stmt->get_result();
$sessions = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $sessions[] = $row;
    }
}

$stmt->close();

$activeCount = 0;
$loggedOutCount = 0;
$timedOutCount = 0;
$terminatedCount = 0;

foreach ($sessions as $session) {
    switch ($session['status'] ?? '') {
        case 'ACTIVE':
            ++$activeCount;
            break;

        case 'LOGGED_OUT':
            ++$loggedOutCount;
            break;

        case 'TIMED_OUT':
            ++$timedOutCount;
            break;

        case 'TERMINATED':
            ++$terminatedCount;
            break;
    }
}

function swiftOrderSessionStatusClass(string $status): string
{
    switch ($status) {
        case 'ACTIVE':
            return 'session-status-active';

        case 'LOGGED_OUT':
            return 'session-status-logged-out';

        case 'TIMED_OUT':
            return 'session-status-timed-out';

        case 'TERMINATED':
            return 'session-status-terminated';

        default:
            return 'session-status-unknown';
    }
}

function swiftOrderSessionDate(?string $date): string
{
    if ($date === null || $date === '') {
        return '-';
    }

    $timestamp = strtotime($date);

    if ($timestamp === false) {
        return '-';
    }

    return date('d M Y, H:i', $timestamp);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SwiftOrder - Sessions</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<?php require_once __DIR__.'/includes/header.php'; ?>

<main class="sessions-page">

    <section class="sessions-page-header">

        <div>
            <h1>Session Management</h1>

            <p>
                Monitor staff login activity and manage active sessions.
            </p>
        </div>

    </section>

    <section class="sessions-summary">

        <article class="session-summary-card">
            <div class="session-summary-label">
                Active
            </div>

            <div class="session-summary-value">
                <?php echo $activeCount; ?>
            </div>

            <div class="session-summary-description">
                Currently signed in
            </div>
        </article>

        <article class="session-summary-card">
            <div class="session-summary-label">
                Logged Out
            </div>

            <div class="session-summary-value">
                <?php echo $loggedOutCount; ?>
            </div>

            <div class="session-summary-description">
                Normal sign-outs
            </div>
        </article>

        <article class="session-summary-card">
            <div class="session-summary-label">
                Timed Out
            </div>

            <div class="session-summary-value">
                <?php echo $timedOutCount; ?>
            </div>

            <div class="session-summary-description">
                Idle sessions
            </div>
        </article>

        <article class="session-summary-card">
            <div class="session-summary-label">
                Terminated
            </div>

            <div class="session-summary-value">
                <?php echo $terminatedCount; ?>
            </div>

            <div class="session-summary-description">
                Administratively ended
            </div>
        </article>

    </section>

    <section class="sessions-panel">

        <div class="sessions-panel-header">

            <div>
                <h2>Session History</h2>

                <p>
                    Showing the latest 100 recorded sessions.
                </p>
            </div>

        </div>

        <div class="sessions-table-wrapper">

            <table class="sessions-table">

                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Login</th>
                        <th>Last Activity</th>
                        <th>Logout</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                <?php if ($sessions === []) { ?>

                    <tr>
                        <td colspan="7" class="sessions-empty">
                            No session records found.
                        </td>
                    </tr>

                <?php } else { ?>

                    <?php foreach ($sessions as $session) { ?>

                        <?php
                        $userName = (string) ($session['full_name'] ?? '');
                        $role = (string) ($session['role'] ?? '');
                        $status = (string) ($session['status'] ?? '');
                        $userId = (int) ($session['user_id'] ?? 0);
                        $sessionId = (int) ($session['id'] ?? 0);
                        $isCurrentSession = (
                            $userId === (int) $_SESSION['user_id']
                            && $sessionId === (int) ($_SESSION['session_log_id'] ?? 0)
                        );
                        ?>

                        <tr>

                            <td>
                                <div class="session-user">
                                    <strong>
                                        <?php
                                        echo htmlspecialchars(
                            $userName,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                                        ?>
                                    </strong>

                                    <span>
                                        <?php
                                        echo htmlspecialchars(
                                            (string) ($session['username'] ?? ''),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>
                                    </span>
                                </div>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                            $role,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    swiftOrderSessionDate(
                                        $session['login_at'] ?? null
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    swiftOrderSessionDate(
                                        $session['last_activity_at'] ?? null
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    swiftOrderSessionDate(
                                        $session['logout_at'] ?? null
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </td>

                            <td>
                                <span class="<?php
                                    echo htmlspecialchars(
                                    swiftOrderSessionStatusClass($status),
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>">
                                    <?php
                                    echo htmlspecialchars(
                                    $status,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                    ?>
                                </span>
                            </td>

                            <td>

                                <?php if ($status === 'ACTIVE') { ?>

                                    <?php if ($isCurrentSession) { ?>

                                        <span class="session-current">
                                            Current Session
                                        </span>

                                    <?php } else { ?>

                                        <form
                                            method="POST"
                                            action="terminate_session.php"
                                            class="session-action-form"
                                        >

                                            <input
                                                type="hidden"
                                                name="session_id"
                                                value="<?php echo $sessionId; ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?php
                                                    echo htmlspecialchars(
                                        csrfToken(),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                                ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="session-terminate-button"
                                            >
                                                Terminate
                                            </button>

                                        </form>

                                    <?php } ?>

                                <?php } else { ?>

                                    <span class="session-no-action">
                                        -
                                    </span>

                                <?php } ?>

                            </td>

                        </tr>

                    <?php } ?>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </section>

</main>

<?php require_once __DIR__.'/includes/footer.php'; ?>

</body>

</html>

