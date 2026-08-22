# SwiftOrder V1.1 Release Package Checklist

## Engineering package

May contain:

- Source code.
- Tests.
- Development documentation.
- Local Git history when needed for engineering.

## Customer package

Must exclude:

- `.git/` metadata.
- `.vscode/` and development-only settings.
- Customer-like seed data.
- Password hashes from demonstration/customer datasets.
- Temporary/debug files.
- Local machine paths.
- Development secrets.
- Unapproved test fixtures.

## Customer package must contain

- Controlled application files.
- Authoritative clean database schema/migration path.
- Installation instructions.
- Upgrade procedure.
- Backup/restore procedure.
- Supported runtime matrix.
- Customer-facing licence/support/privacy documents when legally approved.
- Version/release notes.
- Smoke/acceptance checklist.

## Release gate

Do not label the package “commercial V1” until all critical/high release controls and evidence are complete.
