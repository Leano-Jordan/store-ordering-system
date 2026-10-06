# SwiftOrder System Changelog

## 2026-10-06 — Swifty Quality & UI Hardening

### Fixed

- Removed Zazu branding contamination from the shared SwiftOrder shell and login page.
- Changed theme persistence to use the SwiftOrder-specific swiftorder-theme key only.
- Added an explicit product search label and improved search input semantics.
- Added accessible pressed-state semantics to category and payment controls.
- Replaced routine POS browser alerts with in-surface live feedback.
- Added mobile POS layout rules so the cart no longer requires a fixed 420px minimum width.
- Enforced the licence gate on product deactivation.
- Narrowed update_user.php database transaction scope and retained atomic last-admin protection.
- Extracted profile-image handling and locked user-update validation into focused helpers.

### Tests Added

- tests/Unit/POSFeedbackTest.js
- tests/Unit/UserUpdateHelpersTest.php

Runtime execution evidence is still required before commercial V1 release.

---

## Version 0.5.0-alpha

Initial Alpha Release

### Added (v0.5.0-alpha)

- Initial SwiftOrder customer interface.
- Product catalogue.
- Shopping cart.
- Customer name input.
- Place Order functionality.
- Clear Cart functionality.
- PHP/MySQL integration.
- Search bar.
- Basic responsive layout.

#### Notes

- First working end-to-end ordering prototype.
- Foundation for future development.

---

## Version 0.5.1-alpha

Shopping Cart & User Experience Improvements

### Added

- Product quantity controls (+ / -).
- Automatic quantity merging for duplicate products.
- Running item counter.
- Product category filtering.
- Product images.
- Clear Search button.

### Improved

- Product grid layout.
- Cart item display.
- Button alignment.
- Search performance.
- Empty cart message.
- Order confirmation.
- Cart calculations.

### Fixed

- Duplicate products in cart.
- Item counter not decreasing correctly.
- Quantity button alignment.
- Place Order JavaScript errors.
- Cart clearing inconsistencies.
- Search filtering bugs.

---

## Version 0.5.2-alpha

Professional Layout Refactor

## Added (v0.5.1-alpha)

- Professional two-column POS layout.
- Dedicated left product panel.
- Dedicated sticky shopping cart panel.
- Improved page structure.

## Improved (v0.5.1-alpha)

- Search section organization.
- Category filter placement.
- Shopping cart positioning.
- Overall user workflow.
- Responsive desktop layout.
- Cart visibility while scrolling.

## Refactored

- HTML page structure.
- CSS layout architecture.
- Main container organization.
- Product section hierarchy.

## Fixed (v0.5.1-alpha)

- Shopping cart alignment.
- Product grid positioning.
- Layout spacing.
- Sticky cart behavior.
- UI consistency.

---

## Version 0.6.0-alpha

Customer Interface

- Product catalogue
- Product images
- Search
- Clear Search
- Category filtering
- Quantity controls
- Sticky shopping cart
- Running total
- Customer name
- Place Order
- Clear Cart
- Responsive desktop layout

---

## Version 0.6.1-alpha

Added

- Products management page
- Add product form
- Save product functionality
- Product image thumbnails
- Products navigation link

Improved

- Shared header
- Reusable action button styling
- Reusable table layout

Fixed

- Project image path corrected
- Projects page image sizing

## Version 0.6.3-alpha

Added
Product image upload.
Unique image filenames.
Product image preview on Edit Product.
Image replacement during product editing.
Separate image styling for customer menu and admin thumbnails.
Placeholder image support started.

Fixed

Description field corruption (“> bug).
Home/Product image sizing separation.

Still To Do

🔲 Delete old image file when replacing an image.
🔲 Delete image file when deleting a product.
🔲 Required field validation (client & server).
🔲 Prevent duplicate form submissions.
🔲 Cancel button on Add Product.
🔲 Finalize placeholder image everywhere.
🔲 Decide whether to reject .jfif uploads or support them properly.

### SwiftOrder v0.6.5-beta Changelog

Fixed

- Fixed order status being saved correctly (Pending).
- Fixed bind_param type mismatch in place_order.php.
- Fixed duplicate success/error popup after placing an order.
- Fixed broken order workflow (Pending → Preparing → Ready → Collected).
- Removed incorrect renderCart() call and restored proper cart UI updates.

Improved

- Stabilized the order placement workflow.
- Removed a regression introduced during the attempted GET→POST conversion.
- Project reviewed from the perspective of a commercial multi-business ordering system instead of generic PHP code.

## Next milestone (v0.6.6-beta)

