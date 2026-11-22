# SwiftOrder Release Hardening Engine Memory

## Current Repository State
- Repository: `Leano-Jordan/store-ordering-system`
- Baseline before this memory bootstrap: `1556c4c1590cde574e818deeabcb431fdf89289e`
- Current sprint: V1.1 Release Hardening
- Current implementation source of truth: GitHub repository
- Product target source of truth: approved SwiftOrder V1.1 specification
- This file is operational continuity state, not evidence by itself.

## Status Rules
Use these labels accurately:
- VERIFIED = direct repository/runtime/test evidence exists.
- KNOWN = established from current source or approved documentation but not necessarily runtime-proven.
- UNVERIFIED = implementation may exist but required evidence is absent.
- PARTIAL = only part of the requirement is implemented or evidenced.
- BLOCKED = cannot safely proceed without missing source, environment evidence, destructive test, or user decision.
- REGRESSION = previously correct behaviour has been broken.

Never treat discussed, planned, or code-present as verified.

## Operating Rules
1. Read this file at the beginning of every release-hardening batch.
2. Read the current GitHub HEAD before issuing fixes.
3. Inspect recent commits and verify previous fixes before finding new defects.
4. GitHub is the implementation source of truth; do not rely on stale prior answers.
5. Read the approved V1.1 specification for the relevant release requirement.
6. Inspect the actual source, surrounding code, callers, callees, variables, SQL consumers, and side effects before proposing a fix.
7. Never fabricate current code, schema, variables, functions, session keys, POST/GET fields, selectors, or business rules.
8. If exact source cannot be established, mark the item BLOCKED instead of inventing a fix.
9. Prefer several safely related fixes in one batch rather than artificial one-fix-at-a-time work.
10. Do not reissue a fix that is already correctly implemented.
11. Preserve protected contracts unless a confirmed defect requires change.
12. Prefer the existing architecture when it is sound; do not introduce unnecessary frameworks, abstractions, Docker, or dependencies.
13. Do not modify the repository, commit, or push changes unless the user explicitly authorizes repository modification.

## Repository Modification Workflow
The Founder retains final authority over repository changes. The assistant may inspect freely and may modify, commit and push directly only when the Founder explicitly authorizes that execution in the active task. Without that authorization, provide exact source changes for the Founder to apply.

Every code fix must use:

CHANGE THIS CODE:
```text
[exact current GitHub code]
```

TO THIS CODE:
```text
[exact replacement code]
```

Always specify:
- exact file path
- exact location/function/block
- exact current code
- exact replacement code
- why the change is required
- concrete verification

Do NOT tell the user only "test this", "run that", "verify it", or similar vague instructions. Every verification instruction must contain the actual command, SQL, HTTP request, input, expected result, or exact failure condition needed to prove the change.

The user does not want to investigate AI findings or reverse-engineer recommendations. The assistant must do the source investigation first.

When direct execution is authorized, preserve the existing main branch unless the Founder explicitly requests otherwise. Do not create feature branches as a default workflow. Do not claim a fix is verified until the relevant CI/runtime evidence exists.

## Batch Completion Contract
After implementation:
1. Verify current HEAD.
2. Verify changed files.
3. Verify the implemented fixes against current source.
4. Scan for regressions.
5. Record actual test/evidence status.
6. Update this memory with only facts supported by evidence.
7. Update the engineering board and release gates.
8. Select the next highest-value defect cluster.

Commit workflow: Apply -> test -> commit -> push -> re-scan.

## Required Fix Priorities
Highest priority:
1. Licensing enforcement
2. Backup / restore
3. Financial reconciliation
4. Stock invariants
5. Order invariants
6. Authorization
7. Authentication
8. Session security
9. Transaction atomicity
10. Invoice reproduction
11. VAT / tax
12. Reporting consistency
13. Critical-path automated tests
14. Recovery
15. Production configuration

Do not spend release-hardening effort on cosmetic UI work while a release blocker remains.

## Financial Reconciliation
Trace:
ORDER -> ORDER ITEMS -> SUBTOTAL -> DISCOUNT -> VAT -> TOTAL -> PAYMENT/COLLECTION -> INVOICE -> DASHBOARD -> REPORT -> CSV -> PDF

Where required, prove that database values and generated outputs agree. Pay attention to duplicated calculations, stale totals, VAT authority differences, rounding, invoice snapshots, report sources, CSV/PDF discrepancies, and collected amounts.

