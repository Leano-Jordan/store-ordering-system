-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 04, 2026 at 01:40 PM
-- Server version: 8.0.36
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: 'swiftorder_release_test'
--

-- --------------------------------------------------------

--
-- Table structure for table 'activity_logs'
--

CREATE TABLE 'activity_logs' (
  'id' int NOT NULL,
  'user_id' int NOT NULL,
  'action' varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  'created_at' timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table 'audit_changes'
--

CREATE TABLE 'audit_changes' (
  'id' int NOT NULL,
  'audit_id' int NOT NULL,
  'field_name' varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  'old_value' text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  'new_value' text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table 'audit_log'
--

CREATE TABLE 'audit_log' (
  'id' int NOT NULL,
  'user_id' int NOT NULL,
  'entity' varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  'entity_id' int NOT NULL,
  'action' varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  'created_at' datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table 'business_settings'
--

CREATE TABLE 'business_settings' (
  'id' int NOT NULL DEFAULT '1',
  'business_name' varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  'business_address' text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  'vat_enabled' tinyint(1) NOT NULL DEFAULT '0',
  'vat_number' varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  'vat_rate' decimal(5,2) NOT NULL DEFAULT '15.00',
  'next_invoice_number' int NOT NULL DEFAULT '1',
  'created_at' datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  'updated_at' datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table 'business_settings'
--

INSERT INTO 'business_settings' ('id', 'business_name', 'business_address', 'vat_enabled', 'vat_number', 'vat_rate', 'next_invoice_number', 'created_at', 'updated_at') VALUES
(1, 'SETUP REQUIRED', 'SETUP REQUIRED', 0, NULL, 15.00, 1, '2026-09-04 12:39:56', '2026-09-04 12:39:56');

-- --------------------------------------------------------

--
-- Table structure for table 'goods_received_notes'
--

CREATE TABLE 'goods_received_notes' (
  'id' int NOT NULL,
  'grn_number' varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  'purchase_order_id' int NOT NULL,
  'supplier_id' int NOT NULL,
  'received_by' int NOT NULL,
  'total' decimal(10,2) NOT NULL,
  'notes' text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  'received_at' timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
-- Table structure for table 'goods_received_note_items'
--

CREATE TABLE 'goods_received_note_items' (
  'id' int NOT NULL,
  'grn_id' int NOT NULL,
  'product_id' int NOT NULL,
  'quantity' decimal(10,2) NOT NULL,
  'cost_price' decimal(10,2) NOT NULL,
  'line_total' decimal(10,2) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table 'login_rate_limits'
--

CREATE TABLE 'login_rate_limits' (
  'username_hash' char(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  'window_started_at' datetime NOT NULL,
  'failed_attempts' int UNSIGNED NOT NULL DEFAULT '0',
  'locked_until' datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table 'orders'
--

CREATE TABLE 'orders' (
  'id' int NOT NULL,
  'order_number' varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  'customer_name' varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  'items' text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  'total' decimal(10,2) NOT NULL DEFAULT '0.00',
  'created_at' timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  'status' varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
  'payment_method' enum('cash_pmt','card_pmt','eft_pmt') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'cash_pmt',
  'invoice_number' varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  'invoice_issued_at' datetime DEFAULT NULL,
  'business_name_at_invoice' varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  'business_address_at_invoice' text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  'business_vat_number_at_invoice' varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  'request_id' char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  'vat_enabled_at_sale' tinyint(1) NOT NULL DEFAULT '0',
  'vat_rate_at_sale' decimal(5,2) NOT NULL DEFAULT '0.00',
  'vat_amount' decimal(10,2) NOT NULL DEFAULT '0.00',
  'subtotal' decimal(10,2) NOT NULL DEFAULT '0.00'
) ;

-- --------------------------------------------------------

--
-- Table structure for table 'order_items'
--

CREATE TABLE 'order_items' (
  'id' int NOT NULL,
  'order_id' int NOT NULL,
  'product_id' int NOT NULL,
  'product_name_at_sale' varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  'quantity' int NOT NULL,
  'price' decimal(10,2) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table 'products'
--

CREATE TABLE 'products' (
  'id' int NOT NULL,
  'name' varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  'description' text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  'price' decimal(10,2) DEFAULT NULL,
  'image' varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  'category' varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Other',
  'status' varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Active',
  'stock' int NOT NULL DEFAULT '0'
) ;

-- --------------------------------------------------------

--
-- Table structure for table 'purchase_orders'
--

CREATE TABLE 'purchase_orders' (
  'id' int NOT NULL,
  'supplier_id' int NOT NULL,
  'po_number' varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  'status' enum('Draft','Pending','Received','Cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Draft',
  'total' decimal(10,2) NOT NULL DEFAULT '0.00',
  'notes' text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  'created_at' timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
-- Table structure for table 'purchase_order_items'
--

CREATE TABLE 'purchase_order_items' (
  'id' int NOT NULL,
  'purchase_order_id' int NOT NULL,
  'product_id' int NOT NULL,
  'quantity' int NOT NULL,
  'cost_price' decimal(10,2) NOT NULL,
  'line_total' decimal(10,2) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table 'stock_adjustments'
--

CREATE TABLE 'stock_adjustments' (
  'id' int NOT NULL,
  'product_id' int NOT NULL,
  'user_id' int NOT NULL,
  'adjustment_type' enum('Increase','Decrease') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  'quantity' int NOT NULL,
  'available_stock' int NOT NULL,
  'reason' varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  'notes' text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  'created_at' timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
-- Table structure for table 'suppliers'
--

CREATE TABLE 'suppliers' (
  'id' int NOT NULL,
  'company_name' varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  'contact_person' varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  'phone' varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  'email' varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  'address' text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  'notes' text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  'status' enum('Active','Inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Active',
  'created_at' timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table 'users'
--

CREATE TABLE 'users' (
  'id' int NOT NULL,
  'full_name' varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  'username' varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  'password' varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  'profile_image' varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  'role' enum('Admin','Manager','Cashier','Kitchen') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  'status' enum('Active','Inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Active',
  'created_at' timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table 'user_sessions'
--

CREATE TABLE 'user_sessions' (
  'id' int NOT NULL,
  'user_id' int NOT NULL,
  'login_at' datetime NOT NULL,
  'last_activity_at' datetime NOT NULL,
  'logout_at' datetime DEFAULT NULL,
  'status' enum('ACTIVE','LOGGED_OUT','TIMED_OUT','TERMINATED') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'ACTIVE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table 'activity_logs'
--
ALTER TABLE 'activity_logs'
  ADD PRIMARY KEY ('id'),
  ADD KEY 'idx_activity_logs_user_id' ('user_id');

--
-- Indexes for table 'audit_changes'
--
ALTER TABLE 'audit_changes'
  ADD PRIMARY KEY ('id'),
  ADD KEY 'idx_audit_changes_audit_id' ('audit_id');

--
-- Indexes for table 'audit_log'
--
ALTER TABLE 'audit_log'
  ADD PRIMARY KEY ('id'),
  ADD KEY 'idx_audit_log_user_id' ('user_id'),
  ADD KEY 'idx_audit_log_entity' ('entity'),
  ADD KEY 'idx_audit_log_entity_id' ('entity_id'),
  ADD KEY 'idx_audit_log_created_at' ('created_at');

--
-- Indexes for table 'business_settings'
--
ALTER TABLE 'business_settings'
  ADD PRIMARY KEY ('id');

--
-- Indexes for table 'goods_received_notes'
--
ALTER TABLE 'goods_received_notes'
  ADD PRIMARY KEY ('id'),
  ADD UNIQUE KEY 'uq_grn_number' ('grn_number'),
  ADD UNIQUE KEY 'uq_grn_purchase_order' ('purchase_order_id'),
  ADD KEY 'idx_grn_supplier_id' ('supplier_id'),
  ADD KEY 'idx_grn_received_by' ('received_by');

--
-- Indexes for table 'goods_received_note_items'
--
ALTER TABLE 'goods_received_note_items'
  ADD PRIMARY KEY ('id'),
  ADD KEY 'idx_grn_items_grn_id' ('grn_id'),
  ADD KEY 'idx_grn_items_product_id' ('product_id');

--
-- Indexes for table 'login_rate_limits'
--
ALTER TABLE 'login_rate_limits'
  ADD PRIMARY KEY ('username_hash');

--
-- Indexes for table 'orders'
--
ALTER TABLE 'orders'
  ADD PRIMARY KEY ('id'),
  ADD UNIQUE KEY 'uq_orders_order_number' ('order_number'),
  ADD UNIQUE KEY 'uq_orders_invoice_number' ('invoice_number'),
  ADD UNIQUE KEY 'uq_orders_request_id' ('request_id'),
  ADD KEY 'idx_orders_status' ('status'),
  ADD KEY 'idx_orders_created_at' ('created_at'),
  ADD KEY 'idx_orders_status_created' ('status','created_at');

--
-- Indexes for table 'order_items'
--
ALTER TABLE 'order_items'
  ADD PRIMARY KEY ('id'),
  ADD KEY 'idx_order_items_order_id' ('order_id'),
  ADD KEY 'idx_order_items_product_id' ('product_id');

--
-- Indexes for table 'products'
--
ALTER TABLE 'products'
  ADD PRIMARY KEY ('id'),
  ADD KEY 'idx_products_status_stock' ('status','stock'),
  ADD KEY 'idx_products_category' ('category');

--
-- Indexes for table 'purchase_orders'
--
ALTER TABLE 'purchase_orders'
  ADD PRIMARY KEY ('id'),
  ADD UNIQUE KEY 'uq_purchase_orders_number' ('po_number'),
  ADD KEY 'idx_purchase_orders_supplier_id' ('supplier_id');

--
-- Indexes for table 'purchase_order_items'
--
ALTER TABLE 'purchase_order_items'
  ADD PRIMARY KEY ('id'),
  ADD UNIQUE KEY 'uq_purchase_order_product' ('purchase_order_id','product_id'),
  ADD KEY 'idx_purchase_order_items_product_id' ('product_id');

--
-- Indexes for table 'stock_adjustments'
--
ALTER TABLE 'stock_adjustments'
  ADD PRIMARY KEY ('id'),
  ADD KEY 'idx_stock_adjustments_user_id' ('user_id'),
  ADD KEY 'idx_stock_adjustments_product_created' ('product_id','created_at'),
  ADD KEY 'idx_stock_adjustments_created_at' ('created_at');

--
-- Indexes for table 'suppliers'
--
ALTER TABLE 'suppliers'
  ADD PRIMARY KEY ('id');

--
-- Indexes for table 'users'
--
ALTER TABLE 'users'
  ADD PRIMARY KEY ('id'),
  ADD UNIQUE KEY 'uq_users_username' ('username');

--
-- Indexes for table 'user_sessions'
--
ALTER TABLE 'user_sessions'
  ADD PRIMARY KEY ('id'),
  ADD KEY 'idx_user_sessions_user_id' ('user_id'),
  ADD KEY 'idx_user_sessions_status' ('status'),
  ADD KEY 'idx_user_sessions_last_activity' ('last_activity_at');

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table 'activity_logs'
--
ALTER TABLE 'activity_logs'
  MODIFY 'id' int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table 'audit_changes'
--
ALTER TABLE 'audit_changes'
  MODIFY 'id' int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table 'audit_log'
--
ALTER TABLE 'audit_log'
  MODIFY 'id' int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table 'goods_received_notes'
--
ALTER TABLE 'goods_received_notes'
  MODIFY 'id' int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table 'goods_received_note_items'
--
ALTER TABLE 'goods_received_note_items'
  MODIFY 'id' int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table 'orders'
--
ALTER TABLE 'orders'
  MODIFY 'id' int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table 'order_items'
--
ALTER TABLE 'order_items'
  MODIFY 'id' int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table 'products'
--
ALTER TABLE 'products'
  MODIFY 'id' int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table 'purchase_orders'
--
ALTER TABLE 'purchase_orders'
  MODIFY 'id' int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table 'purchase_order_items'
--
ALTER TABLE 'purchase_order_items'
  MODIFY 'id' int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table 'stock_adjustments'
--
ALTER TABLE 'stock_adjustments'
  MODIFY 'id' int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table 'suppliers'
--
ALTER TABLE 'suppliers'
  MODIFY 'id' int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table 'users'
--
ALTER TABLE 'users'
  MODIFY 'id' int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table 'user_sessions'
--
ALTER TABLE 'user_sessions'
  MODIFY 'id' int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table 'activity_logs'
--
ALTER TABLE 'activity_logs'
  ADD CONSTRAINT 'fk_activity_logs_user' FOREIGN KEY ('user_id') REFERENCES 'users' ('id');

--
-- Constraints for table 'audit_changes'
--
ALTER TABLE 'audit_changes'
  ADD CONSTRAINT 'fk_audit_changes_audit' FOREIGN KEY ('audit_id') REFERENCES 'audit_log' ('id') ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table 'audit_log'
--
ALTER TABLE 'audit_log'
  ADD CONSTRAINT 'fk_audit_log_user' FOREIGN KEY ('user_id') REFERENCES 'users' ('id') ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table 'goods_received_notes'
--
ALTER TABLE 'goods_received_notes'
  ADD CONSTRAINT 'fk_grn_purchase_order' FOREIGN KEY ('purchase_order_id') REFERENCES 'purchase_orders' ('id'),
  ADD CONSTRAINT 'fk_grn_received_by' FOREIGN KEY ('received_by') REFERENCES 'users' ('id'),
  ADD CONSTRAINT 'fk_grn_supplier' FOREIGN KEY ('supplier_id') REFERENCES 'suppliers' ('id');

--
-- Constraints for table 'goods_received_note_items'
--
ALTER TABLE 'goods_received_note_items'
  ADD CONSTRAINT 'fk_grn_items_grn' FOREIGN KEY ('grn_id') REFERENCES 'goods_received_notes' ('id') ON DELETE CASCADE,
  ADD CONSTRAINT 'fk_grn_items_product' FOREIGN KEY ('product_id') REFERENCES 'products' ('id');

--
-- Constraints for table 'order_items'
--
ALTER TABLE 'order_items'
  ADD CONSTRAINT 'fk_order_items_order' FOREIGN KEY ('order_id') REFERENCES 'orders' ('id') ON DELETE CASCADE,
  ADD CONSTRAINT 'fk_order_items_product' FOREIGN KEY ('product_id') REFERENCES 'products' ('id') ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table 'purchase_orders'
--
ALTER TABLE 'purchase_orders'
  ADD CONSTRAINT 'fk_purchase_orders_supplier' FOREIGN KEY ('supplier_id') REFERENCES 'suppliers' ('id');

--
-- Constraints for table 'purchase_order_items'
--
ALTER TABLE 'purchase_order_items'
  ADD CONSTRAINT 'fk_purchase_order_items_order' FOREIGN KEY ('purchase_order_id') REFERENCES 'purchase_orders' ('id'),
  ADD CONSTRAINT 'fk_purchase_order_items_product' FOREIGN KEY ('product_id') REFERENCES 'products' ('id');

--
-- Constraints for table 'stock_adjustments'
--
ALTER TABLE 'stock_adjustments'
  ADD CONSTRAINT 'fk_stock_adjustments_product' FOREIGN KEY ('product_id') REFERENCES 'products' ('id'),
  ADD CONSTRAINT 'fk_stock_adjustments_user' FOREIGN KEY ('user_id') REFERENCES 'users' ('id');

--
-- Constraints for table 'user_sessions'
--
ALTER TABLE 'user_sessions'
  ADD CONSTRAINT 'fk_user_sessions_user' FOREIGN KEY ('user_id') REFERENCES 'users' ('id') ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
