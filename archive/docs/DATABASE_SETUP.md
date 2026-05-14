# Database Setup Guide

## Quick Start - Setup Database

### Method 1: Automatic Setup (Recommended)
```bash
cd PROJECT-APLIN
php setup_database.php
```

This will:
✅ Create database `db_merish`
✅ Create all 15 tables with proper relationships
✅ Set up all foreign keys and constraints
✅ Verify database connection

### Method 2: Manual MySQL Import
```bash
mysql -u root < db_merish_update.sql
```

Or in MySQL client:
```sql
SOURCE db_merish_update.sql;
```

---

## Verify Setup Completed Successfully

After setup, run:
```bash
php test_db_connection.php
```

Expected output:
```
=== DATABASE CONNECTION & FIELD ALIGNMENT TEST ===

TEST 1: Database Connection
✅ Connection successful
PDO Driver: mysql
Character Set: utf8mb4
...
✅ ALL TESTS PASSED - Database connection verified
```

---

## Database Structure

### Tables Created (15 Total)

**Master Tables (Foundation):**
1. ✅ `users` - User accounts (Admin, Receptionist, Barista, Beautician, Customer)
2. ✅ `services` - Beauty services (Hair, Nails, Lashes, Wax & Eyebrows)
3. ✅ `inventories` - Stock items for BOM
4. ✅ `seats` - Salon seat/zone management
5. ✅ `reward_catalog` - Loyalty rewards catalog

**Detail Tables (Level 1):**
6. ✅ `bom_details` - Bill of Materials for menus
7. ✅ `staff_profiles` - Employee profiles (Beautician, Barista, etc)
8. ✅ `reservations` - Service reservations
9. ✅ `menus` - Menu items (F&B)

**Relationship Tables (Level 2):**
10. ✅ `reservation_details` - Links reservation to services & beauticians
11. ✅ `redemptions` - Loyalty points redemption history
12. ✅ `promotions` - Service/Menu promotions

**Transaction Tables (Level 3):**
13. ✅ `orders` - Food/Beverage orders
14. ✅ `transactions` - Payment transactions
15. ✅ `reviews` - Customer reviews & ratings

---

## Connection Details

```
Host: localhost
Database: db_merish
User: root
Password: (empty)
Charset: utf8mb4
Driver: PDO MySQL
```

Located in: `app/Core/Database.php`

To change connection details:
```php
$host = 'localhost';
$dbName = 'db_merish';
$user = 'root';
$pass = ''; // Change password here if needed
```

---

## Field Mappings Verified

All Models have been updated to use exact database field names:

### Critical Field Names (Case-Sensitive)
- ❌ OLD: id → ✅ NEW: user_id, menu_id, service_id, res_id, order_id, trans_id, profile_id
- ❌ OLD: password → ✅ NEW: PASSWORD
- ❌ OLD: role → ✅ NEW: ROLE
- ❌ OLD: status → ✅ NEW: STATUS
- ❌ OLD: comment → ✅ NEW: COMMENT
- ❌ OLD: full_name → ✅ NEW: NAME

### Foreign Keys
- ❌ OLD: customer_id → ✅ NEW: user_id (in reservations)
- ❌ OLD: reservation_id → ✅ NEW: res_id (in orders, transactions)

### Data Type Changes
- ❌ OLD: menu_id INT → ✅ NEW: menu_id VARCHAR(10)
- ❌ OLD: service_id INT → ✅ NEW: service_id VARCHAR(10)

---

## Troubleshooting

### Error: "Unknown database 'db_merish'"
**Solution:** Run `php setup_database.php` first to create database

### Error: "Connection refused"
**Solution:** Make sure MySQL is running
```bash
# Check MySQL status
Get-Service MySQL*

# Start MySQL if stopped (Windows)
net start MySQL80
```

### Error: "Access denied for user 'root'@'localhost'"
**Solution:** Update password in `app/Core/Database.php`
```php
$pass = 'your_mysql_password'; // Add password here
```

### Error: "SQLSTATE[HY000]: General error: 1030 Got error..."
**Solution:** Database might have incorrect charset. Run setup again:
```bash
php setup_database.php
```

---

## Initialization Code (Optional - Already in setup_database.php)

If you want to auto-initialize in bootstrap.php, you can uncomment this:

```php
<?php
// In bootstrap.php, after autoloader setup:

// === UNCOMMENT TO AUTO-CREATE DATABASE ON FIRST RUN ===
// if (!class_exists('App\Core\Database')) {
//     require_once __DIR__ . '/setup_database.php';
// }
// ===================================================
```

---

## Status Summary

✅ All 8 Models updated for db_merish fields
✅ Database schema created with 15 tables
✅ Foreign key relationships configured
✅ PHP syntax validated (0 errors)
✅ Connection pool ready (PDO static method)
✅ Setup script provided (setup_database.php)

**Next Steps:**
1. Run: `php setup_database.php` 
2. Run: `php test_db_connection.php`
3. Start the application: `php -S localhost:8000`