## Stock Integrity
Continuously protect these invariants:
- stock >= 0
- collected order -> exactly one stock deduction
- GRN -> exactly one stock increase
- cancelled order -> no unintended stock mutation
- duplicate request -> no duplicate mutation
- concurrent collection -> no negative stock
- failed transaction -> no partial stock mutation

Do not re-recommend transactions generically when the repository already has them; inspect the remaining failure path.

## Authorization / Security Verification
Check direct URL, direct POST, forged POST, wrong role, inactive user, expired/terminated session, missing CSRF, and tampered identifiers. Confirm no state mutation occurs when authorization fails.

## Licensing Gate
If licensing is required by the approved V1.1 commercial specification, inspect entitlement storage, expiry, grace period, cached entitlement, clock handling, tampering resistance, offline behaviour, application enforcement, administrative override, renewal, and expired behaviour.

Missing required enforcement is a RELEASE BLOCKER, not generic technical debt.

## Backup / Restore Gate
A backup file is not proof of recoverability. Required evidence is:
BACKUP -> RESET/DESTROY TEST INSTANCE -> RESTORE -> APPLICATION START -> LOGIN -> PRODUCTS -> ORDERS -> STOCK -> INVOICE -> REPORTS -> PASS

If only operational evidence is missing, classify it as MISSING EVIDENCE rather than inventing a code defect.

## Critical Test Priorities
Order totals; duplicate orders; status transitions; stock deduction; insufficient stock; concurrent stock; GRN rollback; invoice numbering; VAT; authorization; CSRF; sessions; invoice reproduction; report reconciliation.

Inspect the existing test framework before adding testing infrastructure.

## Protected Contracts
Unless a confirmed defect requires a change, preserve:
- database table names
- database column names
- status values
- role names
- session keys
- POST parameters
- GET parameters
- form field names
- URLs
- JavaScript IDs/classes
- AJAX/API contracts
- existing order lifecycle
- existing financial formulas
- selector contracts

## PHP Compatibility
Do not introduce syntax incompatible with the project's actual PHP runtime. In particular, do not reintroduce PHP 8 union return syntax such as `mysqli_result | false` unless repository compatibility evidence explicitly permits it and the user authorizes it.

## Current Batch Record
### Batch 01
- Status: KNOWN from repository history.
- Database hardening changes were merged before the current Batch 02 baseline.

### Batch 02
- Requested fixes: 7.
- Fix 01: invoice financial-total reconciliation — COMMITTED at `1556c4c1590cde574e818deeabcb431fdf89289e`.
- Fixes 02-07: product status, order status, purchase-order total, GRN total, supplier status, and obsolete nullable product-price check — NOT YET VERIFIED IN CURRENT HEAD and remain open unless a newer commit proves otherwise.

## Current Release Blockers / Gates
- Licensing enforcement: BLOCKER / must be verified against the commercial V1.1 requirement.
- Backup/restore execution evidence: BLOCKER / operational evidence required.
- Critical-path automated/manual evidence: BLOCKER / evidence required.

Other release gates must be classified as VERIFIED, UNVERIFIED, PARTIAL, BLOCKER, or EXCLUDED based on current repository and test evidence.

## Important Rule
Code exists != verified.
Discussed != fixed.
Fixed != tested.
Tested != commercially released.

## Next Scan Behaviour
Every new commit resets the scan baseline. Start from the new HEAD, verify what changed, confirm previous fixes, scan regressions, then select the highest-value remaining defect cluster. Never restart the audit from zero without evidence requiring it.


## Swifty Handover — 6 October 2026

### Founder code-style constraint
Swift Order is the Founder's first major project and substantial portions were personally edited by the Founder. The Founder understands the existing code style and wants Swifty to continue in that style.

Quality improvement must therefore be evolutionary:
- preserve understandable existing structure and conventions where sound;
- avoid unnecessary abstraction or architectural reinvention;
- do not copy another product's architecture into Swift Order;
- use another product only as a benchmark for quality, reliability, testing, UX and release discipline;
- introduce new patterns only where current evidence shows they solve a real Swift Order problem and remain understandable to the Founder.

### Active mission
Swifty is taking ownership of the next Swift Order audit-and-fix cycle.

