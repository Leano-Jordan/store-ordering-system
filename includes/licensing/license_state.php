<?php

declare(strict_types=1);

const LICENSE_ACTIVE = 'ACTIVE';
const LICENSE_GRACE = 'GRACE';
const LICENSE_EXPIRED = 'EXPIRED';
const LICENSE_SUSPENDED = 'SUSPENDED';

function evaluateLicenseState(
    int $currentTimestamp,
    int $expiresAt,
    int $graceEndsAt,
    bool $suspended
): string {
    if ($suspended) {
        return LICENSE_SUSPENDED;
    }

    if ($currentTimestamp <= $expiresAt) {
        return LICENSE_ACTIVE;
    }

    if ($currentTimestamp <= $graceEndsAt) {
        return LICENSE_GRACE;
    }

    return LICENSE_EXPIRED;
}

function licenseAllowsTrading(string $state): bool
{
    return $state === LICENSE_ACTIVE
        || $state === LICENSE_GRACE;
}