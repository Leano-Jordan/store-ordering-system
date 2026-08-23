<?php

declare(strict_types=1);

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN]);

require_once 'includes/csrf.php';
require_once 'includes/audit.php';
require_once 'includes/business_settings.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: business_settings.php');

    exit();
}

verifyCsrfToken();

$businessNameRaw = $_POST['business_name'] ?? null;
$businessAddressRaw = $_POST['business_address'] ?? null;
$vatEnabledRaw = $_POST['vat_enabled'] ?? null;
$vatNumberRaw = $_POST['vat_number'] ?? '';
$vatRateRaw = $_POST['vat_rate'] ?? null;
$nextInvoiceNumberRaw = $_POST['next_invoice_number'] ?? null;

if (
    !is_string($businessNameRaw) ||
    !is_string($businessAddressRaw) ||
    !is_string($vatEnabledRaw) ||
    !is_string($vatNumberRaw) ||
    !is_string($vatRateRaw) ||
    !is_string($nextInvoiceNumberRaw)
) {
    businessSettingsError('Invalid settings data.');
}

$businessName = trim($businessNameRaw);
$businessAddress = trim($businessAddressRaw);
$vatNumber = normalizeVatNumber($vatNumberRaw);

if ($businessName === '' || strlen($businessName) > 150) {
    businessSettingsError(
        'Business name must contain 1 to 150 characters.'
    );
}

if ($businessAddress === '' || strlen($businessAddress) > 2000
) {
    businessSettingsError('Business address must contain 1 to 2000 characters.');
}

if (!in_array($vatEnabledRaw, ['0', '1'], true)) {
    businessSettingsError('Invalid VAT status.');
}

$vatEnabled = $vatEnabledRaw === '1';

$vatRate = filter_var(
    $vatRateRaw,
    FILTER_VALIDATE_FLOAT
);

if (
    $vatRate === false || !is_finite((float) $vatRate) ||
    $vatRate < 0 || $vatRate > 100) {
    businessSettingsError(
        'VAT rate must be between 0 and 100.'
    );
}

$vatRate = round((float) $vatRate, 2);

if ($vatEnabled && !preg_match('/^\d{10}$/', $vatNumber)) {
    businessSettingsError(
        'VAT-registered businesses require a valid '
        .'10-digit SARS VAT number.'
    );
}

if (!$vatEnabled) {
    $vatNumber = '';
}

$nextInvoiceNumber = filter_var($nextInvoiceNumberRaw, FILTER_VALIDATE_INT);

if ($nextInvoiceNumber === false || $nextInvoiceNumber < 1 || $nextInvoiceNumber > 2147483647) {
    businessSettingsError(
        'Next invoice number must be a positive whole number.'
    );
}

$userId = filter_var(
    $_SESSION['user_id'] ?? null,
    FILTER_VALIDATE_INT
);

if ($userId === false || $userId < 1) {
    http_response_code(403);

    exit('Forbidden.');
}

$conn->begin_transaction();

