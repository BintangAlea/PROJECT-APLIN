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
            'booking_id' => $bookingData['booking_confirmation_id'],
            'customer_id' => $bookingData['user_id'],
            'date' => $bookingData['reservation_date'],
            'time' => $bookingData['reservation_time'],
            'beautician' => $bookingData['beautician_name'] ?? '',
            'service' => $bookingData['service_name'] ?? '',
            'total' => $bookingData['total_price']
        ]);

        // Return URL encoded format yang bisa dipindai manual
        return '/uploads/qr-codes/data:' . base64_encode($qrData);
    }

    /**
     * Build QR code data string
     */
    private function buildQrCodeData(array $bookingData): string
    {
        // Format: APP_NAMESPACE|BOOKING_ID|CUSTOMER_ID|RESERVATION_DATE|TOTAL_PRICE|CHECKSUM
        $baseData = implode('|', [
            'MERISH-BEAUTY',
            $bookingData['booking_confirmation_id'],
            $bookingData['user_id'],
            date('Y-m-d', strtotime($bookingData['reservation_date'])),
            number_format($bookingData['total_price'], 2, '.', '')
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
        if (count($parts) !== 6) {
            return false;
        }

        list($app, $bookingId, $userId, $date, $amount, $checksum) = $parts;

        // Verify checksum
        $verifyData = implode('|', [$app, $bookingId, $userId, $date, $amount]);
        $verifyChecksum = substr(md5($verifyData), 0, 8);

        if ($checksum !== $verifyChecksum) {
            return false;
        }

        return [
            'app' => $app,
            'booking_id' => $bookingId,
            'user_id' => $userId,
            'date' => $date,
            'amount' => (float)$amount,
            'valid' => true
        ];
    }

    /**
     * Save QR code URL to reservation
     */
    public function saveQrCodeToReservation(int $resId, string $confirmationId, string $qrCodeUrl): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE reservations 
             SET booking_confirmation_id = :confirmation_id,
                 booking_qr_code_url = :qr_url,
                 confirmation_date = NOW(),
                 updated_at = NOW()
             WHERE res_id = :res_id'
        );

        return $stmt->execute([
            ':confirmation_id' => $confirmationId,
            ':qr_url' => $qrCodeUrl,
            ':res_id' => $resId
        ]);
    }

    /**
     * Get reservation dengan QR code
     */
    public function getReservationWithQr(int $resId): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT res_id, booking_confirmation_id, booking_qr_code_url, 
                    confirmation_date, user_id, reservation_date, reservation_time, 
                    total_price
             FROM reservations 
             WHERE res_id = :res_id'
        );
        $stmt->execute([':res_id' => $resId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
