# SwiftOrder V1.1 Database Release Baseline

## Current state

The supplied schema has strong business constraints and transactional tables, but the release path is not yet a clean authoritative baseline.

## Required release standard

- One authoritative clean schema.
- Versioned migrations that can be applied from that baseline.
- All transactional business tables explicitly use InnoDB.
- Foreign keys match deployed code.
- Unique request/order/invoice identifiers remain authoritative.
- Stock and quantity invariants are enforced at the database/application boundary.
- Historical order/order-item facts remain the financial truth.

## Specific schema observation

The supplied SQL dump does not explicitly declare `ENGINE=InnoDB` on every table definition; notably `products` and `purchase_order_items` rely on the server default. The clean release schema should state the engine explicitly instead of depending on environment defaults.

## Business settings

`business_settings` is treated by invoice code as a single-row configuration, but the schema does not currently enforce a singleton. The release solution must choose and document one of:

- a database-enforced singleton row;
- a unique configuration key;
- a controlled installer/update design that guarantees one authoritative configuration row.

Existing data must be assessed before adding a uniqueness constraint.

## Migration baseline

The current migration sequence begins at `0004` and is incremental. Before commercial release, publish a clean baseline or an explicitly documented clean-schema-plus-migrations installation contract.

## Verification examples

- Verify all business tables are InnoDB.
- Verify all expected foreign keys exist.
- Verify unique keys for order/request/invoice identifiers.
- Verify one authoritative business settings row.
- Run the schema against an empty database and then apply all migrations.
