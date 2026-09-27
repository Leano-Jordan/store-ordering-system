<?php

declare(strict_types=1);

/**
 * Build a presentation-only dashboard profile from verified current metrics.
 *
 * This helper intentionally makes no business-size claims. It decides which
 * dashboard areas are useful to display based on role and available activity.
 */
function buildDashboardContext(string $role, array $metrics): array
{
    $totalProducts = max(0, (int) ($metrics['totalProducts'] ?? 0));
    $todayOrders = max(0, (int) ($metrics['todayOrders'] ?? 0));
    $pendingOrders = max(0, (int) ($metrics['pendingOrders'] ?? 0));
    $collectedOrders = max(0, (int) ($metrics['collectedOrders'] ?? 0));
    $lowStock = max(0, (int) ($metrics['lowStock'] ?? 0));
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
    $hasOrderActivity = $todayOrders > 0 || $pendingOrders > 0 || $collectedOrders > 0 || $hasRecentOrders;

    return [
        'isAdmin' => $isAdmin,
        'isManager' => $isManager,
        'isCashier' => $isCashier,
        'canViewBusinessMetrics' => $canViewBusinessMetrics,
        'canManageProducts' => $canManageProducts,
        'showProducts' => $canManageProducts && $hasProducts,
        'showOrdersToday' => true,
        'showPendingOrders' => $hasOrderActivity,
        'showRevenueMetrics' => $canViewBusinessMetrics && $collectedOrders > 0,
        'showAverageOrder' => $canViewBusinessMetrics && $collectedOrders >= 3,
        'showInventoryMetrics' => $canViewBusinessMetrics && $hasProducts,
        'showProcurementMetrics' => $canViewBusinessMetrics && $hasProcurementActivity,
        'showSalesChart' => $canViewBusinessMetrics && $collectedOrders >= 7,
        'showTopSelling' => $canViewBusinessMetrics && $collectedOrders >= 3,
        'showLowStockAlert' => $lowStock > 0,
        'showRecentOrders' => $hasRecentOrders,
        'showStarterPanel' => !$hasProducts && !$hasOrderActivity,
        'hasProducts' => $hasProducts,
        'hasProcurementActivity' => $hasProcurementActivity,
        'hasOrderActivity' => $hasOrderActivity,
        'profile' => !$hasProducts && !$hasOrderActivity
            ? 'starter'
            : ($collectedOrders >= 50 || $totalProducts >= 100 || $totalSuppliers >= 10
                ? 'busy'
                : 'active'),
    ];
}
