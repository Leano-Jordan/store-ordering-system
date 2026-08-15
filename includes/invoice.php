<?php

declare(strict_types=1);

function issueInvoiceNumber(
    mysqli $conn,
    int $orderId,
    int $userId
): string {
    if ($orderId < 1 || $userId < 1) {
        throw new InvalidArgumentException('Invalid invoice order or user.');
    }

    $settingsStmt = $conn->prepare(
        'SELECT
            id,
            next_invoice_number
        FROM business_settings
        LIMIT 1
        FOR UPDATE'
    );

    if (!$settingsStmt) {
        error_log(
            'SwiftOrder invoice settings prepare failed: '
            .$conn->error
        );

        throw new RuntimeException('Unable to allocate invoice number.');
    }

    if (!$settingsStmt->execute()) {
        error_log(
            'SwiftOrder invoice settings execute failed: '
            .$settingsStmt->error
        );

        $settingsStmt->close();

        throw new RuntimeException('Unable to allocate invoice number.');
    }

    $settingsResult = $settingsStmt->get_result();
    $settings = $settingsResult
        ? $settingsResult->fetch_assoc()
        : null;

    $settingsStmt->close();

    if (!$settings) {
        throw new RuntimeException('Business settings are not configured.');
    }

    $nextNumber = (int) ($settings['next_invoice_number'] ?? 0);

    if ($nextNumber < 1) {
        throw new RuntimeException('Invoice sequence is invalid.');
    }

    $invoiceNumber = 'INV-'.str_pad(
        (string) $nextNumber,
        6,
        '0',
        STR_PAD_LEFT
    );

    $updateStmt = $conn->prepare(
        'UPDATE business_settings
        SET next_invoice_number = next_invoice_number + 1
        WHERE id = ?'
    );

    if (!$updateStmt) {
        error_log(
            'SwiftOrder invoice sequence update prepare failed: '
            .$conn->error
        );

        throw new RuntimeException('Unable to allocate invoice number.');
    }

    $settingsId = (int) $settings['id'];

    if (!$updateStmt->bind_param('i', $settingsId)) {
        error_log(
            'SwiftOrder invoice sequence bind failed: '
            .$updateStmt->error
        );

        $updateStmt->close();

        throw new RuntimeException('Unable to allocate invoice number.');
    }

    if (!$updateStmt->execute()) {
        error_log(
            'SwiftOrder invoice sequence update failed: '
            .$updateStmt->error
        );

        $updateStmt->close();

        throw new RuntimeException('Unable to allocate invoice number.');
    }

    if ($updateStmt->affected_rows !== 1) {
        error_log(
            'SwiftOrder invoice sequence affected unexpected rows: '
            .$updateStmt->affected_rows
        );

        $updateStmt->close();

        throw new RuntimeException('Unable to allocate invoice number.');
    }

    $updateStmt->close();

    $orderStmt = $conn->prepare(
        'UPDATE orders
        SET
            invoice_number = ?,
            invoice_issued_at = NOW()
        WHERE id = ?
            AND status = \'Collected\'
            AND invoice_number IS NULL'
    );

    if (!$orderStmt) {
        error_log(
            'SwiftOrder invoice assignment prepare failed: '
            .$conn->error
        );

        throw new RuntimeException('Unable to assign invoice number.');
    }

    if (!$orderStmt->bind_param(
        'si',
        $invoiceNumber,
        $orderId
    )) {
        error_log(
            'SwiftOrder invoice assignment bind failed: '
            .$orderStmt->error
        );

        $orderStmt->close();

        throw new RuntimeException('Unable to assign invoice number.');
    }

    if (!$orderStmt->execute()) {
        error_log(
            'SwiftOrder invoice assignment execute failed: '
            .$orderStmt->error
        );

        $orderStmt->close();

        throw new RuntimeException('Unable to assign invoice number.');
    }

    if ($orderStmt->affected_rows !== 1) {
        $orderStmt->close();

        throw new RuntimeException('Invoice has already been issued for this order.');
    }

    $orderStmt->close();

    return $invoiceNumber;
}
