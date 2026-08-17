<?php

require_once __DIR__.'/includes/session.php';

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
    } elseif (isLoginRateLimited($conn, $username)) {
        $error = 'Too many failed attempts. Please try again.';
    } else {
        $stmt = $conn->prepare(
            "SELECT * FROM users 
            WHERE username = ? 
            AND status = 'Active'
            "
        );

        if (!$stmt) {
            error_log(
                'login.php: Failed to prepare user lookup: '
                .$conn->error
            );

            $error = 'Unable to sign in. Please try again.';
        } elseif (!$stmt->bind_param('s', $username)) {
            error_log(
                'login.php: Failed to bind user lookup: '
                .$stmt->error
            );

            $stmt->close();

            $error = 'Unable to sign in. Please try again.';
        } else {
            $result = $stmt->get_result();

            if (!$result) {
                error_log(
                    'login.php: Failed to retrieve user lookup result: '
                    .$stmt->error
                );

                $stmt->close();

                $error = 'Unable to sign in. Please try again.';
            } elseif ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                $stmt->close();

                if (password_verify($password, $user['password'])) {
                    clearLoginFailures($conn, $username);

                    session_regenerate_id(true);

                    $_SESSION['session_started_at'] = time();
                    $_SESSION['user_id'] = $user['id'] ?? '';
                    $_SESSION['full_name'] = $user['full_name'] ?? '';
                    $_SESSION['role'] = $user['role'] ?? '';
                    $_SESSION['profile_image'] = $user['profile_image'] ?? '';

                    try {
                        $_SESSION['session_log_id'] = createSessionRecord($conn, (int) $_SESSION['user_id']);
                        $_SESSION['last_activity'] = time();
                    } catch (RuntimeException $exception) {
                        error_log('SwiftOrder session record creation failed: '.$exception->getMessage());

                        $_SESSION = [];
                        session_destroy();

                        header('Location: login.php');
                        exit();
                    }

                    if (isset($_SESSION['redirect_after_login'])) {
                        $redirect = $_SESSION['redirect_after_login'];
                        unset($_SESSION['redirect_after_login']);

                        header('Location: '.(strpos($redirect, '/')
                    === 0 && strpos($redirect, '//') !== 0 ? $redirect : 'dashboard.php'));
                    } else {
                        switch ($user['role']) {
                    case ROLE_KITCHEN:
                        header('Location: orders.php');
                        break;

                    case ROLE_CASHIER:
                    case ROLE_MANAGER:
                    case ROLE_ADMIN:
                    default:
                        header('Location: dashboard.php');
                        break;
                    }
                    }

                    exit();
                }

                recordLoginFailure(
                    $conn,
                    $username
                );

                $error = 'Invalid username or password.';
            } else {
                $stmt->close();

                recordLoginFailure(
                    $conn,
                    $username
                );

                $error = 'Invalid username or password.';
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
    <title>SwiftOrder Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>


    <div class="login-container">

        <h2>Login to SwiftOrder</h2>

        <?php if ($error) { ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p><?php } ?>

        <form method="POST" class="login-form">
            <label>Username</label><br>
            <input type="text" name="username" autocomplete="off" required><br><br>

            <label>Password</label><br>
            <input type="password" name="password" autocomplete="off" required><br><br>

            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken()); ?>">
            <button type="submit">Login</button>

        </form>

    </div>
</body>

</html>