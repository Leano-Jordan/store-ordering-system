<?php

declare(strict_types=1);

function businessSettingsError(string $message): never
{
    $_SESSION['error'] = $message;

    header('Location: business_settings.php');

    exit();
}

function normalizeVatNumber(string $value): string
{
    $normalized = preg_replace('/[\s-]+/', '', trim($value));

    return $normalized === null ? '' : $normalized;
}

function maskVatNumber(?string $vatNumber): ?string
{
    if ($vatNumber === null || $vatNumber === '') {
        return null;
    }

    $length = strlen($vatNumber);

    if ($length <= 4) {
        return str_repeat('*', $length);
    }

    return str_repeat('*', $length - 4)
        .substr($vatNumber, -4);
}
