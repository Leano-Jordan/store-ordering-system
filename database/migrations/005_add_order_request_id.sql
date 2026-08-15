ALTER TABLE orders 
    ADD COLUMN request_id CHAR(36) 
    NULL;

    CREATE UNIQUE INDEX 
    uq_orders_request_id 
        ON orders (request_id);