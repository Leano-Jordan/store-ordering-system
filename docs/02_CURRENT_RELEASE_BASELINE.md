# SwiftOrder V1.1 Current Release Baseline

Date: 20 August 2026
Source basis: supplied `SwiftOrder-System_v0.9.2.1.zip`, supplied SQL dump, current governing documents.

## Inventory

- ZIP archive: 1,173 files.
- Application PHP excluding vendor: 125 files.
- Application JavaScript: 4 files.
- Database migrations: 10 files.
- Composer dependency tree present; Dompdf is the main declared application dependency.
- `.git` and `.vscode` are present in the engineering archive.
- Project documentation is present under `docs/`.
- No PHPUnit configuration, CI workflow, Docker deployment definition or repository README was found in the supplied source.

## Static verification for the current edited source

- PHP syntax: 125/125 passed.
- JavaScript syntax: 4/4 passed.
- No production PHP/JS/SQL `TODO`, `FIXME` or `HACK` markers were found in the sweep.
- Redirect verification must resolve paths relative to the including script or project root; a naive relative-to-file scan gives false positives for shared includes.

## Runtime evidence

Observed local CLI runtime: PHP 8.4.23.

Production runtime is not independently evidenced by the project package and must be recorded in the release acceptance record.

## Engineering worktree

The supplied checkout is dirty and contains a large pre-existing set of modified entries. Treat the current checkout as an engineering worktree, not as a clean release baseline.

## Release rule

A static pass does not equal behavioural assurance. Every money, stock, authorization, session, invoice, rollback and recovery path requires executable acceptance evidence before commercial release.
