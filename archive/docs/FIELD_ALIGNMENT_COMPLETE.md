# Database Field Alignment - COMPLETE ✅

**Date Updated:** 2024-Present
**Database:** db_merish
**All Models Updated:** 8/8 ✅

---

## Summary of Changes

All Models have been updated to use exact field names matching the `db_merish` database schema. No field name mismatches remain.

---

## Field Mapping by Model

### 1. UsersModel ✅
**Database Table:** `users`
**PK:** `user_id` (INT AUTO_INCREMENT)

| Old Field | Database Field | New Implementation |
|-----------|----------------|-------------------|
| id | user_id | ✅ Updated |
| full_name | NAME | ✅ Updated |
| phone | (removed) | ✅ Removed |
| status | (removed) | ✅ Removed |
| password | PASSWORD | ✅ Updated |
| role | ROLE | ✅ Updated |
| created_at | (removed) | ✅ Removed |
| (new) | email | ✅ Added |
| (new) | loyalty_stage | ✅ Added |
| (new) | total_spent | ✅ Added |
| (new) | reward_points | ✅ Added |

**Methods Updated:**
- `findAll()` - removed ORDER BY created_at
- `findById()` - changed to WHERE user_id = :id
- `findByRole()` - changed to WHERE ROLE = :role (removed status check)
- `register()` - now inserts: NAME, email, PASSWORD, ROLE, loyalty_stage, total_spent, reward_points
- `login()` - changed to use PASSWORD field for verification
- `update()` - changed WHERE to user_id = :id
- `delete()` - changed WHERE to user_id = :id
- `getTotalByRole()` - changed to WHERE ROLE = :role

---

### 2. MenusModel ✅
**Database Table:** `menus`
**PK:** `menu_id` (VARCHAR(10)) - Not INT!

| Old Field | Database Field | Change |
|-----------|----------------|--------|
| id | menu_id | ✅ Changed to VARCHAR |

**Methods Updated:**
- `findById()` - parameter changed from `int` to `string`
- `findById()` - WHERE clause: menu_id = :id

---

### 3. ServicesModel ✅
**Database Table:** `services`
**PK:** `service_id` (VARCHAR(10)) - Not INT!

| Old Field | Database Field | Change |
|-----------|----------------|--------|
| id | service_id | ✅ Changed to VARCHAR |

**Methods Updated:**
- `findById()` - parameter changed from `int` to `string`
- `findById()` - WHERE clause: service_id = :id

---

### 4. ReservationsModel ✅
**Database Table:** `reservations` (main), `reservation_details` (related)
**PK:** `res_id` (INT AUTO_INCREMENT)

**Major Restructure - Database uses TWO tables:**

| Old Field | Database Field | Notes |
|-----------|----------------|-------|
| id | res_id | ✅ Updated |
| customer_id | user_id | ✅ Updated |
| beautician_id | (in reservation_details) | ✅ Moved to detail JOIN |
| service_id | (in reservation_details) | ✅ Moved to detail JOIN |
| reservation_date | schedule_time | ✅ Updated (combined with time) |
| reservation_time | schedule_time | ✅ Merged into DATETIME |
| status | STATUS | ✅ Updated |
| created_at | (removed) | ✅ Removed |
| updated_at | (removed) | ✅ Removed |
| (new) | seat_id | ✅ Added |
| (new) | is_dp_paid | ✅ Added |
| (new) | dp_amount | ✅ Added |
| (new) | payment_proof_url | ✅ Added |

**Methods Restructured:**
- `findAll()` - simple SELECT from reservations
- `findById()` - WHERE res_id = :id
- `findByCustomerId()` - joins with reservation_details, services, users
- `findByDate()` - WHERE DATE(schedule_time) = :date
- `create()` - now inserts: user_id, seat_id, STATUS, schedule_time, is_dp_paid, dp_amount, payment_proof_url
- `update()` - WHERE res_id = :id
- `delete()` - WHERE res_id = :id
- `getTodayScheduleByBeautician()` - REMOVED (not applicable to new structure)
- `getUpcomingByBeautician()` - REMOVED (not applicable to new structure)

---

### 5. BeauticiansModel ✅
**Database Table:** `staff_profiles` + `users` join
**PK:** `profile_id` (INT) from staff_profiles

| Old Table | New Table | Change |
|-----------|-----------|--------|
| beauticians | staff_profiles | ✅ Updated - database has no beauticians table |

**Query Changes:**
- Joins with `staff_profiles` instead of non-existent `beauticians` table
- Added WHERE u.ROLE = "Beautician" filter
- Uses `sp.profile_id` as primary key

**Methods Updated:**
- `findAll()` - joins staff_profiles with users
- `findById()` - uses profile_id
- `findAvailable()` - now filters by ROLE = "Beautician"
- `findByStatus()` - REMOVED (not in database)
- `getSchedule()` - updated to use reservation_details table
- `update()` - WHERE profile_id = :id

---

### 6. OrdersModel ✅
**Database Table:** `orders`
**PK:** `order_id` (INT AUTO_INCREMENT)

| Old Field | Database Field | Change |
|-----------|----------------|--------|
| id | order_id | ✅ Updated |
| reservation_id | res_id | ✅ Updated |
| quantity | qty | ✅ Updated |
| status | STATUS | ✅ Updated |
| created_at | (removed) | ✅ Removed |
| price_per_item | (removed) | ✅ Removed |
| table_number | (removed) | ✅ Removed |

**Methods Updated:**
- `findAll()` - removed created_at ordering
- `findById()` - WHERE order_id = :id, uses menu_id VARCHAR join
- `findByReservationId()` - WHERE o.res_id = :res_id
- `findByStatus()` - WHERE o.STATUS = :status
- `findPendingOrInProgress()` - WHERE o.STATUS IN ("In Progress", "Selesai")
- `create()` - inserts: res_id, menu_id, qty, STATUS
- `update()` - WHERE order_id = :id
- `delete()` - WHERE order_id = :id

