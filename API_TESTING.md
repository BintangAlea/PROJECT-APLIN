# API Testing Documentation

## Overview

This document provides comprehensive testing scenarios for all 6 required API testing features in MERISH system:

1. **API Login** - Authentication with 8 test accounts
2. **API Order Kafe** - Dine-In and Takeaway ordering
3. **API Kasir & Promo** - Unified billing with bundling discount
4. **API Filter Booking** - Filter beauticians by Online status
5. **API Inventaris** - Auto stock deduction from BOM & extra materials
6. **API Admin Dashboard** - Low stock alerts and warnings

---

## 1. API Login - Test Authentication (8 Test Accounts)

### Scenario: Create Test Data

**Endpoint:** `GET /api/seed-test-data`

**Description:** Create 8 test accounts with different roles

**cURL Command:**
```bash
curl -X GET http://localhost/SIB/PROJECT-APLIN/api.php/seed-test-data
```

**Expected Response:**
```json
{
  "status": "success",
  "code": 200,
  "message": "Test data seeded successfully",
  "data": {
    "created_count": 8,
    "failed_count": 0,
    "created_accounts": [
      {
        "email": "admin@test.com",
        "role": "Admin",
        "password": "admin123"
      },
      {
        "email": "receptionist@test.com",
        "role": "Receptionist",
        "password": "receptionist123"
      },
      {
        "email": "barista@test.com",
        "role": "Barista",
        "password": "barista123"
      },
      {
        "email": "beautician1@test.com",
        "role": "Beautician",
        "password": "beautician123",
        "status": "Online"
      },
      {
        "email": "beautician2@test.com",
        "role": "Beautician",
        "password": "beautician123",
        "status": "Online"
      },
      {
        "email": "customer1@test.com",
        "role": "Customer",
        "password": "customer123"
      },
      {
        "email": "customer2@test.com",
        "role": "Customer",
        "password": "customer123"
      },
      {
        "email": "customer3@test.com",
        "role": "Customer",
        "password": "customer123"
      }
    ]
  },
  "timestamp": "2024-01-01 10:00:00"
}
```

### Scenario: Login with Different Roles

#### Test 1: Admin Login

**Endpoint:** `POST /api/login`

**cURL Command:**
```bash
curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@test.com",
    "password": "admin123"
  }'
```

**Expected Response:**
```json
{
  "status": "success",
  "code": 200,
  "message": "Login successful",
  "data": {
    "user_id": 1,
    "NAME": "Admin",
    "email": "admin@test.com",
    "role": "Admin",
    "token": "base64_encoded_token"
  },
  "timestamp": "2024-01-01 10:05:00"
}
```

#### Test 2: Customer Login

**cURL Command:**
```bash
curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "customer1@test.com",
    "password": "customer123"
  }'
```

#### Test 3: Beautician Login

**cURL Command:**
```bash
curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "beautician1@test.com",
    "password": "beautician123"
  }'
```

**Expected Response:**
```json
{
  "status": "success",
  "code": 200,
  "message": "Login successful",
  "data": {
    "user_id": 4,
    "NAME": "Beautician 1",
    "email": "beautician1@test.com",
    "role": "Beautician",
    "token": "base64_encoded_token"
  },
  "timestamp": "2024-01-01 10:06:00"
}
```

**Test all 8 accounts:**
- admin@test.com
- receptionist@test.com
- barista@test.com
- beautician1@test.com
- beautician2@test.com
- customer1@test.com
- customer2@test.com
- customer3@test.com

---

## 2. API Order Kafe - Test Dine-In & Takeaway

### Scenario: Registered Customer - Dine-In Order

**Endpoint:** `POST /api/orders/create`

**Prerequisites:**
- First, login as customer1@test.com (see API Login tests)
- Get valid menu_id from menus table
- Get valid seat_id from seats table

**cURL Command:**
```bash
curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/orders/create \
  -H "Content-Type: application/json" \
  -d '{
    "order_type": "Dine-In",
    "menu_id": "MENU001",
    "qty": 2,
    "seat_id": 5
  }'
```

