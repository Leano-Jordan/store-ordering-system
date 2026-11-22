# SwiftOrder Quality Assurance and Security Standard

**Status:** Active engineering standard  
**Applies to:** SwiftOrder PHP/MySQL application, local installation, release packages and GitHub automation  
**Release principle:** A passing static scan is not proof of runtime or commercial readiness.

## Automated GitHub gates

The repository runs the following checks against changes targeting `main` and on scheduled scans:

| Control | Purpose | Release treatment |
|---|---|---|
| PHP syntax | Detect parse errors in tracked PHP files | Blocking |
| Composer validation | Detect invalid or inconsistent dependency metadata | Blocking |
| PHPUnit | Execute unit and regression tests | Blocking |\n| MySQL/MariaDB schema matrix | Import clean schema and initial migration; compare tables, columns, indexes and foreign keys on MySQL 8.4 and MariaDB 11.4 | Blocking |
| Composer audit | Identify known dependency vulnerabilities | Blocking |
| JavaScript syntax | Detect malformed tracked JavaScript | Blocking |
| Semgrep | Detect risky application-code patterns | Findings require triage; scan job is initially non-blocking |
| Gitleaks | Detect committed credentials and secrets | Blocking |
| Trivy filesystem scan | Detect critical/high dependency, secret and configuration findings | Blocking |\n| Trivy license inventory | Identify third-party license obligations for commercial distribution | Informational; legal review required |
| CodeQL | Analyze supported JavaScript and workflow code | Existing repository workflow |
| ESLint | JavaScript lint and code scanning | Existing workflow; findings require triage |

The PHP codebase is not covered by the configured CodeQL language matrix. Semgrep and Trivy provide additional PHP and filesystem coverage; neither replaces manual review or runtime tests. Open-source license classifications are reported separately from security gates and must be reviewed for distribution obligations rather than treated as vulnerabilities.

## Test strategy

Tests must protect business invariants, not merely exercise individual functions.

### POS and order lifecycle
- Reject malformed, duplicate and empty cart submissions.
- Recalculate prices from trusted database records.
- Reject inactive or missing products.
- Prevent quantities above available stock.
- Ensure repeated request identifiers do not create duplicate orders.
- Enforce allowed order status transitions and role restrictions.
- Prove that failed operations leave no partial writes.

### Inventory and purchasing
- Stock never becomes negative.
- Collection deducts stock exactly once.
- Cancelled orders do not deduct stock.
- Receiving a purchase order increases stock exactly once.
- Repeated or invalid receipts do not mutate stock.
- Stock history matches the resulting product quantity.

### Money, VAT and invoices
- Verify decimal rounding and subtotal/VAT/total reconciliation.
- Verify VAT-disabled and VAT-enabled cases.
- Verify invoice numbers are unique and allocated atomically.
- Verify invoice output reproduces stored transaction and business snapshots.
- Verify failed invoice creation rolls back sequence changes.

### Authentication and authorization
- Test login success/failure and inactive accounts.
- Test session ID regeneration, expiry and logout invalidation.
- Test role restrictions against direct GET and POST requests.
- Test missing/invalid CSRF tokens.
- Test upload content, size, extension and storage restrictions.
- Verify denied requests do not mutate data.

### Database and recovery
- Test clean installation against the supported MySQL/MariaDB versions.
- Test upgrades from each supported schema version using a copy of populated data.
- Test backup, restore and post-restore application integrity.
- Test rollback under database errors and interrupted workflows.

## QA and release acceptance

A release candidate is not accepted until evidence exists for:

1. Automated CI checks passing on the exact release commit.
2. Critical workflows tested against a disposable database.
3. Clean installation and supported upgrade paths verified.
4. Backup restored into an isolated test installation.
5. Desktop and mobile usability acceptance completed.
6. Offline local operation verified with network access unavailable.
7. Security findings triaged; no unresolved critical/high exploitable issue.
8. Release package excludes development files, test data, secrets and repository metadata.
9. Operational documentation matches the shipped version.

## Finding severity and handling

- **Critical:** exploitable compromise, data loss, unauthorized financial/stock mutation, or release-blocking failure. Stop release.
- **High:** material security or business-integrity weakness. Fix before release unless explicitly accepted with a documented mitigation.
- **Medium:** meaningful weakness with constrained impact. Prioritize and document.
- **Low/Informational:** hygiene or defense-in-depth improvement. Track without disguising it as a release blocker.

Every finding must record affected file/flow, impact, reproducibility, evidence, fix, regression test and verification result.

## Scope and limitations

Automated scanners can produce false positives and miss business-logic vulnerabilities. The scheduled and pull-request scans do not establish that the live/local installation is secure. Manual source review, database-backed tests, recovery drills and hands-on acceptance remain mandatory.

Do not add network-dependent runtime requirements to the core local POS workflow. CI may download dependencies and scanner databases; the installed application must remain usable without internet connectivity.
