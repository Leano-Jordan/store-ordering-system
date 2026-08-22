# SwiftOrder V1.1 Engineering Governance

## Change model

- Work from a known baseline.
- Use small, reviewable changes.
- Do not mix formatting churn with behavioural fixes.
- Keep production PHP modules below 300 lines as an end-state target; extract responsibilities incrementally.
- Preserve working architecture unless correctness, security or maintainability requires a change.

## Git workflow

Target control model:

- protected release branch;
- feature/fix branches;
- pull request for material changes;
- required status checks;
- appropriate reviewer approval;
- CODEOWNERS for security/financial/sensitive paths where the repository supports it;
- dependency/security review in CI.

The connected GitHub account could not independently expose the referenced private repository during the 20-August audit, so these are governance requirements, not claims about the remote repository's current state.

## Pre-merge checks

- PHP syntax lint.
- JavaScript syntax check.
- Automated tests.
- Coverage threshold.
- Dependency/security audit.
- Migration/schema verification where applicable.
- Targeted regression tests for every business-critical change.

## Release rule

A code change is not “fixed” until the intended verification is run and its result is recorded.