**Expected Response:**
```json
{
  "status": "success",
  "code": 201,
  "message": "Order created successfully",
  "data": {
    "order_id": 10,
    "order_type": "Dine-In",
    "menu_id": "MENU001",
    "menu_name": "Iced Latte",
    "qty": 2,
    "unit_price": 75000,
    "total_price": 150000,
    "seat_id": 5,
    "status": "New",
    "payment_status": "Unpaid",
    "order_time": "2024-01-01 10:10:00"
  },
  "timestamp": "2024-01-01 10:10:00"
}
```

### Scenario: Walk-in Guest - Takeaway Order

**Endpoint:** `POST /api/orders/create`

**Note:** No authentication required for walk-in guests

**cURL Command:**
```bash
curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/orders/create \
  -H "Content-Type: application/json" \
  -d '{
    "order_type": "Takeaway",
    "menu_id": "MENU002",
    "qty": 1,
    "guest_name": "John Doe"
  }'
```

**Expected Response:**
```json
{
  "status": "success",
  "code": 201,
  "message": "Order created successfully",
  "data": {
    "order_id": 11,
    "order_type": "Takeaway",
    "menu_id": "MENU002",
    "menu_name": "Cappuccino",
    "qty": 1,
    "unit_price": 80000,
    "total_price": 80000,
    "guest_name": "John Doe",
    "status": "New",
    "payment_status": "Unpaid",
    "order_time": "2024-01-01 10:12:00"
  },
  "timestamp": "2024-01-01 10:12:00"
}
```

### Test: Get Order Details

**Endpoint:** `GET /api/orders/{order_id}`

**cURL Command:**
```bash
curl -X GET http://localhost/SIB/PROJECT-APLIN/api.php/orders/10
```

### Test: Update Order Status

**Endpoint:** `PUT /api/orders/{order_id}/status`

**Valid Status Values:**
- New
- In Progress
- Selesai

**cURL Command:**
```bash
curl -X PUT http://localhost/SIB/PROJECT-APLIN/api.php/orders/10 \
  -H "Content-Type: application/json" \
  -d '{
    "status": "In Progress"
  }'
```

---

## 3. API Kasir & Promo - Unified Billing with Bundling Discount

### Scenario: Calculate Unified Bill (Salon + Cafe) with 20% Bundling Discount

**Endpoint:** `POST /api/billing/calculate`

**Description:** 
- Salon services subtotal: Rp 500,000
- Cafe orders subtotal: Rp 150,000
- **Bundling Discount (20%):** Rp 130,000 (20% of 650,000)
- Grand Total: Rp 520,000

**cURL Command:**
```bash
curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/billing/calculate \
  -H "Content-Type: application/json" \
  -d '{
    "res_id": 1,
    "salon_services": [
      {
        "service_id": "SRV001",
        "qty": 1,
        "price": 500000
      }
    ],
    "cafe_orders": [
      {
        "order_id": 10,
        "price": 150000
      }
    ],
    "extra_materials": [
      {
        "item_id": 5,
        "qty": 1,
        "price_per_unit": 25000
      }
    ],
    "promo_id": null
  }'
```

**Expected Response:**
```json
{
  "status": "success",
  "code": 200,
  "message": "Billing calculated successfully",
  "data": {
    "res_id": 1,
    "breakdown": {
      "salon_services": 500000,
      "cafe_orders": 150000,
      "extra_materials": 25000
    },
    "subtotal": 675000,
    "discounts": {
      "promo_discount": 0,
      "promo_name": null,
      "bundling_discount": 130000,
      "total_discount": 130000
    },
    "grand_total": 545000,
    "payment_status": "Ready for payment"
  },
  "timestamp": "2024-01-01 10:15:00"
}
```

### Scenario: With Promo Discount

**cURL Command:**
```bash
curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/billing/calculate \
  -H "Content-Type: application/json" \
  -d '{
    "res_id": 2,
    "salon_services": [
      {
        "service_id": "SRV001",
        "qty": 1,
        "price": 500000
      }
    ],
    "cafe_orders": [
      {
        "order_id": 11,
        "price": 150000
      }
    ],
    "extra_materials": [],
    "promo_id": 1
  }'
```

