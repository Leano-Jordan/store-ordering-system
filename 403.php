<?php
http_response_code(403);
require_once "includes/auth.php";
include "includes/header.php";
?>

<div class="container">
    <h2>Access Denied</h2>
    <p>You don't have permission to access this page.</p>

    <a href="dashboard.php" class="btn">Return to Dashboard</a>
</div>

<?php include "includes/footer.php" ?>