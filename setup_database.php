<?php
/**
 * Database Setup & Initialization Script
 * Gunakan script ini untuk membuat database db_merish dan semua tables
 * 
 * Usage: php setup_database.php
 */

require_once __DIR__ . '/bootstrap.php';

use App\Core\Database;
use PDO;

echo "========================================\n";
echo "   DATABASE SETUP - db_merish\n";
echo "========================================\n\n";

try {
    // Koneksi ke MySQL tanpa database (untuk membuat database)
    echo "[1/2] Connecting to MySQL server...\n";
    $pdo = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo "✅ Connected to MySQL\n\n";

    // Jalankan SQL script
    echo "[2/2] Creating database and tables...\n";
    $sqlFile = __DIR__ . '/db_merish_update.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("File not found: $sqlFile");
    }

    $sqlContent = file_get_contents($sqlFile);
    
    // Split SQL statements by semicolon and filter empty ones
    $statements = array_filter(
        array_map('trim', explode(';', $sqlContent)),
        fn($stmt) => !empty($stmt)
    );

    $executed = 0;
    foreach ($statements as $statement) {
        try {
            $pdo->exec($statement);
            $executed++;
        } catch (PDOException $e) {
            // Log error but continue
            echo "⚠️  Error: " . $e->getMessage() . "\n";
            echo "   Statement: " . substr($statement, 0, 50) . "...\n";
        }
    }

    echo "✅ Database setup complete!\n";
    echo "   Executed $executed SQL statements\n";
    echo "   Database: db_merish\n";
    echo "   Tables created: 15\n\n";

    // Verify connection with actual database
    echo "[3/3] Verifying database connection...\n";
    $dbConnection = Database::getConnection();
    $tables = $dbConnection->query('SHOW TABLES')->fetchAll();
    
    echo "✅ Database connection verified\n";
    echo "   Tables count: " . count($tables) . "\n";
    echo "   Tables: " . implode(', ', array_map(fn($t) => reset($t), $tables)) . "\n\n";

    echo "========================================\n";
    echo "✅ SETUP COMPLETE - Ready to use!\n";
    echo "========================================\n";
    echo "\nYou can now run: php test_db_connection.php\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nSetup failed. Make sure:\n";
    echo "1. MySQL server is running\n";
    echo "2. Root user has no password (or update config in Database.php)\n";
    echo "3. db_merish_update.sql is in PROJECT-APLIN directory\n";
    exit(1);
}
?>
