<?php

require_once __DIR__.'/includes/session.php';
$scriptPath = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/'));
$assetBase = rtrim(dirname($scriptPath), '/');
if ($assetBase === '.' || $assetBase === '') {
    $assetBase = '';
}

require_once 'includes/db.php';
require_once 'includes/session_tracker.php';
require_once 'includes/login_rate_limit.php';

if (!isset($conn)) {
    exit('Database connection not established.');
}
require_once 'includes/permissions.php';
require_once 'includes/csrf.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken();

    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Invalid username or password.';
    } else {
        try {
            if (isLoginRateLimited($conn, $username)) {
                $error = 'Too many failed attempts. Please try again.';
            } else {
                $stmt = $conn->prepare(
                    "SELECT id, full_name, username, password, profile_image, role
                    FROM users
                    WHERE username = ?
                    AND status = 'Active'
                    LIMIT 1"
                );

                if (!$stmt) {
                    throw new RuntimeException('Unable to prepare user lookup: '.$conn->error);
                }

                if (!$stmt->bind_param('s', $username)) {
                    $stmt->close();
                    throw new RuntimeException('Unable to bind user lookup.');
                }

                if (!$stmt->execute()) {
                    $errorMessage = $stmt->error;
                    $stmt->close();
                    throw new RuntimeException('Unable to execute user lookup: '.$errorMessage);
                }

                $result = $stmt->get_result();

                if (!$result) {
                    $errorMessage = $stmt->error;
                    $stmt->close();
                    throw new RuntimeException('Unable to retrieve user lookup result: '.$errorMessage);
                }

                $user = $result->fetch_assoc();
                $stmt->close();

                if (
                    $user === null
                    || !isset($user['password'])
                    || !is_string($user['password'])
                    || !password_verify($password, $user['password'])
                ) {
                    try {
                        recordLoginFailure($conn, $username);
                    } catch (RuntimeException $exception) {
                        error_log(
                            'login.php: Failed to record login failure: '
                            .$exception->getMessage()
                        );
                    }

                    $error = 'Invalid username or password.';
                } else {
                    try {
                        clearLoginFailures($conn, $username);
                    } catch (RuntimeException $exception) {
                        error_log(
                            'login.php: Failed to clear login rate limit: '
                            .$exception->getMessage()
                        );
                    }

                    session_regenerate_id(true);

                    $_SESSION['session_started_at'] = time();
                    $_SESSION['user_id'] = (int) $user['id'];
                    $_SESSION['full_name'] = (string) $user['full_name'];
                    $_SESSION['role'] = (string) $user['role'];
                    $_SESSION['profile_image'] = (string) ($user['profile_image'] ?? '');

                    try {
                        $_SESSION['session_log_id'] = createSessionRecord(
                            $conn,
                            $_SESSION['user_id']
                        );
                        $_SESSION['last_activity'] = time();
                    } catch (RuntimeException $exception) {
                        error_log(
                            'SwiftOrder session record creation failed: '
                            .$exception->getMessage()
                        );

                        $_SESSION = [];
                        session_destroy();

                        $error = 'Unable to sign in. Please try again.';
                    }

                    if ($error === '') {
                        if (isset($_SESSION['redirect_after_login'])) {
                            $redirect = $_SESSION['redirect_after_login'];
                            unset($_SESSION['redirect_after_login']);

                            $safeRedirect = (
                                is_string($redirect)
                                && strpos($redirect, '/') === 0
                                && strpos($redirect, '//') !== 0
                            ) ? $redirect : 'dashboard.php';

                            header('Location: '.$safeRedirect);
                        } elseif ($user['role'] === ROLE_KITCHEN) {
                            header('Location: orders.php');
                        } else {
                            header('Location: dashboard.php');
                        }

                        exit();
                    }
                }
            }
        } catch (RuntimeException $exception) {
            error_log('login.php: Authentication failure: '.$exception->getMessage());

            if ($error === '') {
                $error = 'Unable to sign in. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zazu EMP Login</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($assetBase, ENT_QUOTES, 'UTF-8'); ?>/assets/css/tokens.css?v=20260928-ui3">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($assetBase, ENT_QUOTES, 'UTF-8'); ?>/assets/css/style.css?v=20260928-ui3">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($assetBase, ENT_QUOTES, 'UTF-8'); ?>/assets/css/components/controls.css?v=20260928-ui3">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($assetBase, ENT_QUOTES, 'UTF-8'); ?>/assets/css/components/panels.css?v=20260928-ui3">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($assetBase, ENT_QUOTES, 'UTF-8'); ?>/assets/css/theme.css?v=20260928-ui3">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($assetBase, ENT_QUOTES, 'UTF-8'); ?>/assets/css/zazu-ui-repair.css?v=20260928-ui3">
</head>

<body class="login-page">


    <main class="login-container">

        <h2>Login to SwiftOrder</h2>

        <?php if ($error) { ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p><?php } ?>

        <form method="POST" class="login-form">
            <div class="form-group">
                <label for="login-username">Username</label>
                <input id="login-username" type="text" name="username" autocomplete="username" required>
            </div>

            <div class="form-group">
                <label for="login-password">Password</label>
                <input id="login-password" type="password" name="password" autocomplete="current-password" required>
            </div>

            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken()); ?>">
            <button type="submit">Sign in</button>
        </form>

    </main>
</body>

</html>