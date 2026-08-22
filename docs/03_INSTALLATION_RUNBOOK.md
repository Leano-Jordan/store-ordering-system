# SwiftOrder V1.1 Installation & Upgrade Runbook

## Scope

For local PHP/MySQL installations. This procedure is an operational target and is not proof that the current source already satisfies every release gate.

## Clean installation

1. Install a supported PHP 8.x branch and required extensions.
2. Install a supported Apache/PHP-capable local web server.
3. Install MySQL-compatible database software using InnoDB for transactional tables.
4. Create an empty SwiftOrder database and dedicated database credentials.
5. Import the authoritative clean schema baseline.
6. Apply versioned migrations in numeric order.
7. Configure the external database connection and production secret values.
8. Configure business identity and tax settings through the secured administrator workflow.
9. Create the initial administrator account.
10. Run authentication, RBAC, CSRF and core workflow smoke tests.
11. Create and verify the first backup before customer data is entered.
12. Record installed application version, schema version and installation date.

## Upgrade procedure

1. Confirm the approved release version.
2. Perform and verify a database backup.
3. Back up required application/profile/product assets.
4. Apply versioned migrations.
5. Deploy the controlled application package.
6. Run the smoke suite: login, product access, order creation, stock, purchasing/GRN, reports, invoice access and permissions.
7. Verify application logs and PHP error logs.
8. Record the installed version and migration result.
9. Keep rollback/recovery evidence.

## Failure rule

Never treat a successful file copy as a successful deployment. A release is successful only after the post-deployment smoke and acceptance checks pass.
