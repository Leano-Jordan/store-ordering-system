<?php

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once 'includes/db.php';
require_once 'includes/logger.php';
require_once 'includes/audit.php';
require_once __DIR__.'/includes/licensing/license_gate.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: purchase_orders.php');
    exit();
}

require_once 'includes/csrf.php';
verifyCsrfToken();
requireActiveLicense($conn);

$idRaw = $_POST['id'] ?? null;

if (!is_string($idRaw)) {
    header('Location: purchase_orders.php');
    exit();
}

$id = filter_var($idRaw, FILTER_VALIDATE_INT);

if ($id === false || $id <= 0) {
    header('Location: purchase_orders.php');
    exit();
}

$transactionStarted = false;

try {
    if (!$conn->begin_transaction()) {
        throw new RuntimeException('Failed to begin purchase order cancellation transaction: '.$conn->error);
    }

    $transactionStarted = true;

    $stmt = $conn->prepare(
        'SELECT status 
        FROM purchase_orders 
        WHERE id = ? 
        FOR UPDATE'
    );

    if (!$stmt) {
        throw new RuntimeException('Failed to prepare purchase order lookup.');
    }

    if (!$stmt->bind_param('i', $id)) {
        $stmt->close();

        throw new RuntimeException('Failed to bind purchase order lookup.');
    }

    if (!$stmt->execute()) {
        $stmt->close();

        throw new RuntimeException('Failed to execute purchase order lookup.');
    }
    $result = $stmt->get_result();

    if (!$result) {
        $error = $stmt->error;
        $stmt->close();

        throw new RuntimeException('Failed to retrieve purchase order lookup result: '.$error);
    }

    $po = $result->fetch_assoc();

    $stmt->close();

    if (!$po) {
        throw new RuntimeException('Purchase order not found.');
    }

    if (
        $po['status'] !== 'Pending'
        && $po['status'] !== 'Draft') {
        throw new RuntimeException('Only Draft or Pending purchase orders can be cancelled.');
    }

    $updateStmt = $conn->prepare(
        "UPDATE purchase_orders 
        SET status = 'Cancelled' 
        WHERE id = ? 
        AND status IN ('Pending', 'Draft')"
    );

    if (!$updateStmt) {
        throw new RuntimeException('Failed to prepare purchase order cancellation.');
    }

    if (!$updateStmt->bind_param('i', $id)) {
        $updateStmt->close();

        throw new RuntimeException('Failed to bind purchase order cancellation.');
    }

    if (!$updateStmt->execute()) {
        $error = $updateStmt->error;
        $updateStmt->close();

        throw new RuntimeException('Failed to execute purchase order cancellation: '.$error);
    }

    if ($updateStmt->affected_rows !== 1) {
        $updateStmt->close();

        throw new RuntimeException('Purchase order could not be cancelled.');
    }

    $updateStmt->close();

    recordAudit(
        $conn,
        (int) $_SESSION['user_id'],
        'purchase_order',
        $id,
        'CANCEL',
        ['status' => [$po['status'], 'Cancelled']]
    );

    if (!logActivity(
        $conn,
        (int) $_SESSION['user_id'],
        'Cancelled Purchase Order ID '.$id
    )) {
        throw new RuntimeException('Failed to record purchase order activity.');
    }

    if (!$conn->commit()) {
        throw new RuntimeException('Purchase order cancellation commit failed.');
    }

    $_SESSION['success'] = 'Purchase order cancelled successfully.';

    header('Location: purchase_orders.php');
    exit();
} catch (Throwable $e) {
    if ($transactionStarted) {
        $conn->rollback();
    }

    error_log('cancel_purchase_order.php: '.$e->getMessage());

    $_SESSION['error'] = 'Unable to cancel purchase order. Please try again.';

    header('Location: purchase_orders.php');
    exit();
}
