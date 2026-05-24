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
use App\Controllers\BookingController;
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
    'booking'     => new BookingController(),
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
    if ($page === 'login' && $action === 'login') {
        $controller->login();
        exit;
    } else if ($page === 'login' && $action === 'logout') {
        $controller->logout();
        exit;
    } else if ($page === 'register' && $action === 'register') {
        $controller->register();
        exit;
    } else if ($page === 'booking' && $action === 'submit') {
        // Handle booking submission
        $controller->submit();
        exit;
    } else if ($action === 'bookAppointment' || $action === 'createOrder' || $action === 'store' || $action === 'update' || $action === 'delete') {
        // Convert action to method name if it exists
        if (method_exists($controller, $action)) {
            $controller->$action();
            exit;
        }
    }
}

// Call the appropriate action method
if ($page === 'booking') {
    // For booking, route based on step parameter
    $step = $_GET['step'] ?? 1;
    if ($step === '4.1') {
        $method = 'step4Auth';
    } else {
        $method = 'step' . $step;
    }
    
    if (method_exists($controller, $method)) {
        $result = $controller->$method();
        if (is_array($result) && isset($result['view'])) {
            // Render view
            renderView($result['view'], $result['data'] ?? []);
        }
    } else {
        $controller->index();
    }
} else if ($action !== 'index' && method_exists($controller, $action)) {
    $controller->$action();
} else {
    $result = $controller->index();
    // Check if controller returned a view to render
    if (is_array($result) && isset($result['view'])) {
        renderView($result['view'], $result['data'] ?? []);
    }
}

/**
 * Render view helper
 */
function renderView($view, $data = [])
{
    extract($data);
    require __DIR__ . '/app/Views/' . str_replace('.', '/', $view) . '.php';
}
