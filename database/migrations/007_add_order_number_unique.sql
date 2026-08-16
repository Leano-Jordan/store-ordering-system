ALTER TABLE orders 
ADD UNIQUE INDEX 
uq_orders_order_number (order_number);