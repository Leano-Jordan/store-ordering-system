# SwiftOrder V1.1 Backup & Restore Runbook

## Backup scope

Back up:

- MySQL database.
- Product/profile/application assets required to reproduce the installation.
- Controlled configuration/secret material using an appropriate secure mechanism.
- Installed version and schema/migration version.

## Minimum practice

- Keep at least one backup copy separate from the primary working environment when justified by customer risk.
- Protect backup files with appropriate access control and encryption.
- Never log passwords, connection secrets or unnecessary personal information.

## Restore drill

1. Start a clean test environment.
2. Restore the database backup.
3. Restore required files/assets.
4. Restore the matching application version.
5. Reapply only the configuration that is intentionally environment-specific.
6. Run login and RBAC tests.
7. Verify representative products, orders, stock history, purchasing/GRNs, reports and audit records.
8. Verify invoice reproduction from stored transaction facts.
9. Confirm no schema migration is missing.
10. Record the restore result, duration, observed issues and the verified recovery point.

A backup file existing on disk is not evidence of recoverability. The restore drill itself is the evidence.

## Customer responsibility

The commercial agreement must define what SwiftOrder support does and does not cover. Customer backup responsibility must not be implied away by support language.
