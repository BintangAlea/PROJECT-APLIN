<?php
/**
 * Generate QR Tokens for Cafe Seats
 * Creates unique tokens for each Relaxation Lounge seat with 7-day expiry
 */

require_once __DIR__ . '/bootstrap.php';

use App\Core\Database;

try {
    $db = Database::getConnection();
    
    // Get all Relaxation Lounge (cafe) seats
    $seatsStmt = $db->query(
        "SELECT seat_id, seat_name
         FROM seats
         WHERE zone_type = 'Relaxation Lounge'
         ORDER BY seat_id ASC"
    );
    $seats = $seatsStmt->fetchAll();
    
    if (empty($seats)) {
        die("No Relaxation Lounge seats found in database.\n");
    }
    
    echo "Found " . count($seats) . " cafe seats\n";
    echo "Generating QR tokens...\n\n";
    
    $generated = 0;
    $skipped = 0;
    
    foreach ($seats as $seat) {
        $seatId = (string) ($seat['seat_id'] ?? '');
        $seatName = (string) ($seat['seat_name'] ?? '');
        
        // Check if token already exists
        $existsStmt = $db->prepare(
            "SELECT token_id FROM qr_tokens WHERE seat_id = :seat_id LIMIT 1"
        );
        $existsStmt->execute([':seat_id' => $seatId]);
        
        if ($existsStmt->fetch()) {
            echo "[⊘] Seat $seatId ($seatName) - token already exists (skipping)\n";
            $skipped++;
            continue;
        }
        
        // Generate unique token
        $tokenRaw = $seatId . '_' . bin2hex(random_bytes(16)) . '_' . time();
        $tokenHash = hash('sha256', $tokenRaw);
        
        // Insert token with 7-day expiry
        $insertStmt = $db->prepare(
            "INSERT INTO qr_tokens (seat_id, token_hash, is_active, expires_at)
             VALUES (:seat_id, :token_hash, 1, DATE_ADD(NOW(), INTERVAL 7 DAY))"
        );
        $insertStmt->execute([
            ':seat_id' => $seatId,
            ':token_hash' => $tokenHash
        ]);
        
        echo "[✓] Seat $seatId ($seatName) - token generated\n";
        echo "    Token: " . substr($tokenHash, 0, 16) . "...\n";
        $generated++;
    }
    
    echo "\n✅ QR Token Generation Complete!\n";
    echo "Generated: $generated tokens\n";
    echo "Skipped: $skipped (already exist)\n";
    echo "\nTokens expire in 7 days and can be refreshed in tokens table.\n";
    
} catch (\Throwable $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
