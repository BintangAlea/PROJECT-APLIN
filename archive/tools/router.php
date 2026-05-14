<?php
/**
 * router.php - Working router fallback
 */

require_once __DIR__ . '/bootstrap.php';

use App\Core\Session;
use App\Core\Auth;

Session::start();

// Determine the URI path, stripping query string and leading slash
$uri = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');

// Remove project path from URI if present
if (strpos($uri, 'SIB/PROJECT-APLIN/') === 0) {
    $uri = substr($uri, strlen('SIB/PROJECT-APLIN/'));
    $uri = trim($uri, '/');
}

// Remove router.php from URI if present
if (strpos($uri, 'router.php') === 0) {
    $uri = substr($uri, strlen('router.php'));
    $uri = trim($uri, '/');
}

// Fallback: check if route is passed via query string
if ((empty($uri) || $uri === 'router.php') && isset($_GET['route'])) {
    $uri = $_GET['route'];
}

$segments = array_filter(explode('/', $uri));
$segments = array_values($segments);

// Simple route table
$routes = [
    // Public routes
    ''                          => [\App\Controllers\Home::class,             'index'],
    'home'                      => [\App\Controllers\Home::class,             'index'],
    'login'                     => [\App\Controllers\AuthController::class,   'loginForm'],
    'register'                  => [\App\Controllers\AuthController::class,   'registerForm'],
    'auth/login'                => [\App\Controllers\AuthController::class,   'login'],
    'auth/register'             => [\App\Controllers\AuthController::class,   'register'],
    'auth/logout'               => [\App\Controllers\AuthController::class,   'logout'],

    // Customer routes
    'customer'                  => [\App\Controllers\CustomerController::class,    'index'],
    'customer/appointment'      => [\App\Controllers\CustomerController::class,    'appointment'],
    'customer/book-appointment' => [\App\Controllers\CustomerController::class,    'bookAppointment'],
    'customer/order-menu'       => [\App\Controllers\CustomerController::class,    'orderMenu'],
    'customer/create-order'     => [\App\Controllers\CustomerController::class,    'createOrder'],

    // Admin routes
    'admin'                     => [\App\Controllers\AdminController::class,       'index'],
    'admin/manage-users'        => [\App\Controllers\AdminController::class,       'manageUsers'],
    'admin/manage-reservations' => [\App\Controllers\AdminController::class,       'manageReservations'],
    'admin/manage-services'     => [\App\Controllers\AdminController::class,       'manageServices'],
    'admin/manage-menus'        => [\App\Controllers\AdminController::class,       'manageMenus'],
    'admin/manage-staff'        => [\App\Controllers\AdminController::class,       'manageStaff'],
    'admin/reports'             => [\App\Controllers\AdminController::class,       'reports'],
    'admin/settings'            => [\App\Controllers\AdminController::class,       'settings'],

    // Receptionist routes
    'receptionist'              => [\App\Controllers\ReceptionistController::class, 'index'],
    'receptionist/schedule'     => [\App\Controllers\ReceptionistController::class, 'scheduleBooking'],
    'receptionist/reservations' => [\App\Controllers\ReceptionistController::class, 'viewReservations'],
    'receptionist/update-reservation' => [\App\Controllers\ReceptionistController::class, 'updateReservationStatus'],
    'receptionist/orders'       => [\App\Controllers\ReceptionistController::class, 'viewOrders'],
    'receptionist/check-in'     => [\App\Controllers\ReceptionistController::class, 'checkIn'],

    // Beautician routes
    'beautician'                => [\App\Controllers\BeauticianController::class,   'index'],
    'beautician/today'          => [\App\Controllers\BeauticianController::class,   'todaySchedule'],
    'beautician/upcoming'       => [\App\Controllers\BeauticianController::class,   'upcomingSchedule'],
    'beautician/update-status'  => [\App\Controllers\BeauticianController::class,   'updateReservationStatus'],

    // Barista routes
    'barista'                   => [\App\Controllers\BaristaController::class,      'index'],
    'barista/update-order'      => [\App\Controllers\BaristaController::class,      'updateOrderStatus'],
    'barista/history'           => [\App\Controllers\BaristaController::class,      'orderHistory'],
];

// Build route key
$routeKey = implode('/', $segments);
if ($routeKey === 'router.php' || $routeKey === 'router') {
    $routeKey = '';
}

// Find and execute route
if (isset($routes[$routeKey])) {
    [$controller, $method] = $routes[$routeKey];
    $controller = new $controller();
    $controller->$method();
} else {
    // Not found - show error or home page
    http_response_code(404);
    echo "404 - Route not found: " . htmlspecialchars($routeKey);
}
?>
