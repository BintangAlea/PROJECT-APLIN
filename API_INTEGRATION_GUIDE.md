# API Integration Guide

## How to Use MERISH API Endpoints

### Quick Start

All API endpoints are available through the unified router at:
```
http://localhost/SIB/PROJECT-APLIN/api.php
```

### Step 1: Access API Router

The `api.php` file contains the unified router that:
1. ✅ Accepts all HTTP methods (GET, POST, PUT)
2. ✅ Routes requests to appropriate controller
3. ✅ Returns standardized JSON responses
4. ✅ Handles parameterized routes

### Step 2: Test Each API Group

#### Group 1: Authentication
```bash
# Create test data (8 accounts)
curl http://localhost/SIB/PROJECT-APLIN/api.php/seed-test-data

# Login
curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@test.com","password":"admin123"}'
```

#### Group 2: Cafe Orders
```bash
# Dine-In order (customer with seat)
curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/orders/create \
  -H "Content-Type: application/json" \
  -d '{
    "order_type":"Dine-In",
    "menu_id":"MENU001",
    "qty":2,
    "seat_id":5
  }'

# Takeaway order (guest, no auth required)
curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/orders/create \
  -H "Content-Type: application/json" \
  -d '{
    "order_type":"Takeaway",
    "menu_id":"MENU002",
    "qty":1,
    "guest_name":"John Doe"
  }'
```

#### Group 3: Billing & Promotions
```bash
# Calculate bill with 20% bundling discount
curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/billing/calculate \
  -H "Content-Type: application/json" \
  -d '{
    "res_id":1,
    "salon_services":[{"service_id":"SRV001","qty":1,"price":500000}],
    "cafe_orders":[{"order_id":10,"price":150000}],
    "extra_materials":[{"item_id":5,"qty":2,"price_per_unit":50000}],
    "promo_id":null
  }'

# Checkout (process payment)
curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/billing/checkout \
  -H "Content-Type: application/json" \
  -d '{
    "res_id":1,
    "total_amount":545000,
    "payment_method":"QRIS"
  }'
```

#### Group 4: Beautician Booking Filters
```bash
# Get online beauticians
curl http://localhost/SIB/PROJECT-APLIN/api.php/beauticians/online

# Filter by specialization
curl "http://localhost/SIB/PROJECT-APLIN/api.php/beauticians/online?specialization=Hair%20Stylist"

# Get available time slots
curl "http://localhost/SIB/PROJECT-APLIN/api.php/time-slots?beautician_id=4&date=2024-01-15"

# Change beautician status
curl -X PUT http://localhost/SIB/PROJECT-APLIN/api.php/beauticians/4/status \
  -H "Content-Type: application/json" \
  -d '{"work_status":"Offline"}'
```

#### Group 5: Inventory Management
```bash
# Get all items
curl http://localhost/SIB/PROJECT-APLIN/api.php/inventory

# Deduct stock based on BOM (auto when order placed)
curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/inventory/deduct-from-bom \
  -H "Content-Type: application/json" \
  -d '{
    "menu_id":"MENU001",
    "qty":2
  }'

# Record extra material usage
curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/inventory/use-extra-material \
  -H "Content-Type: application/json" \
  -d '{
    "res_id":1,
    "item_id":10,
    "qty_used":2
  }'

# Get low stock alerts
curl http://localhost/SIB/PROJECT-APLIN/api.php/inventory/low-stock-alerts
```

#### Group 6: Admin Dashboard
```bash
# Get dashboard summary
curl http://localhost/SIB/PROJECT-APLIN/api.php/admin/dashboard

# Get detailed low stock alerts
curl http://localhost/SIB/PROJECT-APLIN/api.php/admin/low-stock-alerts

# Get revenue report
curl "http://localhost/SIB/PROJECT-APLIN/api.php/admin/revenue?period=today"

# Get user statistics
curl http://localhost/SIB/PROJECT-APLIN/api.php/admin/users

# Get top selling menus
curl http://localhost/SIB/PROJECT-APLIN/api.php/admin/top-menus

# Get booking statistics
curl "http://localhost/SIB/PROJECT-APLIN/api.php/admin/bookings?date=2024-01-01"

# Trigger low stock alert
curl -X POST http://localhost/SIB/PROJECT-APLIN/api.php/admin/trigger-low-stock-alert \
  -H "Content-Type: application/json" \
  -d '{"item_id":5}'
```

