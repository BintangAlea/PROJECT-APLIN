<?php
require 'bootstrap.php';
$db = \App\Core\Database::getConnection();
$sql = file_get_contents('migration_tier1_booking_flow.sql');
// Remove comments
$sql = preg_replace('/--.*$/m', '', $sql);
$statements = array_filter(
    array_map('trim', preg_split('/;/', $sql)),
    function($stmt) { return !empty($stmt); }
);
foreach ($statements as $statement) {
    if (empty(trim($statement))) continue;
    try {
        $db->exec($statement);
        echo "Executed: " . substr($statement, 0, 50) . "\n";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false || strpos($e->getMessage(), 'already exists') !== false) {
            echo "Skipped (already exists): " . substr($statement, 0, 50) . "\n";
        } else {
            echo "Error on: " . substr($statement, 0, 50) . " - " . $e->getMessage() . "\n";
        }
    }
}
echo "Done.\n";