**Expected Response:**
```json
{
  "status": "success",
  "code": 200,
  "message": "Billing calculated successfully",
  "data": {
    "res_id": 2,
    "breakdown": {
      "salon_services": 500000,
      "cafe_orders": 150000,
      "extra_materials": 0
    },
    "subtotal": 650000,
    "discounts": {
      "promo_discount": 50000,
      "promo_name": "Member Discount 10%",
      "bundling_discount": 130000,
      "total_discount": 180000
    },
    "grand_total": 470000,
    "payment_status": "Ready for payment"
  },
  "timestamp": "2024-01-01 10:16:00"
}
```

### Test: Checkout / Process Payment

**Endpoint:** `POST /api/billing/checkout`

**Valid Payment Methods:**
- Cash
- Debit
- QRIS
- Transfer

**cURL Command:**
```bash
curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/billing/checkout \
  -H "Content-Type: application/json" \
  -d '{
    "res_id": 1,
    "total_amount": 545000,
    "payment_method": "QRIS"
  }'
```

**Expected Response:**
```json
{
  "status": "success",
  "code": 201,
  "message": "Payment processed successfully",
  "data": {
    "transaction_id": 1,
    "res_id": 1,
    "total_amount": 545000,
    "payment_method": "QRIS",
    "payment_date": "2024-01-01 10:18:00",
    "status": "Completed"
  },
  "timestamp": "2024-01-01 10:18:00"
}
```

---

## 4. API Filter Booking - Filter Beauticians by Online Status

### Scenario: Get All Online Beauticians

**Endpoint:** `GET /api/beauticians/online`

**cURL Command:**
```bash
curl -X GET http://localhost/SIB/PROJECT-APLIN/api.php/beauticians/online
```

**Expected Response:**
```json
{
  "status": "success",
  "code": 200,
  "message": "Online beauticians retrieved",
  "data": [
    {
      "profile_id": 1,
      "user_id": 4,
      "name": "Beautician 1",
      "email": "beautician1@test.com",
      "specialization": "Hair Stylist",
      "work_status": "Online",
      "hire_date": "2024-01-01"
    },
    {
      "profile_id": 2,
      "user_id": 5,
      "name": "Beautician 2",
      "email": "beautician2@test.com",
      "specialization": "Hair Stylist",
      "work_status": "Online",
      "hire_date": "2024-01-01"
    }
  ],
  "pagination": {
    "page": 1,
    "limit": 10,
    "total": 2
  },
  "timestamp": "2024-01-01 10:20:00"
}
```

### Scenario: Filter by Specialization

**Endpoint:** `GET /api/beauticians/online?specialization=Nailist&page=1&limit=10`

**cURL Command:**
```bash
curl -X GET "http://localhost/SIB/PROJECT-APLIN/api.php/beauticians/online?specialization=Nailist"
```

### Scenario: Get Available Time Slots

**Endpoint:** `GET /api/time-slots?beautician_id=4&date=2024-01-15`

**cURL Command:**
```bash
curl -X GET "http://localhost/SIB/PROJECT-APLIN/api.php/time-slots?beautician_id=4&date=2024-01-15"
```

**Expected Response:**
```json
{
  "status": "success",
  "code": 200,
  "message": "Time slots retrieved",
  "data": {
    "beautician_id": 4,
    "date": "2024-01-15",
    "available_slots": [
      {
        "time": "2024-01-15 09:00:00",
        "available": true,
        "formatted_time": "09:00"
      },
      {
        "time": "2024-01-15 09:30:00",
        "available": true,
        "formatted_time": "09:30"
      },
      {
        "time": "2024-01-15 10:00:00",
        "available": false,
        "formatted_time": "10:00"
      }
    ],
    "total_slots": 18,
    "available_count": 16
  },
  "timestamp": "2024-01-01 10:22:00"
}
```

### Test: Update Beautician Status

**Endpoint:** `PUT /api/beauticians/{user_id}/status`

**cURL Command:**
```bash
curl -X PUT http://localhost/SIB/PROJECT-APLIN/api.php/beauticians/4/status \
  -H "Content-Type: application/json" \
  -d '{
    "work_status": "Offline"
  }'
```

