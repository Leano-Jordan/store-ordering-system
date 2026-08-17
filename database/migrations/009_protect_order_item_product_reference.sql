ALTER TABLE order_items
DROP FOREIGN KEY fk_order_items_product;

ALTER TABLE order_items
MODIFY product_id INT NOT NULL;

ALTER TABLE order_items
ADD CONSTRAINT fk_order_items_product
FOREIGN KEY (product_id)
REFERENCES products(id)
ON DELETE RESTRICT
ON UPDATE CASCADE
;