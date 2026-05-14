<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection !== null) {
            return self::$connection;
        }

        // Simple flexible ports: try 3307 first, then 3306.
        $host = 'localhost';
        $dbName = 'db_merish';
        $user = 'root';
        $pass = '';

        $ports = [3307, 3306];
        $lastException = null;

        foreach ($ports as $port) {
            $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";
            try {
                self::$connection = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
                return self::$connection;
            } catch (PDOException $e) {
                $lastException = $e;
                // try next port
            }
        }

        $portsTried = implode(', ', $ports);
        $err = 'Koneksi database gagal (mencoba port: ' . $portsTried . ')';
        if ($lastException) $err .= ': ' . $lastException->getMessage();
        die($err);

        return self::$connection;
    }
}
