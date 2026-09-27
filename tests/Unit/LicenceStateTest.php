<?php

declare(strict_types=1);

require_once __DIR__.'/../../includes/licensing/license_state.php';

use PHPUnit\Framework\TestCase;

final class LicenceStateTest extends TestCase
{
    public function testActiveBeforeExpiry(): void
    {
        $state = evaluateLicenseState(
            1_000,
            2_000,
            3_000,
            false
        );

        $this->assertSame(LICENSE_ACTIVE, $state);
    }

    public function testExpiryBoundaryRemainsActive(): void
    {
        $state = evaluateLicenseState(
            2_000,
            2_000,
            3_000,
            false
        );

        $this->assertSame(LICENSE_ACTIVE, $state);
    }

    public function testGraceAfterExpiry(): void
    {
        $state = evaluateLicenseState(
            2_001,
            2_000,
            3_000,
            false
        );

        $this->assertSame(LICENSE_GRACE, $state);
    }

    public function testGraceBoundaryRemainsGrace(): void
    {
        $state = evaluateLicenseState(
            3_000,
            2_000,
            3_000,
            false
        );

        $this->assertSame(LICENSE_GRACE, $state);
    }

    public function testExpiredAfterGracePeriod(): void
    {
        $state = evaluateLicenseState(
            3_001,
            2_000,
            3_000,
            false
        );

        $this->assertSame(LICENSE_EXPIRED, $state);
    }

    public function testSuspensionOverridesDateState(): void
    {
        $state = evaluateLicenseState(
            1_000,
            2_000,
            3_000,
            true
        );

        $this->assertSame(LICENSE_SUSPENDED, $state);
    }

    public function testActiveAllowsTrading(): void
    {
        $this->assertTrue(licenseAllowsTrading(LICENSE_ACTIVE));
    }

    public function testGraceAllowsTrading(): void
    {
        $this->assertTrue(licenseAllowsTrading(LICENSE_GRACE));
    }

    public function testExpiredBlocksTrading(): void
    {
        $this->assertFalse(licenseAllowsTrading(LICENSE_EXPIRED));
    }

    public function testSuspendedBlocksTrading(): void
    {
        $this->assertFalse(licenseAllowsTrading(LICENSE_SUSPENDED));
    }

    public function testSuspensionBlocksTradingAfterExpiry(): void 
    {
        $state = evaluateLicenseState(
            4_000,
            2_000,
            3_000,
            true
        );
        
        $this->assertSame(LICENSE_SUSPENDED, $state);
        $this->assertFalse(licenseAllowsTrading($state));
    }

    public function testGraceAllowsTradingAfterExpiry(): void
    {
        $state = evaluateLicenseState(
            2_500,
            2_000,
            3_000,
            false
        );

        $this->assertSame(LICENSE_GRACE, $state);
        $this->assertTrue(licenseAllowsTrading($state));
    }
}