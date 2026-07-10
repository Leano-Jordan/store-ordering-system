<?php

if (session_status() === PHP_SESSION_NONE) {

    session_start();
}

require_once "includes/db.php";
require_once "includes/permissions.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND status = 'Active'");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {

            session_regenerate_id(true);
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["full_name"] = $user["full_name"];
            $_SESSION["role"] = $user["role"];

            if (isset($_SESSION["redirect_after_login"])) {
                $redirect = $_SESSION["redirect_after_login"];
                unset($_SESSION["redirect_after_login"]);

                header("Location: " . (strpos($redirect, '/')
                    === 0 && strpos($redirect, '//') !== 0 ? $redirect : "dashboard.php"));
            } else {
                switch ($user["role"]) {

                    case ROLE_KITCHEN:
                        header("Location: orders.php");
                        break;

                    case ROLE_CASHIER:
                    case ROLE_MANAGER:
                    case ROLE_ADMIN:
                    default:
                        header("Location: dashboard.php");
                        break;
                }
            }
            exit();
        } else {

            $error = "Invalid username or password.";
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

        <?php if ($error): ?>
            <p class="error-message"><?php echo $error; ?></p><?php endif; ?>

        <form method="POST" class="login-form">
            <label>Username</label><br>
            <input type="text" name="username" autocomplete="off" required><br><br>

            <label>Password</label><br>
            <input type="password" name="password" autocomplete="off" required><br><br>

            <button type="submit">Login</button>

        </form>

    </div>
</body>

</html>