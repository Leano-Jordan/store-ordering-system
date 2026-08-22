# SwiftOrder V1.1 Test Strategy

## Standard

Static syntax checks are necessary but are not release assurance. Critical business/security paths require automated behavioural tests.

## Minimum test families

### Authentication

- Valid login.
- Invalid credentials.
- Lockout/rate limiting.
- Session creation.
- Logout.
- Timeout.
- Administrative session termination.

### Authorization

- Every role against every protected operation.
- Direct GET/POST access bypass attempts.
- Last-active-admin protection with 0, 1 and multiple active administrators.

### CSRF

- Missing token.
- Invalid token.
- Valid token.

### Orders

- Valid sale.
- Empty cart.
- Inactive product.
- Insufficient stock.
- Invalid product ID.
- Duplicate line items.
- Duplicate request ID.
- Double-click/duplicate submission.
- Database failure after partial writes.
- Audit failure according to the final audit contract.

### Inventory

- Increase.
- Decrease.
- Insufficient stock.
- Concurrent stock operations.
- Rollback after history/audit failure.

### Purchasing/GRN

- Create.
- Edit while editable.
- Reject invalid products/quantities.
- Receive stock atomically.
- Rollback on forced failure.

### Tax invoice

- Required fields present.
- VAT/non-VAT behaviour.
- Invoice number uniqueness.
- Reprint of the same order reproduces stored transaction facts.
- Product price changes after sale do not alter the historical invoice.

## Coverage target

The board target is 80% automated coverage with critical-path tests mandatory regardless of percentage. Coverage must be measured by the chosen test runner and stored as release evidence.

## CI target

Pull requests should run syntax/static checks, automated tests, coverage threshold and dependency/security checks before protected-branch merge.
