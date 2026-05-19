<?php

/**
 * API Router - api.php
 * Route all API requests to appropriate controllers
 * 
 * Usage:
 * POST /api/login - Login
 * POST /api/register - Register
 * GET /api/me - Current user info
 * GET /api/logout - Logout
 * 
 * See API_TESTING.md for all endpoints
 */

require_once __DIR__ . '/bootstrap.php';

use App\Controllers\ApiLoginController;
use App\Controllers\ApiOrderController;
use App\Controllers\ApiKasirController;
use App\Controllers\ApiBookingController;
use App\Controllers\ApiInventarisController;
use App\Controllers\ApiAdminDashboardController;

header('Content-Type: application/json');

// Parse URL
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = '/SIB/PROJECT-APLIN/api.php';
$path = str_replace($basePath, '', $requestUri);
$pathSegments = array_filter(explode('/', $path));

// Extract route
$method = $_SERVER['REQUEST_METHOD'];
$route = implode('/', $pathSegments);

// Route mapping
$routes = [
    // AUTH ROUTES
    'POST:login' => [ApiLoginController::class, 'login'],
    'POST:register' => [ApiLoginController::class, 'register'],
    'GET:logout' => [ApiLoginController::class, 'logout'],
    'GET:me' => [ApiLoginController::class, 'me'],
    'GET:seed-test-data' => [ApiLoginController::class, 'seedTestData'],

    // ORDER ROUTES
    'POST:orders/create' => [ApiOrderController::class, 'create'],
    'GET:orders' => [ApiOrderController::class, 'getAll'],

    // KASIR ROUTES
    'POST:billing/calculate' => [ApiKasirController::class, 'calculate'],
    'POST:billing/checkout' => [ApiKasirController::class, 'checkout'],
    'GET:promotions' => [ApiKasirController::class, 'getPromotions'],

    // BOOKING ROUTES
    'GET:beauticians/online' => [ApiBookingController::class, 'getOnlineBeauticians'],
    'GET:time-slots' => [ApiBookingController::class, 'getTimeSlots'],
    'GET:specializations' => [ApiBookingController::class, 'getSpecializations'],

    // INVENTORY ROUTES
    'GET:inventory' => [ApiInventarisController::class, 'getAll'],
    'POST:inventory/deduct-from-bom' => [ApiInventarisController::class, 'deductFromBOM'],
    'POST:inventory/use-extra-material' => [ApiInventarisController::class, 'useExtraMaterial'],
    'GET:inventory/low-stock-alerts' => [ApiInventarisController::class, 'getLowStockAlerts'],

    // ADMIN ROUTES
    'GET:admin/dashboard' => [ApiAdminDashboardController::class, 'getDashboard'],
    'GET:admin/low-stock-alerts' => [ApiAdminDashboardController::class, 'getLowStockAlerts'],
    'GET:admin/revenue' => [ApiAdminDashboardController::class, 'getRevenue'],
    'GET:admin/users' => [ApiAdminDashboardController::class, 'getUsers'],
    'GET:admin/top-menus' => [ApiAdminDashboardController::class, 'getTopMenus'],
    'GET:admin/bookings' => [ApiAdminDashboardController::class, 'getBookings'],
    'POST:admin/trigger-low-stock-alert' => [ApiAdminDashboardController::class, 'triggerLowStockAlert'],
];

// Handle parameterized routes
if (preg_match('/^orders\/(\d+)$/', $route, $matches)) {
    if ($method === 'GET') {
        $controller = new ApiOrderController();
        $controller->getOrder($matches[1]);
        exit;
    }
}

if (preg_match('/^orders\/(\d+)\/status$/', $route, $matches)) {
    if ($method === 'PUT') {
        $controller = new ApiOrderController();
        $controller->updateStatus($matches[1]);
        exit;
    }
}

if (preg_match('/^beauticians\/(\d+)$/', $route, $matches)) {
    if ($method === 'GET') {
        $controller = new ApiBookingController();
        $controller->getBeautician($matches[1]);
        exit;
    }
}

if (preg_match('/^beauticians\/(\d+)\/status$/', $route, $matches)) {
    if ($method === 'PUT') {
        $controller = new ApiBookingController();
        $controller->updateBeauticianStatus($matches[1]);
        exit;
    }
}

if (preg_match('/^inventory\/(\d+)$/', $route, $matches)) {
    if ($method === 'GET') {
        $controller = new ApiInventarisController();
        $controller->getItem($matches[1]);
        exit;
    }
}

if (preg_match('/^billing\/(\d+)$/', $route, $matches)) {
    if ($method === 'GET') {
        $controller = new ApiKasirController();
        $controller->getBilling($matches[1]);
        exit;
    }
}

// Match route
$routeKey = "{$method}:{$route}";
if (isset($routes[$routeKey])) {
    [$controllerClass, $method] = $routes[$routeKey];
    $controller = new $controllerClass();
    $controller->$method();
} else {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'code' => 404,
        'message' => 'API endpoint not found',
        'path' => $route,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
