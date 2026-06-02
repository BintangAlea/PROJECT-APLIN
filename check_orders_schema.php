<?php
require 'bootstrap.php';
use App\Core\Database;

$db = Database::getConnection();
$result = $db->query('DESCRIBE `orders`');
echo "Orders table columns:\n";
foreach ($result->fetchAll() as $row) {
    echo $row['Field'] . " - " . $row['Type'] . " - " . ($row['Null'] === 'YES' ? 'NULL' : 'NOT NULL') . "\n";
}
