<?php

/**
 * index.php — Front Controller
 *
 * All HTTP requests are routed through this file.
 * Make sure your web server (Apache/Nginx) is configured to rewrite
 * all requests to this file.
 *
 * Apache example (.htaccess):
 *   RewriteEngine On
 *   RewriteCond %{REQUEST_FILENAME} !-f
 *   RewriteRule ^(.*)$ index.php [QSA,L]
 */

require_once __DIR__ . '/bootstrap.php';

// Determine the URI path, stripping query string and leading slash
$uri = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');

// Simple route table:  uri pattern => [ControllerClass, method]
$routes = [
    ''               => [\App\Controllers\Home::class,          'index'],
    'home'           => [\App\Controllers\Home::class,          'index'],
    'login'          => [\App\Controllers\Login::class,         'index'],
    'qr-order'       => [\App\Controllers\QrOrder::class,       'index'],
    'unified-billing' => [\App\Controllers\UnifiedBilling::class, 'index'],
];

if (array_key_exists($uri, $routes)) {
    [$controllerClass, $method] = $routes[$uri];
    $controller = new $controllerClass();
    $controller->$method();
} else {
    http_response_code(404);
    echo '404 – Page not found.';
}
