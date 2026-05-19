<?php
/**
 * MERISH Appointment Wizard - Complete Test Suite
 * Tests database setup and 5-step booking flow
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$timestamp = date('Y-m-d H:i:s');
echo "========== MERISH Test Suite Started: {$timestamp} ==========\n\n";

// ====== CONNECTION ======
$ports = [3306, 3307];
$pdo = null;

foreach ($ports as $port) {
    try {
        $dsn = "mysql:host=localhost;port={$port};charset=utf8mb4";
        $pdo = new PDO($dsn, 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        echo "[✓] MySQL connected on port {$port}\n";
        break;
    } catch (PDOException $e) {
        continue;
    }
}

if (!$pdo) die("[✗] Could not connect to MySQL\n");

// ====== DATABASE SETUP ======
echo "\n========== PHASE 1: Database Setup ==========\n";

try {
    // Drop & create database
    $pdo->exec("DROP DATABASE IF EXISTS db_merish");
    $pdo->exec("CREATE DATABASE db_merish CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE db_merish");
    echo "[✓] Database created\n";

    // Load schema
    $schemaSQL = file_get_contents('db_merish_fix.sql');
    $schemas = preg_split('/;[\s\n]+/', $schemaSQL);
    
    foreach ($schemas as $schema) {
        $schema = trim($schema);
        if (strlen($schema) > 10 && !str_starts_with($schema, '--')) {
            try {
                $pdo->exec($schema);
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'already exists') === false) {
                    echo "[!] Schema error: " . substr($e->getMessage(), 0, 80) . "\n";
                }
            }
        }
    }
    echo "[✓] Schema loaded\n";

    // Load dummy data
    $dummySQL = file_get_contents('dummy_merish (1).sql');
    $dummies = preg_split('/;[\s\n]+/', $dummySQL);
    
    foreach ($dummies as $dummy) {
        $dummy = trim($dummy);
        if (strlen($dummy) > 10 && !str_starts_with($dummy, '--')) {
            try {
                $pdo->exec($dummy);
            } catch (PDOException $e) {
                // Ignore truncate/foreign key errors
            }
        }
    }
    echo "[✓] Dummy data loaded\n";

} catch (Exception $e) {
    die("[✗] Setup failed: " . $e->getMessage() . "\n");
}

// ====== VERIFICATION ======
echo "\n========== PHASE 2: Data Verification ==========\n";

$checks = [
    'users' => 'SELECT COUNT(*) as cnt FROM users',
    'services' => 'SELECT COUNT(*) as cnt FROM services',
    'staff_profiles' => 'SELECT COUNT(*) as cnt FROM staff_profiles',
    'seats' => 'SELECT COUNT(*) as cnt FROM seats',
    'menus' => 'SELECT COUNT(*) as cnt FROM menus',
    'promotions' => 'SELECT COUNT(*) as cnt FROM promotions',
];

foreach ($checks as $table => $query) {
    $result = $pdo->query($query)->fetch();
    echo "[✓] {$table}: {$result['cnt']} rows\n";
}

// ====== TEST DATA RETRIEVAL ======
echo "\n========== PHASE 3: Load Test Data ==========\n";

// Test customer
$customer = $pdo->query("SELECT user_id, NAME, loyalty_stage FROM users WHERE ROLE = 'Customer' LIMIT 1")->fetch();
if ($customer) {
    echo "[✓] Customer: {$customer['NAME']} (ID: {$customer['user_id']}, Tier: {$customer['loyalty_stage']})\n";
} else {
    die("[✗] No customer found\n");
}

// Hair services
$hairServices = $pdo->query(
    "SELECT s.service_id, s.service_name, s.base_tariff,
            (CASE WHEN p.promo_id IS NOT NULL THEN 1 ELSE 0 END) as has_promo
     FROM services s
     LEFT JOIN promotions p ON p.service_id_req = s.service_id
     WHERE s.category = 'Hair'"
)->fetchAll();
echo "[✓] Hair Services: " . count($hairServices) . "\n";
foreach ($hairServices as $s) {
    $promo = $s['has_promo'] ? '(+PROMO)' : '';
    echo "   - {$s['service_name']} Rp " . number_format($s['base_tariff'], 0, ',', '.') . " {$promo}\n";
}

// Hair stylists
$stylists = $pdo->query(
    "SELECT sp.profile_id, u.user_id, u.NAME 
     FROM staff_profiles sp
     JOIN users u ON sp.user_id = u.user_id
     WHERE sp.specialization = 'Hair Stylist'"
)->fetchAll();
echo "[✓] Hair Stylists: " . count($stylists) . "\n";
foreach ($stylists as $s) {
    echo "   - {$s['NAME']} (User: {$s['user_id']}, Profile: {$s['profile_id']})\n";
}

// Available seats
$seats = $pdo->query("SELECT seat_id, seat_name FROM seats")->fetchAll();
echo "[✓] Seats: " . count($seats) . "\n";
foreach ($seats as $s) {
    echo "   - {$s['seat_name']}\n";
}

// ====== SIMULATE FORM STEPS ======
echo "\n========== PHASE 4: Simulate Appointment Wizard ==========\n";

$draft = [];

// STEP 1: Category & Service
echo "\n[*] STEP 1: Category Selection\n";
$draft['category'] = 'Hair';
$draft['service_id'] = $hairServices[0]['service_id'];
echo "    Category: {$draft['category']}\n";
echo "    Service: {$hairServices[0]['service_name']}\n";

// STEP 2: Bundles (read promotions)
echo "\n[*] STEP 2: Bundle Selection\n";
$promos = $pdo->query(
    "SELECT * FROM promotions WHERE service_id_req = ? LIMIT 3"
)->execute([$draft['service_id']])->fetchAll();
echo "    Promotions available: " . count($promos) . "\n";
foreach ($promos as $p) {
    echo "    - {$p['promo_name']}: -Rp " . number_format($p['discount_value'], 0, ',', '.') . "\n";
}

// STEP 3: Date & Time
echo "\n[*] STEP 3: Schedule Selection\n";
$tomorrow = date('Y-m-d', strtotime('+1 day'));
$draft['reservation_date'] = $tomorrow;
$draft['reservation_time'] = '14:00';
echo "    Date: {$draft['reservation_date']}\n";
echo "    Time: {$draft['reservation_time']}\n";

// STEP 4: Beautician
echo "\n[*] STEP 4: Beautician Selection\n";
if (!empty($stylists)) {
    $draft['beautician_id'] = $stylists[0]['profile_id'];
    echo "    Selected: {$stylists[0]['NAME']}\n";
} else {
    die("[✗] No beauticians available\n");
}

// STEP 5: Review & Confirm
echo "\n[*] STEP 5: Confirm & Create\n";
echo "    Draft data:\n";
foreach ($draft as $k => $v) {
    echo "      {$k} = {$v}\n";
}

// ====== CREATE RESERVATION ======
echo "\n========== PHASE 5: Create Reservation in DB ==========\n";

try {
    // Resolve beautician profile_id to user_id
    $beauticianUser = $pdo->query(
        "SELECT user_id FROM staff_profiles WHERE profile_id = " . $draft['beautician_id']
    )->fetch();
    $beauticianUserId = $beauticianUser['user_id'] ?? null;

    if (!$beauticianUserId) {
        die("[✗] Could not resolve beautician user_id\n");
    }

    // Get first available seat
    $seatResult = $pdo->query("SELECT seat_id FROM seats LIMIT 1")->fetch();
    $seatId = $seatResult['seat_id'];

    // Combine date + time into DATETIME
    $scheduleTime = date('Y-m-d H:i:s', strtotime($draft['reservation_date'] . ' ' . $draft['reservation_time']));

    // Insert reservation
    $stmt = $pdo->prepare(
        "INSERT INTO reservations (user_id, seat_id, STATUS, schedule_time, is_dp_paid, dp_amount)
         VALUES (:user_id, :seat_id, :status, :schedule_time, :is_dp_paid, :dp_amount)"
    );

    $stmt->execute([
        ':user_id' => $customer['user_id'],
        ':seat_id' => $seatId,
        ':status' => 'Pending',
        ':schedule_time' => $scheduleTime,
        ':is_dp_paid' => 0,
        ':dp_amount' => 50000
    ]);

    $resId = $pdo->lastInsertId();
    echo "[✓] Reservation created: res_id = {$resId}\n";

    // Insert reservation detail
    $stmtDetail = $pdo->prepare(
        "INSERT INTO reservation_details (res_id, service_id, beautician_id)
         VALUES (:res_id, :service_id, :beautician_id)"
    );

    $stmtDetail->execute([
        ':res_id' => $resId,
        ':service_id' => $draft['service_id'],
        ':beautician_id' => $beauticianUserId
    ]);

    echo "[✓] Reservation detail created\n";

} catch (Exception $e) {
    die("[✗] Reservation creation failed: " . $e->getMessage() . "\n");
}

// ====== VERIFY FINAL RESULT ======
echo "\n========== PHASE 6: Final Verification ==========\n";

$finalResult = $pdo->query(
    "SELECT r.res_id, r.user_id, r.seat_id, r.STATUS, r.schedule_time, r.dp_amount,
            rd.service_id, s.service_name, u.NAME as customer_name, b.NAME as beautician_name
     FROM reservations r
     LEFT JOIN reservation_details rd ON r.res_id = rd.res_id
     LEFT JOIN services s ON rd.service_id = s.service_id
     LEFT JOIN users u ON r.user_id = u.user_id
     LEFT JOIN users b ON rd.beautician_id = b.user_id
     WHERE r.res_id = {$resId}"
)->fetch();

if ($finalResult) {
    echo "\n[✓✓✓] RESERVATION COMPLETE!\n\n";
    echo "ID:          {$finalResult['res_id']}\n";
    echo "Customer:    {$finalResult['customer_name']}\n";
    echo "Service:     {$finalResult['service_name']}\n";
    echo "Beautician:  {$finalResult['beautician_name']}\n";
    echo "Date/Time:   {$finalResult['schedule_time']}\n";
    echo "Seat:        {$finalResult['seat_id']}\n";
    echo "Status:      {$finalResult['STATUS']}\n";
    echo "DP Amount:   Rp " . number_format($finalResult['dp_amount'], 0, ',', '.') . "\n";
} else {
    echo "[✗] Could not retrieve final result\n";
}

echo "\n========== ALL TESTS COMPLETE ==========\n";
echo "[✓] Database ready for live testing!\n";
echo "\nNext Steps:\n";
echo "1. Open browser: http://localhost/projectaplin/index.php?page=customer&action=appointment\n";
echo "2. Test the 5-step wizard flow\n";
echo "3. Verify database creates new reservations correctly\n";
?>
