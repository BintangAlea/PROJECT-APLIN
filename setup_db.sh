#!/bin/bash
# Quick setup script for db_merish using MySQL CLI

echo "========== Setting up MERISH Database =========="

# Create database and load schema
echo "[*] Creating database and tables..."
mysql -u root -h localhost -e "DROP DATABASE IF EXISTS db_merish;" 2>/dev/null
mysql -u root -h localhost < "c:\ISTTS\Pelajaran\Semester 4\APLIN\projectaplin\db_merish_fix.sql" 2>/dev/null

if [ $? -eq 0 ]; then
    echo "[✓] Database schema created successfully"
else
    echo "[✗] Failed to create database schema"
    exit 1
fi

# Load dummy data
echo "[*] Inserting dummy data..."
mysql -u root -h localhost db_merish < "c:\ISTTS\Pelajaran\Semester 4\APLIN\projectaplin\dummy_merish (1).sql" 2>/dev/null

if [ $? -eq 0 ]; then
    echo "[✓] Dummy data inserted successfully"
else
    echo "[✗] Failed to insert dummy data"
    exit 1
fi

# Verify
echo ""
echo "========== Verification =========="
mysql -u root -h localhost db_merish -e "
    SELECT 'Users' as Table_Name, COUNT(*) as Row_Count FROM users
    UNION ALL
    SELECT 'Services', COUNT(*) FROM services
    UNION ALL
    SELECT 'Seats', COUNT(*) FROM seats
    UNION ALL
    SELECT 'Staff Profiles', COUNT(*) FROM staff_profiles
    UNION ALL
    SELECT 'Menus', COUNT(*) FROM menus
    UNION ALL
    SELECT 'Reservations', COUNT(*) FROM reservations
    UNION ALL
    SELECT 'Services (with Promo)', COUNT(*) FROM services s LEFT JOIN promotions p ON p.service_id_req = s.service_id WHERE p.promo_id IS NOT NULL
    UNION ALL
    SELECT 'Promotions', COUNT(*) FROM promotions;
"

echo ""
echo "[✓] Database setup complete!"
