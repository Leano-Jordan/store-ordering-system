# SwiftOrder Targeted Batch Fix Record — 20 August 2026

## Scope

Smallest permanent defects first. No framework migration, database redesign, multi-tenancy, payment integration, AI, Redis or wholesale UI rewrite.

## Files changed in this batch

- `update_user.php`
- `save_purchase_order.php`
- `index.php`
- `dashboard.php`
- `edit_product.php`
- `includes/queries/orders_query.php`
- `save_stock_adjustments.php`
- `save_supplier.php`
- `update_supplier.php`
- `goods_received_notes.php`
- `products.php`
- `activity_logs.php`
- `suppliers.php`
- `purchase_orders.php`
- `users.php`
- `reports.php`

## Fixes

### 1. Last-active-admin scalar count

`update_user.php` now reads the `COUNT(*) AS total` value with `fetch_assoc()` and casts it to an integer. `num_rows` is no longer used as the administrator count.

### 2. Dead purchase-order redirects

`save_purchase_order.php` now points its error paths to `add_purchase_order.php`, the actual page.

### 3. Request type hardening

String inputs are checked before `trim()` in supplier, stock-adjustment and purchase-order/search handlers. This prevents array-valued request parameters from reaching PHP string APIs.

### 4. Query-result hardening

Read pages now verify primary/secondary query results before dereferencing them, including dashboard/report/user/supplier/activity/purchase-order related paths.

### 5. POS query guards

`index.php` checks product and category query results before iteration.

### 6. Product edit output encoding

Stored image values in `edit_product.php` are encoded for their HTML/URL contexts, reducing stored-output XSS exposure.

## Verification performed

- PHP 8.4.23 CLI available.
- All 125 application PHP files pass syntax lint.
- All 4 application JavaScript files pass `node --check`.
- No production PHP/JS/SQL `TODO`, `FIXME` or `HACK` markers were found.

## Not claimed as fixed by this batch

- Tax invoice endpoint.
- Business settings management/singleton enforcement.
- Audit failure transaction semantics.
- `update_user.php` broad transaction scope.
- `place_order.php` full commit-failure semantics.
- Test suite/CI.
- Clean schema/migration baseline.
- Backup/restore proof.
- Upload storage outside webroot.
- Oversized-file decomposition.

Those remain scheduled hardening/release work and must not be marked complete merely because this batch passes syntax checks.
