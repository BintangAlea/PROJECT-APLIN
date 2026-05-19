# API Integration & Testing Summary

## Overview

MERISH system now has **6 complete API testing suites** covering all major business logic:

| # | Feature | Endpoint | Purpose |
|---|---------|----------|---------|
| 1 | **API Login** | POST `/api/login` | Authenticate 8 test accounts with different roles |
| 2 | **API Order Kafe** | POST `/api/orders/create` | Handle Dine-In + Takeaway orders |
| 3 | **API Kasir & Promo** | POST `/api/billing/calculate` | Unified billing with 20% bundling discount |
| 4 | **API Filter Booking** | GET `/api/beauticians/online` | Filter beauticians by Online status |
| 5 | **API Inventaris** | POST `/api/inventory/deduct-from-bom` | Auto stock deduction from BOM |
| 6 | **API Admin Dashboard** | GET `/api/admin/dashboard` | Stock alerts & system overview |

---

## Project Structure

```
app/Controllers/
├── ApiLoginController.php          ✅ 5 methods
├── ApiOrderController.php          ✅ 4 methods
├── ApiKasirController.php          ✅ 4 methods
├── ApiBookingController.php        ✅ 5 methods
├── ApiInventarisController.php     ✅ 6 methods
└── ApiAdminDashboardController.php ✅ 7 methods

Root:
├── api.php                         ✅ Unified API Router
└── API_TESTING.md                  ✅ Full testing documentation
```

---

## 1. API Login Controller

**File:** `app/Controllers/ApiLoginController.php`

**Methods:**

### POST `/api/login`
Authenticate user with email & password
```json
{
  "email": "admin@test.com",
  "password": "admin123"
}
```
Response: `user_id`, `NAME`, `role`, `token`

### POST `/api/register`
Create new user account
```json
{
  "full_name": "John Doe",
  "email": "john@test.com",
  "role": "Customer",
  "password": "secure123",
  "password_confirm": "secure123"
}
```

### GET `/api/logout`
Clear session & logout

### GET `/api/me`
Get current authenticated user

### GET `/api/seed-test-data`
**Create 8 test accounts:**
- admin@test.com (Admin)
- receptionist@test.com (Receptionist)
- barista@test.com (Barista)
- beautician1@test.com (Beautician, Online)
- beautician2@test.com (Beautician, Online)
- customer1@test.com (Customer)
- customer2@test.com (Customer)
- customer3@test.com (Customer)

---

## 2. API Order Controller

**File:** `app/Controllers/ApiOrderController.php`

**Dual Scenario Support:**

### Scenario A: Registered Customer - Dine-In
```json
{
  "order_type": "Dine-In",
  "menu_id": "MENU001",
  "qty": 2,
  "seat_id": 5
}
```
- Validates seat exists
- Links to customer account
- Tracks seating

### Scenario B: Walk-in Guest - Takeaway
```json
{
  "order_type": "Takeaway",
  "menu_id": "MENU002",
  "qty": 1,
  "guest_name": "John Doe"
}
```
- No authentication required
- Guest name required
- No seat tracking

### Methods:
- **POST** `/api/orders/create` - Create order
- **GET** `/api/orders` - List orders with pagination
- **GET** `/api/orders/{id}` - Get order details
- **PUT** `/api/orders/{id}/status` - Update order status

---

## 3. API Kasir Controller

**File:** `app/Controllers/ApiKasirController.php`

### Key Feature: Bundling Discount (20%)
When order contains BOTH salon services AND cafe orders:
- **20% discount** applied to combined salon + cafe subtotal

**Example Calculation:**
```
Salon Services:      Rp 500,000
Cafe Orders:         Rp 150,000
Extra Materials:     Rp  25,000
─────────────────────────────
Subtotal:            Rp 675,000
Bundling Discount:   Rp 130,000 (20% of Rp 650,000)
─────────────────────────────
Grand Total:         Rp 545,000
```

### Methods:
- **POST** `/api/billing/calculate` - Calculate bill with discounts
- **POST** `/api/billing/checkout` - Process payment (Cash/Debit/QRIS/Transfer)
- **GET** `/api/promotions` - List active promotions
- **GET** `/api/billing/{res_id}` - Get transaction history

---

## 4. API Booking Controller

**File:** `app/Controllers/ApiBookingController.php`

### Filtering by Online Status
Get beauticians currently available (work_status = 'Online')

### Methods:
- **GET** `/api/beauticians/online` - List online beauticians with pagination
  - Optional filter: `?specialization=Hair Stylist`
- **GET** `/api/beauticians/{id}` - Get beautician profile
- **GET** `/api/time-slots?beautician_id=4&date=2024-01-15` - Get available slots
- **PUT** `/api/beauticians/{id}/status` - Change online/offline status
- **GET** `/api/specializations` - List available specializations

**Time Slot Generation:**
- 30-minute intervals
- 9:00 AM to 6:00 PM
- Excludes booked times
- Returns availability per slot

---

## 5. API Inventaris Controller

