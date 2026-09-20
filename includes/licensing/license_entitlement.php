<?php

declare(strict_types=1);

require_once __DIR__.'/../db.php';
require_once __DIR__.'/license_state.php';

function loadLicenseEntitlement(mysqli $conn): ?array
{
    $stmt = $conn->prepare(
        'SELECT license_id, license_key_hash,
            installation_id, expires_at,
            grace_ends_at, suspended,
            last_validated_at, last_known_good_at
        FROM license_entitlements
        WHERE id = 1
        LIMIT 1'
    );

    if (!$stmt) {
        error_log('license_entitlement.php: Failed to prepare entitlement lookup: '.$conn->error);

        throw new RuntimeException('Unable to load licence entitlement.');
    }

    if (!$stmt->execute()) {
        $error = $stmt->error;
        $stmt->close();

        error_log('license_entitlement.php: Failed to execute entitlement lookup: '.$error);

        throw new RuntimeException('Unable to load licence entitlement.');
    }

    $result = $stmt->get_result();
    $entitlement = $result ? $result->fetch_assoc() : null;

    $stmt->close();

    if (!is_array($entitlement)) {
        return null;
    }

    return $entitlement;
}

function currentLicenseState(
    mysqli $conn,
    int $currentTimestamp): string {
    $entitlement = loadLicenseEntitlement($conn);

    if ($entitlement === null) {
        return LICENSE_NOT_CONFIGURED;
    }

    $expiresAt = strtotime(
        (string) $entitlement['expires_at']
    );

    $graceEndsAt = strtotime(
        (string) $entitlement['grace_ends_at']
    );

    if ($expiresAt === false || $graceEndsAt === false) {
        throw new RuntimeException(
            'Licence entitlement contains invalid dates.'
        );
    }

    return evaluateLicenseState(
        $currentTimestamp,
        $expiresAt,
        $graceEndsAt,
        (int) $entitlement['suspended'] === 1
    );
}