---

## 5. API Inventaris - Stock Auto-Deduction from BOM & Extra Materials

### Scenario: Place Order with Automatic BOM Stock Deduction

**Flow:**
1. Check current inventory stock for Menu001's BOM items
2. Place order for Menu001 (qty: 2)
3. Verify inventory automatically deducted

**Step 1: Get Current Inventory**

**Endpoint:** `GET /api/inventory`

**cURL Command:**
```bash
curl -X GET http://localhost/SIB/PROJECT-APLIN/api.php/inventory
```

**Expected Response (filtered to show 3 items):**
```json
{
  "status": "success",
  "code": 200,
  "message": "Inventory items retrieved",
  "data": [
    {
      "item_id": 1,
      "item_name": "Coffee Beans (kg)",
      "stock_qty": 50,
      "min_stock": 10,
      "unit": "kg",
      "extra_charge_per_unit": 0
    },
    {
      "item_id": 2,
      "item_name": "Milk (liter)",
      "stock_qty": 30,
      "min_stock": 5,
      "unit": "liter",
      "extra_charge_per_unit": 0
    },
    {
      "item_id": 3,
      "item_name": "Sugar (kg)",
      "stock_qty": 25,
      "min_stock": 5,
      "unit": "kg",
      "extra_charge_per_unit": 0
    }
  ],
  "pagination": {
    "page": 1,
    "limit": 20,
    "total": 15
  },
  "timestamp": "2024-01-01 10:25:00"
}
```

**Step 2: Deduct from BOM**

**Endpoint:** `POST /api/inventory/deduct-from-bom`

**cURL Command:**
```bash
curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/inventory/deduct-from-bom \
  -H "Content-Type: application/json" \
  -d '{
    "menu_id": "MENU001",
    "qty": 2
  }'
```

**Expected Response:**
```json
{
  "status": "success",
  "code": 200,
  "message": "Stock deducted from BOM successfully",
  "data": {
    "menu_id": "MENU001",
    "order_qty": 2,
    "deducted_items": [
      {
        "item_id": 1,
        "item_name": "Coffee Beans (kg)",
        "quantity_deducted": 4,
        "remaining_stock": 46
      },
      {
        "item_id": 2,
        "item_name": "Milk (liter)",
        "quantity_deducted": 2,
        "remaining_stock": 28
      }
    ]
  },
  "timestamp": "2024-01-01 10:26:00"
}
```

**Step 3: Verify Stock Deducted**

**Endpoint:** `GET /api/inventory?low_stock=false`

Verify that stock_qty for items 1 and 2 decreased.

### Scenario: Use Extra Material

**Endpoint:** `POST /api/inventory/use-extra-material`

**Description:**
Beautician uses extra material (e.g., premium hair treatment) that has extra charge

**cURL Command:**
```bash
curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/inventory/use-extra-material \
  -H "Content-Type: application/json" \
  -d '{
    "res_id": 1,
    "item_id": 10,
    "qty_used": 2
  }'
```

**Expected Response:**
```json
{
  "status": "success",
  "code": 201,
  "message": "Extra material usage recorded",
  "data": {
    "usage_id": 1,
    "res_id": 1,
    "item_id": 10,
    "item_name": "Premium Hair Treatment",
    "qty_used": 2,
    "unit_price": 50000,
    "charge_amount": 100000,
    "remaining_stock": 18
  },
  "timestamp": "2024-01-01 10:27:00"
}
```

### Test: Low Stock Alerts

**Endpoint:** `GET /api/inventory/low-stock-alerts`

**cURL Command:**
```bash
curl -X GET http://localhost/SIB/PROJECT-APLIN/api.php/inventory/low-stock-alerts
```

**Expected Response:**
```json
{
  "status": "success",
  "code": 200,
  "message": "Low stock alerts retrieved",
  "data": {
    "total_alerts": 3,
    "critical_count": 1,
    "warning_count": 2,
    "alerts": [
      {
        "item_id": 5,
        "item_name": "Hair Coloring Cream",
        "stock_qty": 0,
        "min_stock": 5,
        "unit": "bottle",
        "shortage": 5,
        "alert_level": "Critical"
      },
      {
        "item_id": 6,
        "item_name": "Nail Polish",
        "stock_qty": 7,
        "min_stock": 10,
        "unit": "bottle",
        "shortage": 3,
        "alert_level": "Warning"
      }
    ]
  },
  "timestamp": "2024-01-01 10:28:00"
}
```

