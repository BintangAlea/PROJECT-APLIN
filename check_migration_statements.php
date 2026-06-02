<?php
$migrationFile = __DIR__ . '/migration_cafe_integration.sql';
$sql = file_get_contents($migrationFile);

$statements = array_filter(
    array_map('trim', explode(';', $sql)),
    fn($stmt) => !empty($stmt) && !str_starts_with(trim($stmt), '--') && !str_starts_with(trim($stmt), '/*')
);

foreach ($statements as $i => $stmt) {
    echo "[$i] " . substr($stmt, 0, 60) . "...\n";
}
