ALTER TABLE products 
ADD CONSTRAINT chk_products_stock_non_negative 
CHECK (stock >= 0);

ALTER TABLE products 
ADD CONSTRAINT chk_products_price_non_negative 
CHECK (price is NULL OR price >= 0);

ALTER TABLE purchase_order_items 
ADD CONSTRAINT chk_purchase_order_items_quantity_positive 
CHECK (quantity >= 0);

ALTER TABLE purchase_order_items 
ADD CONSTRAINT chk_purchase_order_items_cost_positive 
CHECK (cost_price >= 0);

ALTER TABLE purchase_order_items 
ADD CONSTRAINT chk_purchase_order_items_total_non_negative 
CHECK (line_total >= 0);