**File:** `app/Controllers/ApiInventarisController.php`

### Two Stock Deduction Methods:

#### Method A: BOM-Based Deduction
When cafe order placed:
1. Get menu's BOM (Bill of Materials)
2. Check stock availability
3. Auto-deduct each ingredient

**Request:**
```json
{
  "menu_id": "MENU001",
  "qty": 2
}
```

**Result:**
```json
{
  "deducted_items": [
    {
      "item_name": "Coffee Beans",
      "quantity_deducted": 4,
      "remaining_stock": 46
    }
  ]
}
```

#### Method B: Extra Materials Usage
Beautician uses premium materials:
1. Record usage for specific reservation
2. Deduct from inventory
3. Charge customer

**Request:**
```json
{
  "res_id": 1,
  "item_id": 10,
  "qty_used": 2
}
```

**Result:**
```json
{
  "charge_amount": 100000,
  "remaining_stock": 18
}
```

### Methods:
- **GET** `/api/inventory` - List items with pagination
- **GET** `/api/inventory/{id}` - Get item details
- **POST** `/api/inventory/deduct-from-bom` - Auto deduct from BOM
- **POST** `/api/inventory/use-extra-material` - Record extra material usage
- **GET** `/api/inventory/low-stock-alerts` - Get low stock warnings

---

## 6. API Admin Dashboard Controller

**File:** `app/Controllers/ApiAdminDashboardController.php`

### Dashboard Summary
```json
{
  "summary": {
    "total_revenue_today": 1250000,
    "total_orders_today": 8,
    "total_bookings_today": 5,
    "low_stock_alerts": 3
  }
}
```

### Methods:
- **GET** `/api/admin/dashboard` - Dashboard overview
- **GET** `/api/admin/low-stock-alerts` - Detailed stock alerts
- **GET** `/api/admin/revenue?period=today|week|month|year` - Revenue report
- **GET** `/api/admin/users` - User statistics by role
- **GET** `/api/admin/top-menus?limit=10` - Best selling items
- **GET** `/api/admin/bookings?date=2024-01-01&status=Confirmed` - Booking stats
- **POST** `/api/admin/trigger-low-stock-alert` - Manual alert trigger

### Alert Levels:
| Level | Condition | Status |
|-------|-----------|--------|
| Normal | stock_qty > min_stock * 1.5 | ✅ |
| Warning | stock_qty <= min_stock * 1.5 | ⚠️ |
| Low Stock | stock_qty < min_stock | 🔴 |
| Critical | stock_qty = 0 | 🔴🔴 |

---

## API Router

**File:** `api.php`

Unified router handling all 31 API endpoints:

```php
// Define route
$routes = [
    'POST:login' => [ApiLoginController::class, 'login'],
    'POST:register' => [ApiLoginController::class, 'register'],
    // ... 28 more routes
];

// Match & execute
$routeKey = "{$method}:{$route}";
if (isset($routes[$routeKey])) {
    $controller = new $routes[$routeKey][0]();
    $controller->$routes[$routeKey][1]();
}
```

### URL Format:
```
GET  http://localhost/SIB/PROJECT-APLIN/api.php/beauticians/online
POST http://localhost/SIB/PROJECT-APLIN/api.php/orders/create
PUT  http://localhost/SIB/PROJECT-APLIN/api.php/orders/10/status
```

---

## Standardized Response Format

All endpoints return JSON with consistent structure:

### Success (200, 201)
```json
{
  "status": "success",
  "code": 200,
  "message": "Operation successful",
  "data": {...},
  "timestamp": "2024-01-01 10:00:00"
}
```

### Validation Error (422)
```json
{
  "status": "error",
  "code": 422,
  "message": "Validation failed",
  "errors": {
    "email": "Email already exists",
    "password": "Password too short"
  },
  "timestamp": "2024-01-01 10:00:00"
}
```

### Not Found (404)
```json
{
  "status": "error",
  "code": 404,
  "message": "Resource not found",
  "timestamp": "2024-01-01 10:00:00"
}
```

---

## Security Features

✅ **Password Hashing:** BCRYPT (PASSWORD_BCRYPT)
✅ **Prepared Statements:** All queries use PDO prepared statements
✅ **Input Validation:** All inputs validated before processing
✅ **Role-Based Access:** Admin endpoints require admin role
✅ **Session Management:** PHP $_SESSION for auth state
✅ **SQL Injection Prevention:** Named parameters in queries

---

## Testing Checklist

- [x] API Login: 8 test accounts created with different roles
- [x] API Order: Dine-In (with seat) + Takeaway (with guest_name) working
- [x] API Kasir: Bundling discount (20%) correctly calculated
- [x] API Kasir: Extra materials charged properly
- [x] API Filter Booking: Beauticians filtered by Online status
- [x] API Filter Booking: Time slots generated with availability
- [x] API Inventaris: BOM-based stock auto-deduction working
- [x] API Inventaris: Extra material usage recorded & charged
- [x] API Admin: Dashboard summary working
- [x] API Admin: Low stock alerts triggered correctly
- [x] API Admin: Alert levels (Normal/Warning/Critical) showing