---

## 6. API Admin Dashboard - Stock Alerts & Warnings

### Scenario: Admin Gets Dashboard Summary

**Endpoint:** `GET /api/admin/dashboard`

**Requires:** Admin authentication (login as admin@test.com first)

**cURL Command:**
```bash
curl -X GET http://localhost/SIB/PROJECT-APLIN/api.php/admin/dashboard
```

**Expected Response:**
```json
{
  "status": "success",
  "code": 200,
  "message": "Dashboard summary retrieved",
  "data": {
    "summary": {
      "total_revenue_today": 1250000,
      "total_orders_today": 8,
      "total_bookings_today": 5,
      "low_stock_alerts": 3
    },
    "date": "2024-01-01"
  },
  "timestamp": "2024-01-01 10:30:00"
}
```

### Scenario: Admin Views Low Stock Alerts

**Endpoint:** `GET /api/admin/low-stock-alerts`

**cURL Command:**
```bash
curl -X GET http://localhost/SIB/PROJECT-APLIN/api.php/admin/low-stock-alerts
```

**Expected Response:**
```json
{
  "status": "success",
  "code": 200,
  "message": "Low stock alerts",
  "data": {
    "total_alerts": 3,
    "out_of_stock": 1,
    "low_stock": 2,
    "warning": 0,
    "items": [
      {
        "item_id": 5,
        "item_name": "Hair Coloring Cream",
        "stock_qty": 0,
        "min_stock": 5,
        "unit": "bottle",
        "shortage": 5,
        "alert_type": "Out of Stock"
      },
      {
        "item_id": 6,
        "item_name": "Nail Polish",
        "stock_qty": 4,
        "min_stock": 10,
        "unit": "bottle",
        "shortage": 6,
        "alert_type": "Low Stock"
      },
      {
        "item_id": 7,
        "item_name": "Face Mask Powder",
        "stock_qty": 12,
        "min_stock": 10,
        "unit": "kg",
        "shortage": 0,
        "alert_type": "Warning"
      }
    ]
  },
  "timestamp": "2024-01-01 10:31:00"
}
```

### Scenario: Trigger Low Stock Alert

**Endpoint:** `POST /api/admin/trigger-low-stock-alert`

**Description:** Manually trigger alert for a specific item (auto-called after stock deduction)

**cURL Command:**
```bash
curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/admin/trigger-low-stock-alert \
  -H "Content-Type: application/json" \
  -d '{
    "item_id": 5
  }'
```

**Expected Response (Item at Critical Level):**
```json
{
  "status": "success",
  "code": 200,
  "message": "Alert triggered",
  "data": {
    "item_id": 5,
    "item_name": "Hair Coloring Cream",
    "current_stock": 0,
    "minimum_stock": 5,
    "alert_level": "CRITICAL",
    "alert_triggered": true,
    "message": "Stock Alert: Hair Coloring Cream is at critical level (Stock: 0, Min: 5)",
    "timestamp": "2024-01-01 10:32:00"
  },
  "timestamp": "2024-01-01 10:32:00"
}
```

### Test: Revenue Report

**Endpoint:** `GET /api/admin/revenue?period=today`

**Query Parameters:**
- today
- week
- month
- year

**cURL Command:**
```bash
curl -X GET "http://localhost/SIB/PROJECT-APLIN/api.php/admin/revenue?period=today"
```

### Test: User Statistics

**Endpoint:** `GET /api/admin/users`

**cURL Command:**
```bash
curl -X GET http://localhost/SIB/PROJECT-APLIN/api.php/admin/users
```

**Expected Response:**
```json
{
  "status": "success",
  "code": 200,
  "message": "User statistics",
  "data": {
    "total_users": 8,
    "by_role": [
      {"role": "Admin", "count": 1},
      {"role": "Receptionist", "count": 1},
      {"role": "Barista", "count": 1},
      {"role": "Beautician", "count": 2},
      {"role": "Customer", "count": 3}
    ]
  },
  "timestamp": "2024-01-01 10:33:00"
}
```

