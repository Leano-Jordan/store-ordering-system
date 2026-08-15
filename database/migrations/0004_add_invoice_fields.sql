ALTER TABLE orders 
ADD COLUMN invoice_number 
VARCHAR(50) NULL, ADD COLUMN invoice_issued_at DATETIME NULL;

CREATE UNIQUE INDEX uq_orders_invoice_number ON orders (invoice_number);