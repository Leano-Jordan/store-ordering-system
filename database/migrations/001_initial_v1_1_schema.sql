SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';

CREATE TABLE users (
    id int NOT NULL AUTO_INCREMENT,
    full_name varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
    username varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
    password varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
    profile_image varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    role enum('Admin','Manager','Cashier','Kitchen') COLLATE utf8mb4_general_ci NOT NULL,
    status enum('Active','Inactive') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Active',
    created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY username (username)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE business_settings (
    id int NOT NULL AUTO_INCREMENT,
    business_name varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
    business_address text COLLATE utf8mb4_general_ci NOT NULL,
    vat_enabled tinyint(1) NOT NULL DEFAULT '0',
    vat_number varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
    vat_rate decimal(5,2) NOT NULL DEFAULT '15.00',
    next_invoice_number int NOT NULL DEFAULT '1',
    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE products (
    id int NOT NULL AUTO_INCREMENT,
    name varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
    description text COLLATE utf8mb4_general_ci,
    price decimal(10,2) NOT NULL DEFAULT '0.00',
    image varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    category varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Other',
    status varchar(10) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Active',
    stock int NOT NULL DEFAULT '0',
    PRIMARY KEY (id),
    KEY idx_status_stock (status,stock),
    KEY idx_category (category),
    CONSTRAINT chk_products_stock_non_negative CHECK (stock >= 0),
    CONSTRAINT chk_products_price_non_negative CHECK (price IS NULL OR price >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE suppliers (
    id int NOT NULL AUTO_INCREMENT,
    company_name varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
    contact_person varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
    phone varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
    email varchar(120) COLLATE utf8mb4_general_ci DEFAULT NULL,
    address text COLLATE utf8mb4_general_ci,
    notes text COLLATE utf8mb4_general_ci,
    status enum('Active','Inactive') COLLATE utf8mb4_general_ci DEFAULT 'Active',
    created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE orders (
    id int NOT NULL AUTO_INCREMENT,
    order_number varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
    customer_name varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
    items text COLLATE utf8mb4_general_ci,
    total decimal(10,2) NOT NULL DEFAULT '0.00',
    created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    status varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
    payment_method enum('cash_pmt','card_pmt','eft_pmt') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'cash_pmt',
    invoice_number varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
    invoice_issued_at datetime DEFAULT NULL,
    business_name_at_invoice varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
    business_address_at_invoice text COLLATE utf8mb4_general_ci,
    business_vat_number_at_invoice varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
    request_id char(36) COLLATE utf8mb4_general_ci DEFAULT NULL,
    vat_enabled_at_sale tinyint(1) NOT NULL DEFAULT '0',
    vat_rate_at_sale decimal(5,2) NOT NULL DEFAULT '0.00',
    vat_amount decimal(10,2) NOT NULL DEFAULT '0.00',
    subtotal decimal(10,2) NOT NULL DEFAULT '0.00',
    PRIMARY KEY (id),
    UNIQUE KEY order_number (order_number),
    UNIQUE KEY uq_orders_invoice_number (invoice_number),
    UNIQUE KEY uq_orders_request_id (request_id),
    KEY idx_status (status),
    KEY idx_created_at (created_at),
    KEY idx_status_created (status,created_at),
    CONSTRAINT chk_orders_total_non_negative CHECK (total >= 0),
    CONSTRAINT chk_orders_vat_rate_non_negative CHECK (vat_rate_at_sale >= 0),
    CONSTRAINT chk_orders_vat_amount_non_negative CHECK (vat_amount >= 0),
    CONSTRAINT chk_orders_subtotal_non_negative CHECK (subtotal >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE order_items (
    id int NOT NULL AUTO_INCREMENT, order_id int NOT NULL,
    product_id int NOT NULL,
    product_name_at_sale varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
    quantity int NOT NULL, price decimal(10,2) NOT NULL,
    PRIMARY KEY (id), KEY order_id (order_id),
    KEY idx_order_items_product_id (product_id),
    CONSTRAINT chk_order_items_quantity_positive
    CHECK (quantity > 0),
    CONSTRAINT chk_order_items_price_non_negative
    CHECK (price >= 0),
    CONSTRAINT fk_order_items_product
    FOREIGN KEY (product_id) REFERENCES products (id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
    CONSTRAINT order_items_ibfk_1
    FOREIGN KEY (order_id)
    REFERENCES orders (id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE purchase_orders (
    id int NOT NULL AUTO_INCREMENT,
    supplier_id int NOT NULL,
    po_number varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
    status enum('Draft','Pending','Received','Cancelled') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Draft',
    total decimal(10,2) DEFAULT '0.00',
    notes text COLLATE utf8mb4_general_ci,
    created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY po_number (po_number),
    KEY supplier_id (supplier_id),
    CONSTRAINT purchase_orders_ibfk_1
    FOREIGN KEY (supplier_id)
    REFERENCES suppliers (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE purchase_order_items (
    id int NOT NULL AUTO_INCREMENT,
    purchase_order_id int NOT NULL,
    product_id int NOT NULL,
    quantity int NOT NULL,
    cost_price decimal(10,2) NOT NULL,
    line_total decimal(10,2) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_purchase_order_product (purchase_order_id,product_id),
    KEY product_id (product_id),
    CONSTRAINT purchase_order_items_ibfk_1
    FOREIGN KEY (purchase_order_id)
    REFERENCES purchase_orders (id),
    CONSTRAINT purchase_order_items_ibfk_2
    FOREIGN KEY (product_id)
    REFERENCES products (id),
    CONSTRAINT chk_purchase_order_items_quantity_positive
    CHECK (quantity > 0),
    CONSTRAINT chk_purchase_order_items_cost_positive
    CHECK (cost_price > 0),
    CONSTRAINT chk_purchase_order_items_total_non_negative
    CHECK (line_total >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE goods_received_notes (
    id int NOT NULL AUTO_INCREMENT,
    grn_number varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
    purchase_order_id int NOT NULL,
    supplier_id int NOT NULL,
    received_by int NOT NULL,
    total decimal(10,2) NOT NULL,
    notes text COLLATE utf8mb4_general_ci,
    received_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_goods_received_notes_grn_number (grn_number),
    UNIQUE KEY uq_goods_received_notes_purchase_order (purchase_order_id),
    KEY supplier_id (supplier_id),
    KEY received_by (received_by),
    CONSTRAINT goods_received_notes_ibfk_1
    FOREIGN KEY (purchase_order_id)
    REFERENCES purchase_orders (id),
    CONSTRAINT goods_received_notes_ibfk_2
    FOREIGN KEY (supplier_id)
    REFERENCES suppliers (id),
    CONSTRAINT goods_received_notes_ibfk_3
    FOREIGN KEY (received_by)
    REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE goods_received_note_items (
    id int NOT NULL AUTO_INCREMENT,
    grn_id int NOT NULL,
    product_id int NOT NULL,
    quantity decimal(10,2) NOT NULL,
    cost_price decimal(10,2) NOT NULL,
    line_total decimal(10,2) NOT NULL,
    PRIMARY KEY (id), KEY grn_id (grn_id),
    KEY product_id (product_id),
    CONSTRAINT chk_grn_items_quantity_positive
    CHECK (quantity > 0),
    CONSTRAINT chk_grn_items_cost_positive
    CHECK (cost_price > 0),
    CONSTRAINT chk_grn_items_total_non_negative
    CHECK (line_total >= 0),
    CONSTRAINT goods_received_note_items_ibfk_1
    FOREIGN KEY (grn_id)
    REFERENCES goods_received_notes (id) ON DELETE CASCADE,
    CONSTRAINT goods_received_note_items_ibfk_2
    FOREIGN KEY (product_id) REFERENCES products (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE stock_adjustments (
    id int NOT NULL AUTO_INCREMENT,
    product_id int NOT NULL,
    user_id int NOT NULL,
    adjustment_type enum('Increase','Decrease') COLLATE utf8mb4_general_ci NOT NULL,
    quantity int NOT NULL,
    available_stock int NOT NULL,
    reason varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
    notes text COLLATE utf8mb4_general_ci,
    created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id), KEY user_id (user_id),
    KEY idx_product_created (product_id,created_at),
    KEY idx_created_at (created_at),
    CONSTRAINT chk_stock_adjustments_quantity_positive
    CHECK (quantity > 0),
    CONSTRAINT chk_stock_adjustments_available_stock_non_negative
    CHECK (available_stock >= 0),
    CONSTRAINT stock_adjustments_ibfk_1
    FOREIGN KEY (product_id)
    REFERENCES products (id),
    CONSTRAINT stock_adjustments_ibfk_2
    FOREIGN KEY (user_id)
    REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE audit_log (
    id int NOT NULL AUTO_INCREMENT,
    user_id int NOT NULL,
    entity varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
    entity_id int NOT NULL,
    action varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_audit_log_user_id (user_id),
    KEY idx_audit_log_entity (entity),
    KEY idx_audit_log_entity_id (entity_id),
    KEY idx_audit_log_created_at (created_at),
    CONSTRAINT fk_audit_log_user
    FOREIGN KEY (user_id)
    REFERENCES users (id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE audit_changes (
    id int NOT NULL AUTO_INCREMENT,
    audit_id int NOT NULL,
    field_name varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
    old_value text COLLATE utf8mb4_general_ci,
    new_value text COLLATE utf8mb4_general_ci,
    PRIMARY KEY (id),
    KEY idx_audit_changes_audit_id (audit_id),
    CONSTRAINT fk_audit_changes_audit
    FOREIGN KEY (audit_id)
    REFERENCES audit_log (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE activity_logs (
    id int NOT NULL AUTO_INCREMENT,
    user_id int NOT NULL,
    action varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
    created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY user_id (user_id),
    CONSTRAINT activity_logs_ibfk_1
    FOREIGN KEY (user_id)
    REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE login_rate_limits (
    username_hash char(64) COLLATE utf8mb4_general_ci NOT NULL,
    window_started_at datetime NOT NULL,
    failed_attempts int UNSIGNED NOT NULL DEFAULT '0',
    locked_until datetime DEFAULT NULL,
    PRIMARY KEY (username_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE user_sessions (
    id int NOT NULL AUTO_INCREMENT,
    user_id int NOT NULL,
    login_at datetime NOT NULL,
    last_activity_at datetime NOT NULL,
    logout_at datetime DEFAULT NULL,
    status enum('ACTIVE','LOGGED_OUT','TIMED_OUT','TERMINATED') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'ACTIVE',
    PRIMARY KEY (id),
    KEY idx_user_sessions_user_id (user_id),
    KEY idx_user_sessions_status (status),
    KEY idx_user_sessions_last_activity (last_activity_at),
    CONSTRAINT fk_user_sessions_user
    FOREIGN KEY (user_id)
    REFERENCES users (id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;