<?php

declare(strict_types=1);

require_once __DIR__.'/../../includes/shared/errors.php';

use PHPUnit\Framework\TestCase;

final class JsonResponseTest extends TestCase
{
    public function testHtmlSensitiveCharactersAreEncodedAndRoundTrip(): void
    {
        $payload = [
            'success' => false,
            'message' => '<script>alert("x")</script> & more',
        ];

        $encoded = encodeJsonResponse($payload);
        $decoded = json_decode($encoded, true, 512, JSON_THROW_ON_ERROR);

        $this->assertStringNotContainsString('<script>', $encoded);
        $this->assertStringNotContainsString('</script>', $encoded);
        $this->assertSame($payload, $decoded);
    }
}
