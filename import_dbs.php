<?php
require 'app/Core/Database.php';

// Temporarily connect without specific db to create them
$host = 'localhost';
$user = 'root';
$pass = '';

$ports = [3306, 3307];
$db = null;

foreach ($ports as $port) {
    $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
    try {
        $db = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        break;
    } catch (PDOException $e) {
    }
}

if (!$db) die("Could not connect to MySQL.");

function runSqlFile($db, $file) {
    echo "Running $file...\n";
    $sql = file_get_contents($file);
    if (!$sql) {
        echo "Could not read $file\n";
        return;
    }
    
    // Remove comments to avoid syntax errors when splitting
    $sql = preg_replace('/--.*$/m', '', $sql);
    
    $statements = array_filter(
        array_map('trim', preg_split('/;/', $sql)),
        function($stmt) { return !empty($stmt); }
    );
    
    foreach ($statements as $statement) {
        if (empty(trim($statement))) continue;
        try {
            $db->exec($statement);
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'already exists') === false) {
                echo "Error on: " . substr($statement, 0, 50) . "... -> " . $e->getMessage() . "\n";
            }
        }
    }
    echo "Done $file\n";
}

$db->exec("CREATE DATABASE IF NOT EXISTS db_merish_cafe");
$db->exec("CREATE DATABASE IF NOT EXISTS db_merish_salon");

runSqlFile($db, 'salon_merish_db.sql');
runSqlFile($db, 'salon_dummy_merish.sql');
runSqlFile($db, 'kafe_merish_db.sql');
runSqlFile($db, 'kafe_dummy_merish.sql');

echo "All imported!\n";