### Test: Top Selling Menus

**Endpoint:** `GET /api/admin/top-menus?limit=10`

**cURL Command:**
```bash
curl -X GET http://localhost/SIB/PROJECT-APLIN/api.php/admin/top-menus
```

### Test: Booking Statistics

**Endpoint:** `GET /api/admin/bookings?date=2024-01-01&status=Confirmed`

**cURL Command:**
```bash
curl -X GET "http://localhost/SIB/PROJECT-APLIN/api.php/admin/bookings?date=2024-01-01"
```

---

## Testing Workflow

### Complete Testing Sequence:

```bash
# 1. Create test data
curl http://localhost/SIB/PROJECT-APLIN/api.php/seed-test-data

# 2. Login with all 8 accounts
curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@test.com","password":"admin123"}'

# 3. Create cafe orders (Dine-In + Takeaway)
curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/orders/create \
  -H "Content-Type: application/json" \
  -d '{"order_type":"Dine-In","menu_id":"MENU001","qty":2,"seat_id":5}'

# 4. Get inventory and deduct from BOM
curl http://localhost/SIB/PROJECT-APLIN/api.php/inventory

curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/inventory/deduct-from-bom \
  -H "Content-Type: application/json" \
  -d '{"menu_id":"MENU001","qty":2}'

# 5. Use extra materials
curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/inventory/use-extra-material \
  -H "Content-Type: application/json" \
  -d '{"res_id":1,"item_id":10,"qty_used":2}'

# 6. Calculate billing with bundling
curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/billing/calculate \
  -H "Content-Type: application/json" \
  -d '{...billing data...}'

# 7. Filter online beauticians
curl http://localhost/SIB/PROJECT-APLIN/api.php/beauticians/online

# 8. Admin dashboard
curl http://localhost/SIB/PROJECT-APLIN/api.php/admin/dashboard

# 9. Check low stock alerts
curl http://localhost/SIB/PROJECT-APLIN/api.php/inventory/low-stock-alerts
```

---

## Response Format

All API responses follow this standardized format:

### Success Response (200, 201)
```json
{
  "status": "success",
  "code": 200,
  "message": "Operation successful",
  "data": {...},
  "timestamp": "2024-01-01 10:00:00"
}
```

### Error Response (400, 401, 403, 404, 500)
```json
{
  "status": "error",
  "code": 400,
  "message": "Error description",
  "errors": null,
  "timestamp": "2024-01-01 10:00:00"
}
```

### Validation Error Response (422)
```json
{
  "status": "error",
  "code": 422,
  "message": "Validation failed",
  "errors": {
    "field_name": "Error message",
    "another_field": "Another error"
  },
  "timestamp": "2024-01-01 10:00:00"
}
```

### Paginated Response
```json
{
  "status": "success",
  "code": 200,
  "message": "Data retrieved",
  "data": [...],
  "pagination": {
    "page": 1,
    "limit": 10,
    "total": 50
  },
  "timestamp": "2024-01-01 10:00:00"
}
```

---

## HTTP Status Codes

| Code | Meaning | Example |
|------|---------|---------|
| 200 | OK - Success | Login successful |
| 201 | Created - Resource created | Order created |
| 400 | Bad Request | Invalid input data |
| 401 | Unauthorized | Missing/invalid auth |
| 403 | Forbidden | User lacks permissions |
| 404 | Not Found | Resource doesn't exist |
| 405 | Method Not Allowed | Wrong HTTP method |
| 422 | Unprocessable Entity | Validation failed |
| 500 | Server Error | Database error |

---

## Notes

- All timestamps are in format: `YYYY-MM-DD HH:MM:SS`
- Prices are in Indonesian Rupiah (IDR)
- Bundling discount = 20% when BOTH salon_services > 0 AND cafe_orders > 0
- Stock deduction is automatic when order is placed
- Low stock alerts trigger when stock_qty <= min_stock
- Critical alerts when stock_qty = 0
