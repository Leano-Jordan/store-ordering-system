<?php

declare(strict_types=1);

function invoiceHtml(string $value): string
{
    return htmlspecialchars(
        $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

function invoiceMoney(float $value): string
{
    return number_format($value, 2, '.', ' ');
}
