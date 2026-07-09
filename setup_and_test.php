<?php
/**
 * MERISH Database Setup & Test Script
 * Creates database, loads schema and dummy data, tests appointment wizard
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "========== MERISH Database Setup & Test ==========\n\n";

// Database connection parameters
$host = 'localhost';
$user = 'root';
$pass = '';
$dbName = 'db_merish';

// Try different ports
$ports = [3306, 3307];
$pdo = null;

foreach ($ports as $port) {
    try {
        $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        echo "[Ã¢Å“â€œ] Connected to MySQL on port {$port}\n\n";
        break;
    } catch (PDOException $e) {
        continue;
    }
}

if ($pdo === null) {
    die("[Ã¢Å“â€”] Could not connect to MySQL on ports 3306 or 3307\n");
}

// Step 1: Drop and create database
echo "========== STEP 1: Setup Database ==========\n";
try {
    $pdo->exec("DROP DATABASE IF EXISTS {$dbName}");
    $pdo->exec("CREATE DATABASE {$dbName} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE {$dbName}");
    echo "[Ã¢Å“â€œ] Database created and selected\n\n";
} catch (PDOException $e) {
    die("[Ã¢Å“â€”] Failed to create database: " . $e->getMessage() . "\n");
}

// Step 2: Create tables from SQL file
echo "========== STEP 2: Create Tables ==========\n";
try {
    $sqlFile = 'c:\\ISTTS\\Pelajaran\\Semester 4\\APLIN\\projectaplin\\db_merish_fix.sql';
    $sql = file_get_contents($sqlFile);
    
    // Parse SQL simplified
    $pdo->exec(file_get_contents($sqlFile));
    echo "[âœ“] All tables created successfully\n\n";
} catch (Exception $e) {
    die("[Ã¢Å“â€”] Failed to create tables: " . $e->getMessage() . "\n");
}

// Step 3: Insert dummy data simplified
try {
    $pdo->exec(file_get_contents($dummyFile));
    echo "[âœ“] Dummy data inserted successfully\n\n";
} catch (Exception $e) {
    die("[Ã¢Å“â€”] Failed to insert dummy data: " . $e->getMessage() . "\n");
}

// Step 4: Verify data
echo "========== STEP 4: Verify Data ==========\n";
$tables = ['users', 'services', 'seats', 'staff_profiles', 'menus', 'promotions', 'reservations', 'reservation_details'];
foreach ($tables as $table) {
    $result = $pdo->query("SELECT COUNT(*) as cnt FROM {$table}");
    $count = $result->fetch()['cnt'];
    echo "[Ã¢Å“â€œ] {$table}: {$count} rows\n";
}

// Step 5: Test appointment wizard flow
echo "\n========== STEP 5: Test Appointment Wizard Flow ==========\n\n";

// Get test data
$customer = $pdo->query("SELECT * FROM users WHERE ROLE = 'Customer' LIMIT 1")->fetch();
$service = $pdo->query("SELECT * FROM services WHERE category = 'Hair' LIMIT 1")->fetch();
$beauticians = $pdo->query(
    "SELECT sp.profile_id, u.user_id, u.NAME, sp.specialization
     FROM staff_profiles sp
     JOIN users u ON sp.user_id = u.user_id
     WHERE sp.specialization = 'Hair Stylist' LIMIT 1"
)->fetch();
$seat = $pdo->query("SELECT * FROM seats LIMIT 1")->fetch();

echo "Test Data:\n";
echo "- Customer: " . ($customer ? $customer['NAME'] . " (ID: " . $customer['user_id'] . ")" : "NOT FOUND") . "\n";
echo "- Service: " . ($service ? $service['service_name'] . " - Rp " . number_format($service['base_tariff'], 0, ',', '.') : "NOT FOUND") . "\n";
echo "- Beautician: " . ($beauticians ? $beauticians['NAME'] : "NOT FOUND") . "\n";
echo "- Seat: " . ($seat ? $seat['seat_name'] : "NOT FOUND") . "\n";

if ($customer && $service && $beauticians && $seat) {
    echo "\n[*] Simulating appointment creation...\n";
    
    try {
        // Prepare reservation data
        $customerId = $customer['user_id'];
        $serviceId = $service['service_id'];
        $beauticianId = $beauticians['user_id'];
        $seatId = $seat['seat_id'];
        $scheduleTime = date('Y-m-d H:i:s', strtotime('+2 days 14:00'));
        $dpAmount = 50000;
        
        // Create reservation
        $stmt = $pdo->prepare(
            "INSERT INTO reservations (user_id, seat_id, STATUS, schedule_time, is_dp_paid, dp_amount)
             VALUES (:user_id, :seat_id, :status, :schedule_time, :is_dp_paid, :dp_amount)"
        );
        
        $stmt->execute([
            ':user_id' => $customerId,
            ':seat_id' => $seatId,
            ':status' => 'Pending',
            ':schedule_time' => $scheduleTime,
            ':is_dp_paid' => 0,
            ':dp_amount' => $dpAmount
        ]);
        
        $resId = $pdo->lastInsertId();
        
        // Create reservation detail
        $stmt = $pdo->prepare(
            "INSERT INTO reservation_details (res_id, service_id, beautician_id)
             VALUES (:res_id, :service_id, :beautician_id)"
        );
        
        $stmt->execute([
            ':res_id' => $resId,
            ':service_id' => $serviceId,
            ':beautician_id' => $beauticianId
        ]);
        
        // Verify
        $verify = $pdo->query(
            "SELECT r.res_id, r.user_id, r.seat_id, r.STATUS, r.schedule_time, r.dp_amount,
                    rd.service_id, s.service_name, u.NAME as beautician_name
             FROM reservations r
             LEFT JOIN reservation_details rd ON r.res_id = rd.res_id
             LEFT JOIN services s ON rd.service_id = s.service_id
             LEFT JOIN users u ON rd.beautician_id = u.user_id
             WHERE r.res_id = {$resId}"
        )->fetch();
        
        if ($verify) {
            echo "\n[Ã¢Å“â€œ] APPOINTMENT CREATED SUCCESSFULLY!\n";
            echo "   - Reservation ID: {$verify['res_id']}\n";
            echo "   - Customer: {$verify['user_id']}\n";
            echo "   - Service: {$verify['service_name']}\n";
            echo "   - Beautician: {$verify['beautician_name']}\n";
            echo "   - Date/Time: {$verify['schedule_time']}\n";
            echo "   - Seat: {$verify['seat_id']}\n";
            echo "   - Status: {$verify['STATUS']}\n";
            echo "   - DP Amount: Rp " . number_format($verify['dp_amount'], 0, ',', '.') . "\n";
        }
        
    } catch (Exception $e) {
        echo "[Ã¢Å“â€”] Appointment creation failed: " . $e->getMessage() . "\n";
    }
}

echo "\n========== TEST COMPLETE ==========\n";
echo "[Ã¢Å“â€œ] Database is ready for testing!\n";
echo "\nYou can now:\n";
echo "1. Access the appointment booking at: http://localhost/projectaplin/index.php?page=customer&action=appointment\n";
echo "2. Test the full 5-step wizard flow\n";
echo "3. Check database for created reservations\n";
?>

