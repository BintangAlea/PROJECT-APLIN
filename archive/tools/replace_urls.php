<?php
$directory = 'C:/Users/melin/OneDrive/Documents/APLIN_PROJECT/PROJECT-APLIN/app/Views';

if (!is_dir($directory)) {
    echo "Directory not found: $directory\n";
    exit(1);
}

$mapping = [
    "url('')" => "index.php",
    "url('auth/login')" => "index.php?page=login&action=login",
    "url('auth/register')" => "index.php?page=register&action=register",
    "url('auth/logout')" => "index.php?page=login&action=logout",
    "url('admin')" => "index.php?page=admin",
    "url('customer')" => "index.php?page=customer",
    "url('customer/appointment')" => "index.php?page=customer&action=appointment",
    "url('customer/book-appointment')" => "index.php?page=customer&action=bookAppointment",
    "url('customer/order-menu')" => "index.php?page=customer&action=orderMenu",
    "url('customer/create-order')" => "index.php?page=customer&action=createOrder",
    "url('barista')" => "index.php?page=barista",
    "url('barista/update-order')" => "index.php?page=barista&action=updateOrderStatus",
    "url('barista/history')" => "index.php?page=barista&action=orderHistory",
    "url('beautician')" => "index.php?page=beautician",
    "url('beautician/today')" => "index.php?page=beautician&action=todaySchedule",
    "url('beautician/upcoming')" => "index.php?page=beautician&action=upcomingSchedule",
    "url('beautician/update-status')" => "index.php?page=beautician&action=updateReservationStatus",
    "url('receptionist')" => "index.php?page=receptionist",
    "url('receptionist/schedule')" => "index.php?page=receptionist&action=scheduleBooking",
    "url('receptionist/check-in')" => "index.php?page=receptionist&action=checkIn",
    "url('receptionist/reservations')" => "index.php?page=receptionist&action=viewReservations",
    "url('receptionist/orders')" => "index.php?page=receptionist&action=viewOrders",
];

$excludedFiles = [
    'Auth/login.php',
    'Auth/register.php',
];

$it = new RecursiveDirectoryIterator($directory);
foreach (new RecursiveIteratorIterator($it) as $file) {
    if ($file->getExtension() === 'php') {
        $path = $file->getRealPath();
        
        $skip = false;
        foreach ($excludedFiles as $ex) {
            if (strpos(str_replace('\\', '/', $path), $ex) !== false) {
                $skip = true;
                break;
            }
        }
        if ($skip) continue;

        $content = file_get_contents($path);
        $countTotal = 0;
        foreach ($mapping as $search => $replace) {
            $c = 0;
            $content = str_replace($search, $replace, $content, $c);
            $countTotal += $c;
        }

        if ($countTotal > 0) {
            file_put_contents($path, $content);
            echo "Modified: " . basename($path) . " ($countTotal replacements)\n";
        }
    }
}
?>
