# MERISH Cafe - Database Setup Guide

## Overview
This guide explains how to set up the database for the MERISH cafe management system with QR ordering, unified billing, and loyalty tiers.

## Prerequisites
- PHP 8.0+
- MySQL/MariaDB
- Laragon or similar local dev environment

## Quick Setup

### 1. Verify Database Connection
Edit `app/Core/Database.php` to ensure your database credentials are correct:

```php
private const HOST = 'localhost';
private const DB_NAME = 'db_merish';
private const USER = 'root';
private const PASSWORD = '';
```

### 2. Create Base Database (First Time Only)
Run the main database setup file first:

```bash
# In your database client or CLI
mysql -u root < db_merish_fix.sql
```

Or use phpMyAdmin to import `db_merish_fix.sql`.

This creates the base schema with:
- `users` - with loyalty_stage, total_spent, reward_points
- `services` - salon services with categories
- `menus` - cafe menu items
- `reservations` - booking reservations
- `orders` - cafe orders
- `seats` - seat/table mapping
- And all supporting tables

### 3. Run QR & Billing Migrations
Run the migration script to add new tables for QR ordering and unified billing:

```bash
php setup_database_migrations.php
```

Or manually import via MySQL:

```bash
mysql -u root db_merish < migration_qr_and_bills.sql
```

This adds:
- `qr_tokens` - Anti-spam QR code tokens (24-hour expiry)
- `open_bills` - Unified billing (Salon + Cafe combined)
- `cafe_guest_orders` - Guest order tracking with auto-cancel
- Triggers for 5-minute auto-cancel on guest orders

### 4. Verify Tables
```sql
SHOW TABLES;

-- Should include:
-- - qr_tokens
-- - open_bills
-- - cafe_guest_orders
-- - (plus all base tables from db_merish_fix.sql)
```

## Database Schema Overview

### QR Tokens Table (`qr_tokens`)
Stores temporary anti-spam tokens for QR codes.

```sql
- token_id (INT) - Primary key
- seat_id (VARCHAR) - Links to seat
- token (VARCHAR) - Unique 10-char token
- created_at (TIMESTAMP)
- expires_at (TIMESTAMP) - 24 hours later
```

**Why?** Each seat gets a unique token that expires, preventing replay attacks.

### Open Bills Table (`open_bills`)
Unified billing combining salon + cafe charges.

```sql
- bill_id (INT) - Primary key
- res_id (INT) - Links to reservation if from booking
- user_id (INT) - Nullable for guests
- seat_id (VARCHAR) - Physical seat/table
- guest_name (VARCHAR)
- bill_type (ENUM) - 'Salon Only' | 'Cafe Only' | 'Salon+Cafe'
- status (ENUM) - 'Open' | 'Pending Payment' | 'Paid' | 'Cancelled'
- salon_subtotal, cafe_subtotal - Line item totals
- synergy_discount - Auto-applied 20% when both items present
- total_amount - Final total after discount
- payment_method (VARCHAR)
```

**Why?** Allows members to consolidate all charges in one bill, and applies automatic Synergy Discount when mixing salon + cafe.

### Cafe Guest Orders Table (`cafe_guest_orders`)
Tracks guest orders with auto-cancel timer.

```sql
- guest_order_id (INT)
- bill_id (INT)
- seat_token (VARCHAR) - QR token for this order
- guest_name (VARCHAR)
- order_type (ENUM) - 'Dine-In' | 'Takeaway'
- total_amount (DECIMAL)
- payment_method (VARCHAR)
- payment_status (ENUM) - 'Pending' | 'Paid' | 'Failed' | 'Cancelled'
- auto_cancel_at (TIMESTAMP) - Set to NOW() + 5 minutes on insert
- status (ENUM) - 'New' | 'In Progress' | 'Ready' | 'Completed' | 'Cancelled'
```

**Why?** Guests have 5 minutes to pay or order auto-cancels. Prevents orphaned unpaid orders.

## Loyalty Tier Thresholds

Based on `users.total_spent`:
- **Stage 1 (Regular)**: IDR 0 - 750,000
- **Stage 2 (Loyal)**: IDR 750,000 - 2,000,000
- **Stage 3 (VIP)**: IDR 2,000,000+

VIP members get:
- 14-day booking window (vs 1-day for regular)
- Exclusive perks dashboard
- Points multiplier

## Common Operations

### Create Test Seat with QR Token
```php
// In QROrderController or test file
$seatModel = new SeatModel();
$tokenModel = new QRTokenModel();

// Create seat if not exists
if (!$seatModel->findById('C01')) {
    $seatModel->create([
        'seat_id' => 'C01',
        'seat_name' => 'Meja Kafe 1',
        'zone_type' => 'Relaxation Lounge',
        'qr_code_url' => 'merish.test/order?token=AUTO'
    ]);
}

// Generate token
$token = $tokenModel->getOrCreateTokenForSeat('C01');
echo "QR URL: merish.test/order?token=" . $token;
```

### Check Active Orders
```sql
SELECT * FROM cafe_guest_orders 
WHERE payment_status = 'Pending' 
AND auto_cancel_at > NOW();
```

### Manual Auto-Cancel (for scheduler)
```php
$guestOrderModel = new CafeGuestOrderModel();
$canceled = $guestOrderModel->cancelAllExpiredOrders();
echo "Cancelled: $canceled orders\n";
```

## Troubleshooting

### Migration Fails with "Table already exists"
This is normal - the script skips existing tables. Check error messages for actual failures.

### QR Token Table Missing
Ensure you ran the migration:
```bash
php setup_database_migrations.php
```

### Foreign Key Constraint Errors
Check that:
1. Seat exists in `seats` table before inserting orders
2. User exists in `users` table
3. Run migrations in order

### Payment Status Not Updating
Ensure `cafe_guest_orders.payment_status` is being updated in payment gateway callback handler.

## Integration Checklist

- [ ] Database created and populated
- [ ] Migration run successfully
- [ ] QR tokens table exists and is queryable
- [ ] Open bills table created
- [ ] Cafe guest orders table created
- [ ] Test seat created with valid token
- [ ] QROrderController endpoints accessible
- [ ] Payment gateway integration planned

## Next Steps

1. **Test QR Flow**: Generate QR for test seat, scan and place order
2. **Payment Integration**: Integrate QRIS/LinkAja gateway for guest checkout
3. **Barista Dashboard**: Build KDS (Kitchen Display System) to consume orders
4. **Member Dashboard**: Build loyalty tier UI with point display
5. **Admin Monitoring**: Dashboard showing real-time open bills and orders

---

For questions or issues, check the main project documentation in `00_READ_ME_FIRST.md`.