---

## Quick Testing Guide

### 1. Test Login (8 accounts)
```bash
curl http://localhost/SIB/PROJECT-APLIN/api.php/seed-test-data

curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@test.com","password":"admin123"}'
```

### 2. Test Orders
```bash
# Dine-In order
curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/orders/create \
  -H "Content-Type: application/json" \
  -d '{"order_type":"Dine-In","menu_id":"MENU001","qty":2,"seat_id":5}'

# Takeaway order (no auth required)
curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/orders/create \
  -H "Content-Type: application/json" \
  -d '{"order_type":"Takeaway","menu_id":"MENU002","qty":1,"guest_name":"John Doe"}'
```

### 3. Test Billing with Bundling
```bash
curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/billing/calculate \
  -H "Content-Type: application/json" \
  -d '{
    "res_id":1,
    "salon_services":[{"service_id":"SRV001","qty":1,"price":500000}],
    "cafe_orders":[{"order_id":1,"price":150000}],
    "extra_materials":[]
  }'
```

### 4. Test Filter Beauticians
```bash
curl http://localhost/SIB/PROJECT-APLIN/api.php/beauticians/online
```

### 5. Test Stock Deduction
```bash
# Check inventory
curl http://localhost/SIB/PROJECT-APLIN/api.php/inventory

# Deduct from BOM
curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/inventory/deduct-from-bom \
  -H "Content-Type: application/json" \
  -d '{"menu_id":"MENU001","qty":2}'

# Verify stock reduced
curl http://localhost/SIB/PROJECT-APLIN/api.php/inventory
```

### 6. Test Admin Dashboard
```bash
curl http://localhost/SIB/PROJECT-APLIN/api.php/admin/dashboard

curl http://localhost/SIB/PROJECT-APLIN/api.php/admin/low-stock-alerts
```

---

## Complete API Endpoint List

### Authentication (5)
```
POST   /api/login
POST   /api/register
GET    /api/logout
GET    /api/me
GET    /api/seed-test-data
```

### Orders (4)
```
POST   /api/orders/create
GET    /api/orders
GET    /api/orders/{id}
PUT    /api/orders/{id}/status
```

### Billing (4)
```
POST   /api/billing/calculate
POST   /api/billing/checkout
GET    /api/promotions
GET    /api/billing/{res_id}
```

### Booking (5)
```
GET    /api/beauticians/online
GET    /api/beauticians/{id}
GET    /api/time-slots
PUT    /api/beauticians/{id}/status
GET    /api/specializations
```

### Inventory (5)
```
GET    /api/inventory
GET    /api/inventory/{id}
POST   /api/inventory/deduct-from-bom
POST   /api/inventory/use-extra-material
GET    /api/inventory/low-stock-alerts
```

### Admin Dashboard (7)
```
GET    /api/admin/dashboard
GET    /api/admin/low-stock-alerts
GET    /api/admin/revenue
GET    /api/admin/users
GET    /api/admin/top-menus
GET    /api/admin/bookings
POST   /api/admin/trigger-low-stock-alert
```

**Total: 30 endpoints across 6 API groups**

---

## Implementation Notes

### Key Design Decisions:

1. **Bundling Discount Logic**
   - 20% discount applies when BOTH salon_services > 0 AND cafe_orders > 0
   - Extra materials NOT included in discount calculation base
   - Promo discount applied separately

2. **BOM-Based Inventory**
   - Each menu has optional bom_recipe_id
   - BOM specifies required quantities per dish
   - Auto-deduction happens when order placed
   - Prevents overselling through quantity validation

3. **Extra Material System**
   - Tracked separately from BOM items
   - Each material has extra_charge_per_unit
   - Usage recorded with reservation reference
   - Charge calculated: qty_used × unit_price

4. **Beautician Availability**
   - Status: Online/Offline
   - Time slots: 30-min intervals, 9AM-6PM
   - Auto-excludes booked slots
   - Filtering by specialization supported

5. **Stock Alerts**
   - Critical: stock = 0
   - Warning: min_stock ≤ stock ≤ min_stock × 1.5
   - Low: stock < min_stock
   - Auto-trigger on stock deduction

---

## Performance Considerations

- ✅ All queries use prepared statements (prevent SQL injection)
- ✅ Pagination built-in for large datasets
- ✅ Indexes on frequently queried columns (user_id, status, date)
- ✅ Caching friendly response format
- ✅ Efficient BOM lookup (single query + join)

---

## Next Steps (Optional Enhancements)

1. Add request rate limiting
2. Implement JWT tokens for stateless auth
3. Add API request logging
4. Create API documentation with Swagger/OpenAPI
5. Add webhook support for low stock alerts
6. Implement real-time notifications
7. Add advanced filtering & search
8. Create API SDK for frontend integration

---

*Last Updated: 2024*
*All 6 API testing suites fully implemented and documented*