---

## Integration Options

### Option A: Use Existing HTML Views

The web interface still works via:
- `index.php` - Routes to controllers
- `app/Views/` - Contains all HTML templates

#### Current Routes:
```php
// Login/Register
GET /  → Home view
POST /login → AuthController::login()
POST /register → AuthController::register()

// Protected Routes (require role)
GET /admin → AdminController (Admin only)
GET /barista → BaristaController (Barista only)
GET /beautician → BeauticianController (Beautician only)
GET /customer → CustomerController (Customer only)
```

### Option B: Use API Only

Call API endpoints directly from:
- Mobile apps
- Desktop applications
- Third-party integrations
- Frontend frameworks (React, Vue, etc.)

**Base URL:** `http://localhost/SIB/PROJECT-APLIN/api.php`

### Option C: Hybrid Approach

Combine web views with API calls:
1. Load HTML views from `index.php`
2. Use AJAX/Fetch to call API endpoints
3. Update DOM without page reload

**Example (JavaScript):**
```javascript
// Login via API
fetch('/SIB/PROJECT-APLIN/api.php/login', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({
    email: 'admin@test.com',
    password: 'admin123'
  })
})
.then(res => res.json())
.then(data => {
  if (data.status === 'success') {
    localStorage.setItem('token', data.data.token);
    window.location.href = '/dashboard';
  }
});
```

---

## Error Handling

All API errors follow consistent format:

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

### Server Error (500)
```json
{
  "status": "error",
  "code": 500,
  "message": "Server error: database connection failed",
  "timestamp": "2024-01-01 10:00:00"
}
```

---

## Testing with Postman

### 1. Import Collection

Create new collection "MERISH API" with:

**Request 1: Login**
```
POST http://localhost/SIB/PROJECT-APLIN/api.php/login
Content-Type: application/json

{
  "email": "admin@test.com",
  "password": "admin123"
}
```

**Request 2: Create Order**
```
POST http://localhost/SIB/PROJECT-APLIN/api.php/orders/create
Content-Type: application/json

{
  "order_type": "Dine-In",
  "menu_id": "MENU001",
  "qty": 2,
  "seat_id": 5
}
```

**Request 3: Calculate Bill**
```
POST http://localhost/SIB/PROJECT-APLIN/api.php/billing/calculate
Content-Type: application/json

{
  "res_id": 1,
  "salon_services": [
    {"service_id": "SRV001", "qty": 1, "price": 500000}
  ],
  "cafe_orders": [
    {"order_id": 10, "price": 150000}
  ],
  "extra_materials": [],
  "promo_id": null
}
```

**Request 4: Get Online Beauticians**
```
GET http://localhost/SIB/PROJECT-APLIN/api.php/beauticians/online
```

**Request 5: Inventory Deduction**
```
POST http://localhost/SIB/PROJECT-APLIN/api.php/inventory/deduct-from-bom
Content-Type: application/json

{
  "menu_id": "MENU001",
  "qty": 2
}
```

**Request 6: Admin Dashboard**
```
GET http://localhost/SIB/PROJECT-APLIN/api.php/admin/dashboard
```

### 2. Run Collection

- Use "Runner" to execute all requests
- Verify response codes (200, 201, 400, etc.)
- Check response JSON structure

---

## Testing with cURL Scripts

