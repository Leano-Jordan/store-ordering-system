<?php

declare(strict_types=1);

require_once __DIR__.'/../../includes/asset_path.php';

use PHPUnit\\Framework\\TestCase;

final class AssetPathTest extends TestCase
{
    public function testRootScriptUsesRootAssetPath(): void
    {
        $this->assertSame('', swiftOrderAssetBaseFromScriptName('/login.php'));
    }

    public function testAbsolutePreviewUrlDoesNotBecomeAssetHostname(): void
    {
        $this->assertSame(
            '',
            swiftOrderAssetBaseFromScriptName('http://localhost:8000/login.php')
        );
    }

    public function testSubdirectoryScriptKeepsApplicationPrefix(): void
    {
        $this->assertSame(
            '/SwiftOrder-System',
            swiftOrderAssetBaseFromScriptName(
                'http://localhost:8000/SwiftOrder-System/login.php'
            )
        );
    }

    public function testRelativeScriptUsesRootAssetPath(): void
    {
        $this->assertSame('', swiftOrderAssetBaseFromScriptName('login.php'));
    }
}
