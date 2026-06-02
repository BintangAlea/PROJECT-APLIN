<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\ReservationsModel;
use App\Models\OrdersModel;
use PDO;

class ReceptionistController
{
    private ReservationsModel $reservationsModel;
    private OrdersModel $ordersModel;
    private PDO $db;

    public function __construct()
    {
        // Check role
        if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'receptionist') {
            header('Location: index.php?page=login');
            exit;
        }

        $this->db = Database::getConnection();
        $this->reservationsModel = new ReservationsModel();
        $this->ordersModel = new OrdersModel();
    }

    public function index()
    {
        $activeAreaSeats = $this->getActiveAreaSeats();
        $loungeSeats = $this->getLoungeSeats();

        $seatOccupancy = $this->getCurrentSeatOccupancy();
        $loungeQueue = $this->getLoungeQueue();

        $availableTransferSeats = array_values(array_filter($activeAreaSeats, function (array $seat) use ($seatOccupancy): bool {
            return !isset($seatOccupancy[$seat['seat_id']]);
        }));

        $flashSuccess = $_SESSION['success'] ?? null;
        $flashError = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        require __DIR__ . '/../Views/Receptionist/index.php';
    }

    public function walkInCheckIn()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=receptionist');
            exit;
        }

        $guestName = trim($_POST['guest_name'] ?? '');
        $destination = trim($_POST['destination'] ?? 'salon');

        if ($guestName === '') {
            $_SESSION['error'] = 'Nama pelanggan walk-in wajib diisi.';
            header('Location: index.php?page=receptionist');
            exit;
        }

        if (!in_array($destination, ['salon', 'cafe'], true)) {
            $_SESSION['error'] = 'Tujuan check-in tidak valid.';
            header('Location: index.php?page=receptionist');
            exit;
        }

        try {
            $targetSeatId = $destination === 'salon'
                ? $this->findFirstAvailableSeatByZone('Active Area')
                : $this->findFirstAvailableSeatByZone('Relaxation Lounge');

            if ($targetSeatId === null) {
                $fallbackZone = $destination === 'salon' ? 'kursi salon' : 'meja lounge';
                $_SESSION['error'] = 'Tidak ada ' . $fallbackZone . ' kosong untuk check-in saat ini.';
                header('Location: index.php?page=receptionist');
                exit;
            }

            $status = $destination === 'salon' ? 'In-Service' : 'Pending';

            $stmt = $this->db->prepare(
                'INSERT INTO reservations (user_id, guest_name, seat_id, STATUS, schedule_time, is_dp_paid, dp_amount)
                 VALUES (NULL, :guest_name, :seat_id, :status, NOW(), 0, 0)'
            );
            $stmt->execute([
                ':guest_name' => $guestName,
                ':seat_id' => $targetSeatId,
                ':status' => $status,
            ]);

            $this->createOrUpdateOpenBill(
                (int) $this->db->lastInsertId(),
                $targetSeatId,
                null,
                $destination === 'cafe' ? 'Cafe Only' : 'Salon Only'
            );

            $_SESSION['success'] = $destination === 'salon'
                ? 'Walk-in berhasil check-in dan langsung masuk kursi salon.'
                : 'Walk-in berhasil check-in ke lounge (waiting is earning).';
        } catch (\Throwable $exception) {
            $_SESSION['error'] = 'Gagal proses walk-in: ' . $exception->getMessage();
        }

        header('Location: index.php?page=receptionist');
        exit;
    }

    public function transferSeat()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=receptionist');
            exit;
        }

        $reservationId = (int) ($_POST['res_id'] ?? 0);
        $targetSeatId = trim($_POST['target_seat_id'] ?? '');

        if ($reservationId <= 0 || $targetSeatId === '') {
            $_SESSION['error'] = 'Data transfer kursi tidak lengkap.';
            header('Location: index.php?page=receptionist');
            exit;
        }

        try {
            $reservationStmt = $this->db->prepare(
                "SELECT res_id, STATUS
                 FROM reservations
                 WHERE res_id = :res_id
                 LIMIT 1"
            );
            $reservationStmt->execute([':res_id' => $reservationId]);
            $reservation = $reservationStmt->fetch();

            if (!$reservation) {
                $_SESSION['error'] = 'Reservasi tidak ditemukan.';
                header('Location: index.php?page=receptionist');
                exit;
            }

            $seatStmt = $this->db->prepare(
                "SELECT seat_id
                 FROM seats
                 WHERE seat_id = :seat_id
                 AND zone_type = 'Active Area'
                 LIMIT 1"
            );
            $seatStmt->execute([':seat_id' => $targetSeatId]);
            if (!$seatStmt->fetch()) {
                $_SESSION['error'] = 'Target kursi salon tidak valid.';
                header('Location: index.php?page=receptionist');
                exit;
            }

            if (!$this->isSeatAvailable($targetSeatId, $reservationId)) {
                $_SESSION['error'] = 'Kursi target sedang terisi. Pilih kursi lain.';
                header('Location: index.php?page=receptionist');
                exit;
            }

            $updateStmt = $this->db->prepare(
                "UPDATE reservations
                 SET seat_id = :seat_id,
                     STATUS = 'In-Service'
                 WHERE res_id = :res_id"
            );
            $updateStmt->execute([
                ':seat_id' => $targetSeatId,
                ':res_id' => $reservationId,
            ]);

            $billSeatStmt = $this->db->prepare(
                "SELECT companion_seat_id
                 FROM reservations
                 WHERE res_id = :res_id
                 LIMIT 1"
            );
            $billSeatStmt->execute([':res_id' => $reservationId]);
            $billSeatRow = $billSeatStmt->fetch();
            $this->createOrUpdateOpenBill(
                $reservationId,
                $targetSeatId,
                $billSeatRow['companion_seat_id'] ?? null,
                !empty($billSeatRow['companion_seat_id']) ? 'Salon + Cafe' : 'Salon Only'
            );

            $_SESSION['success'] = 'Pelanggan berhasil dipindahkan ke kursi salon. Open bill tetap berjalan.';
        } catch (\Throwable $exception) {
            $_SESSION['error'] = 'Gagal transfer kursi: ' . $exception->getMessage();
        }

        header('Location: index.php?page=receptionist');
        exit;
    }

    public function scheduleBooking()
    {
        $reservations = $this->reservationsModel->findAll();
        require __DIR__ . '/../Views/Receptionist/schedule_booking.php';
    }

    public function viewReservations()
    {
        $date = $_GET['date'] ?? date('Y-m-d');
        $filter = strtolower(trim((string) ($_GET['filter'] ?? 'all')));
        if (!in_array($filter, ['all', 'pending', 'checked-in'], true)) {
            $filter = 'all';
        }

        $reservations = $this->getAppointmentsByDate($date, $filter);
        $availableSalonSeats = $this->getAvailableSeatsByZone('Active Area');
        $availableLoungeSeats = $this->getAvailableSeatsByZone('Relaxation Lounge');

        $flashSuccess = $_SESSION['success'] ?? null;
        $flashError = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        require __DIR__ . '/../Views/Receptionist/view_reservations.php';
    }

    public function checkInAllocate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=receptionist&action=viewReservations');
            exit;
        }

        $reservationId = (int) ($_POST['res_id'] ?? 0);
        $salonSeatId = trim($_POST['salon_seat_id'] ?? '');
        $hasCompanion = isset($_POST['has_companion']) && $_POST['has_companion'] === '1';
        $loungeSeatId = trim($_POST['lounge_seat_id'] ?? '');
        $date = trim($_POST['date'] ?? date('Y-m-d'));

        if ($reservationId <= 0 || $salonSeatId === '') {
            $_SESSION['error'] = 'Reservasi dan kursi salon wajib dipilih.';
            header('Location: index.php?page=receptionist&action=viewReservations&date=' . urlencode($date));
            exit;
        }

        if ($hasCompanion && $loungeSeatId === '') {
            $_SESSION['error'] = 'Pilih meja kafe untuk pendamping.';
            header('Location: index.php?page=receptionist&action=viewReservations&date=' . urlencode($date));
            exit;
        }

        try {
            $reservationStmt = $this->db->prepare(
                "SELECT res_id
                 FROM reservations
                 WHERE res_id = :res_id
                 LIMIT 1"
            );
            $reservationStmt->execute([':res_id' => $reservationId]);
            if (!$reservationStmt->fetch()) {
                $_SESSION['error'] = 'Reservasi tidak ditemukan.';
                header('Location: index.php?page=receptionist&action=viewReservations&date=' . urlencode($date));
                exit;
            }

            if (!$this->isSeatInZone($salonSeatId, 'Active Area')) {
                $_SESSION['error'] = 'Kursi salon tidak valid.';
                header('Location: index.php?page=receptionist&action=viewReservations&date=' . urlencode($date));
                exit;
            }
            if (!$this->isSeatAvailable($salonSeatId, $reservationId)) {
                $_SESSION['error'] = 'Kursi salon terpilih sedang dipakai.';
                header('Location: index.php?page=receptionist&action=viewReservations&date=' . urlencode($date));
                exit;
            }

            if ($hasCompanion) {
                if (!$this->isSeatInZone($loungeSeatId, 'Relaxation Lounge')) {
                    $_SESSION['error'] = 'Meja kafe pendamping tidak valid.';
                    header('Location: index.php?page=receptionist&action=viewReservations&date=' . urlencode($date));
                    exit;
                }
                if (!$this->isSeatAvailable($loungeSeatId)) {
                    $_SESSION['error'] = 'Meja kafe pendamping sedang terisi.';
                    header('Location: index.php?page=receptionist&action=viewReservations&date=' . urlencode($date));
                    exit;
                }
            }

            $updateStmt = $this->db->prepare(
                "UPDATE reservations
                 SET seat_id = :seat_id,
                     companion_seat_id = :companion_seat_id,
                     STATUS = 'In-Service'
                 WHERE res_id = :res_id"
            );
            $updateStmt->execute([
                ':seat_id' => $salonSeatId,
                ':companion_seat_id' => $hasCompanion ? $loungeSeatId : null,
                ':res_id' => $reservationId,
            ]);

            // Create or update open bill for unified billing
            $this->createOrUpdateOpenBill($reservationId, $salonSeatId, $hasCompanion ? $loungeSeatId : null);

            $_SESSION['success'] = 'Check-In & alokasi berhasil disimpan.';
        } catch (\Throwable $exception) {
            $_SESSION['error'] = 'Gagal check-in: ' . $exception->getMessage();
        }

        header('Location: index.php?page=receptionist&action=viewReservations&date=' . urlencode($date));
        exit;
    }

    public function updateReservationStatus()
    {
        $reservationId = (int)($_POST['reservation_id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if ($reservationId > 0 && !empty($status)) {
            $this->reservationsModel->update($reservationId, ['status' => $status]);
            $_SESSION['success'] = 'Reservation status updated';
        }

        header('Location: index.php?page=receptionist');
        exit;
    }

    public function viewOrders()
    {
        $activeBills = $this->getActiveBillsForCheckout();
        $selectedReservationId = (int) ($_GET['res_id'] ?? 0);

        if ($selectedReservationId <= 0 && !empty($activeBills)) {
            $selectedReservationId = (int) $activeBills[0]['res_id'];
        }

        $selectedBill = null;
        if ($selectedReservationId > 0) {
            $selectedBill = $this->buildUnifiedBill($selectedReservationId);
        }

        $flashSuccess = $_SESSION['success'] ?? null;
        $flashError = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        require __DIR__ . '/../Views/Receptionist/view_orders.php';
    }

    public function processPayment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=receptionist&action=viewOrders');
            exit;
        }

        $reservationId = (int) ($_POST['res_id'] ?? 0);
        $paymentMethod = trim($_POST['payment_method'] ?? 'Cash');

        $validMethods = ['Cash', 'Debit', 'QRIS', 'Transfer'];
        if ($reservationId <= 0) {
            $_SESSION['error'] = 'Tagihan tidak valid.';
            header('Location: index.php?page=receptionist&action=viewOrders');
            exit;
        }
        if (!in_array($paymentMethod, $validMethods, true)) {
            $_SESSION['error'] = 'Metode pembayaran tidak valid.';
            header('Location: index.php?page=receptionist&action=viewOrders&res_id=' . urlencode((string) $reservationId));
            exit;
        }

        try {
            $existingTransactionStmt = $this->db->prepare(
                'SELECT trans_id FROM transactions WHERE res_id = :res_id LIMIT 1'
            );
            $existingTransactionStmt->execute([':res_id' => $reservationId]);
            if ($existingTransactionStmt->fetch()) {
                $_SESSION['error'] = 'Tagihan ini sudah pernah dibayar.';
                header('Location: index.php?page=receptionist&action=viewOrders&res_id=' . urlencode((string) $reservationId));
                exit;
            }

            $bill = $this->buildUnifiedBill($reservationId);
            if ($bill === null) {
                $_SESSION['error'] = 'Tagihan tidak ditemukan.';
                header('Location: index.php?page=receptionist&action=viewOrders');
                exit;
            }

            $this->db->beginTransaction();

            $insertTransaction = $this->db->prepare(
                'INSERT INTO transactions (res_id, total_amount, payment_method, payment_date)
                 VALUES (:res_id, :total_amount, :payment_method, NOW())'
            );
            $insertTransaction->execute([
                ':res_id' => $reservationId,
                ':total_amount' => $bill['summary']['total_due'],
                ':payment_method' => $paymentMethod,
            ]);

            $updateReservation = $this->db->prepare(
                "UPDATE reservations
                 SET STATUS = 'Selesai'
                 WHERE res_id = :res_id"
            );
            $updateReservation->execute([':res_id' => $reservationId]);

            // Close open bill
            $closeBillStmt = $this->db->prepare(
                "UPDATE open_bills
                 SET bill_status = 'Closed',
                     closed_at = NOW(),
                     updated_at = NOW()
                 WHERE res_id = :res_id"
            );
            $closeBillStmt->execute([':res_id' => $reservationId]);

            // Mark all orders in bill as paid
            $billId = (int) ($bill['bill_id'] ?? 0);
            if ($billId > 0) {
                $markOrdersPaid = $this->db->prepare(
                    "UPDATE orders
                     SET payment_status = 'Paid'
                     WHERE bill_id = :bill_id"
                );
                $markOrdersPaid->execute([':bill_id' => $billId]);
            } else {
                // Fallback for older orders without bill_id
                $markOrdersPaid = $this->db->prepare(
                    "UPDATE orders
                     SET payment_status = 'Paid'
                     WHERE res_id = :res_id"
                );
                $markOrdersPaid->execute([':res_id' => $reservationId]);
            }

            $this->db->commit();
            $_SESSION['success'] = 'Pembayaran berhasil diterima dan setruk siap dicetak.';
        } catch (\Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            $_SESSION['error'] = 'Gagal memproses pembayaran: ' . $exception->getMessage();
        }

        header('Location: index.php?page=receptionist&action=viewOrders');
        exit;
    }

    public function checkIn()
    {
        require __DIR__ . '/../Views/Receptionist/check_in.php';
    }

    private function getActiveAreaSeats(): array
    {
        $stmt = $this->db->query(
            "SELECT seat_id, seat_name
             FROM seats
             WHERE zone_type = 'Active Area'
             ORDER BY seat_id ASC
             LIMIT 5"
        );
        $rows = $stmt->fetchAll();

        if (!empty($rows)) {
            return $rows;
        }

        return [
            ['seat_id' => 'S01', 'seat_name' => '01'],
            ['seat_id' => 'S02', 'seat_name' => '02'],
            ['seat_id' => 'S03', 'seat_name' => '03'],
            ['seat_id' => 'S04', 'seat_name' => '04'],
            ['seat_id' => 'S05', 'seat_name' => '05'],
        ];
    }

    private function getLoungeSeats(): array
    {
        $stmt = $this->db->query(
            "SELECT seat_id, seat_name
             FROM seats
             WHERE zone_type = 'Relaxation Lounge'
             ORDER BY seat_id ASC
             LIMIT 5"
        );
        $rows = $stmt->fetchAll();

        if (!empty($rows)) {
            return $rows;
        }

        return [
            ['seat_id' => 'T-01', 'seat_name' => 'T-01'],
            ['seat_id' => 'T-02', 'seat_name' => 'T-02'],
            ['seat_id' => 'T-03', 'seat_name' => 'T-03'],
            ['seat_id' => 'T-04', 'seat_name' => 'T-04'],
            ['seat_id' => 'T-05', 'seat_name' => 'T-05'],
        ];
    }

    private function getCurrentSeatOccupancy(): array
    {
        $stmt = $this->db->query(
            "SELECT r.res_id,
                    r.seat_id,
                    r.STATUS,
                    r.schedule_time,
                    COALESCE(r.guest_name, u.NAME, 'Guest') AS customer_name,
                    COALESCE(s.service_name, 'Salon Service') AS service_name,
                    GREATEST(TIMESTAMPDIFF(MINUTE, r.schedule_time, NOW()), 0) AS duration_minutes
             FROM reservations r
             LEFT JOIN users u ON r.user_id = u.user_id
             LEFT JOIN (
                SELECT rd.res_id, MIN(rd.service_id) AS service_id
                FROM reservation_details rd
                GROUP BY rd.res_id
             ) rd_first ON rd_first.res_id = r.res_id
             LEFT JOIN services s ON s.service_id = rd_first.service_id
             WHERE r.STATUS IN ('Pending', 'Confirmed', 'In-Service')
             ORDER BY FIELD(r.STATUS, 'In-Service', 'Confirmed', 'Pending'), r.schedule_time DESC"
        );

        $map = [];
        foreach ($stmt->fetchAll() as $row) {
            $seatId = (string) ($row['seat_id'] ?? '');
            if ($seatId === '' || isset($map[$seatId])) {
                continue;
            }
            $map[$seatId] = $row;
        }

        return $map;
    }

    private function getLoungeQueue(): array
    {
        $stmt = $this->db->query(
            "SELECT r.res_id,
                    r.schedule_time,
                    COALESCE(r.guest_name, u.NAME, 'Guest') AS customer_name,
                    COUNT(o.order_id) AS fnb_count
             FROM reservations r
             LEFT JOIN users u ON r.user_id = u.user_id
             LEFT JOIN orders o ON o.res_id = r.res_id
             WHERE r.STATUS = 'Pending'
             GROUP BY r.res_id, r.schedule_time, r.guest_name, u.NAME
             ORDER BY r.schedule_time ASC
             LIMIT 5"
        );

        return $stmt->fetchAll();
    }

    private function findFirstAvailableSeatByZone(string $zone): ?string
    {
        $stmt = $this->db->prepare(
            "SELECT s.seat_id
             FROM seats s
             WHERE s.zone_type = :zone
             AND NOT EXISTS (
                 SELECT 1
                 FROM reservations r
                 WHERE r.seat_id = s.seat_id
                 AND r.STATUS IN ('Pending', 'Confirmed', 'In-Service')
             )
             ORDER BY s.seat_id ASC
             LIMIT 1"
        );
        $stmt->execute([':zone' => $zone]);
        $row = $stmt->fetch();

        return $row['seat_id'] ?? null;
    }

    private function isSeatAvailable(string $seatId, int $excludeReservationId = 0): bool
    {
        $sql =
            "SELECT COUNT(*) AS total
             FROM reservations
             WHERE seat_id = :seat_id
             AND STATUS IN ('Pending', 'Confirmed', 'In-Service')";

        $params = [':seat_id' => $seatId];
        if ($excludeReservationId > 0) {
            $sql .= ' AND res_id <> :exclude_res_id';
            $params[':exclude_res_id'] = $excludeReservationId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return ((int) ($stmt->fetch()['total'] ?? 0)) === 0;
    }

    private function isSeatInZone(string $seatId, string $zone): bool
    {
        $stmt = $this->db->prepare(
            "SELECT seat_id
             FROM seats
             WHERE seat_id = :seat_id AND zone_type = :zone
             LIMIT 1"
        );
        $stmt->execute([
            ':seat_id' => $seatId,
            ':zone' => $zone,
        ]);

        return (bool) $stmt->fetch();
    }

    private function getAvailableSeatsByZone(string $zone): array
    {
        $stmt = $this->db->prepare(
            "SELECT s.seat_id, s.seat_name
             FROM seats s
             WHERE s.zone_type = :zone
             AND NOT EXISTS (
                 SELECT 1
                 FROM reservations r
                 WHERE r.seat_id = s.seat_id
                 AND r.STATUS IN ('Pending', 'Confirmed', 'In-Service')
             )
             ORDER BY s.seat_id ASC"
        );
        $stmt->execute([':zone' => $zone]);
        return $stmt->fetchAll();
    }

    private function getAppointmentsByDate(string $date, string $filter): array
    {
        $statusFilterSql = '';
        if ($filter === 'pending') {
            $statusFilterSql = " AND r.STATUS = 'Pending'";
        } elseif ($filter === 'checked-in') {
            $statusFilterSql = " AND r.STATUS IN ('Confirmed', 'In-Service')";
        }

        $stmt = $this->db->prepare(
            "SELECT r.res_id,
                    r.seat_id,
                    r.companion_seat_id,
                    r.STATUS,
                    r.schedule_time,
                    COALESCE(r.guest_name, u.NAME, 'Guest') AS customer_name,
                    COALESCE(beautician.NAME, '-') AS beautician_name,
                    COALESCE(s.service_name, '-') AS service_name,
                    COALESCE(TIME_FORMAT(r.schedule_time, '%h:%i %p'), '-') AS booking_time,
                    GREATEST(TIMESTAMPDIFF(MINUTE, r.schedule_time, NOW()), 0) AS lateness_minutes
             FROM reservations r
             LEFT JOIN users u ON r.user_id = u.user_id
             LEFT JOIN reservation_details rd ON rd.res_id = r.res_id
             LEFT JOIN services s ON s.service_id = rd.service_id
             LEFT JOIN users beautician ON beautician.user_id = rd.beautician_id
             WHERE DATE(r.schedule_time) = :date
             {$statusFilterSql}
             ORDER BY r.schedule_time ASC"
        );
        $stmt->execute([':date' => $date]);
        return $stmt->fetchAll();
    }

    private function getActiveBillsForCheckout(): array
    {
        $stmt = $this->db->query(
            "SELECT b.bill_id,
                    r.res_id,
                    r.schedule_time,
                    COALESCE(r.guest_name, u.NAME, b.guest_name, 'Guest') AS customer_name,
                    COALESCE((
                        SELECT SUM(s.base_tariff)
                        FROM reservation_details rd
                        JOIN services s ON s.service_id = rd.service_id
                        WHERE rd.res_id = r.res_id
                    ), 0) AS salon_total,
                    COALESCE((
                        SELECT SUM(o.qty * m.price)
                        FROM orders o
                        JOIN menus m ON m.menu_id = o.menu_id
                        WHERE o.bill_id = b.bill_id
                        AND o.payment_status = 'Unpaid'
                    ), 0) AS cafe_total
             FROM open_bills b
             LEFT JOIN reservations r ON b.res_id = r.res_id
             LEFT JOIN users u ON r.user_id = u.user_id
             LEFT JOIN transactions t ON t.res_id = r.res_id
             WHERE b.bill_status = 'Open'
             AND t.trans_id IS NULL
             ORDER BY r.schedule_time ASC, b.created_at ASC"
        );

        $rows = $stmt->fetchAll();

        return array_map(function (array $row): array {
            $salonTotal = (float) ($row['salon_total'] ?? 0);
            $cafeTotal = (float) ($row['cafe_total'] ?? 0);
            $subtotal = $salonTotal + $cafeTotal;
            return [
                'res_id' => (int) ($row['res_id'] ?? 0),
                'bill_id' => (int) ($row['bill_id'] ?? 0),
                'customer_name' => $row['customer_name'] ?? 'Guest',
                'schedule_time' => $row['schedule_time'] ?? null,
                'salon_total' => $salonTotal,
                'cafe_total' => $cafeTotal,
                'subtotal' => $subtotal,
                'label' => ($salonTotal > 0 && $cafeTotal > 0) ? 'Salon + Cafe' : (($cafeTotal > 0) ? 'Cafe Only' : 'Salon Services'),
            ];
        }, $rows);
    }

    private function buildUnifiedBill(int $reservationId): ?array
    {
        // Get open bill for this reservation
        $billStmt = $this->db->prepare(
            "SELECT bill_id, res_id, guest_name
             FROM open_bills
             WHERE res_id = :res_id
             LIMIT 1"
        );
        $billStmt->execute([':res_id' => $reservationId]);
        $openBill = $billStmt->fetch();
        
        if (!$openBill) {
            return null;
        }
        
        $billId = (int) ($openBill['bill_id'] ?? 0);

        $reservationStmt = $this->db->prepare(
            "SELECT r.res_id,
                    r.schedule_time,
                    COALESCE(r.guest_name, u.NAME, 'Guest') AS customer_name
             FROM reservations r
             LEFT JOIN users u ON u.user_id = r.user_id
             WHERE r.res_id = :res_id
             LIMIT 1"
        );
        $reservationStmt->execute([':res_id' => $reservationId]);
        $reservation = $reservationStmt->fetch();
        if (!$reservation) {
            return null;
        }

        $salonStmt = $this->db->prepare(
            "SELECT s.service_name,
                    s.base_tariff,
                    COALESCE(beautician.NAME, '-') AS stylist_name
             FROM reservation_details rd
             JOIN services s ON s.service_id = rd.service_id
             LEFT JOIN users beautician ON beautician.user_id = rd.beautician_id
             WHERE rd.res_id = :res_id"
        );
        $salonStmt->execute([':res_id' => $reservationId]);
        $salonItems = $salonStmt->fetchAll();

        // Query cafe items using bill_id instead of res_id
        $cafeStmt = $this->db->prepare(
            "SELECT m.menu_name,
                    o.qty,
                    m.price,
                    o.STATUS AS order_status,
                    (o.qty * m.price) AS line_total
             FROM orders o
             JOIN menus m ON m.menu_id = o.menu_id
             WHERE o.bill_id = :bill_id
             AND o.payment_status = 'Unpaid'"
        );
        $cafeStmt->execute([':bill_id' => $billId]);
        $cafeItems = $cafeStmt->fetchAll();
        $cafeInProgressItems = array_filter($cafeItems, static function (array $item): bool {
            return strtoupper(trim((string) ($item['order_status'] ?? ''))) === 'IN PROGRESS';
        });

        $salonTotal = array_reduce($salonItems, static function (float $carry, array $item): float {
            return $carry + (float) ($item['base_tariff'] ?? 0);
        }, 0.0);

        $cafeTotal = array_reduce($cafeItems, static function (float $carry, array $item): float {
            return $carry + (float) ($item['line_total'] ?? 0);
        }, 0.0);

        $subtotal = $salonTotal + $cafeTotal;
        $synergyDiscount = ($salonTotal > 0 && $cafeTotal > 0) ? ($cafeTotal * 0.20) : 0.0;
        $afterDiscount = max($subtotal - $synergyDiscount, 0);
        $taxAmount = $afterDiscount * 0.11;
        $totalDue = $afterDiscount + $taxAmount;

        return [
            'bill_id' => $billId,
            'reservation' => $reservation,
            'salon_items' => $salonItems,
            'cafe_items' => $cafeItems,
            'cafe_in_progress_items' => array_values($cafeInProgressItems),
            'summary' => [
                'salon_total' => $salonTotal,
                'cafe_total' => $cafeTotal,
                'subtotal' => $subtotal,
                'synergy_discount' => $synergyDiscount,
                'tax' => $taxAmount,
                'total_due' => $totalDue,
            ],
        ];
    }

    /**
     * Create or update open_bill for unified billing
     * Called when receptionist check-in customer (with or without companion)
     */
    private function createOrUpdateOpenBill(int $reservationId, string $mainSeatId, ?string $companionSeatId = null, ?string $billType = null): void
    {
        try {
            // Check if bill already exists
            $existingStmt = $this->db->prepare(
                "SELECT bill_id FROM open_bills WHERE res_id = :res_id LIMIT 1"
            );
            $existingStmt->execute([':res_id' => $reservationId]);
            $existing = $existingStmt->fetch();

            if ($existing) {
                // Update existing bill with new seat allocation
                $updateStmt = $this->db->prepare(
                    "UPDATE open_bills
                     SET main_seat_id = :main_seat_id,
                         companion_seat_id = :companion_seat_id,
                         zone_type = :zone_type,
                         updated_at = NOW()
                     WHERE res_id = :res_id"
                );
                $updateStmt->execute([
                    ':main_seat_id' => $mainSeatId,
                    ':companion_seat_id' => $companionSeatId,
                    ':zone_type' => $billType ?? ($companionSeatId ? 'Salon + Cafe' : 'Salon Only'),
                    ':res_id' => $reservationId
                ]);
            } else {
                // Get customer name
                $resStmt = $this->db->prepare(
                    "SELECT COALESCE(guest_name, u.NAME, 'Customer') as name
                     FROM reservations r
                     LEFT JOIN users u ON r.user_id = u.user_id
                     WHERE r.res_id = :res_id
                     LIMIT 1"
                );
                $resStmt->execute([':res_id' => $reservationId]);
                $resData = $resStmt->fetch();
                $customerName = $resData['name'] ?? 'Customer';

                // Create new bill
                $insertStmt = $this->db->prepare(
                    "INSERT INTO open_bills (res_id, guest_name, zone_type, main_seat_id, companion_seat_id, bill_status)
                     VALUES (:res_id, :guest_name, :zone_type, :main_seat_id, :companion_seat_id, 'Open')"
                );
                $insertStmt->execute([
                    ':res_id' => $reservationId,
                    ':guest_name' => $customerName,
                    ':zone_type' => $billType ?? ($companionSeatId ? 'Salon + Cafe' : 'Salon Only'),
                    ':main_seat_id' => $mainSeatId,
                    ':companion_seat_id' => $companionSeatId
                ]);
            }
        } catch (\Throwable $e) {
            // Log error but don't break check-in flow
            error_log('Error creating open bill: ' . $e->getMessage());
        }
    }
}
