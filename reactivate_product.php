<?php

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN]);
require_once 'includes/db.php';
require_once 'includes/audit.php';
require_once __DIR__.'/includes/licensing/license_gate.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: products.php');
    exit();
}

require_once 'includes/csrf.php';
verifyCsrfToken();
requireActiveLicense($conn);

$idRaw = $_POST['id'] ?? null;

if (!is_string($idRaw)) {
    header('Location: products.php');
    exit();
}

$id = filter_var($idRaw, FILTER_VALIDATE_INT);

if ($id === false || $id <= 0) {
    header('Location: products.php');
    exit();
}

$transactionStarted = false;

try {
    if (!$conn->begin_transaction()) {
        throw new RuntimeException('Failed to begin product reactivation transaction: '.$conn->error);
    }

    $transactionStarted = true;

    $stmt = $conn->prepare(
        "UPDATE products
        SET status = 'Active'
        WHERE id = ?
        AND status = 'Inactive'"
    );

    if (!$stmt) {
        throw new RuntimeException('Failed to prepare product reactivation.');
    }

    if (!$stmt->bind_param('i', $id)) {
        $stmt->close();

        throw new RuntimeException('Failed to bind product reactivation.');
    }

    if (!$stmt->execute()) {
        $error = $stmt->error;
        $stmt->close();

        throw new RuntimeException('Failed to reactivate product: '.$error);
    }

    if ($stmt->affected_rows !== 1) {
        $stmt->close();

        throw new RuntimeException('Product was not inactive.');
    }

    $stmt->close();

    recordAudit(
        $conn,
        (int) $_SESSION['user_id'],
        'product',
        $id,
        'REACTIVATE',
        [
            'status' => ['Inactive', 'Active'],
        ]
    );

    if (!$conn->commit()) {
        throw new RuntimeException('Product reactivation commit failed.');
    }

    $_SESSION['success'] = 'Product reactivated successfully.';

    header('Location: products.php');
    exit();
} catch (Throwable $e) {
    if ($transactionStarted) {
        $conn->rollback();
    }

    error_log(
        'reactivate_product.php: '.$e->getMessage()
    );

    $_SESSION['error'] =
        'Unable to reactivate product. Please try again.';

    header('Location: products.php');
    exit();
}
