# SwiftOrder V1.1 Installation & Upgrade Runbook

## Scope

For local PHP/MySQL installations. This procedure is an operational target and is not proof that the current source already satisfies every release gate.

## Clean installation

1. Install a supported PHP 8.x branch and required extensions.
2. Install a supported Apache/PHP-capable local web server.
3. Install MySQL-compatible database software using InnoDB for transactional tables.
4. Create an empty SwiftOrder database and dedicated database credentials.
5. Import `database/schema.sql` as the clean installation baseline. Alternatively, a migration-first installation may import `database/migrations/001_initial_v1_1_schema.sql`; these two files describe the same initial schema, so never import both into the same database.
6. The active migration directory currently contains only the initial V1.1 baseline. No post-baseline upgrade migration sequence is published yet; do not invent or apply archived pre-V1.1 migrations to a fresh V1.1 database.
7. Configure the external database connection and production secret values.
8. Configure business identity and tax settings through the secured administrator workflow.
9. Create the initial administrator account.
10. Run authentication, RBAC, CSRF and core workflow smoke tests.
11. Create and verify the first backup before customer data is entered.
12. Record installed application version, schema version and installation date.

## Upgrade procedure

1. Confirm the approved release version and its documented source-schema version.
2. Perform and verify a database backup.
3. Back up required application/profile/product assets.
4. Confirm that an explicit forward migration exists for the installed schema version. If none exists, stop the upgrade; do not re-import the clean schema over customer data.
5. Apply only the approved forward migration(s) in documented order.
6. Deploy the controlled application package.
7. Run the smoke suite: login, product access, order creation, stock, purchasing/GRN, reports, invoice access and permissions.
8. Verify application logs and PHP error logs.
9. Record the installed version and migration result.
10. Keep rollback/recovery evidence.

## Failure rule

Never treat a successful file copy as a successful deployment. A release is successful only after the post-deployment smoke and acceptance checks pass.
