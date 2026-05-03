<?php

/**
 * bootstrap.php
 * Registers a PSR-4-style autoloader for the App namespace.
 */

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
