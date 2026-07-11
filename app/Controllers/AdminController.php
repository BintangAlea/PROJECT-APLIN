<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\UsersModel;
use App\Models\ReservationsModel;
use App\Models\OrdersModel;
use App\Models\TransactionsModel;
use App\Models\MenusModel;
use App\Models\ServicesModel;
use PDO;

class AdminController
{
    private UsersModel $usersModel;
    private ReservationsModel $reservationsModel;
    private OrdersModel $ordersModel;
    private TransactionsModel $transactionsModel;
    private MenusModel $menusModel;
    private ServicesModel $servicesModel;
    private PDO $db;

    public function __construct()
    {
        // Check role
        if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
            header('Location: index.php?page=login');
            exit;
        }
        
        $this->db = Database::getConnection();
        $this->usersModel = new UsersModel();
        $this->reservationsModel = new ReservationsModel();
        $this->ordersModel = new OrdersModel();
        $this->transactionsModel = new TransactionsModel();
        $this->menusModel = new MenusModel();
        $this->servicesModel = new ServicesModel();
    }

    public function index()
    {
        $today = date('Y-m-d');
        $totalRevenueToday = $this->transactionsModel->getTotalRevenue($today, $today);

        $activeReservationsStmt = $this->db->query(
            "SELECT COUNT(*) AS total
             FROM reservations
             WHERE STATUS IN ('Confirmed', 'In-Service')"
        );
        $activeReservations = (int) ($activeReservationsStmt->fetch()['total'] ?? 0);

        $queueStmt = $this->db->query(
            "SELECT coordinate, customer_name, order_detail, status, queue_time, queue_type
             FROM (
                 SELECT
                     MAX(CONCAT('Seat ', r.seat_id)) AS coordinate,
                     MAX(COALESCE(u.NAME, 'Guest')) AS customer_name,
                     COALESCE(GROUP_CONCAT(DISTINCT s.service_name SEPARATOR ' + '), 'Salon Service') AS order_detail,
                     MAX(r.STATUS) AS status,
                     MAX(r.schedule_time) AS queue_time,
                     'salon' AS queue_type
                 FROM reservations r
                 LEFT JOIN users u ON r.user_id = u.user_id
                 LEFT JOIN reservation_details rd ON r.res_id = rd.res_id
                 LEFT JOIN services s ON rd.service_id = s.service_id
                 WHERE r.STATUS IN ('Pending', 'Confirmed', 'In-Service')
                 GROUP BY r.res_id

                 UNION ALL

                 SELECT
                     MAX(CONCAT('Cafe ', COALESCE(o.seat_id, 'Table'))) AS coordinate,
                     MAX(o.guest_name) AS customer_name,
                     GROUP_CONCAT(CONCAT(od.qty, 'x ', m.menu_name) SEPARATOR ' + ') AS order_detail,
                     CASE
                         WHEN o.STATUS = 'Completed' THEN 'Ready'
                         WHEN o.STATUS = 'In Progress' THEN 'In Progress'
                         ELSE 'Waiting'
                     END AS status,
                     MAX(o.order_date) AS queue_time,
                     'cafe' AS queue_type
                 FROM db_merish_cafe.orders o
                 LEFT JOIN db_merish_cafe.order_details od ON o.order_id = od.order_id
                 LEFT JOIN db_merish_cafe.menus m ON od.menu_id = m.menu_id
                 WHERE o.payment_status = 'Paid' OR o.STATUS IN ('New', 'In Progress', 'Ready')
                 GROUP BY o.order_id
             ) AS queue_data
             ORDER BY queue_time DESC
             LIMIT 6"
        );
        $queueItems = $queueStmt->fetchAll();

        $lowStockStmt = $this->db->query(
            "SELECT item_name, stock_qty, min_stock, unit,
                    (min_stock - stock_qty) AS shortage
               FROM db_merish_cafe.inventories
             WHERE stock_qty <= min_stock
             ORDER BY stock_qty ASC, item_name ASC
             LIMIT 4"
        );
        $lowStockItems = $lowStockStmt->fetchAll();

        $lowStockCountStmt = $this->db->query(
            "SELECT COUNT(*) AS total
               FROM db_merish_cafe.inventories
             WHERE stock_qty <= min_stock"
        );
        $lowStockCount = (int) ($lowStockCountStmt->fetch()['total'] ?? 0);

        $reservationsTodayStmt = $this->db->prepare(
            "SELECT COUNT(*) AS total
             FROM reservations
             WHERE DATE(schedule_time) = :today
             AND STATUS IN ('Pending', 'Confirmed', 'In-Service')"
        );
        $reservationsTodayStmt->execute([':today' => $today]);
        $reservationsToday = (int) ($reservationsTodayStmt->fetch()['total'] ?? 0);

        $recentTransactionsStmt = $this->db->query(
            "SELECT t.total_amount, t.payment_date, r.seat_id, COALESCE(u.NAME, 'Walk-in') AS customer_name
             FROM transactions t
             LEFT JOIN reservations r ON t.res_id = r.res_id
             LEFT JOIN users u ON r.user_id = u.user_id
             ORDER BY t.payment_date DESC
             LIMIT 4"
        );
        $recentTransactions = $recentTransactionsStmt->fetchAll();

        require __DIR__ . '/../Views/Admin/index.php';
    }

    public function manageUsers()
    {
        $users = $this->usersModel->findAll();
        require __DIR__ . '/../Views/Admin/manage_users.php';
    }

    public function manageReservations()
    {
        $editReservationId = isset($_GET['edit']) ? (int) $_GET['edit'] : null;

        $reservationsStmt = $this->db->query(
            "SELECT r.res_id,
                    COALESCE(u.NAME, 'Guest') AS customer_name,
                    r.seat_id,
                    r.STATUS AS status,
                    r.schedule_time,
                    TIME(r.schedule_time) AS reservation_time,
                    DATE(r.schedule_time) AS reservation_date,
                    COALESCE(GROUP_CONCAT(DISTINCT s.service_name SEPARATOR ' + '), '-') AS service_name,
                    COALESCE(GROUP_CONCAT(DISTINCT b.NAME SEPARATOR ', '), '-') AS beautician_name
             FROM reservations r
             LEFT JOIN users u ON r.user_id = u.user_id
             LEFT JOIN reservation_details rd ON r.res_id = rd.res_id
             LEFT JOIN services s ON rd.service_id = s.service_id
             LEFT JOIN users b ON rd.beautician_id = b.user_id
             GROUP BY r.res_id, u.NAME, r.seat_id, r.STATUS, r.schedule_time
             ORDER BY r.schedule_time DESC"
        );
        $reservations = $reservationsStmt->fetchAll();

        $reservationForEdit = null;
        if ($editReservationId) {
            $reservationForEdit = $this->db->prepare(
                "SELECT r.res_id,
                        r.user_id,
                        u.NAME AS guest_name,
                        r.seat_id,
                        r.STATUS,
                        DATE(r.schedule_time) AS reservation_date,
                        TIME(r.schedule_time) AS reservation_time,
                        rd.service_id,
                        rd.beautician_id
                 FROM reservations r
                 LEFT JOIN users u ON r.user_id = u.user_id
                 LEFT JOIN reservation_details rd ON r.res_id = rd.res_id
                 WHERE r.res_id = :id
                 LIMIT 1"
            );
            $reservationForEdit->execute([':id' => $editReservationId]);
            $reservationForEdit = $reservationForEdit->fetch() ?: null;
        }

        $services = $this->servicesModel->findAll();
        $beauticians = $this->usersModel->findByRole('Beautician');
        $seatsStmt = $this->db->query("SELECT seat_id, seat_name, zone_type FROM seats WHERE zone_type = 'Kursi Salon' ORDER BY seat_id ASC");
        $seats = $seatsStmt->fetchAll();

        require __DIR__ . '/../Views/Admin/manage_reservations.php';
    }

    public function saveReservation()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin&action=manageReservations');
            exit;
        }

        $reservationId = (int) ($_POST['res_id'] ?? 0);
        $guestName = trim($_POST['guest_name'] ?? '');
        $seatId = trim($_POST['seat_id'] ?? '');
        $reservationDate = trim($_POST['reservation_date'] ?? '');
        $reservationTime = trim($_POST['reservation_time'] ?? '');
        $status = trim($_POST['status'] ?? 'Pending');
        $serviceId = trim($_POST['service_id'] ?? '');
        $beauticianId = trim($_POST['beautician_id'] ?? '');

        if ($guestName === '' || $seatId === '' || $reservationDate === '' || $reservationTime === '') {
            $_SESSION['error'] = 'Nama pelanggan, seat, tanggal, dan jam harus diisi.';
            header('Location: index.php?page=admin&action=manageReservations' . ($reservationId > 0 ? '&edit=' . $reservationId : ''));
            exit;
        }

        $seatStmt = $this->db->prepare(
            'SELECT seat_id FROM seats WHERE seat_id = :seat_id AND zone_type = :zone_type LIMIT 1'
        );
        $seatStmt->execute([
            ':seat_id' => $seatId,
            ':zone_type' => 'Kursi Salon',
        ]);
        if (!$seatStmt->fetch()) {
            $_SESSION['error'] = 'Seat yang dipilih harus kursi salon.';
            header('Location: index.php?page=admin&action=manageReservations' . ($reservationId > 0 ? '&edit=' . $reservationId : ''));
            exit;
        }

        $scheduleTime = $reservationDate . ' ' . $reservationTime . ':00';

        try {
            $this->db->beginTransaction();

            if ($reservationId > 0) {
                $stmt = $this->db->prepare(
                    'UPDATE reservations
                     SET seat_id = :seat_id,
                         STATUS = :status,
                         schedule_time = :schedule_time,
                         user_id = NULL
                     WHERE res_id = :res_id'
                );
                $stmt->execute([
                    ':seat_id' => $seatId,
                    ':status' => $status,
                    ':schedule_time' => $scheduleTime,
                    ':res_id' => $reservationId,
                ]);

                $deleteDetail = $this->db->prepare('DELETE FROM reservation_details WHERE res_id = :res_id');
                $deleteDetail->execute([':res_id' => $reservationId]);

                $targetResId = $reservationId;
            } else {
                $stmt = $this->db->prepare(
                    'INSERT INTO reservations (user_id, seat_id, STATUS, schedule_time, is_dp_paid, dp_amount)
                     VALUES (NULL, :seat_id, :status, :schedule_time, 0, 0)'
                );
                $stmt->execute([
                    ':seat_id' => $seatId,
                    ':status' => $status,
                    ':schedule_time' => $scheduleTime,
                ]);
                $targetResId = (int) $this->db->lastInsertId();
            }

            if ($serviceId !== '') {
                $detailStmt = $this->db->prepare(
                    'INSERT INTO reservation_details (res_id, service_id, beautician_id)
                     VALUES (:res_id, :service_id, :beautician_id)'
                );
                $detailStmt->execute([
                    ':res_id' => $targetResId,
                    ':service_id' => $serviceId,
                    ':beautician_id' => $beauticianId !== '' ? $beauticianId : null,
                ]);
            }

            $this->db->commit();
            $_SESSION['success'] = $reservationId > 0 ? 'Booking berhasil diperbarui.' : 'Booking berhasil ditambahkan.';
        } catch (\Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            $_SESSION['error'] = 'Gagal menyimpan booking: ' . $exception->getMessage();
        }

        header('Location: index.php?page=admin&action=manageReservations');
        exit;
    }

    public function cancelReservation()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin&action=manageReservations');
            exit;
        }

        $reservationId = (int) ($_POST['res_id'] ?? 0);
        if ($reservationId <= 0) {
            $_SESSION['error'] = 'Reservasi tidak valid.';
            header('Location: index.php?page=admin&action=manageReservations');
            exit;
        }

        $stmt = $this->db->prepare("UPDATE reservations SET STATUS = 'Canceled' WHERE res_id = :res_id");
        $stmt->execute([':res_id' => $reservationId]);

        $_SESSION['success'] = 'Reservasi dibatalkan dan jadwal dibebaskan.';
        header('Location: index.php?page=admin&action=manageReservations');
        exit;
    }

    public function manageServices()
    {
        require __DIR__ . '/../Views/Admin/manage_services.php';
    }

    public function manageMenus()
    {
        // Cafe inventory with BOM usage
        $cafeInventoriesStmt = $this->db->query(
            "SELECT i.item_id,
                    i.item_name,
                    i.stock_qty,
                    i.min_stock,
                    i.unit,
                    CASE
                        WHEN i.stock_qty <= i.min_stock THEN 'Low Stock'
                        ELSE 'Healthy'
                    END AS stock_status,
                    COALESCE(
                        GROUP_CONCAT(DISTINCT m.menu_name ORDER BY m.menu_name SEPARATOR ', '),
                        '-'
                    ) AS used_in_menus
             FROM db_merish_cafe.inventories i
             LEFT JOIN db_merish_cafe.bom_details bd ON bd.item_id = i.item_id
             LEFT JOIN db_merish_cafe.menus m ON m.menu_id = bd.menu_id
             GROUP BY i.item_id, i.item_name, i.stock_qty, i.min_stock, i.unit
             ORDER BY i.stock_qty ASC, i.item_name ASC"
        );
        $cafeInventories = $cafeInventoriesStmt->fetchAll();

        // Salon inventory — kosong untuk sementara
        $salonInventories = [];

        // Keep $inventories pointing to cafe for backward compat (low stock alerts etc)
        $inventories = $cafeInventories;

        $servicesStmt = $this->db->query(
            "SELECT service_id,
                    service_name,
                    category,
                    base_tariff,
                    est_duration
             FROM db_merish_salon.services
             ORDER BY category ASC, service_name ASC"
        );
        $services = $servicesStmt->fetchAll();

        $menusStmt = $this->db->query(
            "SELECT m.menu_id,
                    m.menu_name,
                    m.price,
                    m.is_available,
                    COUNT(bd.bom_recipe_id) AS ingredient_count,
                    COALESCE(GROUP_CONCAT(CONCAT(i.item_name, ' x ', bd.quantity_required, ' ', i.unit) SEPARATOR ', '), '-') AS bom_items,
                    CASE
                        WHEN COUNT(bd.bom_recipe_id) = 0 THEN 'Incomplete'
                        ELSE 'Complete'
                    END AS bom_status
             FROM db_merish_cafe.menus m
             LEFT JOIN db_merish_cafe.bom_details bd ON bd.menu_id = m.menu_id
             LEFT JOIN db_merish_cafe.inventories i ON bd.item_id = i.item_id
             GROUP BY m.menu_id, m.menu_name, m.price, m.is_available
             ORDER BY m.menu_name ASC"
        );
        $menus = $menusStmt->fetchAll();

        $summaryStmt = $this->db->query(
            "SELECT
                (SELECT COUNT(*) FROM db_merish_cafe.inventories) AS total_inventory_items,
                (SELECT COUNT(*) FROM db_merish_cafe.inventories WHERE stock_qty <= min_stock) AS low_stock_items,
                (SELECT COUNT(*) FROM db_merish_salon.services) AS total_services,
                (SELECT COUNT(*) FROM db_merish_cafe.menus) AS total_menus"
        );
        $summary = $summaryStmt->fetch() ?: [];

        $lowStockItems = array_slice(array_filter($inventories, static function (array $item): bool {
            return (float) $item['stock_qty'] <= (float) $item['min_stock'];
        }), 0, 4);

        $summaryStats = [
            'total_inventory_items' => (int) ($summary['total_inventory_items'] ?? 0),
            'low_stock_items' => (int) ($summary['low_stock_items'] ?? 0),
            'total_services' => (int) ($summary['total_services'] ?? 0),
            'total_menus' => (int) ($summary['total_menus'] ?? 0),
        ];

        require __DIR__ . '/../Views/Admin/manage_menus.php';
    }

    public function saveInventory()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin&action=manageMenus');
            exit;
        }

        $itemId = (int) ($_POST['item_id'] ?? 0);
        $itemName = trim($_POST['item_name'] ?? '');
        $unit = trim($_POST['unit'] ?? '');
        $stockAddRaw = trim((string) ($_POST['stock_add'] ?? ''));
        $minStockRaw = trim((string) ($_POST['min_stock'] ?? ''));
        $extraChargeRaw = trim((string) ($_POST['extra_charge_per_unit'] ?? ''));

        if ($stockAddRaw === '' || (float) $stockAddRaw <= 0) {
            $_SESSION['error'] = 'Jumlah stok yang ditambahkan harus lebih dari 0.';
            header('Location: index.php?page=admin&action=manageMenus');
            exit;
        }

        try {
            if ($itemId > 0) {
                $existingStmt = $this->db->prepare('SELECT * FROM db_merish_cafe.inventories WHERE item_id = :item_id LIMIT 1');
                $existingStmt->execute([':item_id' => $itemId]);
                $existing = $existingStmt->fetch();

                if (!$existing) {
                    $_SESSION['error'] = 'Item inventory tidak ditemukan.';
                    header('Location: index.php?page=admin&action=manageMenus');
                    exit;
                }

                $updateStmt = $this->db->prepare(
                    'UPDATE db_merish_cafe.inventories
                     SET item_name = :item_name,
                         stock_qty = stock_qty + :stock_add,
                         min_stock = :min_stock,
                         unit = :unit
                     WHERE item_id = :item_id'
                );
                $updateStmt->execute([
                    ':item_name' => $itemName !== '' ? $itemName : $existing['item_name'],
                    ':stock_add' => (float) $stockAddRaw,
                    ':min_stock' => $minStockRaw !== '' ? (float) $minStockRaw : (float) $existing['min_stock'],
                    ':unit' => $unit !== '' ? $unit : $existing['unit'],
                    ':item_id' => $itemId,
                ]);

                $_SESSION['success'] = 'Stok inventory berhasil diperbarui.';
            } else {
                if ($itemName === '' || $unit === '') {
                    $_SESSION['error'] = 'Nama item dan satuan wajib diisi untuk item baru.';
                    header('Location: index.php?page=admin&action=manageMenus');
                    exit;
                }

                $insertStmt = $this->db->prepare(
                    'INSERT INTO db_merish_cafe.inventories (item_name, stock_qty, min_stock, unit)
                     VALUES (:item_name, :stock_qty, :min_stock, :unit)'
                );
                $insertStmt->execute([
                    ':item_name' => $itemName,
                    ':stock_qty' => (float) $stockAddRaw,
                    ':min_stock' => $minStockRaw !== '' ? (float) $minStockRaw : 0,
                    ':unit' => $unit,
                ]);

                $_SESSION['success'] = 'Item inventory baru berhasil ditambahkan.';
            }
        } catch (\Throwable $exception) {
            $_SESSION['error'] = 'Gagal menyimpan inventory: ' . $exception->getMessage();
        }

        header('Location: index.php?page=admin&action=manageMenus');
        exit;
    }

    public function saveCatalogItem()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin&action=manageMenus');
            exit;
        }

        $catalogType = trim($_POST['catalog_type'] ?? 'service');

        try {
            if ($catalogType === 'menu') {
                $menuName = trim($_POST['menu_name'] ?? '');
                $priceRaw = trim((string) ($_POST['menu_price'] ?? ''));
                $bomItemId = trim($_POST['bom_item_id'] ?? '');
                $bomQtyRaw = trim((string) ($_POST['bom_qty_required'] ?? ''));
                $availability = isset($_POST['menu_available']) ? 1 : 0;

                if ($menuName === '' || $priceRaw === '') {
                    $_SESSION['error'] = 'Nama menu dan harga wajib diisi.';
                    header('Location: index.php?page=admin&action=manageMenus');
                    exit;
                }

                $menuId = $this->generateNextCatalogId('menus', 'menu_id', 'MENU');
                $bomRecipeId = null;

                if ($bomItemId !== '' && $bomQtyRaw !== '' && (float) $bomQtyRaw > 0) {
                    $bomStmt = $this->db->prepare(
                        'INSERT INTO bom_details (item_id, quantity_required)
                         VALUES (:item_id, :quantity_required)'
                    );
                    $bomStmt->execute([
                        ':item_id' => $bomItemId,
                        ':quantity_required' => (float) $bomQtyRaw,
                    ]);
                    $bomRecipeId = (int) $this->db->lastInsertId();
                }

                $menuStmt = $this->db->prepare(
                    'INSERT INTO menus (menu_id, menu_name, price, is_available, bom_recipe_id)
                     VALUES (:menu_id, :menu_name, :price, :is_available, :bom_recipe_id)'
                );
                $menuStmt->execute([
                    ':menu_id' => $menuId,
                    ':menu_name' => $menuName,
                    ':price' => (float) $priceRaw,
                    ':is_available' => $availability,
                    ':bom_recipe_id' => $bomRecipeId,
                ]);

                $_SESSION['success'] = 'Menu cafe baru berhasil ditambahkan.';
            } else {
                $serviceName = trim($_POST['service_name'] ?? '');
                $serviceCategory = trim($_POST['service_category'] ?? 'Hair');
                $servicePriceRaw = trim((string) ($_POST['service_price'] ?? ''));
                $serviceDurationRaw = trim((string) ($_POST['service_duration'] ?? ''));

                if ($serviceName === '' || $servicePriceRaw === '' || $serviceDurationRaw === '') {
                    $_SESSION['error'] = 'Nama service, kategori, harga, dan durasi wajib diisi.';
                    header('Location: index.php?page=admin&action=manageMenus');
                    exit;
                }

                $serviceId = $this->generateNextCatalogId('services', 'service_id', 'SRV');
                $serviceStmt = $this->db->prepare(
                    'INSERT INTO services (service_id, service_name, category, base_tariff, est_duration)
                     VALUES (:service_id, :service_name, :category, :base_tariff, :est_duration)'
                );
                $serviceStmt->execute([
                    ':service_id' => $serviceId,
                    ':service_name' => $serviceName,
                    ':category' => $serviceCategory,
                    ':base_tariff' => (float) $servicePriceRaw,
                    ':est_duration' => (int) $serviceDurationRaw,
                ]);

                $_SESSION['success'] = 'Service baru berhasil ditambahkan.';
            }
        } catch (\Throwable $exception) {
            $_SESSION['error'] = 'Gagal menyimpan data master: ' . $exception->getMessage();
        }

        header('Location: index.php?page=admin&action=manageMenus');
        exit;
    }

    private function generateNextCatalogId(string $table, string $column, string $prefix): string
    {
        $stmt = $this->db->prepare("SELECT {$column} FROM {$table} WHERE {$column} LIKE :prefix ORDER BY {$column} ASC");
        $stmt->execute([':prefix' => $prefix . '%']);
        $existingIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $highestNumber = 0;
        foreach ($existingIds as $existingId) {
            if (preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', (string) $existingId, $matches)) {
                $highestNumber = max($highestNumber, (int) $matches[1]);
            }
        }

        return $prefix . str_pad((string) ($highestNumber + 1), 3, '0', STR_PAD_LEFT);
    }

    public function manageCafeOrders()
    {
        $editOrderId = isset($_GET['edit']) ? (int) $_GET['edit'] : null;

        $ordersStmt = $this->db->query(
            "SELECT o.order_id,
                    o.guest_name,
                    o.seat_id,
                    o.total_amount,
                    o.payment_method,
                    o.payment_status,
                    o.STATUS AS status,
                    o.order_date,
                    GROUP_CONCAT(CONCAT(od.qty, 'x ', m.menu_name) SEPARATOR ', ') AS order_items,
                    CASE
                        WHEN o.STATUS = 'In Progress' THEN 1
                        WHEN o.STATUS = 'New' THEN 2
                        WHEN o.STATUS = 'Ready' THEN 3
                        WHEN o.STATUS = 'Completed' THEN 4
                        ELSE 5
                    END AS sort_order
             FROM db_merish_cafe.orders o
             LEFT JOIN db_merish_cafe.order_details od ON o.order_id = od.order_id
             LEFT JOIN db_merish_cafe.menus m ON od.menu_id = m.menu_id
             GROUP BY o.order_id
             ORDER BY sort_order ASC, o.order_id DESC"
        );
        $orders = $ordersStmt->fetchAll();

        $orderForEdit = null;
        if ($editOrderId) {
            $orderStmt = $this->db->prepare(
                "SELECT o.*, od.menu_id, od.qty, od.subtotal, m.menu_name, m.price
                 FROM db_merish_cafe.orders o
                 LEFT JOIN db_merish_cafe.order_details od ON o.order_id = od.order_id
                 LEFT JOIN db_merish_cafe.menus m ON od.menu_id = m.menu_id
                 WHERE o.order_id = :id"
            );
            $orderStmt->execute([':id' => $editOrderId]);
            $orderForEdit = $orderStmt->fetch() ?: null;
        }

        $menus = $this->menusModel->findAll();
        $reservations = $this->db->query(
            "SELECT r.res_id,
                    COALESCE(u.NAME, 'Guest') AS customer_name,
                    CONCAT('Seat ', r.seat_id, ' • ', DATE_FORMAT(r.schedule_time, '%d %b %Y %H:%i')) AS label
             FROM reservations r
             LEFT JOIN users u ON r.user_id = u.user_id
             WHERE r.STATUS IN ('Pending', 'Confirmed', 'In-Service', 'Selesai')
             ORDER BY r.schedule_time DESC"
        )->fetchAll();

        require __DIR__ . '/../Views/Admin/manage_cafe_orders.php';
    }

    public function saveCafeOrder()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin&action=manageCafeOrders');
            exit;
        }

        $orderId = (int) ($_POST['order_id'] ?? 0);
        $menuId = trim($_POST['menu_id'] ?? '');
        $qty = max(1, (int) ($_POST['qty'] ?? 1));
        $status = trim($_POST['status'] ?? 'New');
        $seatId = trim($_POST['seat_id'] ?? '');
        $paymentStatus = trim($_POST['payment_status'] ?? 'Unpaid');
        $guestName = trim($_POST['guest_name'] ?? 'Guest');

        if ($menuId === '') {
            $_SESSION['error'] = 'Menu harus dipilih.';
            header('Location: index.php?page=admin&action=manageCafeOrders' . ($orderId > 0 ? '&edit=' . $orderId : ''));
            exit;
        }

        try {
            // Get menu price for subtotal calculation
            $priceStmt = $this->db->prepare('SELECT price FROM db_merish_cafe.menus WHERE menu_id = :id');
            $priceStmt->execute([':id' => $menuId]);
            $menuPrice = (float) ($priceStmt->fetch()['price'] ?? 0);
            $subtotal = $menuPrice * $qty;

            if ($orderId > 0) {
                // Update orders header
                $stmt = $this->db->prepare(
                    'UPDATE db_merish_cafe.orders
                     SET guest_name = :guest_name,
                         seat_id = :seat_id,
                         total_amount = :total_amount,
                         payment_status = :payment_status,
                         STATUS = :status
                     WHERE order_id = :order_id'
                );
                $stmt->execute([
                    ':guest_name' => $guestName,
                    ':seat_id' => $seatId !== '' ? $seatId : null,
                    ':total_amount' => $subtotal,
                    ':payment_status' => $paymentStatus,
                    ':status' => $status,
                    ':order_id' => $orderId,
                ]);

                // Delete old order details and re-insert
                $this->db->prepare('DELETE FROM db_merish_cafe.order_details WHERE order_id = :id')
                    ->execute([':id' => $orderId]);

                $detStmt = $this->db->prepare(
                    'INSERT INTO db_merish_cafe.order_details (order_id, menu_id, qty, subtotal)
                     VALUES (:order_id, :menu_id, :qty, :subtotal)'
                );
                $detStmt->execute([
                    ':order_id' => $orderId,
                    ':menu_id' => $menuId,
                    ':qty' => $qty,
                    ':subtotal' => $subtotal,
                ]);

                $_SESSION['success'] = 'Order berhasil diperbarui.';
            } else {
                // Insert new order header
                $stmt = $this->db->prepare(
                    'INSERT INTO db_merish_cafe.orders (guest_name, seat_id, total_amount, payment_method, payment_status, STATUS, order_date)
                     VALUES (:guest_name, :seat_id, :total_amount, :payment_method, :payment_status, :status, NOW())'
                );
                $stmt->execute([
                    ':guest_name' => $guestName,
                    ':seat_id' => $seatId !== '' ? $seatId : null,
                    ':total_amount' => $subtotal,
                    ':payment_method' => 'Cash',
                    ':payment_status' => $paymentStatus,
                    ':status' => $status,
                ]);

                $newOrderId = (int) $this->db->lastInsertId();

                // Insert order detail
                $detStmt = $this->db->prepare(
                    'INSERT INTO db_merish_cafe.order_details (order_id, menu_id, qty, subtotal)
                     VALUES (:order_id, :menu_id, :qty, :subtotal)'
                );
                $detStmt->execute([
                    ':order_id' => $newOrderId,
                    ':menu_id' => $menuId,
                    ':qty' => $qty,
                    ':subtotal' => $subtotal,
                ]);

                $_SESSION['success'] = 'Order baru berhasil ditambahkan.';
            }
        } catch (\Throwable $exception) {
            $_SESSION['error'] = 'Gagal menyimpan order: ' . $exception->getMessage();
        }

        header('Location: index.php?page=admin&action=manageCafeOrders');
        exit;
    }

    public function forceCompleteOrder()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin&action=manageCafeOrders');
            exit;
        }

        $orderId = (int) ($_POST['order_id'] ?? 0);
        if ($orderId <= 0) {
            $_SESSION['error'] = 'Order tidak valid.';
            header('Location: index.php?page=admin&action=manageCafeOrders');
            exit;
        }

        $stmt = $this->db->prepare("UPDATE db_merish_cafe.orders SET STATUS = 'Completed', payment_status = 'Paid' WHERE order_id = :order_id");
        $stmt->execute([':order_id' => $orderId]);

        $_SESSION['success'] = 'Order dipaksa selesai.';
        header('Location: index.php?page=admin&action=manageCafeOrders');
        exit;
    }

    public function cancelCafeOrder()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin&action=manageCafeOrders');
            exit;
        }

        $orderId = (int) ($_POST['order_id'] ?? 0);
        if ($orderId <= 0) {
            $_SESSION['error'] = 'Order tidak valid.';
            header('Location: index.php?page=admin&action=manageCafeOrders');
            exit;
        }

        $stmt = $this->db->prepare('DELETE FROM db_merish_cafe.orders WHERE order_id = :order_id');
        $stmt->execute([':order_id' => $orderId]);

        $_SESSION['success'] = 'Order dibatalkan dan dihapus dari daftar.';
        header('Location: index.php?page=admin&action=manageCafeOrders');
        exit;
    }

    public function manageStaff()
    {
        $activeTab = $_GET['tab'] ?? 'staff';
        if (!in_array($activeTab, ['staff', 'review'], true)) {
            $activeTab = 'staff';
        }

        $staffStmt = $this->db->query(
            "SELECT u.user_id,
                    u.NAME,
                    u.email,
                    u.ROLE,
                    COALESCE(AVG(rv.rating), 0) AS avg_rating,
                    COUNT(DISTINCT rv.review_id) AS total_reviews
             FROM users u
             LEFT JOIN reservation_details rd_staff ON rd_staff.beautician_id = u.user_id
             LEFT JOIN reviews rv ON rv.res_id = rd_staff.res_id
             WHERE u.ROLE IN ('Receptionist', 'Barista', 'Beautician')
             GROUP BY u.user_id, u.NAME, u.email, u.ROLE
             ORDER BY FIELD(u.ROLE, 'Beautician', 'Barista', 'Receptionist'), u.NAME ASC"
        );
        $staff = $staffStmt->fetchAll();

        $reviewsStmt = $this->db->query(
            "SELECT rv.review_id,
                    rv.rating,
                    rv.COMMENT AS review_comment,
                    rv.res_id,
                    COALESCE(c.NAME, 'Guest') AS customer_name,
                    COALESCE(MAX(b.NAME), 'Unassigned') AS staff_name,
                    COALESCE(MAX(s.service_name), 'General Experience') AS subject_name
             FROM reviews rv
             LEFT JOIN reservations res ON rv.res_id = res.res_id
             LEFT JOIN users c ON res.user_id = c.user_id
             LEFT JOIN reservation_details rd ON rv.res_id = rd.res_id
             LEFT JOIN services s ON rd.service_id = s.service_id
             LEFT JOIN users b ON b.user_id = rd.beautician_id
             GROUP BY rv.review_id, rv.rating, rv.COMMENT, rv.res_id, c.NAME
             ORDER BY rv.review_id DESC
             LIMIT 20"
        );
        $reviews = $reviewsStmt->fetchAll();

        $staffCountByRoleStmt = $this->db->query(
            "SELECT ROLE, COUNT(*) AS total
             FROM users
             WHERE ROLE IN ('Receptionist', 'Barista', 'Beautician')
             GROUP BY ROLE"
        );
        $roleRows = $staffCountByRoleStmt->fetchAll();
        $roleSummary = [
            'Receptionist' => 0,
            'Barista' => 0,
            'Beautician' => 0,
        ];
        foreach ($roleRows as $row) {
            $role = (string) ($row['ROLE'] ?? '');
            if (array_key_exists($role, $roleSummary)) {
                $roleSummary[$role] = (int) ($row['total'] ?? 0);
            }
        }

        $totalStaff = (int) array_sum($roleSummary);
        $topPerformer = null;
        foreach ($staff as $person) {
            $reviewCount = (int) ($person['total_reviews'] ?? 0);
            if ($reviewCount === 0) {
                continue;
            }
            if ($topPerformer === null || (float) $person['avg_rating'] > (float) $topPerformer['avg_rating']) {
                $topPerformer = $person;
            }
        }

        require __DIR__ . '/../Views/Admin/manage_staff.php';
    }

    public function registerStaff()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin&action=manageStaff');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $role = trim($_POST['role'] ?? '');

        $allowedRoles = ['Receptionist', 'Barista', 'Beautician'];
        if ($name === '' || $email === '' || $password === '' || !in_array($role, $allowedRoles, true)) {
            $_SESSION['error'] = 'Nama, email, password, dan role staff harus valid.';
            header('Location: index.php?page=admin&action=manageStaff&tab=staff');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Format email tidak valid.';
            header('Location: index.php?page=admin&action=manageStaff&tab=staff');
            exit;
        }

        if (strlen($password) < 5) {
            $_SESSION['error'] = 'Password minimal 5 karakter.';
            header('Location: index.php?page=admin&action=manageStaff&tab=staff');
            exit;
        }

        if ($this->usersModel->findByEmail($email)) {
            $_SESSION['error'] = 'Email sudah digunakan oleh user lain.';
            header('Location: index.php?page=admin&action=manageStaff&tab=staff');
            exit;
        }

        $registered = $this->usersModel->register($email, $password, $name, '', $role);
        if (!$registered) {
            $_SESSION['error'] = 'Gagal membuat akun staff baru.';
            header('Location: index.php?page=admin&action=manageStaff&tab=staff');
            exit;
        }

        $_SESSION['success'] = 'Staff baru berhasil didaftarkan.';
        header('Location: index.php?page=admin&action=manageStaff&tab=staff');
        exit;
    }

    public function reports()
    {
        $today = new \DateTimeImmutable('today');
        $defaultStart = $today->modify('first day of this month')->format('Y-m-d');
        $defaultEnd = $today->modify('last day of this month')->format('Y-m-d');

        $startDate = (string) ($_GET['start_date'] ?? $defaultStart);
        $endDate = (string) ($_GET['end_date'] ?? $defaultEnd);
        if (!$this->isValidDate($startDate)) {
            $startDate = $defaultStart;
        }
        if (!$this->isValidDate($endDate)) {
            $endDate = $defaultEnd;
        }
        if ($startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $reportType = (string) ($_GET['report_type'] ?? 'revenue');
        if (!in_array($reportType, ['revenue', 'stock', 'top-services'], true)) {
            $reportType = 'revenue';
        }

        $selectedExport = strtolower((string) ($_GET['export'] ?? ''));

        $reportData = $this->getReportData($reportType, $startDate, $endDate);
        $summaryCards = $this->getReportSummaryCards($startDate, $endDate);
        $trendData = $this->getRevenueTrendData($startDate, $endDate);

        if ($selectedExport === 'excel') {
            $this->exportReportExcel($reportData, $startDate, $endDate);
            return;
        }
        if ($selectedExport === 'pdf') {
            $this->exportReportPdf($reportData, $startDate, $endDate);
            return;
        }

        require __DIR__ . '/../Views/Admin/reports.php';
    }

    private function getReportData(string $reportType, string $startDate, string $endDate): array
    {
        return match ($reportType) {
            'stock' => $this->buildStockReportData(),
            'top-services' => $this->buildTopServicesReportData($startDate, $endDate),
            default => $this->buildRevenueReportData($startDate, $endDate),
        };
    }

    private function buildRevenueReportData(string $startDate, string $endDate): array
    {
        $stmt = $this->db->prepare(
            "SELECT t.trans_id,
                    DATE(t.payment_date) AS payment_day,
                    t.payment_method,
                    t.total_amount,
                    COALESCE(r.seat_id, '-') AS seat_id,
                    COALESCE(u.NAME, 'Guest') AS customer_name
             FROM transactions t
             LEFT JOIN reservations r ON t.res_id = r.res_id
             LEFT JOIN users u ON r.user_id = u.user_id
             WHERE DATE(t.payment_date) BETWEEN :start_date AND :end_date
             ORDER BY t.payment_date DESC"
        );
        $stmt->execute([
            ':start_date' => $startDate,
            ':end_date' => $endDate,
        ]);
        $rows = $stmt->fetchAll();

        return [
            'title' => 'Laporan Pendapatan',
            'description' => 'Ringkasan transaksi pembayaran dalam periode terpilih.',
            'headers' => ['Tanggal', 'Pelanggan', 'Seat', 'Metode', 'Total'],
            'rows' => array_map(static function (array $row): array {
                return [
                    $row['payment_day'],
                    $row['customer_name'],
                    $row['seat_id'],
                    $row['payment_method'],
                    'Rp ' . number_format((float) $row['total_amount'], 0, ',', '.'),
                ];
            }, $rows),
        ];
    }

    private function buildStockReportData(): array
    {
        $stmt = $this->db->query(
            "SELECT item_name,
                    stock_qty,
                    min_stock,
                    unit,
                    0 AS extra_charge_per_unit,
                    CASE
                        WHEN stock_qty <= min_stock THEN 'Low Stock'
                        ELSE 'Healthy'
                    END AS stock_status
               FROM db_merish_cafe.inventories
             ORDER BY stock_qty ASC, item_name ASC"
        );
        $rows = $stmt->fetchAll();

        return [
            'title' => 'Laporan Stok',
            'description' => 'Status stok bahan baku dan minimum stok saat ini.',
            'headers' => ['Item', 'Stok', 'Minimum', 'Satuan', 'Status', 'Extra Charge/Unit'],
            'rows' => array_map(static function (array $row): array {
                return [
                    $row['item_name'],
                    rtrim(rtrim(number_format((float) $row['stock_qty'], 2, '.', ''), '0'), '.'),
                    rtrim(rtrim(number_format((float) $row['min_stock'], 2, '.', ''), '0'), '.'),
                    $row['unit'],
                    $row['stock_status'],
                    'Rp 0',
                ];
            }, $rows),
        ];
    }

    private function buildTopServicesReportData(string $startDate, string $endDate): array
    {
        $stmt = $this->db->prepare(
            "SELECT s.service_name,
                    s.category,
                    s.base_tariff,
                    COUNT(rd.detail_id) AS total_bookings,
                    COUNT(rd.detail_id) * s.base_tariff AS estimated_revenue
             FROM reservation_details rd
             JOIN services s ON rd.service_id = s.service_id
             JOIN reservations r ON rd.res_id = r.res_id
             WHERE DATE(r.schedule_time) BETWEEN :start_date AND :end_date
             GROUP BY s.service_id, s.service_name, s.category, s.base_tariff
             ORDER BY total_bookings DESC, estimated_revenue DESC"
        );
        $stmt->execute([
            ':start_date' => $startDate,
            ':end_date' => $endDate,
        ]);
        $rows = $stmt->fetchAll();

        return [
            'title' => 'Layanan Paling Laku',
            'description' => 'Performa layanan salon berdasarkan jumlah booking.',
            'headers' => ['Service', 'Kategori', 'Total Booking', 'Tarif', 'Estimasi Revenue'],
            'rows' => array_map(static function (array $row): array {
                return [
                    $row['service_name'],
                    $row['category'],
                    number_format((int) $row['total_bookings']),
                    'Rp ' . number_format((float) $row['base_tariff'], 0, ',', '.'),
                    'Rp ' . number_format((float) $row['estimated_revenue'], 0, ',', '.'),
                ];
            }, $rows),
        ];
    }

    private function getReportSummaryCards(string $startDate, string $endDate): array
    {
        $revenueStmt = $this->db->prepare(
            "SELECT COALESCE(SUM(total_amount), 0) AS total_revenue
             FROM transactions
             WHERE DATE(payment_date) BETWEEN :start_date AND :end_date"
        );
        $revenueStmt->execute([
            ':start_date' => $startDate,
            ':end_date' => $endDate,
        ]);
        $totalRevenue = (float) ($revenueStmt->fetch()['total_revenue'] ?? 0);

        $appointmentStmt = $this->db->prepare(
            "SELECT COUNT(*) AS total
             FROM reservations
             WHERE DATE(schedule_time) BETWEEN :start_date AND :end_date"
        );
        $appointmentStmt->execute([
            ':start_date' => $startDate,
            ':end_date' => $endDate,
        ]);
        $totalAppointments = (int) ($appointmentStmt->fetch()['total'] ?? 0);

        $orderStmt = $this->db->prepare(
            "SELECT COUNT(*) AS total
             FROM db_merish_cafe.orders
             WHERE DATE(order_date) BETWEEN :start_date AND :end_date"
        );
        $orderStmt->execute([
            ':start_date' => $startDate,
            ':end_date' => $endDate,
        ]);
        $totalCafeOrders = (int) ($orderStmt->fetch()['total'] ?? 0);

        return [
            'total_revenue' => $totalRevenue,
            'total_appointments' => $totalAppointments,
            'total_cafe_orders' => $totalCafeOrders,
        ];
    }

    private function getRevenueTrendData(string $startDate, string $endDate): array
    {
        $trend = [
            'labels' => [],
            'salon' => [],
            'cafe' => [],
        ];

        $start = new \DateTimeImmutable($startDate);
        $end = new \DateTimeImmutable($endDate);
        $diffDays = (int) $start->diff($end)->format('%a');
        $segments = max(1, min(5, $diffDays + 1));

        for ($i = 0; $i < $segments; $i++) {
            $segmentStart = $start->modify('+' . (int) floor(($diffDays + 1) * $i / $segments) . ' day');
            $segmentEnd = $start->modify('+' . (int) floor(($diffDays + 1) * ($i + 1) / $segments) . ' day')->modify('-1 day');
            if ($segmentEnd < $segmentStart) {
                $segmentEnd = $segmentStart;
            }

            $trend['labels'][] = $segmentStart->format('M j');

            $salonStmt = $this->db->prepare(
                "SELECT COALESCE(SUM(total_amount), 0) AS total
                 FROM transactions
                 WHERE DATE(payment_date) BETWEEN :start_date AND :end_date"
            );
            $salonStmt->execute([
                ':start_date' => $segmentStart->format('Y-m-d'),
                ':end_date' => $segmentEnd->format('Y-m-d'),
            ]);
            $trend['salon'][] = (float) ($salonStmt->fetch()['total'] ?? 0);

            $cafeStmt = $this->db->prepare(
                "SELECT COALESCE(SUM(total_amount), 0) AS total
                 FROM db_merish_cafe.orders
                 WHERE payment_status = 'Paid'
                   AND DATE(order_date) BETWEEN :start_date AND :end_date"
            );
            $cafeStmt->execute([
                ':start_date' => $segmentStart->format('Y-m-d'),
                ':end_date' => $segmentEnd->format('Y-m-d'),
            ]);
            $trend['cafe'][] = (float) ($cafeStmt->fetch()['total'] ?? 0);
        }

        return $trend;
    }

    private function exportReportExcel(array $reportData, string $startDate, string $endDate): void
    {
        $filename = 'report-' . date('Ymd-His') . '.xls';

        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        echo '<html><head><meta charset="UTF-8"></head><body>';
        echo '<h2>' . htmlspecialchars($reportData['title'] ?? 'Report', ENT_QUOTES, 'UTF-8') . '</h2>';
        echo '<p>Periode: ' . htmlspecialchars($startDate . ' s/d ' . $endDate, ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<table border="1" cellpadding="6" cellspacing="0">';

        echo '<tr>';
        foreach (($reportData['headers'] ?? []) as $header) {
            echo '<th>' . htmlspecialchars((string) $header, ENT_QUOTES, 'UTF-8') . '</th>';
        }
        echo '</tr>';

        $rows = $reportData['rows'] ?? [];
        if (empty($rows)) {
            echo '<tr><td colspan="' . max(1, count($reportData['headers'] ?? [])) . '">Tidak ada data</td></tr>';
        } else {
            foreach ($rows as $row) {
                echo '<tr>';
                foreach ($row as $cell) {
                    echo '<td>' . htmlspecialchars((string) $cell, ENT_QUOTES, 'UTF-8') . '</td>';
                }
                echo '</tr>';
            }
        }
        echo '</table></body></html>';
        exit;
    }

    private function exportReportPdf(array $reportData, string $startDate, string $endDate): void
    {
        $lines = [];
        $lines[] = $reportData['title'] ?? 'Report';
        $lines[] = 'Periode: ' . $startDate . ' s/d ' . $endDate;
        $lines[] = '';
        $lines[] = implode(' | ', $reportData['headers'] ?? []);
        $lines[] = str_repeat('-', 120);

        $rows = $reportData['rows'] ?? [];
        if (empty($rows)) {
            $lines[] = 'Tidak ada data.';
        } else {
            foreach ($rows as $row) {
                $plainRow = array_map(static fn ($value): string => preg_replace('/\s+/', ' ', (string) $value), $row);
                $line = implode(' | ', $plainRow);
                if (strlen($line) > 115) {
                    $line = substr($line, 0, 112) . '...';
                }
                $lines[] = $line;
            }
        }

        $pdfBinary = $this->generateSimplePdf($lines);
        $filename = 'report-' . date('Ymd-His') . '.pdf';

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdfBinary));
        echo $pdfBinary;
        exit;
    }

    private function generateSimplePdf(array $lines): string
    {
        $safeLines = array_map(static function (string $line): string {
            $line = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $line);
            return preg_replace('/[^\x20-\x7E]/', '?', $line);
        }, $lines);

        $content = "BT\n/F1 10 Tf\n14 TL\n50 800 Td\n";
        $first = true;
        foreach ($safeLines as $line) {
            if (!$first) {
                $content .= "T*\n";
            }
            $content .= '(' . $line . ") Tj\n";
            $first = false;
        }
        $content .= "ET";

        $objects = [];
        $objects[] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $objects[] = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $objects[] = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>\nendobj\n";
        $objects[] = "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";
        $objects[] = "5 0 obj\n<< /Length " . strlen($content) . " >>\nstream\n" . $content . "\nendstream\nendobj\n";

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $index => $object) {
            $offsets[$index + 1] = strlen($pdf);
            $pdf .= $object;
        }

        $xrefStart = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= str_pad((string) $offsets[$i], 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        }

        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefStart . "\n%%EOF";

        return $pdf;
    }

    private function isValidDate(string $date): bool
    {
        $parsed = \DateTimeImmutable::createFromFormat('Y-m-d', $date);
        return $parsed !== false && $parsed->format('Y-m-d') === $date;
    }

    public function settings()
    {
        require __DIR__ . '/../Views/Admin/settings.php';
    }
}
