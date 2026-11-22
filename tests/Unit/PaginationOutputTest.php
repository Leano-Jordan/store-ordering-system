<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PaginationOutputTest extends TestCase
{
    public function testOrderQueryIsEncodedForHtmlAttributeOutput(): void
    {
        $originalGet = $_GET;
        $page = 2;
        $totalPages = 3;

        try {
            $_GET['order'] = '" onfocus="alert(1)';

            ob_start();
            include __DIR__.'/../../includes/partials/order/order_pagination.php';
            $html = ob_get_clean();
        } finally {
            $_GET = $originalGet;
        }

        $this->assertIsString($html);
        $this->assertStringContainsString('order=', $html);
        $this->assertStringNotContainsString('onfocus="alert(1)', $html);
        $this->assertStringContainsString('%22', $html);
    }

    public function testArrayOrderQueryFallsBackWithoutWarning(): void
    {
        $originalGet = $_GET;
        $page = 2;
        $totalPages = 3;

        try {
            $_GET['order'] = ['malicious'];

            ob_start();
            include __DIR__.'/../../includes/partials/order/order_pagination.php';
            $html = ob_get_clean();
        } finally {
            $_GET = $originalGet;
        }

        $this->assertIsString($html);
        $this->assertStringContainsString('order=', $html);
        $this->assertStringNotContainsString('malicious', $html);
    }
}