We’ll focus on:
XSS protection (file-by-file).
Image upload validation.
Cart persistence cleanup.
Dashboard improvements (real chart data).
Remove dead/redundant code.
Begin preparing SwiftOrder for multi-business scalability.

### SwiftOrder v0.6.5-beta

Fixed

- Fixed order status being saved correctly (Pending).
- Fixed bind_param type mismatch in place_order.php.
- Fixed duplicate success/error popup after placing an order.
- Fixed broken order workflow (Pending → Preparing → Ready → Collected).
- Removed incorrect renderCart() call and restored proper cart UI updates.

Improved

- Stabilized the order placement workflow.
- Removed a regression introduced during the attempted GET→POST conversion.
- Project reviewed from the perspective of a commercial multi-business ordering system instead of generic PHP code.

## Next milestone v0.6.6-beta

We’ll focus on:

XSS protection (file-by-file).
Image upload validation.
Cart persistence cleanup.
Dashboard improvements (real chart data).
Remove dead/redundant code.
Begin preparing SwiftOrder for multi-business scalability.

2026/07/06

### SwiftOrder v0.6.6-beta

Date: 2026-07-07

## Added Today

- Added Order Details page (`order_details.php`).
- Added clickable order numbers in Orders page.
- Added dashboard statistic: Average Order Value.
- Added dashboard statistic: This Month's Revenue.
- Added dashboard statistic: Top Selling Products.
- Added product pagination.
- Added Back to Orders button on Order Details page.
- Added order status badge styling to Order Details page.
- Added order date/time display on Order Details page.

## Improved Features

- Replaced GET status updates with secure POST forms.
- Removed nested `<button>` elements inside links on Products page.
- Moved inline form styling to CSS (`.inline-form`).
- Improved dashboard handling when no revenue exists (`?? 0` fallback).
- Improved product image fallback handling.
- Improved output escaping using `htmlspecialchars()`.
- Improved ID handling using integer casting where applicable.
- Improved order navigation by linking directly to full order details.

## Fixed Today

- Fixed duplicate order submission protection.
- Fixed JSON response handling between `place_order.php` and JavaScript.
- Fixed customer fields not clearing after successful order placement.
- Fixed invalid order total validation.
- Fixed pagination offset calculation (`+` → `*`).
- Fixed Products page showing no products after pagination.
- Fixed Products page Edit/Delete button structure.
- Fixed PHP short tag (`<?`) usage in `orders.php`.
- Fixed several malformed HTML attributes and minor syntax issues.
- Orders pagination implemented.

## Security

- Added request method validation for order placement.
- Added server-side validation for customer name.
- Added server-side validation for cart contents.
- Added server-side validation for order total.
- Replaced `rand()` with `random_int()` for order number generation.
- Added duplicate order number detection before inserting orders.
- Continued replacing direct output with `htmlspecialchars()` where appropriate.

## Known Issues

- Dashboard sales chart still requires dynamic Chart.js data binding.
- Printable receipts not yet implemented.
- Sales reports with date filtering not yet implemented.

## SwiftOrder v0.6.7-beta

Date: 2026-07-07

## Authentication & Security

- Added centralized page protection through includes/auth.php.
- Implemented automatic redirect to the originally requested page after successful login.
- Fixed login credential verification using password_verify().
- Corrected login redirect flow.
- Continued improving session-based authentication.
- Identified remaining authentication verification and logout testing for completion.
Orders
- Fixed action button workflow to remain on orders.php instead of redirecting to the order details page.
- Continued refining order status progression.
Bug Fixes
- Fixed credential validation issues caused by incorrect password hashing.
- Resolved redirect behaviour after authentication.
- Corrected require_once include issues on protected pages.
- Improved overall stability of the authentication system.
Code Improvements
- Continued moving authentication logic into reusable includes.
- Improved project structure and maintainability.
- Reviewed security practices and identified future hardening tasks (session regeneration, role-based authorization, logout validation).
Planning & Architecture
- Defined the long-term roadmap for evolving SwiftOrder into a commercial SaaS platform.
- Planned future multi-tenancy support using a businesses table and business_id relationships across business-owned data.
- Compiled a comprehensive feature backlog covering POS, inventory, reporting, customer management, payment integrations, multi-location support, and SaaS capabilities.
- Established the preferred technology roadmap:
Tailwind CSS
Alpine.js
Advanced Chart.js usage
DomPDF/FPDF
Laravel
MySQL indexing and query optimization
Redis for caching and session management

## Next Priority

- Complete authentication flow testing (login → protected pages → logout).
- Fix any remaining page protection issues.
- Begin role-based access control.
- Continue security and code quality review.

## Version 0.7.0-beta

Date: 2026-07-09

## User Management & Role-Based Access Control

