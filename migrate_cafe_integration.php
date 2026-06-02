<?php
/**
 * Cafe Integration Database Migration
 * Executes migration_cafe_integration.sql to create required tables and schema changes
 */

require_once __DIR__ . '/bootstrap.php';

use App\Core\Database;

try {
    $db = Database::getConnection();
    
    // Read migration SQL file
    $migrationFile = __DIR__ . '/migration_cafe_integration.sql';
    if (!file_exists($migrationFile)) {
        die("Migration file not found: $migrationFile\n");
    }
    
    $sql = file_get_contents($migrationFile);
    
    // Remove SQL comments better
    $sql = preg_replace('/--.*$/m', '', $sql);  // Remove line comments
    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);  // Remove block comments
    
    // Split into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        fn($stmt) => !empty($stmt)
    );
    
    echo "Executing " . count($statements) . " migration statements...\n\n";
    
    foreach ($statements as $i => $statement) {
        try {
            $db->exec($statement);
            echo "[✓] Statement " . ($i + 1) . " completed\n";
        } catch (\Throwable $e) {
            // Check if it's a "constraint already exists" or "duplicate column" - this is OK
            $errorMsg = $e->getMessage();
            if (
                (strpos($errorMsg, 'fk_orders_bill') !== false && strpos($errorMsg, 'Constraint') !== false) ||
                (strpos($errorMsg, 'Duplicate column') !== false) ||
                (strpos($errorMsg, 'already exists') !== false && strpos($errorMsg, 'Column') !== false) ||
                (strpos($errorMsg, 'Duplicate key name') !== false)
            ) {
                echo "[⚠] Statement " . ($i + 1) . " - already exists (skipping)\n";
                continue;
            }
            echo "[✗] Statement " . ($i + 1) . " failed: " . $errorMsg . "\n";
            echo "   SQL: " . substr($statement, 0, 100) . "...\n";
            throw $e;
        }
    }
    
    echo "\n✅ Migration completed successfully!\n";
    echo "Tables created: open_bills, qr_tokens\n";
    echo "Schema modified: orders table updated with bill_id column\n";
    
} catch (\Throwable $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
