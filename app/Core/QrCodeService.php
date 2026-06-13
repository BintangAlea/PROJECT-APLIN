<?php

namespace App\Core;

/**
 * QR Code Service
 * Handle QR code generation untuk booking confirmation
 */
class QrCodeService
{
    private $db;
    private $uploadsPath;

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->uploadsPath = __DIR__ . '/../../uploads/qr-codes';
        
        // Create uploads directory jika belum ada
        if (!is_dir($this->uploadsPath)) {
            mkdir($this->uploadsPath, 0755, true);
        }
    }

    /**
     * Generate unique booking confirmation ID
     */
    public function generateBookingId(): string
    {
        // Format: BK20250524-ABC123 (BK + date + random)
        $date = date('Ymd');
        $random = strtoupper(substr(uniqid(rand()), 0, 6));
        return "BK{$date}-{$random}";
    }

    /**
     * Generate QR code untuk booking
     * 
     * Require: composer require endroid/qr-code
     */
    public function generateQrCode(array $bookingData): string|false
    {
        try {
            // Check if QR Code library installed
            if (!class_exists('Endroid\\QrCode\\QrCode')) {
                // Fallback: generate simple text-based QR or use alternative method
                return $this->generateSimpleQrCode($bookingData);
            }

            $qrCode = new \Endroid\QrCode\QrCode(
                $this->buildQrCodeData($bookingData),
                new \Endroid\QrCode\Encoding\Encoding('UTF-8'),
                new \Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow(),
                new \Endroid\QrCode\Size\Size(200),
                new \Endroid\QrCode\Margin\Margin(10),
                null
            );

            $filename = 'qr_' . date('YmdHis') . '_' . uniqid() . '.png';
            $filePath = $this->uploadsPath . '/' . $filename;
            
            $qrCode->writeFile($filePath);
            
            return '/uploads/qr-codes/' . $filename;
        } catch (\Exception $e) {
            error_log("QR Code generation failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fallback: Generate simple QR code data format
     * (dapat digunakan dengan external QR generator)
     */
    private function generateSimpleQrCode(array $bookingData): string
    {
        // Format QR data sebagai URL/data yang bisa dipindai
        $qrData = http_build_query([
            'res_id' => $bookingData['res_id'] ?? ($bookingData['booking_confirmation_id'] ?? ''),
            'customer_id' => $bookingData['user_id'] ?? '',
            'date' => $bookingData['reservation_date'] ?? '',
            'time' => $bookingData['reservation_time'] ?? '',
            'beautician' => $bookingData['beautician_name'] ?? '',
            'service' => $bookingData['service_name'] ?? '',
        ]);

        // Return URL encoded format yang bisa dipindai manual
        return '/uploads/qr-codes/data:' . base64_encode($qrData);
    }

    /**
     * Build QR code data string
     */
    private function buildQrCodeData(array $bookingData): string
    {
        // Format: APP_NAMESPACE|RES_ID|CUSTOMER_ID|RESERVATION_DATE|CHECKSUM
        $baseData = implode('|', [
            'MERISH-BEAUTY',
            $bookingData['res_id'] ?? ($bookingData['booking_confirmation_id'] ?? ''),
            $bookingData['user_id'] ?? '',
            date('Y-m-d', strtotime($bookingData['reservation_date'] ?? 'now'))
        ]);

        // Add checksum
        $checksum = substr(md5($baseData), 0, 8);
        return $baseData . '|' . $checksum;
    }

    /**
     * Verify QR code data (untuk scanning)
     */
    public function verifyQrCodeData(string $data): array|false
    {
        $parts = explode('|', $data);
        if (count($parts) !== 5) {
            return false;
        }

        list($app, $resId, $userId, $date, $checksum) = $parts;

        // Verify checksum
        $verifyData = implode('|', [$app, $resId, $userId, $date]);
        $verifyChecksum = substr(md5($verifyData), 0, 8);

        if ($checksum !== $verifyChecksum) {
            return false;
        }

        return [
            'app' => $app,
            'res_id' => $resId,
            'user_id' => $userId,
            'date' => $date,
            'valid' => true
        ];
    }

    /**
     * Save QR code data - stores in session instead of DB
     * (original schema doesn't have QR-related columns on reservations)
     */
    public function saveQrCodeToReservation(int $resId, string $confirmationId, string $qrCodeUrl): bool
    {
        // Store QR data in session since reservations table doesn't have QR columns
        $_SESSION['booking_qr'] = [
            'res_id' => $resId,
            'confirmation_id' => $confirmationId,
            'qr_url' => $qrCodeUrl,
            'generated_at' => date('Y-m-d H:i:s')
        ];
        return true;
    }

    /**
     * Get reservation data for QR display
     */
    public function getReservationWithQr(int $resId): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT r.res_id, r.user_id,
                    DATE(r.schedule_time) AS reservation_date,
                    TIME(r.schedule_time) AS reservation_time,
                    r.STATUS,
                    u.NAME AS customer_name,
                    s.seat_name,
                    se.service_name
             FROM reservations r
             LEFT JOIN users u ON r.user_id = u.user_id
             LEFT JOIN seats s ON r.seat_id = s.seat_id
             LEFT JOIN reservation_details rd ON r.res_id = rd.res_id
             LEFT JOIN services se ON rd.service_id = se.service_id
             WHERE r.res_id = :res_id
             LIMIT 1'
        );
        $stmt->execute([':res_id' => $resId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Merge QR session data if available
        if ($row && isset($_SESSION['booking_qr']) && $_SESSION['booking_qr']['res_id'] == $resId) {
            $row['booking_confirmation_id'] = $_SESSION['booking_qr']['confirmation_id'];
            $row['booking_qr_code_url'] = $_SESSION['booking_qr']['qr_url'];
        }

        return $row;
    }
}
