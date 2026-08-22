# SwiftOrder V1.1 UX Operational Standard

## Core principle

The POS is a work tool. Speed, clarity and error prevention outrank decoration.

## Cashier workflow

Target path:

search/find product → add/edit cart → choose payment → place order → clear success/failure state

Customer name is optional for ordinary retail sales.

## Feedback

Avoid browser `alert()` for routine success/failure where an in-surface message can communicate:

- what happened;
- whether the transaction completed;
- the order number where relevant;
- what the user should do next.

This is a UX improvement, not a justification for a large frontend rewrite.

## Error messages

Every failure message should answer:

1. What happened?
2. What does it mean?
3. What should the user do next?

## Navigation

Group administration-heavy navigation by task, for example:

- Operations: Orders, Products, Stock, Purchasing, GRNs.
- Control: Users, Sessions, Activity Logs.
- Reporting: Dashboard, Reports.

Do this incrementally; do not destabilise working pages merely to modernise the menu.

## Accessibility and input

- Preserve visible labels.
- Ensure keyboard focus remains usable.
- Do not rely on colour alone to communicate state.
- Avoid destructive actions as ambiguous navigation links.
- Keep form validation messages near the field/action that needs attention.
