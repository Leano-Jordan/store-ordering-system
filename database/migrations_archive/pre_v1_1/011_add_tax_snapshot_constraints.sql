ALTER TABLE orders ADD CONSTRAINT chk_orders_vat_rate_non_negative
CHECK (vat_rate_at_sale >= 0),

ADD CONSTRAINT chk_orders_vat_amount_non_negative
CHECK (vat_amount >= 0),

ADD CONSTRAINT chk_orders_subtotal_non_negative
CHECK (subtotal >= 0);