Mission:
1. Audit current HEAD against the approved V1.1 target and actual source.
2. Fix confirmed high-value defects in coherent batches.
3. Preserve protected contracts unless a defect requires change.
4. Verify changes with concrete evidence/tests.
5. Re-scan for regressions after each meaningful batch.
6. Keep engineering memory and release gates evidence-based.
7. Continue through the highest-value remaining defect cluster.

The objective is to raise Swift Order's quality through its own requirements and evidence, without importing external product architecture or making its code unnecessarily difficult for its Founder to maintain.


### Audit/Fix Batch — 6 October 2026
- Confirmed defect: `receive_purchase_order.php` called `recordAudit()` without loading `includes/audit.php`, creating a runtime failure on successful receipt flow before audit execution.
- Fix committed at `d4ccb748d3c2ce53ee8282255aa9dc7f8c102a52`.
- Verification: current repository source now loads `includes/audit.php` before the receipt transaction and its existing `recordAudit()` call.
- No architectural rewrite or new abstraction introduced.
- Next target: continue source audit for confirmed release-impacting defects, prioritising licensing, backup/restore, financial/stock/order invariants, authorization/security, and critical-path evidence.


## 2026-11-22 — QA and Security Automation Batch

### Committed changes
- SwiftOrder-owned stylesheet: `assets/css/swiftorder-ui.css`; legacy stylesheet file removed and all active references migrated.
- GitHub QA workflow: `.github/workflows/swiftorder-qa.yml`.
- GitHub security workflow: `.github/workflows/swiftorder-security.yml`.
- ESLint flat configuration and workflow repair.
- Composer strict metadata and lock refresh workflow.
- PHPUnit test import/filename/configuration repairs and additional regression tests.
- QA/security/release evidence standard added under `docs/QUALITY_ASSURANCE_AND_SECURITY.md`.

### Verified
- PHP syntax scan passed.
- PHPUnit passed with 25 tests and 32 assertions.
- JavaScript syntax passed.
- ESLint passed.
- Semgrep passed on a completed run.
- Gitleaks reported no secrets in the scanned commit.
- Composer lock now resolves PHPUnit 13.4.8, above the affected 13.4.0 release.

### Pending verification
- Post-refresh Composer audit for CVE-2026-119193.
- Corrected Trivy security scan and informational license inventory.
- Database-backed order/stock/payment/invoice integration tests.
- Installation/upgrade and backup/restore drills.
- Manual penetration testing and mobile/desktop acceptance.

### Current release decision
NO-GO until the remaining automated runs complete and the business-critical integration and recovery evidence is recorded.


## 2026-11-22 — Verified QA and Security Convergence

### Main-branch evidence
- PHPUnit: 28 tests, 42 assertions — PASS.
- PHP syntax — PASS.
- Composer validate/install/audit — PASS; no known dependency advisories.
- JavaScript syntax and ESLint — PASS.
- Semgrep: 53 rules / 213 files / 0 findings; SARIF uploaded — PASS.
- GitHub CodeQL default setup — PASS.
- Gitleaks — PASS; no secrets reported in scanned change.
- Trivy dependency scan — PASS; 0 vulnerabilities.
- MySQL 8.4 and MariaDB 11.4 schema/migration parity — PASS.
- Trivy license inventory: LGPL dependencies classified as restricted/high; legal/distribution review remains open.

### Security and source changes
- Bound pagination limit/offset values in activity logs, goods-received notes, products, purchase orders, stock history and suppliers.
- Centralized JSON output encoding with HTML-sensitive flags and added JSON encoding regression coverage.
- Updated the order UI to handle structured HTTP error responses.
- Removed unnecessary request-derived identifiers from operational error logs.
- Added regression coverage for pagination attribute encoding.
- Current source tree and indexed code search contain no active cross-product product-name references or stylesheet filename remnants.

### Release gates still open
- Database-backed financial/order/stock/invoice integration tests.
- Invoice/VAT reconciliation and transaction failure semantics.
- Business settings singleton/operational workflow.
- Upload storage isolation.
- Forward upgrade migrations from the V1.1 baseline.
- Populated-data backup/restore drill.
- Full penetration test and desktop/mobile/offline acceptance.
- Coverage threshold remains unmeasured; do not claim 80% coverage.

**Release decision: NO-GO** pending closure of business, recovery and operational acceptance gates.
