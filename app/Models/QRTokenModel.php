<?php

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * QR Token Model
 * Generate and validate seat tokens for anti-spam cafe ordering
 * Token format: A1B2C3D4E5 (10 chars, alphanumeric)
 */
class QRTokenModel
{
    private PDO $db;
    private const TOKEN_LENGTH = 10;
    private const TOKEN_EXPIRY_MINUTES = 1440; // 24 hours

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Generate unique token for a seat
     */
    public static function generateToken(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $token = '';
        for ($i = 0; $i < self::TOKEN_LENGTH; $i++) {
            $token .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $token;
    }

    /**
     * Create or get existing valid token for a seat
     */
    public function getOrCreateTokenForSeat(string $seatId): ?string
    {
        // Check if valid token already exists
        $existing = $this->getValidToken($seatId);
        if ($existing) {
            return $existing;
        }

        // Generate new token
        $token = self::generateToken();
        $expiry = date('Y-m-d H:i:s', strtotime('+' . self::TOKEN_EXPIRY_MINUTES . ' minutes'));

        $stmt = $this->db->prepare(
            'INSERT INTO qr_tokens (seat_id, token, expires_at, created_at)
             VALUES (:seat_id, :token, :expires_at, NOW())'
        );

        $result = $stmt->execute([
            ':seat_id' => $seatId,
            ':token' => $token,
            ':expires_at' => $expiry
        ]);

        return $result ? $token : null;
    }

    /**
     * Get valid token for seat (not expired)
     */
    public function getValidToken(string $seatId): ?string
    {
        $stmt = $this->db->prepare(
            'SELECT token FROM qr_tokens 
             WHERE seat_id = :seat_id 
             AND expires_at > NOW()
             ORDER BY created_at DESC
             LIMIT 1'
        );
        $stmt->execute([':seat_id' => $seatId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['token'] ?? null;
    }

    /**
     * Validate token belongs to a seat
     */
    public function validateToken(string $token, string $seatId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as count FROM qr_tokens 
             WHERE token = :token 
             AND seat_id = :seat_id 
             AND expires_at > NOW()'
        );
        $stmt->execute([
            ':token' => $token,
            ':seat_id' => $seatId
        ]);
        $result = $stmt->fetch();
        return ($result['count'] ?? 0) > 0;
    }

    /**
     * Get seat ID from valid token
     */
    public function getSeatIdFromToken(string $token): ?string
    {
        $stmt = $this->db->prepare(
            'SELECT seat_id FROM qr_tokens 
             WHERE token = :token 
             AND expires_at > NOW()
             LIMIT 1'
        );
        $stmt->execute([':token' => $token]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['seat_id'] ?? null;
    }

    /**
     * Invalidate token (mark as used)
     */
    public function invalidateToken(string $token): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE qr_tokens SET expires_at = NOW() WHERE token = :token'
        );
        return $stmt->execute([':token' => $token]);
    }

    /**
     * Clean up expired tokens
     */
    public function cleanupExpiredTokens(): int
    {
        $stmt = $this->db->prepare('DELETE FROM qr_tokens WHERE expires_at < NOW()');
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Get all tokens for admin/debug
     */
    public function getAllTokens(): array
    {
        $stmt = $this->db->query(
            'SELECT qt.*, s.seat_name, s.zone_type 
             FROM qr_tokens qt
             JOIN seats s ON qt.seat_id = s.seat_id
             ORDER BY qt.created_at DESC'
        );
        return $stmt->fetchAll();
    }
}
