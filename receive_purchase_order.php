<?php

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once 'includes/db.php';
require_once 'includes/logger.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: purchase_orders.php');
    exit();
}
require_once 'includes/csrf.php';
verifyCsrfToken();

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    header('Location: purchase_orders.php');
    exit();
}

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare('SELECT status FROM purchase_orders WHERE id = ? FOR UPDATE');

        if (!$stmt) {
            throw new Exception($conn->error);
        }

        if (!$stmt->bind_param('i', $id)) {
            throw new Exception('Failed to bind Purchase Order lookup parameters.');
        }

        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        $po = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$po) {
            throw new Exception('Purchase Order not found.');
        }

        if ($po['status'] === 'Received') {
            throw new Exception('This Purchase Order has already been received.');
        }

        if ($po['status'] !== 'Pending') {
            throw new Exception('Only Pending Purchase Orders can be received.');
        }

        $itemsStmt = $conn->prepare('SELECT product_id, quantity 
        FROM purchase_order_items WHERE purchase_order_id = ? ORDER BY product_id ASC FOR UPDATE');

        if (!$itemsStmt) {
            throw new Exception($conn->error);
        }

        if (!$itemsStmt->bind_param('i', $id)) {
            throw new Exception('Failed to bind purchase order item parameters.');
        }

        if (!$itemsStmt->execute()) {
            throw new Exception($itemsStmt->error);
        }

        $items = $itemsStmt->get_result();

        if (!$items) {
            throw new Exception('Failed to retrieve Purchase Order items: '.$itemsStmt->error);
        }

        if (!$items->num_rows) {
            throw new Exception('No items found for this Purchase Order.');
        }

        while ($item = $items->fetch_assoc()) {
            // Update product stock
            $stockStmt = $conn->prepare('UPDATE products SET stock = stock + ? WHERE id = ?');
            if (!$stockStmt) {
                throw new Exception($conn->error);
            }

            if (!$stockStmt->bind_param('ii', $item['quantity'], $item['product_id'])) {
                throw new Exception('Failed to bind stock update parameters.');
            }

            if (!$stockStmt->execute()) {
                throw new Exception($stockStmt->error);
            }

            if ($stockStmt->affected_rows === 0) {
                throw new Exception('Stock update failed for Product Id '.$item['product_id']);
            }

            $stockStmt->close();

            // Get new stock level for history
            $newStockStmt = $conn->prepare(
                'SELECT stock FROM products 
                WHERE id = ? FOR UPDATE'
            );

            if (!$newStockStmt) {
                throw new Exception($conn->error);
            }

            if (!$newStockStmt->bind_param('i', $item['product_id'])) {
                $error = $newStockStmt->error;
                $newStockStmt->close();

                throw new RuntimeException('Failed to bind updated stock lookup: '.$error);
            }

            if (!$newStockStmt->execute()) {
                $error = $newStockStmt->error;
                $newStockStmt->close();

                throw new RuntimeException('Failed to retrieve updated stock: '.$error);
            }

            $newStockResult = $newStockStmt->get_result();

            if (!$newStockResult) {
                $error = $newStockStmt->error;
                $newStockStmt->close();

                throw new RuntimeException('Failed to retrieve updated stock result: '.$error);
            }

            $newStockRow = $newStockResult->fetch_assoc();

            if (!$newStockRow) {
                throw new Exception('Unable to read updated stock for product ID '.$item['product_id']);
            }

            $newStock = (int)
            $newStockRow['stock'];
            $newStockStmt->close();

            // Record in stock adjustment history
            $type = 'Increase';
            $reason = 'Purchase Order Receipt';
            $notes = "PO ID: $id";

            $historyStmt = $conn->prepare('INSERT INTO stock_adjustments
            (product_id, user_id, adjustment_type, quantity, available_stock, reason, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?)');
            if (!$historyStmt) {
                throw new Exception($conn->error);
            }

            if (!$historyStmt->bind_param(
                'iisiiss',
                $item['product_id'],
                $_SESSION['user_id'],
                $type,
                $item['quantity'],
                $newStock,
                $reason,
                $notes
            )) {
                throw new Exception('Failed to bind stock adjustment history parameters.');
            }

            if (!$historyStmt->execute()) {
                throw new Exception($historyStmt->error);
            }

            $historyStmt->close();
        }

        $itemsStmt->close();

        $updateStmt = $conn->prepare("UPDATE purchase_orders SET status = 'Received' WHERE id = ? AND status = 'Pending'");

        if (!$updateStmt) {
            throw new Exception($conn->error);
        }

        if (!$updateStmt->bind_param('i', $id)) {
            throw new Exception('Failed to bind Purchase Order status update.');
        }

        if (!$updateStmt->execute()) {
            throw new Exception($updateStmt->error);
        }

        if ($updateStmt->affected_rows === 0) {
            throw new Exception('Purchase Order could not be marked as Received');
        }

        $updateStmt->close();

        /* CREATING GOODS RECEIVED NOTE */

        $grnNumber = 'GRN-PO-'
        .str_pad((string) $id, 6, '0', STR_PAD_LEFT).
        '-'.date('YmdHis').
'-'.str_pad(random_int(1, 999), 3, '0', STR_PAD_LEFT);

        $grnStmt = $conn->prepare('INSERT INTO goods_received_notes
(purchase_order_id, supplier_id, grn_number, received_by, total, notes)

SELECT id, supplier_id, ?, ?, total, notes FROM purchase_orders
WHERE id = ?
');

        if (!$grnStmt) {
            throw new Exception($conn->error);
        }

        if (!$grnStmt->bind_param(
            'sii',
            $grnNumber,
            $_SESSION['user_id'],
            $id
        )) {
            throw new Exception('Failed to bind GRN creation parameters.');
        }

        if (!$grnStmt->execute()) {
            if ($conn->errno === 1062 || $grnStmt->errno === 1062) {
                throw new Exception('Generated GRN number already exists.');
            }

            throw new Exception('Failed to create GRN: '.$grnStmt->error);
        }

        $grnId = $conn->insert_id;

        $grnStmt->close();

        /*  COPYING PURCHASE ORDER ITEMS INTO GRN */

        $itemCopy = $conn->prepare('INSERT INTO goods_received_note_items
(
grn_id, product_id, quantity, cost_price, line_total
)
SELECT ?, product_id, quantity, cost_price, line_total
FROM purchase_order_items
WHERE purchase_order_id = ?
');

        if (!$itemCopy) {
            throw new Exception($conn->error);
        }

        if (!$itemCopy->bind_param('ii', $grnId, $id)) {
            throw new Exception('Failed to bind GRN parameters.');
        }

        if (!$itemCopy->execute()) {
            throw new Exception($itemCopy->error);
        }

        if ($itemCopy->affected_rows === 0) {
            throw new Exception('Failed to copy items into Goods Received Note');
        }

        $itemCopy->close();

        $receiptChanges = [
            'status' => ['Pending', 'Received'],
            'grn_number' => [null, $grnNumber],
        ];

        recordAudit(
            $conn,
            (int) $_SESSION['user_id'],
            'purchase_order',
            (int) $id,
            'RECEIVE',
            $receiptChanges
        );

        if (!logActivity(
            $conn,
            (int) $_SESSION['user_id'],
            'Received Purchase Order ID '.$id
        )) {
            throw new RuntimeException('Failed to record purchase order receipt activity.');
        }

        if (!$conn->commit()) {
            throw new RuntimeException('Transaction commit failed: '.$conn->error);
        }
    } catch (Throwable $e) {
        $conn->rollback();

        error_log(
            'receive_purchase_order.php: '.$e->getMessage()
        );

        exit('Failed to receive purchase order');
    }

    header('Location: purchase_orders.php');
    exit();
