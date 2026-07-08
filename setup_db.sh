#!/bin/bash
# Quick setup script for db_merish using MySQL CLI

echo "========== Setting up MERISH Database =========="

# Create database and load schema
echo "[*] Creating databases and tables..."
mysql -u root -h localhost -e "DROP DATABASE IF EXISTS db_merish_salon; DROP DATABASE IF EXISTS db_merish_cafe;" 2>/dev/null
mysql -u root -h localhost < "c:\ISTTS\Pelajaran\Semester 4\APLIN\projectaplin\salon_merish_db.sql" 2>/dev/null
mysql -u root -h localhost < "c:\ISTTS\Pelajaran\Semester 4\APLIN\projectaplin\kafe_merish_db.sql" 2>/dev/null

if [ $? -eq 0 ]; then
    echo "[✓] Database schemas created successfully"
else
    echo "[✗] Failed to create database schemas"
    exit 1
fi

# Load dummy data
echo "[*] Inserting dummy data..."
mysql -u root -h localhost db_merish_salon < "c:\ISTTS\Pelajaran\Semester 4\APLIN\projectaplin\salon_dummy_merish.sql" 2>/dev/null
mysql -u root -h localhost db_merish_cafe < "c:\ISTTS\Pelajaran\Semester 4\APLIN\projectaplin\kafe_dummy_merish.sql" 2>/dev/null

if [ $? -eq 0 ]; then
    echo "[✓] Dummy data inserted successfully"
else
    echo "[✗] Failed to insert dummy data"
    exit 1
fi

# Verify
echo ""
echo "========== Verification =========="
mysql -u root -h localhost -e "
    SELECT 'Users' as Table_Name, COUNT(*) as Row_Count FROM db_merish_salon.users
    UNION ALL
    SELECT 'Services', COUNT(*) FROM db_merish_salon.services
    UNION ALL
    SELECT 'Seats', COUNT(*) FROM db_merish_salon.seats
    UNION ALL
    SELECT 'Staff Profiles', COUNT(*) FROM db_merish_salon.staff_profiles
    UNION ALL
    SELECT 'Menus', COUNT(*) FROM db_merish_cafe.menus
    UNION ALL
    SELECT 'Reservations', COUNT(*) FROM db_merish_salon.reservations
    UNION ALL
    SELECT 'Promotions', COUNT(*) FROM db_merish_salon.promotions;
"

echo ""
echo "[✓] Database setup complete!"
