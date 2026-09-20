<?php

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN]);
require_once 'includes/db.php';
require_once 'includes/logger.php';
require_once 'includes/csrf.php';
require_once 'includes/audit.php';
require_once __DIR__.'/includes/licensing/license_gate.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: suppliers.php');
    exit();
}

verifyCsrfToken();
requireActiveLicense($conn);

$idRaw = $_POST['id'] ?? null;

if (!is_string($idRaw)) {
    header('Location: suppliers.php');
    exit();
}

$id = filter_var($idRaw, FILTER_VALIDATE_INT);

if ($id === false || $id <= 0) {
    header('Location: suppliers.php');
    exit();
}

$transactionStarted = false;

try {
    if (!$conn->begin_transaction()) {
        throw new RuntimeException('Failed to begin supplier deactivation transaction: '.$conn->error);
    }

    $transactionStarted = true;

    $stmt = $conn->prepare(
        "UPDATE suppliers
        SET status = 'Inactive' 
        WHERE id = ?
        AND status = 'Active'"
    );

    if (!$stmt) {
        throw new RuntimeException('Failed to prepare Supplier Deactivation.');
    }

    if (!$stmt->bind_param('i', $id)) {
        $stmt->close();

        throw new RuntimeException('Failed to bind Supplier Deactivation.');
    }

    if (!$stmt->execute()) {
        $error = $stmt->error;
        $stmt->close();

        throw new RuntimeException('Supplier Deactivation failed: '.$error);
    }

    if ($stmt->affected_rows !== 1) {
        $stmt->close();

        throw new RuntimeException('Supplier was not active.');
    }

    $stmt->close();

    recordAudit(
        $conn,
        (int) $_SESSION['user_id'],
        'supplier',
        $id,
        'DEACTIVATE',
        ['status' => ['Active', 'Inactive']]
    );

    if (!logActivity(
        $conn,
        (int) $_SESSION['user_id'],
        'Deactivate supplier ID '.$id
    )) {
        throw new RuntimeException('Failed to record supplier activity.');
    }

    if (!$conn->commit()) {
        throw new RuntimeException('Supplier Deactivation commit failed.');
    }

    $_SESSION['success'] = 'Supplier Deactivated Successfully.';

    header('Location: suppliers.php');
    exit();
} catch (Throwable $e) {
    if ($transactionStarted) {
        $conn->rollback();
    }

    error_log('deactivate_supplier.php: '.$e->getMessage());

    $_SESSION['error'] = 'Unable to Deactivate Supplier. Please try again.';

    header('Location: suppliers.php');
    exit();
}
