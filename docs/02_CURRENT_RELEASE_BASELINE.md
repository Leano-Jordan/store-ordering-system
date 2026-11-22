# SwiftOrder V1.1 Current Release Baseline

**Date:** 22 November 2026  
**Source of truth:** GitHub repository `Leano-Jordan/store-ordering-system`, branch `main`  
**Verified application-source commit:** `9ea77ecdd7de70832f8aec1c40576a1789d0b9a8` (documentation-only updates followed; application source is unchanged).

## Current automated verification

The latest completed main-branch GitHub Actions run passed:

| Gate | Result |
|---|---|
| PHP syntax across tracked PHP files | PASS |
| PHPUnit | PASS — 28 tests, 42 assertions |
| Composer validation and locked install | PASS |
| Composer security audit | PASS — no known advisories |
| JavaScript syntax | PASS |
| ESLint | PASS |
| Semgrep | PASS — 53 rules, 213 files, 0 findings |
| GitHub CodeQL default setup | PASS |
| Gitleaks | PASS — no secrets reported in scanned change |
| Trivy dependency scan | PASS — 0 vulnerabilities |
| MySQL 8.4 schema parity | PASS |
| MariaDB 11.4 schema parity | PASS |
| Rosscore Director health report | PASS |

SonarCloud is configured as optional and was skipped because its project variables/secrets are not configured. It is not counted as a passing scan.

## Third-party license inventory

Trivy identified six dependency license records requiring review:
- Dompdf and its supporting libraries include LGPL terms; Dompdf is classified as restricted/high by the scanner.
- MIT and other permissive notices are also present.

These are license-compliance classifications, not vulnerability findings. Obtain a distribution/legal review of LGPL obligations before shipping the proprietary package.

## Engineering changes in this baseline

- Replaced the legacy stylesheet with `assets/css/swiftorder-ui.css`; active references now point to the SwiftOrder-owned file.
- Removed the old stylesheet and migrated its design tokens to SwiftOrder-specific names.
- Removed cross-product references from current source and operating documents.
- Added PHPUnit regression tests for licensing fail-closed behaviour, asset paths, pagination output and safe JSON encoding.
- Parameterized pagination limits/offsets in activity logs, GRNs, products, purchase orders, stock history and suppliers.
- Centralized JSON response encoding with HTML-sensitive JSON flags.
- Added MySQL/MariaDB schema parity checks and corrected supplier status nullability in the clean schema.
- Added GitHub QA, security, ESLint and Composer lock refresh automation.

## Remaining release blockers

- Dedicated tax-invoice workflow and reproducible invoice acceptance.
- Full order/payment/stock/invoice/report transaction and rollback integration tests.
- Business settings singleton enforcement and operational workflow.
- Upload storage outside webroot and complete upload security review.
- Forward database migrations from the released baseline.
- Backup/restore drill against populated business data.
- Full security penetration testing and mobile/desktop acceptance.
- Offline local-operation acceptance and commercial packaging.
- Coverage percentage is not measured yet; the 80% target is not claimed as met.

## Release decision

**NO-GO for commercial V1.** Automated quality gates are now in place and the latest run passes, but critical business-flow, recovery, upgrade and operational acceptance evidence remains outstanding.
