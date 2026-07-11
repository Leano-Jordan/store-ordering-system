# SwiftOrder System Changelog

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

## Isaac Junior Lehogonolo Maluleka
