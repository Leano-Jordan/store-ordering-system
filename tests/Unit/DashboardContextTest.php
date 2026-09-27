<?php

declare(strict_types=1);

require_once __DIR__.'/../../includes/dashboard_context.php';

use PHPUnit\Framework\TestCase;

final class DashboardContextTest extends TestCase
{
    public function testNewCashierGetsOnlyRelevantOperationalSections(): void
    {
        $context = buildDashboardContext('Cashier', []);

        $this->assertTrue($context['showOrdersToday']);
        $this->assertFalse($context['showRevenueMetrics']);
        $this->assertFalse($context['showInventoryMetrics']);
        $this->assertFalse($context['showProducts']);
        $this->assertTrue($context['showStarterPanel']);
    }

    public function testNewAdminKeepsSetupActionsVisible(): void
    {
        $context = buildDashboardContext('Admin', []);

        $this->assertSame('starter', $context['profile']);
        $this->assertTrue($context['canManageProducts']);
        $this->assertTrue($context['showProducts']);
        $this->assertTrue($context['showStarterPanel']);
        $this->assertFalse($context['showPendingOrders']);
        $this->assertFalse($context['showMonthRevenueMetrics']);
        $this->assertFalse($context['showAverageOrder']);
        $this->assertFalse($context['showSalesChart']);
        $this->assertFalse($context['showTopSelling']);
    }

    public function testManagerWithSmallDatasetGetsLeanDashboard(): void
    {
        $context = buildDashboardContext('Manager', [
            'totalProducts' => 8,
            'todayOrders' => 2,
            'pendingOrders' => 1,
            'collectedOrders' => 2,
            'lowStock' => 0,
            'outOfStock' => 0,
            'totalSuppliers' => 0,
            'pendingPOs' => 0,
            'hasRecentOrders' => true,
        ]);

        $this->assertSame('active', $context['profile']);
        $this->assertTrue($context['showProducts']);
        $this->assertTrue($context['showRevenueMetrics']);
        $this->assertTrue($context['showMonthRevenueMetrics']);
        $this->assertFalse($context['showAverageOrder']);
        $this->assertFalse($context['showSalesChart']);
        $this->assertFalse($context['showTopSelling']);
        $this->assertFalse($context['showProcurementMetrics']);
        $this->assertFalse($context['showLowStockAlert']);
        $this->assertFalse($context['showOutOfStockAlert']);
        $this->assertTrue($context['showRecentOrders']);
    }

    public function testBusyBusinessGetsExpandedAnalytics(): void
    {
        $context = buildDashboardContext('Admin', [
            'totalProducts' => 120,
            'todayOrders' => 10,
            'pendingOrders' => 4,
            'collectedOrders' => 80,
            'lowStock' => 5,
            'outOfStock' => 2,
            'totalSuppliers' => 12,
            'pendingPOs' => 3,
            'hasRecentOrders' => true,
        ]);

        $this->assertSame('busy', $context['profile']);
        $this->assertTrue($context['showAverageOrder']);
        $this->assertTrue($context['showSalesChart']);
        $this->assertTrue($context['showTopSelling']);
        $this->assertTrue($context['showProcurementMetrics']);
        $this->assertTrue($context['showLowStockAlert']);
        $this->assertTrue($context['showOutOfStockAlert']);
        $this->assertTrue($context['showPendingPOMetric']);
        $this->assertTrue($context['showSupplierMetric']);
    }
}
