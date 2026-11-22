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

- Critical-path unit tests execute in GitHub Actions; database-backed business-flow integration evidence remains pending.
- Clean schema and initial V1.1 migration parity is verified on MySQL 8.4 and MariaDB 11.4; forward upgrade migrations are not yet published.
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

- All changes are committed directly to `main`; no feature branches were created.
- PHP syntax validation passed across tracked PHP files.
- PHPUnit passed: **28 tests, 42 assertions**.
- Composer validation, locked install and Composer security audit passed; no known dependency advisories remain.
- JavaScript syntax and ESLint passed.
- Semgrep passed: **53 rules, 213 files, zero findings**; SARIF was uploaded to GitHub Code Scanning.
- GitHub CodeQL default setup passed.
- Gitleaks passed with no secrets reported in the scanned change.
- Trivy dependency scan passed with zero vulnerabilities.
- MySQL 8.4 and MariaDB 11.4 schema compatibility/parity tests passed.
- Trivy license inventory identified six license records, including LGPL dependencies classified as restricted/high; this requires distribution/legal review, not vulnerability remediation.
- The latest main-branch SwiftOrder QA and security workflows completed successfully.
- No full penetration test, populated-data backup/restore drill, or end-to-end financial integration acceptance is claimed.
- Commercial release remains NO-GO until the remaining business and recovery gates close.
### Next engineering priorities

1. Add database-backed tests for order idempotency, stock invariants, transaction rollback, invoice/VAT reconciliation, authorization and CSRF.
2. Complete upload storage isolation and security review.
3. Publish and test forward database migrations from the V1.1 baseline.
4. Execute populated-data backup/restore and full installation recovery drills.
5. Complete penetration testing plus desktop, mobile and offline acceptance.
