<?php

declare(strict_types=1);

require_once 'includes/auth.php';
require_once 'includes/permissions.php';

requireRole([ROLE_ADMIN]);

require_once 'includes/csrf.php';

$settingsStmt = $conn->prepare(
    'SELECT
        business_name,
        business_address,
        vat_enabled,
        vat_number,
        vat_rate,
        next_invoice_number
     FROM business_settings
     WHERE id = 1
     LIMIT 1'
);

if (!$settingsStmt || !$settingsStmt->execute()) {
    $error = $settingsStmt
        ? $settingsStmt->error
        : $conn->error;

    if ($settingsStmt) {
        $settingsStmt->close();
    }

    error_log(
        'business_settings.php: settings load failed: '.$error
    );

    http_response_code(500);

    exit('Unable to load business settings.');
}

$settingsResult = $settingsStmt->get_result();

$settings = $settingsResult
    ? $settingsResult->fetch_assoc()
    : null;

$settingsStmt->close();

if (!$settings) {
    error_log(
        'business_settings.php: business_settings id=1 missing.'
    );

    http_response_code(500);

    exit('Business settings are not configured.');
}

$businessName = (string) $settings['business_name'];

$businessAddress = (string) $settings['business_address'];

$vatEnabled = (int) $settings['vat_enabled'] === 1;

$vatNumber = (string) ($settings['vat_number'] ?? '');

$vatRate = number_format(
    (float) $settings['vat_rate'],
    2,
    '.',
    ''
);

$nextInvoiceNumber = (int) $settings['next_invoice_number'];

include 'includes/header.php';
?>

<div class="page-header">
    <h2>Business &amp; Tax Settings</h2>
</div>

<?php require 'includes/shared/flash_message.php'; ?>

<div class="form-container">

<form
    action="save_business_settings.php"
    method="POST"
    autocomplete="off"
>

    <input
        type="hidden"
        name="csrf_token"
        value="<?php
        echo htmlspecialchars(
    csrfToken(),
    ENT_QUOTES,
    'UTF-8'
);
        ?>"
    >

    <div class="form-group">

        <h3>Business identity</h3>

        <label for="business_name">
            Business / trading name *
        </label>

        <input
            id="business_name"
            type="text"
            name="business_name"
            maxlength="150"
            required
            value="<?php
            echo htmlspecialchars(
            $businessName,
            ENT_QUOTES,
            'UTF-8'
        );
            ?>"
        >

        <br><br>

        <label for="business_address">
            Business address *
        </label>

        <textarea
            id="business_address"
            name="business_address"
            rows="4"
            maxlength="2000"
            required
        ><?php
        echo htmlspecialchars(
                $businessAddress,
                ENT_QUOTES,
                'UTF-8'
            );
        ?></textarea>

    </div>

    <div class="form-group">

        <h3>VAT / tax</h3>

        <label for="vat_enabled">
            VAT registration status *
        </label>

        <select
            id="vat_enabled"
            name="vat_enabled"
            required
        >

            <option
                value="1"
                <?php echo $vatEnabled ? 'selected' : ''; ?>
            >
                VAT registered
            </option>

            <option
                value="0"
                <?php echo !$vatEnabled ? 'selected' : ''; ?>
            >
                Not VAT registered
            </option>

        </select>

        <br><br>

        <label for="vat_number">
            VAT registration number
        </label>

        <input
            id="vat_number"
            type="text"
            name="vat_number"
            inputmode="numeric"
            maxlength="12"
            value="<?php
            echo htmlspecialchars(
            $vatNumber,
            ENT_QUOTES,
            'UTF-8'
        );
            ?>"
            placeholder="10-digit SARS VAT number"
        >

        <small>
            Required when VAT registered.
            Spaces and hyphens are normalized.
        </small>

        <br><br>

        <label for="vat_rate">
            VAT rate (%) *
        </label>

        <input
            id="vat_rate"
            type="number"
            name="vat_rate"
            min="0"
            max="100"
            step="0.01"
            required
            value="<?php
            echo htmlspecialchars(
                $vatRate,
                ENT_QUOTES,
                'UTF-8'
            );
            ?>"
        >

    </div>

    <div class="form-group">

        <h3>Invoice sequence</h3>

        <label for="next_invoice_number">
            Next invoice number *
        </label>

        <input
            id="next_invoice_number"
            type="number"
            name="next_invoice_number"
            min="1"
            max="2147483647"
            step="1"
            required
            value="<?php echo $nextInvoiceNumber; ?>"
        >

        <small>
            The system prevents moving the sequence backwards
            or below an already issued invoice.
        </small>

    </div>

    <div class="form-actions">

        <button type="submit">
            Save Business Settings
        </button>

        <a
            href="dashboard.php"
            class="action-btn"
        >
            Cancel
        </a>

    </div>

</form>

</div>

<?php include 'includes/footer.php'; ?>