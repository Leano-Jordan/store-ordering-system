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

- Critical-path unit tests now execute in GitHub Actions; database-backed critical-path integration evidence remains pending.
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

## 2026-11-22 — QA and identity hardening

### Changes committed to GitHub main

- Replaced the legacy stylesheet filename with `assets/css/swiftorder-ui.css`.
- Renamed the stylesheet design tokens to the SwiftOrder namespace.
- Updated the shared header, login page and invoice print view to load the SwiftOrder-owned stylesheet.
- Removed the legacy stylesheet file after updating its consumers.
- Corrected the PHPUnit base-class import in `tests/Unit/UserUpdateHelpersTest.php`.
- Added `.github/workflows/swiftorder-qa.yml` for PHP syntax, Composer validation, PHPUnit, JavaScript syntax, dependency audit and Semgrep scanning.
- Added `.github/workflows/swiftorder-security.yml` for Gitleaks and Trivy.
- Added `docs/QUALITY_ASSURANCE_AND_SECURITY.md` as the QA, security and release-evidence standard.
- Added automated Composer lock refresh on main when dependency constraints change.
- Upgraded PHPUnit to 13.4.8 after CI dependency auditing detected CVE-2026-119193 in the prior 13.4.0 lock.

### Verification status

- Repository writes are committed on `main`; no feature branch was created.
- PHP syntax validation passed across tracked PHP files.
- PHPUnit passed: 25 tests and 32 assertions on the first full run after correcting the test filename and PHPUnit configuration.
- JavaScript syntax and ESLint workflows passed; Semgrep completed successfully on an earlier run.
- Gitleaks completed successfully with no secrets detected in the scanned commit.
- Composer audit identified CVE-2026-119193 in PHPUnit 13.4.0; the lockfile was refreshed to PHPUnit 13.4.8, a patched version. A post-refresh Composer audit run is still required.
- Trivy initially reported zero dependency vulnerabilities but flagged three LGPL license classifications as HIGH; the workflow now separates license inventory from blocking security findings. The corrected Trivy workflow still requires a completed run.
- No database-backed integration result, penetration-test result or recovery-drill result is claimed.
- Commercial release remains NO-GO until the automated workflows execute successfully and the critical business/recovery acceptance evidence is recorded.

### Next engineering priorities

1. Confirm the post-refresh Composer audit and corrected Trivy runs are clean.
2. Expand executable tests around order idempotency, stock invariants, transaction rollback, invoice/VAT reconciliation, authorization and CSRF.
3. Verify schema installation/upgrades and backup/restore against disposable databases.
4. Complete upload storage isolation and security review.
5. Add database-backed acceptance tests for money, stock, permissions and recovery.
