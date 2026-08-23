ALTER TABLE orders
    ADD COLUMN business_name_at_invoice VARCHAR(150) NULL
        AFTER invoice_issued_at,
    ADD COLUMN business_address_at_invoice TEXT NULL
        AFTER business_name_at_invoice,
    ADD COLUMN business_vat_number_at_invoice VARCHAR(50) NULL
        AFTER business_address_at_invoice;

ALTER TABLE order_items
    ADD COLUMN product_name_at_sale VARCHAR(150) NULL
        AFTER product_id;

UPDATE order_items oi
INNER JOIN products p
    ON p.id = oi.product_id
SET oi.product_name_at_sale = LEFT(TRIM(p.name), 150)
WHERE oi.product_name_at_sale IS NULL;

ALTER TABLE order_items
    MODIFY COLUMN product_name_at_sale VARCHAR(150) NOT NULL;