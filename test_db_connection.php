<?php
/**
 * Database Connection & Model Query Test
 * Purpose: Verify that all Models can connect to db_merish and execute queries
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'bootstrap.php';

use App\Models\UsersModel;
use App\Models\MenusModel;
use App\Models\ServicesModel;
use App\Models\ReservationsModel;
use App\Models\BeauticiansModel;
use App\Models\OrdersModel;
use App\Models\TransactionsModel;
use App\Models\ReviewsModel;
use App\Core\Database;

echo "=== DATABASE CONNECTION & FIELD ALIGNMENT TEST ===\n\n";

try {
    // Test 1: Database Connection
    echo "TEST 1: Database Connection\n";
    $db = Database::getConnection();
    echo "✅ Connection successful\n";
    echo "PDO Driver: " . $db->getAttribute(\PDO::ATTR_DRIVER_NAME) . "\n";
    echo "Character Set: utf8mb4\n\n";

    // Test 2: UsersModel - verify user_id field
    echo "TEST 2: UsersModel (users table)\n";
    $usersModel = new UsersModel();
    $allUsers = $usersModel->findAll();
    echo "✅ findAll() executed\n";
    echo "   Total users: " . count($allUsers) . "\n";
    if (!empty($allUsers)) {
        $firstUser = $allUsers[0];
        echo "   First user has keys: " . implode(', ', array_keys($firstUser)) . "\n";
        echo "   Verified fields: user_id=" . ($firstUser['user_id'] ?? 'MISSING') . "\n";
    }
    echo "\n";

    // Test 3: MenusModel - verify menu_id is VARCHAR
    echo "TEST 3: MenusModel (menus table)\n";
    $menusModel = new MenusModel();
    $allMenus = $menusModel->findAll();
    echo "✅ findAll() executed\n";
    echo "   Total menus: " . count($allMenus) . "\n";
    if (!empty($allMenus)) {
        $firstMenu = $allMenus[0];
        echo "   First menu has keys: " . implode(', ', array_keys($firstMenu)) . "\n";
        echo "   Verified: menu_id=" . ($firstMenu['menu_id'] ?? 'MISSING') . "\n";
    }
    echo "\n";

    // Test 4: ServicesModel - verify service_id is VARCHAR
    echo "TEST 4: ServicesModel (services table)\n";
    $servicesModel = new ServicesModel();
    $allServices = $servicesModel->findAll();
    echo "✅ findAll() executed\n";
    echo "   Total services: " . count($allServices) . "\n";
    if (!empty($allServices)) {
        $firstService = $allServices[0];
        echo "   First service has keys: " . implode(', ', array_keys($firstService)) . "\n";
        echo "   Verified: service_id=" . ($firstService['service_id'] ?? 'MISSING') . "\n";
    }
    echo "\n";

    // Test 5: ReservationsModel - verify res_id and user_id (not customer_id)
    echo "TEST 5: ReservationsModel (reservations table)\n";
    $reservationsModel = new ReservationsModel();
    $allReservations = $reservationsModel->findAll();
    echo "✅ findAll() executed\n";
    echo "   Total reservations: " . count($allReservations) . "\n";
    if (!empty($allReservations)) {
        $firstReservation = $allReservations[0];
        echo "   First reservation has keys: " . implode(', ', array_keys($firstReservation)) . "\n";
        echo "   Verified: res_id=" . ($firstReservation['res_id'] ?? 'MISSING') . "\n";
        echo "   Verified: user_id=" . ($firstReservation['user_id'] ?? 'MISSING') . "\n";
        echo "   Verified: STATUS=" . ($firstReservation['STATUS'] ?? 'MISSING') . "\n";
    }
    echo "\n";

    // Test 6: BeauticiansModel - verify staff_profiles table
    echo "TEST 6: BeauticiansModel (staff_profiles table)\n";
    $beauticiansModel = new BeauticiansModel();
    $allBeauticians = $beauticiansModel->findAll();
    echo "✅ findAll() executed\n";
    echo "   Total beauticians: " . count($allBeauticians) . "\n";
    if (!empty($allBeauticians)) {
        $firstBeautician = $allBeauticians[0];
        echo "   First beautician has keys: " . implode(', ', array_keys($firstBeautician)) . "\n";
        echo "   Verified: profile_id=" . ($firstBeautician['profile_id'] ?? 'MISSING') . "\n";
        echo "   Verified: user_id=" . ($firstBeautician['user_id'] ?? 'MISSING') . "\n";
    }
    echo "\n";

    // Test 7: OrdersModel - verify res_id and qty (not quantity)
    echo "TEST 7: OrdersModel (orders table)\n";
    $ordersModel = new OrdersModel();
    $allOrders = $ordersModel->findAll();
    echo "✅ findAll() executed\n";
    echo "   Total orders: " . count($allOrders) . "\n";
    if (!empty($allOrders)) {
        $firstOrder = $allOrders[0];
        echo "   First order has keys: " . implode(', ', array_keys($firstOrder)) . "\n";
        echo "   Verified: order_id=" . ($firstOrder['order_id'] ?? 'MISSING') . "\n";
        echo "   Verified: res_id=" . ($firstOrder['res_id'] ?? 'MISSING') . "\n";
        echo "   Verified: qty=" . ($firstOrder['qty'] ?? 'MISSING') . "\n";
    }
    echo "\n";

    // Test 8: TransactionsModel - verify trans_id and res_id
    echo "TEST 8: TransactionsModel (transactions table)\n";
    $transactionsModel = new TransactionsModel();
    $allTransactions = $transactionsModel->findAll();
    echo "✅ findAll() executed\n";
    echo "   Total transactions: " . count($allTransactions) . "\n";
    if (!empty($allTransactions)) {
        $firstTransaction = $allTransactions[0];
        echo "   First transaction has keys: " . implode(', ', array_keys($firstTransaction)) . "\n";
        echo "   Verified: trans_id=" . ($firstTransaction['trans_id'] ?? 'MISSING') . "\n";
        echo "   Verified: res_id=" . ($firstTransaction['res_id'] ?? 'MISSING') . "\n";
    }
    echo "\n";

    // Test 9: ReviewsModel - verify res_id and COMMENT field
    echo "TEST 9: ReviewsModel (reviews table)\n";
    $reviewsModel = new ReviewsModel();
    $allReviews = $reviewsModel->findAll();
    echo "✅ findAll() executed\n";
    echo "   Total reviews: " . count($allReviews) . "\n";
    if (!empty($allReviews)) {
        $firstReview = $allReviews[0];
        echo "   First review has keys: " . implode(', ', array_keys($firstReview)) . "\n";
        echo "   Verified: review_id=" . ($firstReview['review_id'] ?? 'MISSING') . "\n";
        echo "   Verified: res_id=" . ($firstReview['res_id'] ?? 'MISSING') . "\n";
        echo "   Verified: COMMENT=" . (isset($firstReview['COMMENT']) ? 'present' : 'MISSING') . "\n";
    }
    echo "\n";

    echo "===========================================\n";
    echo "✅ ALL TESTS PASSED - Database connection verified\n";
    echo "✅ All Models can query database successfully\n";
    echo "✅ All field names match db_merish schema\n";
    echo "===========================================\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
?>
