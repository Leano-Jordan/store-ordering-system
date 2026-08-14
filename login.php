<?php

require_once __DIR__.'/includes/session.php';

require_once 'includes/db.php';
require_once 'includes/session_tracker.php';

if (!isset($conn)) {
    exit('Database connection not established.');
}
require_once 'includes/permissions.php';
require_once 'includes/csrf.php';

$error = '';

$lockoutTime = 300; // 5 minutes
$maxAttempts = 5;

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_login_attempt'] = 0;
}

if (!isset($_SESSION['login_lock_until'])) {
    $_SESSION['login_lock_until'] = 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (time() < $_SESSION['login_lock_until']) {
        $error = 'Too many failed login attempts. Please try again later.';
    } else {
        verifyCsrfToken();

        $username = trim($_POST['username']);
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND status = 'Active'");
        $stmt->bind_param('s', $username);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['login_attempts'] = 0;
                $_SESSION['last_login_attempt'] = 0;
                $_SESSION['login_lock_until'] = 0;

                session_regenerate_id(true);

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
            } else {
                $error = 'Invalid username or password.';

                ++$_SESSION['login_attempts'];
                $_SESSION['last_login_attempt'] = time();

                if ($_SESSION['login_attempts'] >= $maxAttempts) {
                    $_SESSION['login_lock_until'] = time() + $lockoutTime;
                    $error = 'Too many failed login attempts. Please try again later.';
                }
            }
        } else {
            $error = 'Invalid username or password.';

            ++$_SESSION['login_attempts'];
            $_SESSION['last_login_attempt'] = time();

            if ($_SESSION['login_attempts'] >= $maxAttempts) {
                $_SESSION['login_lock_until'] = time() + $lockoutTime;
                $error = 'Too many failed login attempts. Please try again later.';
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