<?php

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER, ROLE_CASHIER, ROLE_KITCHEN]);
require_once 'includes/db.php';
require_once 'includes/csrf.php';
verifyCsrfToken();
require_once 'includes/helpers.php';
require_once 'includes/logger.php';
require_once 'includes/audit.php';
require_once 'includes/invoice.php';
require_once __DIR__.'/includes/invoice_snapshot.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Invalid request.');
}

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    exit('Invalid order.');
}

$status = $_POST['status'] ?? '';

$conn->begin_transaction();

try {
    $orderResult = executeQuery($conn, 'SELECT status, order_number, total FROM orders WHERE id = ? FOR UPDATE', 'i', [$id]);

    if (!$orderResult) {
        throw new Exception('Failed to retrieve order.');
    }

    $orderData = $orderResult->fetch_assoc();
    if (!$orderData) {
        throw new Exception('Order not found.');
    }

    $currentStatus = $orderData['status'];
    $orderNumber = $orderData['order_number'];
    $invoiceNumber = null;

    if ($currentStatus === 'Collected' || $currentStatus === 'Cancelled') {
        throw new Exception("Cannot change status of an order that is already $currentStatus.");
    }

    $allowedStatuses = ['Pending', 'Preparing', 'Ready', 'Collected', 'Cancelled'];

    if (!in_array($status, $allowedStatuses, true)) {
        throw new Exception('Invalid status.');
    }

    /***********         ************* ROLE BASED STATUS**********     *********/

    $role = $_SESSION['role'];

    if ($role === ROLE_KITCHEN) {
        if (
        !(
            ($currentStatus === 'Pending' && $status === 'Preparing') || ($currentStatus === 'Preparing' && $status === 'Ready')
        )
    ) {
            throw new Exception('User cannot perform this action.');
        }
    }

    if ($role === ROLE_CASHIER) {
        if (
            !($currentStatus === 'Ready' && $status === 'Collected')) {
            throw new Exception('Cashiers can only collect ready orders.');
        }
    }

    $allowedTransitions = [
    'Pending' => ['Preparing', 'Cancelled'],
    'Preparing' => ['Ready', 'Cancelled'],
    'Ready' => ['Collected', 'Cancelled'],
    'Collected' => [],
    'Cancelled' => [],
];

    if (!isset($allowedTransitions[$currentStatus])) {
        throw new Exception('Invalid current status.');
    }

    if (!in_array($status, $allowedTransitions[$currentStatus], true)) {
        throw new Exception("Cannot change status from $currentStatus to $status.");
    }

    $orderUpdated = executeStatementAffectedRows(
        $conn,
        'UPDATE orders 
SET status = ? 
WHERE id = ? AND status = ?',
        'sis',
        [$status, $id, $currentStatus]
    );

    if ($orderUpdated !== 1) {
        throw new Exception('Order status update failed.');
    }

    if ($status === 'Collected') {
        $orderItemResult = executeQuery($conn, 'SELECT oi.product_id, oi.quantity FROM order_items oi 
        INNER JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = ? FOR UPDATE', 'i', [$id]);

        if (!$orderItemResult) {
            throw new Exception('Failed to retrieve order items.');
        }
        while ($item = $orderItemResult->fetch_assoc()) {
            $qty = (int) $item['quantity'];
            $productId = (int) $item['product_id'];

            $affected = executeStatementAffectedRows(
                $conn,
                'UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?',
                'iii',
                [$qty, $productId, $qty]
            );

            if ($affected <= 0) {
                throw new Exception("Unable to deduct stock for product ID $productId");
            }

            $newStockResult = executeQuery(
                $conn,
                'SELECT stock FROM products WHERE id = ?',
                'i',
                [$productId]
            );

            if (!$newStockResult) {
                throw new Exception('Failed to retrieve updated stock for product ID '.$productId);
            }

            $newStockRow = $newStockResult->fetch_assoc();

            if (!$newStockRow) {
                throw new Exception('Unable to read updated stock for product ID '.$productId);
            }

            $newStock = (int)
                $newStockRow['stock'];

            $adjType = 'Decrease';
            $adjReason = 'Order Collected';
            $adjNotes = "Order: $orderNumber";

            $histStmt = executeStatement(
                $conn,
                'INSERT INTO stock_adjustments (product_id, 
                user_id, adjustment_type, 
                quantity, available_stock, reason, 
                notes) VALUES (?, ?, ?, ?, ?, ?, ?)',
                'iisiiss',
                [$productId,
                $_SESSION['user_id'],
                $adjType, $qty, $newStock,
                $adjReason, $adjNotes, ]
            );

            if (!$histStmt) {
                throw new Exception("Failed to create stock history for product ID $productId");
            }
        }

        $vatStmt = $conn->prepare('SELECT vat_enabled, vat_rate FROM business_settings ORDER BY id ASC LIMIT 1');

        if (!$vatStmt) {
            throw new RuntimeException('Failed to prepare VAT settings lookup.');
        }

        if (!$vatStmt->execute()) {
            $error = $vatStmt->error;
            $vatStmt->close();

            throw new RuntimeException('Failed to load VAT settings: '.$error);
        }

        $vatResult = $vatStmt->get_result();

        if (!$vatResult) {
            $vatStmt->close();

            throw new RuntimeException('Failed to retrieve VAT settings.');
        }

        $vatSettings = $vatResult->fetch_assoc();

        $vatStmt->close();

        if (!$vatSettings) {
            throw new RuntimeException('VAT settings are not configured.');
        }

        $vatEnabledAtSale = (int) $vatSettings['vat_enabled'] === 1;

        $vatRateAtSate = max(0.00, (float) $vatSettings['vat_rate']);

        $orderTotal = (float) ($orderData['total'] ?? 0);

        $vatAmount = 0.00;

        if ($vatEnabledAtSale && $vatRateAtSate > 0) {
            $vatAmount = round(
                $orderTotal - (
                    $orderTotal / (1 + ($vatRateAtSate / 100))
                ),
                2
            );
        }

        $subTotal = round($orderTotal - $vatAmount, 2);

        $taxUpdated = executeStatementAffectedRows(
            $conn,
            'UPDATE orders 
            SET 
                vat_enabled_at_sale = ?,
                vat_rate_at_sale = ?,
                vat_amount = ?,
                subtotal = ? WHERE id = ?',
            'idddi',
            [
                    $vatEnabledAtSale ? 1 : 0,
                    $vatRateAtSate,
                    $vatAmount,
                    $subTotal,
                    $id,
                ]
        );

        if ($taxUpdated !== 1) {
            throw new RuntimeException('Failed to store VAT snapshot.');
        }

        $invoiceNumber = issueInvoiceNumber(
            $conn,
            $id,
            (int)
            $_SESSION['user_id']
        );

        snapshotInvoiceBusinessDetails(
            $conn,
            $id
        );
    }

    if ($currentStatus !== $status) {
        $auditChanges = ['status' => [$currentStatus, $status]];

        if ($status === 'Collected' && $invoiceNumber !== null) {
            $auditChanges['invoice_number'] = [null, $invoiceNumber];
        }

        recordAudit(
            $conn,
            (int) $_SESSION['user_id'],
            'order',
            $id,
            'STATUS_CHANGE',
            $auditChanges
        );
    }

    if (!$conn->commit()) {
        throw new Exception('Failed to commit order status update.');
    }
} catch (Throwable $e) {
    $conn->rollback();

    error_log('update_status.php: '.$e->getMessage());

    $_SESSION['flash_error'] = 'Failed to update order status. Please try again.';

    header('Location: order_details.php?id='.(int) $id);
    exit();
}

if (!logActivity(
    $conn,
    (int) $_SESSION['user_id'],
    "Changed Order $orderNumber from $currentStatus to $status"
)) {
    error_log(
        'update_status.php: Activity log failed for order ID '.(int) $id
    );
}

    if (($_POST['return_to'] ?? '') === 'orders') {
        header('Location: orders.php');
    } else {
        header('Location: order_details.php?id='.(int) $id);
    }

    exit();
