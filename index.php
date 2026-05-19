<?php
require_once __DIR__ . '/bootstrap.php';

use App\Controllers\Home;
use App\Controllers\AuthController;
use App\Controllers\AdminController;
use App\Controllers\CustomerController;
use App\Controllers\ReceptionistController;
use App\Controllers\BeauticianController;
use App\Controllers\BaristaController;
use App\Controllers\QrOrder;
use App\Controllers\UnifiedBilling;
use App\Controllers\ServicesController;
use App\Controllers\CafeController;

$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'index';

// Determine which controller to use based on page
$controller = match($page) {
    'login'       => new AuthController(),
    'register'    => new AuthController(),
    'home'        => new Home(),
    'services'    => new ServicesController(),
    'cafe'        => new CafeController(),
    'admin'       => new AdminController(),
    'customer'    => new CustomerController(),
    'receptionist' => new ReceptionistController(),
    'beautician'  => new BeauticianController(),
    'barista'     => new BaristaController(),
    'qrorder'     => new QrOrder(),
    'billing'     => new UnifiedBilling(),
    default       => new Home(),
};

// Route POST actions to appropriate controller methods
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Allow controllers to expose methods via action names if implemented
    if (method_exists($controller, $action)) {
        $controller->{$action}();
        exit;
    }
    // fallback for auth helpers
    if ($page === 'login' && $action === 'login' && method_exists($controller, 'login')) {
        $controller->login();
        exit;
    }
}

// Call the appropriate action method
if ($action !== 'index' && method_exists($controller, $action)) {
    $controller->$action();
} else {
    $controller->index();
}
