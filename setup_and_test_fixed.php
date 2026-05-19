<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
echo "========== MERISH Database Setup & Test ==========\n\n";
$host = "localhost";
$user = "root";
$pass = "";
$dbName = "db_merish";
$port = 3306;
try {
    $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "[✓] Connected to MySQL on port {$port}\n\n";
} catch (PDOException $e) {
    die("[✗] Failed to connect: " . $e->getMessage() . "\n");
}
echo "========== STEP 1: Setup Database ==========\n";
try {
    $pdo->exec("DROP DATABASE IF EXISTS {$dbName}");
    $pdo->exec("CREATE DATABASE {$dbName} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE {$dbName}");
    echo "[✓] Database created and selected\n\n";
} catch (PDOException $e) {
    die("[✗] Failed to create database: " . $e->getMessage() . "\n");
}
echo "========== STEP 2: Create Tables ==========\n";
try {
    $sql = file_get_contents("c:\\ISTTS\\Pelajaran\\Semester 4\\APLIN\\projectaplin\\db_merish_fix.sql");
    $pdo->exec($sql);
    echo "[✓] All tables created successfully\n\n";
} catch (Exception $e) {
    die("[✗] Failed to create tables: " . $e->getMessage() . "\n");
}
echo "========== STEP 3: Insert Dummy Data ==========\n";
try {
    $sql = file_get_contents("c:\\ISTTS\\Pelajaran\\Semester 4\\APLIN\\projectaplin\\dummy_merish (1).sql");
    $pdo->exec($sql);
    echo "[✓] Dummy data inserted successfully\n\n";
} catch (Exception $e) {
    die("[✗] Failed to insert dummy data: " . $e->getMessage() . "\n");
}
echo "========== STEP 4: Verify Data ==========\n";
$tables = ["users", "services", "seats", "staff_profiles", "menus", "promotions", "reservations", "reservation_details"];
foreach ($tables as $table) {
    $result = $pdo->query("SELECT COUNT(*) as cnt FROM {$table}");
    $count = $result->fetch(PDO::FETCH_ASSOC)["cnt"];
    echo "[✓] {$table}: {$count} rows\n";
}
echo "\n========== STEP 5: Test Appointment Wizard Flow ==========\n\n";
$customer = $pdo->query("SELECT * FROM users WHERE ROLE = 'Customer' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$service = $pdo->query("SELECT * FROM services WHERE category = 'Hair' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$beauticians = $pdo->query("SELECT sp.profile_id, u.user_id, u.NAME, sp.specialization FROM staff_profiles sp JOIN users u ON sp.user_id = u.user_id WHERE sp.specialization = 'Hair Stylist' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$seat = $pdo->query("SELECT * FROM seats LIMIT 1")->fetch(PDO::FETCH_ASSOC);
echo "Test Data:\n";
echo "- Customer: " . ($customer ? $customer["NAME"] : "NOT FOUND") . "\n";
echo "- Service: " . ($service ? $service["service_name"] : "NOT FOUND") . "\n";
echo "- Beautician: " . ($beauticians ? $beauticians["NAME"] : "NOT FOUND") . "\n";
echo "- Seat: " . ($seat ? $seat["seat_name"] : "NOT FOUND") . "\n";
if ($customer && $service && $beauticians && $seat) {
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO reservations (user_id, reservation_date, status, total_price) VALUES (?, NOW(), 'Pending', ?)");
        $stmt->execute([$customer["user_id"], $service["base_tariff"]]);
        $resId = $pdo->lastInsertId();
        $stmt = $pdo->prepare("INSERT INTO reservation_details (reservation_id, item_id, item_type, beautician_id, seat_id, price) VALUES (?, ?, 'Service', ?, ?, ?)");
        $stmt->execute([$resId, $service["service_id"], $beauticians["profile_id"], $seat["seat_id"], $service["base_tariff"]]);
        $pdo->commit();
        echo "\n[✓] Appointment creation test SUCCESSFUL! Reservation ID: $resId\n";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "\n[✗] Appointment creation test FAILED: " . $e->getMessage() . "\n";
    }
} else {
    echo "\n[✗] Appointment creation test FAILED: Missing test data\n";
}