---

### 7. TransactionsModel ✅
**Database Table:** `transactions`
**PK:** `trans_id` (INT AUTO_INCREMENT)

| Old Field | Database Field | Change |
|-----------|----------------|--------|
| id | trans_id | ✅ Updated |
| reservation_id | res_id | ✅ Updated |
| subtotal | (removed) | ✅ Removed |
| discount_amount | (removed) | ✅ Removed |
| discount_reason | (removed) | ✅ Removed |
| synergy_discount | (removed) | ✅ Removed |
| multiplier_applied | (removed) | ✅ Removed |
| payment_method | (removed) | ✅ Removed |
| payment_status | (removed) | ✅ Removed |
| created_at | (removed) | ✅ Removed |
| total_amount | total_amount | ✅ Correct |
| (new) | payment_date | ✅ Added |

**Methods Updated:**
- `findAll()` - joins users via reservations
- `findById()` - WHERE t.trans_id = :id
- `findByReservationId()` - WHERE res_id = :res_id
- `findByStatus()` - REMOVED (no payment_status in DB)
- `create()` - inserts: res_id, total_amount, payment_date (NOW())
- `update()` - WHERE trans_id = :id
- `delete()` - WHERE trans_id = :id
- `getTotalRevenue()` - simplified calculation

---

### 8. ReviewsModel ✅
**Database Table:** `reviews`
**PK:** `review_id` (INT AUTO_INCREMENT)

| Old Field | Database Field | Change |
|-----------|----------------|--------|
| reservation_id | res_id | ✅ Updated |
| customer_id | customer_id | ✅ Correct |
| beautician_id | beautician_id | ✅ Correct |
| comment | COMMENT | ✅ Updated (uppercase) |
| created_at | (removed) | ✅ Removed |
| (new) | menu_id | ✅ Added (VARCHAR, nullable) |
| (new) | rating | ✅ Correct (1-5) |

**Methods Updated:**
- `findAll()` - joins changed to use user_id instead of beauticians table
- `findByBeautician()` - joins users directly
- `findByCustomer()` - joins users directly
- `create()` - now includes: res_id, customer_id, beautician_id, menu_id, rating, COMMENT
- `getAverageRating()` - uses beautician_id

---

## Database Validation

### Connection Status: ✅ VERIFIED
- **Host:** localhost
- **Database:** db_merish
- **User:** root
- **Password:** (empty)
- **Charset:** utf8mb4
- **Driver:** PDO with INNODB

### Tables Verified: 15 Total
1. ✅ users (with user_id PK)
2. ✅ services (with service_id VARCHAR PK)
3. ✅ menus (with menu_id VARCHAR PK)
4. ✅ staff_profiles (with profile_id PK)
5. ✅ reservations (with res_id PK)
6. ✅ reservation_details (with detail_id PK, links beautician)
7. ✅ orders (with order_id PK)
8. ✅ transactions (with trans_id PK)
9. ✅ reviews (with review_id PK)
10. ✅ inventories
11. ✅ seats
12. ✅ reward_catalog
13. ✅ bom_details
14. ✅ redemptions
15. ✅ promotions

---

## Code Validation Results

**PHP Syntax Validation:** ✅ ALL PASS
```
UsersModel.php: No syntax errors
MenusModel.php: No syntax errors
ServicesModel.php: No syntax errors
ReservationsModel.php: No syntax errors
BeauticiansModel.php: No syntax errors
OrdersModel.php: No syntax errors
TransactionsModel.php: No syntax errors
ReviewsModel.php: No syntax errors
```

---

## Critical Field Name Changes Summary

### Case Sensitivity (Database is case-sensitive in Linux):
- ❌ OLD: role → ✅ NEW: ROLE
- ❌ OLD: status → ✅ NEW: STATUS (WHERE applicable)
- ❌ OLD: password → ✅ NEW: PASSWORD
- ❌ OLD: comment → ✅ NEW: COMMENT
- ❌ OLD: full_name → ✅ NEW: NAME

### Primary Key Changes:
- ❌ OLD: id → ✅ NEW: user_id (users)
- ❌ OLD: id → ✅ NEW: menu_id (menus) - VARCHAR not INT
- ❌ OLD: id → ✅ NEW: service_id (services) - VARCHAR not INT
- ❌ OLD: id → ✅ NEW: res_id (reservations)
- ❌ OLD: id → ✅ NEW: order_id (orders)
- ❌ OLD: id → ✅ NEW: trans_id (transactions)

### Foreign Key Changes:
- ❌ OLD: customer_id → ✅ NEW: user_id (in reservations)
- ❌ OLD: reservation_id → ✅ NEW: res_id (in orders, transactions)

### DateTime Changes:
- ❌ OLD: reservation_date + reservation_time → ✅ NEW: schedule_time (DATETIME combined)

### Removed Non-Existent Table:
- ❌ OLD: beauticians table → ✅ NEW: staff_profiles table

---

## Next Steps Completed

✅ All Models updated to match database schema exactly
✅ All field names verified against db_merish
✅ All PHP syntax validated (0 errors)
✅ All primary keys corrected
✅ All foreign key relationships updated
✅ All JOINs updated for correct table names

## Status: READY FOR TESTING

All field alignments are complete. The application should now connect to the database correctly and query data without field name mismatches.

---

**Last Updated:** 2024
**Verified By:** Automated PHP linter + Manual Review
**Compatibility:** PHP 7.4+ with PDO MySQL driver
