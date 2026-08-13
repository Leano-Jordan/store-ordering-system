<?php

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN]);
require_once 'includes/db.php';
require_once 'includes/logger.php';
require_once 'includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: suppliers.php');
    exit();
}

verifyCsrfToken();

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    header('Location: suppliers.php');
    exit();
}

$stmt = $conn->prepare("
UPDATE suppliers
SET status = 'Active'
WHERE id = ?
");

if (!$stmt) {
    error_log('reactivate_supplier.php: Failed to prepare supplier reactivation: '.$conn->error);
    exit('Unable to reactivate supplier.');
}

$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    logActivity($conn, $_SESSION['user_id'], 'Reactivated supplier ID '.$id);
    header('Location: suppliers.php');
    exit();
}

exit('Unable to reactivate supplier.');
