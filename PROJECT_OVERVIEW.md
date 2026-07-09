# MERISH Project Overview

## Purpose
This project is a PHP MVC application for a combined salon and cafe business. The salon side manages customer booking, beautician schedules, receptionist check-in, barista order handling, admin reporting, and QR-based interactions. The cafe side handles menu browsing, cart/checkout flows, QR ordering, and integration with salon reservations when the customer is already in a salon seat.

The application is designed as a single entry-point MVC app with role-based dashboards and a shared login/session system.

## How The App Starts
The application bootstraps through [bootstrap.php](bootstrap.php) and routes every request through [index.php](index.php).

### Bootstrap
- Starts the PHP session.
- Loads Composer autoload if available.
- Falls back to a simple PSR-4 style autoloader for `App\` classes.

### Front Controller
[index.php](index.php) is the router for the whole app.
- `$_GET['page']` selects the controller.
- `$_GET['action']` selects the method.
- GET requests render a view or call the selected action.
- POST requests are dispatched directly to matching controller methods.
- Booking is special-cased and uses `step1`, `step2`, `step3`, etc.

### Controller Map
- `login` and `register` -> `AuthController`
- `home` -> `Home`
- `services` -> `ServicesController`
- `cafe` -> `CafeController`
- `booking` -> `BookingController`
- `admin` -> `AdminController`
- `receptionist` -> `ReceptionistController`
- `beautician` -> `BeauticianController`
- `barista` -> `BaristaController`
- `qrorder` -> `QROrderController`
- `billing` -> `UnifiedBilling`

## Architecture
The code follows a light MVC pattern.

### Core Layer
- [app/Core/Database.php](app/Core/Database.php) creates the shared PDO connection.
- [app/Core/Auth.php](app/Core/Auth.php) provides authentication and role checks.
- [app/Core/Session.php](app/Core/Session.php) wraps session access.

### Model Layer
Models encapsulate SQL access and business data access patterns.

### Controller Layer
Controllers coordinate request validation, data loading, business logic, redirects, and view rendering.

### View Layer
Views are mostly PHP templates with HTML and inline styling. Some pages are static-looking dashboards, others are functional forms and data tables.

## Database Strategy
The app uses a shared PDO connection that defaults to the salon database:
- `db_merish_salon`

Some features explicitly query the cafe database:
- `db_merish_cafe`

This means the project is effectively a cross-schema application.

### Main Cross-Schema Pattern
- Salon data: users, reservations, seats, transactions, reviews, services.
- Cafe data: menus, orders, order_details, inventories, bom_details.
- Some pages merge both domains for operator convenience, especially admin, receptionist, and unified billing.

## Core Business Roles

### Admin
Admin is the widest operational role.
- Dashboard summary
- User management
- Reservation management
- Service and menu management
- Inventory / stock monitoring
- Cafe order management
- Staff management
- Reports and export

### Receptionist
Receptionist handles front-desk operations.
- Check-in walk-in customers
- Seat transfer
- Reservation tracking
- Salon seat and cafe table visibility
- Operational queue management

### Beautician
Beautician sees their schedule and can update treatment progress.
- Today schedule
- Upcoming schedule
- Start treatment
- Complete service
- Settings page
- Status updates on reservations

### Barista
Barista manages cafe orders.
- KDS-style live board
- Orders in New / In Progress / Done columns
- Menu availability management
- Order completion actions

### Customer / Guest
Customers can browse services and cafe menus, book appointments, and order cafe items depending on the flow.

## Main Workflows

## 1. Authentication Flow
Authentication is managed by [app/Controllers/AuthController.php](app/Controllers/AuthController.php).

### Login
- User submits email and password.
- `UsersModel` validates credentials.
- Password is checked with `password_verify()`.
- Session values are stored for `user_id`, `role`, `email`, and `full_name`.
- Redirect is role-based.

### Register
- User provides name, email, role, password, and confirmation.
- The app validates required fields and uniqueness.
- Password is hashed before insert.
- Beautician accounts may also create related staff profile data.

### Logout
- Session values are cleared and the user is redirected to login.

## 2. Salon Booking Flow
The salon booking funnel is handled by [app/Controllers/BookingController.php](app/Controllers/BookingController.php).

The booking system is built as a multi-step wizard.

### Step Overview
- Step 1: choose services
- Step 2: choose bundles / add-ons
- Step 3: choose date and time
- Step 4: choose beautician
- Step 4.1: authentication gate if needed
- Step 5: review / checkout
- Step 6: confirmation / QR output

### Booking Characteristics
- Can work for logged-in users.
- Uses loyalty logic to determine booking window.
- Supports add-ons and promotional bundling.
- Stores booking state in session until final submit.
- Reservation creation can also trigger related cafe order creation when a promo includes food and beverage items.

### Booking Output
- Reservation row in `reservations`.
- Detail rows in `reservation_details`.
- QR confirmation and booking ID generation.

## 3. Cafe Legacy Flow
The legacy cafe flow is controlled by [app/Controllers/CafeController.php](app/Controllers/CafeController.php).

### Flow
- The user opens the cafe landing page.
- The user clicks into the cart page.
- Items are added to a session-based cart.
- Checkout posts cart content and creates cafe order records.
- A confirmation page is shown after order creation.

### Important Detail
The legacy flow used to stop at session state only. The current implementation creates actual records in:
- `db_merish_cafe.orders`
- `db_merish_cafe.order_details`

### Cafe Pages
- `Cafe/index.php` - landing page
- `Cafe/cart.php` - cart and legacy checkout form
- `Cafe/checkout.php` - checkout review
- `Cafe/confirm.php` - order status confirmation

## 4. Cafe QR Flow
There is also a QR-based cafe flow.

### QROrderController
[app/Controllers/QROrderController.php](app/Controllers/QROrderController.php) handles QR ordering.
- QR token is treated as a seat identifier.
- Seat is validated.
- Cart state is stored in session for the QR order.
- Payment can be processed via QRIS or Cash depending on the flow.

### ApiCafeIntegrationController
[app/Controllers/ApiCafeIntegrationController.php](app/Controllers/ApiCafeIntegrationController.php) handles deeper cafe integration.
- QR scan resolves seat and scenario.
- Detects whether the seat has an active salon reservation.
- Lets the system distinguish between salon-linked customers and walk-in cafe guests.
- Creates cafe orders and order details.
- Can summarize seat-level cafe bills.
- Can process cafe payment updates.

## 5. Barista Flow
[app/Controllers/BaristaController.php](app/Controllers/BaristaController.php) powers the barista dashboard.

### What It Does
- Loads all cafe orders for the dashboard.
- Renders a live KDS board with:
  - New Orders
  - In Progress
  - Done
- Lets barista move orders through the workflow.
- Normalizes UI labels like `Selesai` into database-valid status values.

### Order Statuses
The barista UI may show labels such as:
- New
- In Progress
- Selesai
- Completed

The backend normalizes these into valid persisted values for the orders table.

## 6. Beautician Flow
[app/Controllers/BeauticianController.php](app/Controllers/BeauticianController.php) manages stylist-side execution.

### Responsibilities
- Show today's schedule.
- Show upcoming appointments.
- Update reservation status.
- Provide a settings page.
- Normalize completion status updates.

### Reservation Status Handling
- UI may send `Completed`.
- Backend normalizes it to the value accepted by the salon reservation enum.
- This prevents enum truncation errors during service completion.

## 7. Receptionist Flow
[app/Controllers/ReceptionistController.php](app/Controllers/ReceptionistController.php) handles front-desk and queue orchestration.

### Responsibilities
- Salon seat overview.
- Cafe table overview.
- Walk-in check-in.
- Seat transfer.
- Reservation and active queue handling.
- Combined salon and cafe operational visibility.

### Seat Zones
The system distinguishes between:
- `Kursi Salon`
- `Meja Kafe`

This zoning matters for booking, queue display, transfers, and seat assignment.

## 8. Admin Flow
[app/Controllers/AdminController.php](app/Controllers/AdminController.php) is the main operator console.

### Main Sections
- Dashboard summary
- Manage users
- Manage reservations
- Manage services / menus / inventory
- Manage cafe orders
- Manage staff
- Reports

### Cross-Domain Dashboard Behavior
Admin is the main page that merges salon and cafe information.
- Salon reservations are displayed alongside cafe orders.
- Low stock alerts come from cafe inventory.
- Recent transactions show salon payment data.
- Reports aggregate across different modules.

## Important Models

### UsersModel
[app/Models/UsersModel.php](app/Models/UsersModel.php)
- User registration and authentication.
- User lookup.
- Role filtering.
- Salon and cafe history aggregation for the customer-facing cafe view.

### ReservationsModel
[app/Models/ReservationsModel.php](app/Models/ReservationsModel.php)
- Creates reservations.
- Inserts reservation details.
- Validates seat assignment.
- Loads beautician schedules.
- Supplies reservation queries to beautician, receptionist, and admin flows.

### OrdersModel
[app/Models/OrdersModel.php](app/Models/OrdersModel.php)
- Creates cafe orders.
- Creates order details.
- Reads cafe order history.
- Updates cafe order status.
- Deletes cafe orders.

### MenusModel
[app/Models/MenusModel.php](app/Models/MenusModel.php)
- Reads cafe menu items from `db_merish_cafe.menus`.

### ServicesModel
[app/Models/ServicesModel.php](app/Models/ServicesModel.php)
- Reads salon services.

### SeatModel
[app/Models/SeatModel.php](app/Models/SeatModel.php)
- Reads seats.
- Filters by zone.
- Tracks occupancy.
- Can summarize seat state across salon and cafe usage.

### TransactionsModel
[app/Models/TransactionsModel.php](app/Models/TransactionsModel.php)
- Salon transaction records.
- Revenue reporting.

### QRTokenModel
[app/Models/QRTokenModel.php](app/Models/QRTokenModel.php)
- Seat QR token generation and validation.

### LoyaltyModel
[app/Models/LoyaltyModel.php](app/Models/LoyaltyModel.php)
- Booking window logic.
- Loyalty tier names.
- Points and spending thresholds.

### OpenBillModel
[app/Models/OpenBillModel.php](app/Models/OpenBillModel.php)
- Unified salon + cafe billing.
- Synergy discount logic.
- Combines both domains into a single bill view.

## Data Relationships

### Salon Side
- `users` -> `reservations` via `user_id`
- `reservations` -> `reservation_details` via `res_id`
- `reservation_details` -> `services` via `service_id`
- `reservation_details` -> `users` via `beautician_id`
- `reservations` -> `seats` via `seat_id`
- `reservations` -> `transactions` via payment flow

### Cafe Side
- `menus` -> `order_details` via `menu_id`
- `orders` -> `order_details` via `order_id`
- `orders` -> `seats` via `seat_id`
- `menus` -> `bom_details` for recipe / inventory deduction
- `bom_details` -> `inventories` for stock tracking

### Unified / Cross-Domain
- Admin dashboard merges salon and cafe queues.
- Receptionist sees both salon seats and cafe tables.
- Booking flow can create a cafe order when a promo includes F&B.
- Customer history can include both salon and cafe records.

## Runtime Rules That Matter

### Role Checks
Several controllers enforce role-based access directly in constructors.
If a user role does not match, the controller redirects to login.

### Status Normalization
Status values matter because many columns are enum-driven.
Examples:
- Barista order status updates must resolve to the database value `Completed`.
- Salon completion may use `Selesai` in UI but is normalized before save when needed.

### Zone Validation
Seat zoning must stay consistent.
- Salon bookings must use `Kursi Salon`.
- Cafe tables must stay in cafe seating logic.
- Seat transfers and check-ins depend on these zones.

### Cross-Schema SQL
Some SQL is fully qualified to avoid ambiguity.
This is important because both salon and cafe databases have similar concepts.

## Key User Journeys

### Customer Books A Salon Service
1. Open booking page.
2. Choose service(s).
3. Optionally choose bundle/add-ons.
4. Pick date and time.
5. Pick beautician.
6. Log in if required.
7. Submit booking.
8. Receive confirmation and QR.

### Customer Orders Cafe Items During Salon Visit
1. Seat is detected by QR or reservation context.
2. Cafe menu is shown.
3. Customer adds items to cart.
4. Checkout creates cafe order records.
5. Barista sees the order on KDS.
6. Order moves to Completed.

### Receptionist Checks In a Walk-In
1. Enter guest name.
2. Choose destination.
3. Move guest to salon or cafe zone.
4. Track seat occupancy and active orders.

### Barista Completes an Order
1. Open KDS board.
2. Move new order to In Progress.
3. Finish order.
4. Backend saves a valid completed status.
5. Done column reflects the finished item.

## Known Implementation Notes
- The app currently uses a mix of generic tables and schema-qualified tables.
- Some views are polished dashboards, while others are functional operational panels.
- Many features are tightly coupled to session state.
- The router in `index.php` is central; if a page/action is wrong, the feature will appear broken even when the underlying model works.

## Maintenance Notes
If you extend the project, keep these rules consistent:
- Update controller, model, and view together.
- Keep status strings aligned with database enums.
- Keep cafe and salon zones distinct.
- Prefer schema-qualified SQL for cross-database queries.
- Use the front controller routes rather than hard-coded direct paths.

## File Map

### Entry and Core
- [index.php](index.php)
- [bootstrap.php](bootstrap.php)
- [app/Core/Database.php](app/Core/Database.php)
- [app/Core/Auth.php](app/Core/Auth.php)
- [app/Core/Session.php](app/Core/Session.php)

### Main Controllers
- [app/Controllers/AuthController.php](app/Controllers/AuthController.php)
- [app/Controllers/Home.php](app/Controllers/Home.php)
- [app/Controllers/ServicesController.php](app/Controllers/ServicesController.php)
- [app/Controllers/BookingController.php](app/Controllers/BookingController.php)
- [app/Controllers/CafeController.php](app/Controllers/CafeController.php)
- [app/Controllers/QROrderController.php](app/Controllers/QROrderController.php)
- [app/Controllers/BaristaController.php](app/Controllers/BaristaController.php)
- [app/Controllers/BeauticianController.php](app/Controllers/BeauticianController.php)
- [app/Controllers/ReceptionistController.php](app/Controllers/ReceptionistController.php)
- [app/Controllers/AdminController.php](app/Controllers/AdminController.php)
- [app/Controllers/UnifiedBilling.php](app/Controllers/UnifiedBilling.php)

### Main Models
- [app/Models/UsersModel.php](app/Models/UsersModel.php)
- [app/Models/ReservationsModel.php](app/Models/ReservationsModel.php)
- [app/Models/OrdersModel.php](app/Models/OrdersModel.php)
- [app/Models/MenusModel.php](app/Models/MenusModel.php)
- [app/Models/ServicesModel.php](app/Models/ServicesModel.php)
- [app/Models/SeatModel.php](app/Models/SeatModel.php)
- [app/Models/TransactionsModel.php](app/Models/TransactionsModel.php)
- [app/Models/QRTokenModel.php](app/Models/QRTokenModel.php)
- [app/Models/LoyaltyModel.php](app/Models/LoyaltyModel.php)
- [app/Models/OpenBillModel.php](app/Models/OpenBillModel.php)

## Short Summary
This is a salon plus cafe MVC app with one front controller, role-based dashboards, shared sessions, and cross-database business logic. The salon side handles booking and service completion. The cafe side handles menu browsing, cart ordering, QR ordering, and barista fulfillment. Admin and receptionist views merge both domains for operations and reporting.