### Added (v0.7.0-beta)

User Management module.
Users page.
Add User page.
Edit User page.
Update User functionality.
Deactivate User functionality.
Reactivate User functionality.
Role constants.
Role-based navigation.
Active navigation highlighting.
Logged-in user display.
Self-deactivation protection.
Last active Admin protection.
Username uniqueness validation.

### Improved (v0.7.0-beta)

Authentication system.
Session security.
Login handling.
Permission management.
Navigation structure.
Code maintainability.
Role consistency.
User account management.
Page protection.

### Refactored1

Role checks to use constants.
Permission handling.
Navigation permission logic.
Authentication flow.
User CRUD structure.

### Fixed (v0.7.0-beta)

New user login issue.
Password verification issue.
Prepared statement errors.
Users pagination query.
Update User validation.
Navigation permission warnings.
Header permission loading.
Self-deactivation bug.
Last Admin deactivation bug.
Persistent error messages after refresh.
Incorrect SQL table references.

### Notes1

Completed User Management module.
Completed Role-Based Access Control foundation.
Navigation now changes based on logged-in user role.
SwiftOrder now supports Admin, Manager, Cashier and Kitchen accounts.
Foundation completed for staff permissions and future activity logging.

### Version: 0.7.1-beta

## Date: 2026-07-11

## Inventory Management & Product Improvements

## Added (v0.7.1-beta)

Stock deduction on completed orders. Order Items database integration. Product search. Product search persistence with filters. Low Stock product filter. Out of Stock product filter. Top Selling Products dashboard widget. Stock level indicators (In Stock, Low Stock, Out of Stock). Dashboard Quick Actions. Sales range filtering (7 Days, 30 Days, This Month). Recent Orders dashboard widget.

## Improved (v0.7.1-beta)

Dashboard analytics. Revenue reporting. Product sorting. Product pagination. Product management workflow. Inventory visibility. Sales overview chart. Order completion workflow. Product listing usability. Dashboard navigation.

## Refactored (v0.7.1-beta)

Order processing flow. Stock deduction logic. Dashboard SQL queries. Product sorting queries. Product search queries. Inventory update process. Order item handling. Reporting queries.

## Fixed (v0.7.1-beta)

Stock not decreasing after completed orders. Product name matching during stock deduction. SQL update query for inventory. Order item parsing. Dashboard chart loading. Dashboard revenue calculations. Product pagination issues. Product search filtering. Undefined query variables. Product sorting inconsistencies. Order status inventory update reliability.

### Notes.1

Completed Inventory Management foundation. Stock is now automatically deducted when an order reaches Collected status. Dashboard now provides sales analytics, revenue summaries and top-selling products. Product management now includes search, inventory filters and improved stock visibility. SwiftOrder inventory and reporting foundation is now ready for future stock adjustments, supplier management and purchase order features.

### SwiftOrder v0.7.2-beta Development Log

## Date: 12-July-2026

---

## ### Inventory Module

### Stock Adjustments

- Completed Stock Adjustment history module.
- Added stock adjustment listing page.
- Added pagination.
- Added product search.
- Added filtering:
  - All
  - Increase
  - Decrease
  - Today
- Added Available Stock column.
- Available Stock now stores the resulting stock level after each adjustment.
- Quantity column now displays:

  - Increase
  - Decrease
- Fixed multiple SQL query issues.
- Fixed pagination calculations.
- Fixed search variable bugs.
- Fixed filter variable bugs.
- Corrected joins between:
  - products
  - users
  - stock_adjustments
- Improved page layout.
- Styled search toolbar.
- Styled filter buttons.
- Styled Clear button.
- Removed layout inconsistencies.
- Dashboard card sizing corrected.

---

## ### Supplier Management Module

### Completed

Created Suppliers module from scratch.

Added:

- suppliers.php
- add_supplier.php
- save_supplier.php
- edit_supplier.php
- update_supplier.php
- deactivate_supplier.php
- reactivate_supplier.php

### Features

- Supplier listing
- Search suppliers
- Pagination
- Add supplier
- Edit supplier
- Soft deactivate supplier
- Reactivate supplier
- Active / Inactive status
- Notes
- Contact details
- Company details
- Phone
- Email
- Address

### Security 3

- Role protected
- Prepared statements
- Input validation
- Soft delete approach maintained

---

## ### Purchase Orders Module

### Database

Created:

purchase_orders

Created:

purchase_order_items

### Files Created

- purchase_orders.php
- add_purchase_order.php
- save_purchase_order.php
- view_purchase_order.php
- add_purchase_order_item.php
- save_po_item.php
- receive_purchase_order.php *(placeholder)*
- cancel_purchase_order.php *(placeholder)*

