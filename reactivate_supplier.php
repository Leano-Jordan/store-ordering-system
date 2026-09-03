<?php

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN]);
require_once 'includes/db.php';
require_once 'includes/logger.php';
require_once 'includes/csrf.php';
require_once 'includes/audit.php';

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

$conn->begin_transaction();

try {
    $stmt = $conn->prepare(
        "UPDATE suppliers
        SET status = 'Active' 
        WHERE id = ?
        AND status = 'Inactive'"
    );

    if (!$stmt) {
        throw new RuntimeException('Failed to prepare Supplier Reactivation.');
    }

    if (!$stmt->bind_param('i', $id)) {
        $stmt->close();

        throw new RuntimeException('Failed to bind Supplier Reactivation.');
    }

    if (!$stmt->execute()) {
        $error = $stmt->error;
        $stmt->close();

        throw new RuntimeException('Supplier Reactivation failed: '.$error);
    }

    if ($stmt->affected_rows !== 1) {
        $stmt->close();

        throw new RuntimeException('Supplier was not Inactive.');
    }

    $stmt->close();

    recordAudit(
        $conn,
        (int) $_SESSION['user_id'],
        'supplier',
        $id,
        'REACTIVATE',
        ['status' => ['Inactive', 'Active']]
    );

    if (!logActivity(
        $conn,
        (int) $_SESSION['user_id'],
        'Reactivate supplier ID '.$id
    )) {
        throw new RuntimeException('Failed to record supplier activity.');
    }

    if (!$conn->commit()) {
        throw new RuntimeException('Supplier Reactivation commit failed.');
    }

    $_SESSION['success'] = 'Supplier Reactivated successfully.';

    header('Location: suppliers.php');
    exit();
} catch (Throwable $e) {
    $conn->rollback();

    error_log('reactivate_supplier.php: '.$e->getMessage());

    $_SESSION['error'] = 'Unable to Reactivate supplier. Please try again.';

    header('Location: suppliers.php');
    exit();
}
