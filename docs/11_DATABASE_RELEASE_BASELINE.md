# SwiftOrder V1.1 Database Release Baseline

## Current state

The current clean schema and initial V1.1 migration now pass a parity check on MySQL 8.4 and MariaDB 11.4. The commercial upgrade path is still incomplete because no post-baseline forward migration is published.

## Required release standard

- One authoritative clean schema.
- Versioned migrations that can be applied from that baseline.
- All transactional business tables explicitly use InnoDB.
- Foreign keys match deployed code.
- Unique request/order/invoice identifiers remain authoritative.
- Stock and quantity invariants are enforced at the database/application boundary.
- Historical order/order-item facts remain the financial truth.

## Specific schema observation

The archived legacy SQL dump does not explicitly declare `ENGINE=InnoDB` on every table definition. The current clean schema and initial V1.1 migration declare InnoDB for transactional tables; the database compatibility workflow verifies all imported base tables use InnoDB.

## Business settings

`business_settings` is treated by invoice code as a single-row configuration, but the schema does not currently enforce a singleton. The release solution must choose and document one of:

- a database-enforced singleton row;
- a unique configuration key;
- a controlled installer/update design that guarantees one authoritative configuration row.

Existing data must be assessed before adding a uniqueness constraint.

## Migration baseline

The active `database/migrations/` directory currently contains only `001_initial_v1_1_schema.sql`, which is an initial clean baseline, not a forward upgrade sequence. Older incremental SQL files are archived under `database/migrations_archive/pre_v1_1/` and must not be applied to a fresh V1.1 database. No post-baseline upgrade migration is currently published. Before commercial release, publish and test explicit forward migrations from the released baseline against populated database copies.

## Current schema parity finding — 22 November 2026

The clean schema and initial V1.1 migration were compared. The supplier status column differed: the clean schema allowed NULL while the migration required NOT NULL. The clean schema has been aligned to NOT NULL. A GitHub Actions matrix now imports both files independently on MySQL 8.4 and MariaDB 11.4 and compares table engines, columns, indexes and foreign keys. The completed GitHub Actions matrix passed on both MySQL 8.4 and MariaDB 11.4, comparing table engines, columns, indexes and foreign keys.

## Verification examples

- Verify all business tables are InnoDB.
- Verify all expected foreign keys exist.
- Verify unique keys for order/request/invoice identifiers.
- Verify one authoritative business settings row.
- Import the clean schema and initial migration independently into empty databases and compare resulting table, column, index and foreign-key definitions.
- Test every published forward migration against a copy of a populated prior-version database.
