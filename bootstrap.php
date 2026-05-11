<?php

/**
 * bootstrap.php
 * Registers a PSR-4-style autoloader for the App namespace.
 * Also loads environment variables from .env file
 */

// Start session
session_start();

// Load .env file
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments and empty lines
        if (empty($line) || strpos(trim($line), '#') === 0) {
            continue;
        }
        
        if (strpos($line, '=') !== false) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// PSR-4 Autoloader
spl_autoload_register(function (string $class): void {
    // Convert namespace separator to directory separator
    // e.g. App\Controllers\Home  ->  app/Controllers/Home.php
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/app/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Load helper functions
require_once __DIR__ . '/app/Core/Helper.php';
