<?php

declare(strict_types=1);

function snapshotInvoiceBusinessDetails(
    mysqli $conn,
    int $orderId
): void {
    if ($orderId < 1) {
        throw new InvalidArgumentException('Invalid order identifier.');
    }

    $settingsStmt = $conn->prepare(
        'SELECT business_name,
            business_address,
            vat_enabled,
            vat_number
        FROM business_settings
        WHERE id = 1
        LIMIT 1 FOR UPDATE'
    );

    if (!$settingsStmt) {
        throw new RuntimeException('Unable to prepare business settings lookup.');
    }

    if (!$settingsStmt->execute()) {
        $error = $settingsStmt->error;
        $settingsStmt->close();

        throw new RuntimeException('Unable to load business settings: '.$error);
    }

    $result = $settingsStmt->get_result();

    $settings = $result
        ? $result->fetch_assoc()
        : null;

    $settingsStmt->close();

    if (!$settings) {
        throw new RuntimeException('Business settings are not configured.');
    }

    $businessName = trim(
        (string) $settings['business_name']
    );

    $businessAddress = trim(
        (string) $settings['business_address']
    );

    $vatEnabled = (int) $settings['vat_enabled'] === 1;

    $vatNumber = trim(
        (string) ($settings['vat_number'] ?? '')
    );

    if (
        $businessName === '' ||
        $businessAddress === ''
    ) {
        throw new RuntimeException('Business identity is incomplete.');
    }

    if (
        $vatEnabled &&
        !preg_match('/^\d{10}$/', $vatNumber)
    ) {
        throw new RuntimeException('VAT registration details are incomplete.');
    }

    $orderStmt = $conn->prepare(
        'SELECT
            invoice_number,
            vat_enabled_at_sale
         FROM orders
         WHERE id = ?
         LIMIT 1
         FOR UPDATE'
    );

    if (!$orderStmt) {
        throw new RuntimeException('Unable to prepare order lookup.');
    }

    if (!$orderStmt->bind_param('i', $orderId)) {
        $orderStmt->close();

        throw new RuntimeException('Unable to bind order lookup.');
    }

    if (!$orderStmt->execute()) {
        $error = $orderStmt->error;
        $orderStmt->close();

        throw new RuntimeException('Unable to load order: '.$error);
    }

    $orderResult = $orderStmt->get_result();

    $order = $orderResult
        ? $orderResult->fetch_assoc()
        : null;

    $orderStmt->close();

    if (
        !$order || trim((string) ($order['invoice_number'] ?? '')) === '') {
        throw new RuntimeException('Invoice number is not assigned to this order.');
    }

    $saleVatEnabled = (int) $order['vat_enabled_at_sale'] === 1;

    if ($saleVatEnabled && !$vatEnabled) {
        throw new RuntimeException('Current business VAT configuration cannot '.'reproduce this VAT invoice.');
    }

    $snapshotVatNumber = $saleVatEnabled ? $vatNumber : null;

    $updateStmt = $conn->prepare(
        'UPDATE orders SET business_name_at_invoice = ?,
            business_address_at_invoice = ?,
            business_vat_number_at_invoice = ? WHERE id = ?'
    );

    if (!$updateStmt) {
        throw new RuntimeException('Unable to prepare invoice snapshot update.');
    }

    if (!$updateStmt->bind_param(
        'sssi',
        $businessName,
        $businessAddress,
        $snapshotVatNumber,
        $orderId
    )) {
        $updateStmt->close();

        throw new RuntimeException('Unable to bind invoice snapshot update.');
    }

    if (!$updateStmt->execute()) {
        $error = $updateStmt->error;
        $updateStmt->close();

        throw new RuntimeException('Unable to save invoice business snapshot: '.$error);
    }

    $updateStmt->close();
}
