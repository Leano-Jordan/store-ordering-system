<?php

declare(strict_types=1);

/**
 * Build a presentation-only dashboard context from verified current metrics.
 *
 * The context is deliberately driven by role and available business data.
 * It controls presentation and optional analytics only. It never changes
 * business records or claims that a business is "small" or "large".
 */
function buildDashboardContext(string $role, array $metrics): array
{
    $totalProducts = max(0, (int) ($metrics['totalProducts'] ?? 0));
    $todayOrders = max(0, (int) ($metrics['todayOrders'] ?? 0));
    $pendingOrders = max(0, (int) ($metrics['pendingOrders'] ?? 0));
    $collectedOrders = max(0, (int) ($metrics['collectedOrders'] ?? 0));
    $lowStock = max(0, (int) ($metrics['lowStock'] ?? 0));
    $outOfStock = max(0, (int) ($metrics['outOfStock'] ?? 0));
    $totalSuppliers = max(0, (int) ($metrics['totalSuppliers'] ?? 0));
    $pendingPOs = max(0, (int) ($metrics['pendingPOs'] ?? 0));
    $hasRecentOrders = (bool) ($metrics['hasRecentOrders'] ?? false);

    $isAdmin = $role === 'Admin';
    $isManager = $role === 'Manager';
    $isCashier = $role === 'Cashier';

    $canViewBusinessMetrics = $isAdmin || $isManager;
    $canManageProducts = $isAdmin || $isManager;

    $hasProducts = $totalProducts > 0;
    $hasProcurementActivity = $totalSuppliers > 0 || $pendingPOs > 0 || $lowStock > 0;
    $hasOrderActivity =
        $todayOrders > 0
        || $pendingOrders > 0
        || $collectedOrders > 0
        || $hasRecentOrders;

    $isBusyByDataVolume =
        $collectedOrders >= 50
        || $totalProducts >= 100
        || $totalSuppliers >= 10;

    return [
        'isAdmin' => $isAdmin,
        'isManager' => $isManager,
        'isCashier' => $isCashier,
        'canViewBusinessMetrics' => $canViewBusinessMetrics,
        'canManageProducts' => $canManageProducts,

        // Setup actions remain discoverable even when the business has no data yet.
        'showProducts' => $canManageProducts,

        // Operational information is shown only when it has something useful to say.
        'showOrdersToday' => true,
        'showPendingOrders' => $pendingOrders > 0,
        'showRevenueMetrics' => $canViewBusinessMetrics && $collectedOrders > 0,
        'showMonthRevenueMetrics' => $canViewBusinessMetrics && $collectedOrders > 0,
        'showAverageOrder' => $canViewBusinessMetrics && $collectedOrders >= 3,

        // Inventory/procurement areas appear only when the business has data for them.
        'showInventoryMetrics' => $canViewBusinessMetrics && $hasProducts,
        'showInventoryValue' => $canViewBusinessMetrics && $hasProducts,
        'showLowStockAlert' => $lowStock > 0,
        'showOutOfStockAlert' => $outOfStock > 0,
        'showProcurementMetrics' => $canViewBusinessMetrics && $hasProcurementActivity,
        'showPendingPOMetric' => $canViewBusinessMetrics && $pendingPOs > 0,
        'showSupplierMetric' => $canViewBusinessMetrics && $totalSuppliers > 0,

        // Historical analytics earn their place once there is enough history to read.
        'showSalesChart' => $canViewBusinessMetrics && $collectedOrders >= 7,
        'showTopSelling' => $canViewBusinessMetrics && $collectedOrders >= 3,

        'showRecentOrders' => $hasRecentOrders,
        'showStarterPanel' => !$hasProducts && !$hasOrderActivity,

        'hasProducts' => $hasProducts,
        'hasProcurementActivity' => $hasProcurementActivity,
        'hasOrderActivity' => $hasOrderActivity,

        // These labels describe available dashboard detail, not the business itself.
        'profile' => !$hasProducts && !$hasOrderActivity
            ? 'starter'
            : ($isBusyByDataVolume ? 'busy' : 'active'),
    ];
}