### Features2

- Purchase Order listing
- Purchase Order search
- Pagination
- Purchase Order creation
- Automatic PO number generation
- Supplier selection
- Notes support
- View Purchase Order page

### Purchase Order Items

- Dynamic Add Product button
- JavaScript row creation
- Remove row
- Quantity field
- Unit Cost field
- Automatic line total calculation
- Save Purchase Order Items
- Purchase Order total automatically recalculated after save

---

## ### JavaScript

Updated:

assets/js/script.js

Added:

- Dynamic Purchase Order item rows
- Remove Purchase Order item rows

Prepared foundation for:

- Automatic Purchase Order calculations

---

## ### User Interface

Improved:

- Purchase Order layout
- Supplier pages
- Inventory search layout
- Inventory filter layout
- Button consistency
- Dashboard cards
- Clear button styling
- Purchase Order detail page

---

## ### Bug Fixes

Resolved:

- fetch_assoc() SQL errors
- Incorrect JOIN statements
- Pagination offset bug
- Undefined variables
- Search query issues
- Filter query issues
- Available stock history recording
- Purchase Order page styling
- Supplier CRUD workflow
- Purchase Order total recalculation

---

## ### Current Module Status

### Authentication

? Complete

### Roles & Permissions

? Complete

### Dashboard

? Complete

### Products

? Complete

### Orders

? Complete

### Inventory

? Complete

### Activity Logs

? Complete

### Reports

? Complete

### Suppliers

? Complete

### Purchase Orders

?? In Progress

### Completed1

- Purchase Order CRUD foundation
- Purchase Order Items
- Automatic total updates

## Remaining

- Product dropdown instead of free text
- Receive Purchase Order
- Automatic stock increase
- Prevent editing after receipt
- Purchase Order history

---

## ### Overall Progress

## SwiftOrder has now progressed beyond a basic POS and into a true business management platform

Completed major modules:

- Authentication
- Role Management
- POS
- Orders
- Products
- Inventory
- Reporting
- Activity Logs
- Suppliers

## Current development focus

- Purchase Order workflow and inventory procurement.

---

## Version 0.7.3-beta

## Date: 2026-07-17

## Architecture & Codebase Hardening

## Added (v0.7.3-beta)

Shared executeQuery() helper.
Shared executeStatement() helper.
Shared reusable flash message partial.
Automatic stock adjustment logging during order collection.
Database performance indexes for high-traffic tables.
Foundation for centralized database layer.
Foundation for global error handling.

## Improved (v0.7.3-beta)

Dashboard modular architecture completed.
Reports modular architecture completed.
Activity Logs modular architecture completed.
Product module maintainability.
User module maintainability.
Supplier module maintainability.
Inventory workflow reliability.
Order collection workflow.
Stock history accuracy.
Database query consistency.
Purchase Order preparation for future inventory engine.
Refactored (v0.7.3-beta)
Began separating database execution from business logic.
Reduced repeated prepared statement code.
Standardized reusable helper functions.
Continued moving reusable UI into partials.
Continued reducing duplicated code throughout the system.

#### Fixed (v0.7.3-beta)

Fixed automatic stock deduction SQL error (AND stock >= ?).
Fixed stock history recording after collected orders.
Fixed "bind_param() on bool" fatal error.
Fixed duplicate helper loading.
Fixed helper syntax issues.
Fixed Product search helper migration.
Fixed User Save helper migration.
Fixed User Update helper migration.
Fixed Supplier Save helper migration.
Fixed Supplier Update helper migration.
Fixed Product Save helper migration.
Fixed Product Update helper migration.
Fixed session warning caused by duplicate session_start() calls.
Fixed Activity Log pagination and filtering issues.
Fixed Reports dashboard partial organization.

### Database

Added indexes for performance.
Orders
Status index.
Created Date index.
Composite Status + Created Date index.

## Products

Status + Stock index.
Category index.
Order Items
Order ID index.
Product ID index.
Stock Adjustments
Product + Created Date index.
Created Date index.

## Security1

Continued CSRF implementation planning.
Continued helper migration to reduce SQL duplication.
Continued preparing centralized exception handling.
Continued improving reusable architecture.

## Notes11

SwiftOrder has officially transitioned from feature-focused development into architecture-focused development.
Current emphasis is on:
Cleaner code.
Faster database performance.
Reduced duplication.
Easier maintenance.
Enterprise-ready architecture.
Preparing for multi-tenancy.
The system is now approaching commercial-quality internal architecture rather than tutorial-style PHP development.

## Version SwiftOrder v0.7.2-beta**

## Isaac Junior Lehogonolo Maluleka
