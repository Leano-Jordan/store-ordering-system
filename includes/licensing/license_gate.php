<?php

declare(strict_types=1);

require_once __DIR__.'/license_entitlement.php';

function requireActiveLicense(mysqli $conn): void
{
    $state = currentLicenseState(
        $conn,
        time()
        );

    if ($state === LICENSE_NOT_CONFIGURED) {
        error_log('license_gate.php: Licence entitlement is not configured.');

        http_response_code(503);

    echo '<script>
        alert("SwiftOrder licence entitlement is not configured.");
        window.location.href = "dashboard.php";
    </script>';

    exit();
}

    if (!licenseAllowsTrading($state)) {
        error_log('license_gate.php: Protected operation blocked by licence state: '.$state);

        $_SESSION['flash_error'] = 'Your SwiftOrder licence does not permit this operation.';

        header('Location: dashboard.php');
        exit();
    }
}