try {
    $settingsStmt = $conn->prepare(
        'SELECT business_name,
            business_address,
            vat_enabled,
            vat_number,
            vat_rate,
            next_invoice_number FROM business_settings
        WHERE id = 1 LIMIT 1 FOR UPDATE'
    );

    if (!$settingsStmt || !$settingsStmt->execute()) {
        if ($settingsStmt) {
            $settingsStmt->close();
        }

        throw new RuntimeException('Unable to load current business settings.');
    }

    $result = $settingsStmt->get_result();
    $current = $result ? $result->fetch_assoc() : null;
    $settingsStmt->close();

    if (!$current) {
        throw new RuntimeException('Business settings id=1 is missing.');
    }

    $currentNext = (int)
        $current['next_invoice_number'];

    if ($currentNext < 1) {
        throw new RuntimeException('Existing invoice sequence is invalid.');
    }

    /*
     * Because the business_settings row is locked above,
     * invoice allocation and this configuration change cannot
     * modify the sequence concurrently.
     */
    $maxStmt = $conn->prepare(
        "SELECT COALESCE(MAX(
                    CAST(SUBSTRING(invoice_number, 5)
                        AS UNSIGNED)), 0) AS max_invoice_sequence
        FROM orders WHERE invoice_number IS NOT NULL 
        AND invoice_number REGEXP '^INV-[0-9]+$'"
    );

    if (!$maxStmt || !$maxStmt->execute()) {
        if ($maxStmt) {
            $maxStmt->close();
        }
        throw new RuntimeException('Unable to validate invoice sequence.');
    }

    $maxResult = $maxStmt->get_result();
    $maxRow = $maxResult ? $maxResult->fetch_assoc() : null;
    $maxStmt->close();

    if (!$maxRow) {
        throw new RuntimeException('Unable to read issued invoice sequence.');
    }

    $highestIssued = (int)
        $maxRow['max_invoice_sequence'];

    $minimumNext = max($currentNext, $highestIssued + 1);

    if ($nextInvoiceNumber < $minimumNext) {
        throw new InvalidArgumentException('Next invoice number cannot be lower than '.$minimumNext.'.');
    }

    $oldVatNumber = $current['vat_number'] === null ? null : (string) $current['vat_number'];
    $newVatNumber = $vatNumber === '' ? null : $vatNumber;

    $changes = [];

    if ((string) $current['business_name'] !== $businessName) {
        $changes['business_name'] = [
            (string) $current['business_name'],
            $businessName,
        ];
    }

    if ((string) $current['business_address'] !== $businessAddress) {
        $changes['business_address'] = [
            '[changed]',
            '[changed]',
        ];
    }

    if ((int) $current['vat_enabled'] !== ($vatEnabled ? 1 : 0)) {
        $changes['vat_enabled'] = [
            (int) $current['vat_enabled'],
            $vatEnabled ? 1 : 0,
        ];
    }

    if ($oldVatNumber !== $newVatNumber) {
        $changes['vat_number'] = [
            maskVatNumber($oldVatNumber),
            maskVatNumber($newVatNumber),
        ];
    }

    $newVatRate = number_format($vatRate, 2, '.', '');

    $oldVatRate = number_format((float) $current['vat_rate'], 2, '.', '');

    if ($oldVatRate !== $newVatRate) {
        $changes['vat_rate'] = [
            $oldVatRate,
            $newVatRate,
        ];
    }

    if ((int) $current['next_invoice_number'] !== $nextInvoiceNumber) {
        $changes['next_invoice_number'] = [
            (int) $current['next_invoice_number'],
            $nextInvoiceNumber,
        ];
    }

    $updateStmt = $conn->prepare(
        'UPDATE business_settings SET 
        business_name = ?,
        business_address = ?,
        vat_enabled = ?,
        vat_number = ?,
        vat_rate = ?,
        next_invoice_number = ? WHERE id = 1'
    );

    if (!$updateStmt) {
        throw new RuntimeException('Unable to prepare settings update.');
    }

    $vatEnabledInt = $vatEnabled ? 1 : 0;

    if (!$updateStmt->bind_param(
        'ssisdi',
        $businessName,
        $businessAddress,
        $vatEnabledInt,
        $newVatNumber,
        $vatRate,
        $nextInvoiceNumber
    )) {
        $updateStmt->close();

        throw new RuntimeException('Unable to bind settings update.');
    }

    if (!$updateStmt->execute()) {
        $error = $updateStmt->error;

        $updateStmt->close();

        error_log('save_business_settings.php: update failed: '.$error);

        throw new RuntimeException('Unable to save business settings.');
    }

    $updateStmt->close();

    if ($changes !== []) {
        recordAudit(
            $conn,
            (int) $userId,
            'business_settings',
            1,
            'UPDATE',
            $changes
        );
    }

    if (!$conn->commit()) {
        $error = $conn->error;

        $conn->rollback();

        error_log(
            'save_business_settings.php: commit failed: '
            .$error
        );

        throw new RuntimeException('Unable to commit business settings.');
    }

    $_SESSION['success'] =
        'Business and tax settings saved successfully.';

    header('Location: business_settings.php');

    exit();
} catch (Throwable $exception) {
    $conn->rollback();

    error_log(
        'save_business_settings.php: transaction failed: '
        .$exception->getMessage()
    );

    $_SESSION['error'] =
        $exception instanceof InvalidArgumentException
        ? $exception->getMessage()
        : 'Business and tax settings could not be saved.';

    header('Location: business_settings.php');

    exit();
}