Create `test_api.sh`:
```bash
#!/bin/bash

BASE_URL="http://localhost/SIB/PROJECT-APLIN/api.php"

echo "=== Testing MERISH API ==="

echo -e "\n1. Testing Login"
curl -X POST $BASE_URL/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@test.com","password":"admin123"}'

echo -e "\n2. Testing Orders"
curl -X POST $BASE_URL/orders/create \
  -H "Content-Type: application/json" \
  -d '{"order_type":"Dine-In","menu_id":"MENU001","qty":2,"seat_id":5}'

echo -e "\n3. Testing Billing"
curl -X POST $BASE_URL/billing/calculate \
  -H "Content-Type: application/json" \
  -d '{"res_id":1,"salon_services":[{"service_id":"SRV001","qty":1,"price":500000}],"cafe_orders":[{"order_id":10,"price":150000}]}'

echo -e "\n4. Testing Beauticians"
curl $BASE_URL/beauticians/online

echo -e "\n5. Testing Inventory"
curl $BASE_URL/inventory

echo -e "\n6. Testing Admin Dashboard"
curl $BASE_URL/admin/dashboard

echo -e "\n=== Test Complete ==="
```

Run:
```bash
chmod +x test_api.sh
./test_api.sh
```

---

## Performance Tips

1. **Caching Responses**
   - Cache beautician list (doesn't change often)
   - Cache menu list (update on changes)
   - Cache promotions (refresh hourly)

2. **Pagination**
   - Always paginate large datasets
   - Use `?page=1&limit=20`
   - Reduces response size

3. **Batch Requests**
   - Instead of N requests, send 1 batch
   - Reduces network overhead
   - Example: Get multiple orders in one call

4. **Database Indexing**
   - Ensure indexes on: user_id, status, date, email
   - Improves query speed 10-100x

---

## Security Best Practices

✅ **Always Use HTTPS in Production**
```
https://yourdomain.com/SIB/PROJECT-APLIN/api.php
```

✅ **Validate All Input**
- Email format validation
- Password length check (min 6 chars)
- Numeric ranges (qty > 0)
- Enum values (valid statuses)

✅ **Use Environment Variables**
```php
// Never hardcode database credentials
$db_host = getenv('DB_HOST');
$db_user = getenv('DB_USER');
```

✅ **Rate Limiting**
```php
// Prevent brute force attacks
$attempts = $_SESSION['login_attempts'] ?? 0;
if ($attempts > 5) {
    http_response_code(429);
    die('Too many attempts');
}
```

✅ **CORS Headers** (if used with frontend)
```php
header('Access-Control-Allow-Origin: https://yourdomain.com');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');
```

---

## Troubleshooting

### Issue: 404 - Endpoint not found
**Solution:** Check route spelling in api.php routes array

### Issue: 422 - Validation failed
**Solution:** Verify all required fields included in request body

### Issue: 500 - Server error
**Solution:** Check error logs, verify database connection

### Issue: CORS error (frontend)
**Solution:** Add CORS headers to api.php:
```php
header('Access-Control-Allow-Origin: *');
```

### Issue: Stock not deducting
**Solution:** Verify menu has bom_recipe_id set in database

---

## Next Steps

1. ✅ All 6 API groups implemented
2. ✅ Unified router working
3. ✅ Test data (8 accounts) ready
4. ✅ Documentation complete

**Recommended Next Actions:**
- Test all endpoints with Postman
- Integrate API with frontend app
- Set up monitoring/logging
- Configure backup/recovery
- Deploy to production

---

## Summary

**All 30 API endpoints are now ready for testing:**

| Category | Count | Status |
|----------|-------|--------|
| Authentication | 5 | ✅ Ready |
| Orders | 4 | ✅ Ready |
| Billing | 4 | ✅ Ready |
| Booking | 5 | ✅ Ready |
| Inventory | 5 | ✅ Ready |
| Admin | 7 | ✅ Ready |
| **Total** | **30** | **✅ Ready** |

**Key Features Implemented:**
- ✅ 8 test accounts (different roles)
- ✅ Dine-In + Takeaway orders
- ✅ 20% bundling discount
- ✅ Online beautician filtering
- ✅ BOM-based stock deduction
- ✅ Extra material tracking
- ✅ Admin dashboard alerts
- ✅ Standardized API responses
- ✅ Unified router

---

For detailed endpoint documentation, see: `API_TESTING.md`
For architecture overview, see: `API_INTEGRATION_SUMMARY.md`
