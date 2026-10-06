# SwiftOrder Project Audit Memory

## Current position

**Release status: NO-GO for commercial V1.**

The underlying PHP/MySQL architecture is viable for the board-approved local V1. The current work is stabilization and evidence, not a framework rewrite.

## Verified architecture

Browser / server-rendered PHP pages / form handlers
→ shared authentication, RBAC, CSRF, session and helpers
→ MySQL/InnoDB business data
→ orders → stock → audit → reporting
→ purchasing → receiving → stock → audit

Dompdf is present for PDF output. Core trading is intended to work without internet dependency in the supported local deployment.

## Confirmed release blockers

- Reproducible dedicated tax-invoice workflow is incomplete.
- Audit/transaction semantics require a final explicit contract and failure tests.

- Critical-path tests now have PHPUnit/Node test coverage added; runtime execution and CI evidence are still pending.
- Clean-install schema/migration path is not yet authoritative and reproducible.
- Backup/restore evidence is not demonstrated.
- Business/tax settings need an operational administrator workflow and singleton enforcement.
- Production release packaging must exclude `.git`, development-only material and customer-like data.
- Upload storage remains under webroot and should move to an outside-webroot/controlled path before release.

## Important findings resolved in the 20-August targeted batch

- `update_user.php` now reads the `COUNT(*)` scalar instead of `mysqli_result->num_rows`.
- `update_user.php` now keeps file-system work outside the database transaction and locks the current user/admin set during the final update.
- Product deactivation now passes through the active licence gate.
- The POS now uses in-surface feedback instead of routine browser alerts and has explicit mobile/accessibility polish.
- Dead `save_purchase_order.php` redirects now point to the actual `add_purchase_order.php` endpoint.
- POS product/category query results are checked before dereference.
- Dashboard query/result paths were hardened against direct false-result dereferences.
- Product edit output now uses contextual HTML/URL encoding for stored image values.
- Supplier, stock-adjustment, purchase-order and search request values are checked for scalar/string shape before `trim()`.
- Several read pages now check secondary query results before using them.

The 2026-10-06 Swifty quality batch is source-reviewed and committed. Runtime execution evidence remains a separate release requirement; repository test files cover the new POS feedback and profile-image validation paths.

## Do not regress

- Customer name is optional for ordinary retail sales.
- Server-side product price/state validation remains authoritative.
- `recordAudit()` remains the authoritative audit mechanism where defined.
- No multi-tenancy, payment gateways, Laravel migration, Redis or AI subsystem is part of V1.
