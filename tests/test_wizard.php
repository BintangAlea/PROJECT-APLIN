<?php
// Quick integration test script for appointment wizard flow.
// Run: php tests/test_wizard.php

require_once __DIR__ . '/../bootstrap.php';

use App\Core\Database;
use App\Models\ReservationsModel;

echo "Starting wizard test...\n";

$pdo = Database::getConnection();

// Load SQL schema + dummy data
$sqlFiles = [
    __DIR__ . '/../db_merish_fix.sql',
    __DIR__ . '/../dummy_merish (1).sql'
];

foreach ($sqlFiles as $file) {
    if (!file_exists($file)) {
        echo "SQL file missing: $file\n";
        exit(1);
    }
    echo "Executing: $file ...\n";
    $contents = file_get_contents($file);
    try {
        // PDO::exec can handle multiple statements when using MySQL driver
        $pdo->exec($contents);
    } catch (PDOException $e) {
        echo "Error executing SQL file $file: " . $e->getMessage() . "\n";
        exit(1);
    }
}

// Sanity checks
$tables = ['users','services','promotions','seats','menus'];
foreach ($tables as $t) {
    $count = $pdo->query("SELECT COUNT(*) as c FROM $t")->fetch()['c'] ?? 0;
    echo "Table $t rows: $count\n";
}

// Prepare test reservation using ReservationsModel
$reservations = new ReservationsModel();

// Use existing customer 'Alina Customer' (email alina.customer@gmail.com)
$stmt = $pdo->prepare('SELECT user_id FROM users WHERE email = :e LIMIT 1');
$stmt->execute([':e' => 'alina.customer@gmail.com']);
$userRow = $stmt->fetch();
if (!$userRow) { echo "Test user not found\n"; exit(1); }
$customerId = (int)$userRow['user_id'];

// Find a hair beautician user_id
$stmt = $pdo->query("SELECT u.user_id FROM staff_profiles sp JOIN users u ON sp.user_id=u.user_id WHERE sp.specialization LIKE 'Hair%' LIMIT 1");
$b = $stmt->fetch();
$beauticianId = $b ? (int)$b['user_id'] : null;

// Choose service SV01 (Luminous Balayage)
$serviceId = 'SV01';
$date = date('Y-m-d', strtotime('+2 days'));
$time = '13:00:00';

echo "Creating reservation for user_id=$customerId, service=$serviceId, beautician=$beauticianId, date=$date $time\n";

$res = $reservations->create([
    'customer_id' => $customerId,
    'service_id' => $serviceId,
    'beautician_id' => $beauticianId,
    'reservation_date' => $date,
    'reservation_time' => $time,
    'status' => 'Pending',
    'is_dp_paid' => 0,
    'dp_amount' => 50000,
    'payment_proof_url' => null,
]);

if ($res === false) {
    echo "Failed to create reservation. Check logs.\n";
    exit(1);
}

echo "Reservation created with ID: $res\n";

$r = $pdo->prepare('SELECT * FROM reservations WHERE res_id = :id');
$r->execute([':id'=>$res]);
$row = $r->fetch();
echo "Reservations row: "; print_r($row);

$rd = $pdo->prepare('SELECT * FROM reservation_details WHERE res_id = :id');
$rd->execute([':id'=>$res]);
$rows = $rd->fetchAll();
echo "Reservation details: "; print_r($rows);

echo "Test completed.\n";

?>
