#!/usr/bin/env php
<?php
/**
 * Database Setup Script
 * Runs all migrations to prepare database for cafe QR ordering system
 * 
 * Usage: php setup_database_migrations.php
 */

echo "====================================\n";
echo "MERISH Cafe - Database Setup\n";
echo "====================================\n\n";

// Load database connection
require_once __DIR__ . '/bootstrap.php';

use App\Core\Database;

try {
    $db = Database::getConnection();
    echo "✓ Database connection established\n\n";

    // Read migration file
    $migrationFile = __DIR__ . '/migration_qr_and_bills.sql';
    
    if (!file_exists($migrationFile)) {
        echo "✗ Migration file not found: $migrationFile\n";
        exit(1);
    }

    echo "Running migration: $migrationFile\n";
    echo "---\n\n";

    // Read SQL file
    $sql = file_get_contents($migrationFile);
    
    // Split by statements (simple approach - handles most cases)
    $statements = array_filter(
        array_map('trim', preg_split('/;/', $sql)),
        function($stmt) { return !empty($stmt) && !preg_match('/^--/', $stmt); }
    );

    $successCount = 0;
    $errorCount = 0;

    foreach ($statements as $statement) {
        // Skip comments
        $lines = array_map('trim', explode("\n", $statement));
        $statement = implode(' ', array_filter($lines, function($line) {
            return !empty($line) && !preg_match('/^--/', $line);
        }));

        if (empty($statement)) {
            continue;
        }

        try {
            $db->exec($statement);
            echo "✓ Executed: " . substr($statement, 0, 60) . "...\n";
            $successCount++;
        } catch (\PDOException $e) {
            // Ignore "already exists" errors for IF NOT EXISTS clauses
            if (strpos($e->getMessage(), 'already exists') !== false) {
                echo "⚠ Skipped (already exists): " . substr($statement, 0, 60) . "...\n";
            } else {
                echo "✗ Error: " . $e->getMessage() . "\n";
                echo "  Statement: " . substr($statement, 0, 80) . "...\n";
                $errorCount++;
            }
        }
    }

    echo "\n---\n";
    echo "Migration complete!\n";
    echo "✓ Successful: $successCount\n";
    echo "✗ Errors: $errorCount\n\n";

    if ($errorCount === 0) {
        echo "✓ All migrations completed successfully!\n";
        echo "\nNew tables created:\n";
        echo "  - qr_tokens (for anti-spam cafe ordering)\n";
        echo "  - open_bills (unified billing)\n";
        echo "  - cafe_guest_orders (guest order tracking)\n";
        echo "\nYou can now use the QR ordering system!\n";
    } else {
        echo "⚠ Some migrations had errors. Please review above.\n";
        exit(1);
    }

} catch (\Exception $e) {
    echo "\n✗ Fatal error: " . $e->getMessage() . "\n";
    exit(1);
}
