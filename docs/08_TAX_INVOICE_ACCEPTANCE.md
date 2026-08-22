# SwiftOrder V1.1 Tax Invoice Acceptance Checklist

## Product boundary

SwiftOrder V1 provides basic tax-invoice capability. It is not accounting software and does not perform VAT-return submission.

## Stored facts

The printable invoice must be reproducible from stored transaction facts, including:

- Invoice identifier/number.
- Invoice issue date.
- Supplier/business identity as configured.
- Customer details when applicable/available.
- Line descriptions.
- Quantities.
- Unit values and applicable tax treatment.
- Subtotal.
- VAT/tax amount and rate where applicable.
- Total.
- Payment method where the supported invoice workflow requires it.

## Critical reproducibility rule

Never re-price an historical invoice from the current `products` table. Use the order/order-item records and stored VAT snapshot captured for the transaction.

## Acceptance tests

1. Create a sale.
2. Confirm the order stores all required financial facts.
3. Generate the tax invoice from the order.
4. Change the product's current selling price.
5. Re-open/reprint the historical invoice.
6. Verify historical unit price, tax and total remain unchanged.
7. Verify invoice number is not regenerated merely by reprinting.
8. Verify the invoice is unavailable where no valid stored invoice number exists, unless the workflow explicitly creates it once under controlled rules.

## Professional review

SARS requirements must be checked against the current official guidance at release time, and the final commercial/tax position must receive appropriate South African professional review.
