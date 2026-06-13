<?php

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Loyalty Model
 * Manage loyalty tiers, points, and member benefits
 * 
 * Tier thresholds based on total_spent:
 * - Stage 1 (Regular): IDR 0 - 750,000
 * - Stage 2 (Loyal): IDR 750,000 - 2,000,000
 * - Stage 3 (VIP): IDR 2,000,000+
 */
class LoyaltyModel
{
    private PDO $db;
    private UsersModel $usersModel;

    // Tier thresholds
    private const TIER_REGULAR = 1;
    private const TIER_LOYAL = 2;
    private const TIER_VIP = 3;

    private const THRESHOLD_LOYAL = 750000; // 750k
    private const THRESHOLD_VIP = 2000000;  // 2M

    // Tier benefits
    private const BOOKING_WINDOW = [
        self::TIER_REGULAR => 1,     // H-1 (1 day)
        self::TIER_LOYAL => 1,       // H-1 (1 day)
        self::TIER_VIP => 14         // H-14 (2 weeks)
    ];

    private const POINTS_MULTIPLIER = [
        self::TIER_REGULAR => 1.0,   // 1x points
        self::TIER_LOYAL => 1.5,     // 1.5x points
        self::TIER_VIP => 2.0        // 2x points
    ];

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->usersModel = new UsersModel();
    }

    /**
     * Calculate tier based on total_spent
     */
    public static function calculateTierFromSpent(float $totalSpent): int
    {
        if ($totalSpent >= self::THRESHOLD_VIP) {
            return self::TIER_VIP;
        }
        if ($totalSpent >= self::THRESHOLD_LOYAL) {
            return self::TIER_LOYAL;
        }
        return self::TIER_REGULAR;
    }

    /**
     * Get tier name
     */
    public static function getTierName(int $stage): string
    {
        return match($stage) {
            self::TIER_LOYAL => 'Loyal Member',
            self::TIER_VIP => 'VIP Member',
            default => 'Regular Member'
        };
    }

    /**
     * Get tier emoji/icon
     */
    public static function getTierIcon(int $stage): string
    {
        return match($stage) {
            self::TIER_LOYAL => '⭐',
            self::TIER_VIP => '👑',
            default => '💳'
        };
    }

    /**
     * Get booking window (days) for tier
     */
    public static function getBookingWindow(int $stage): int
    {
        return self::BOOKING_WINDOW[$stage] ?? self::BOOKING_WINDOW[self::TIER_REGULAR];
    }

    /**
     * Get points multiplier for tier
     */
    public static function getPointsMultiplier(int $stage): float
    {
        return self::POINTS_MULTIPLIER[$stage] ?? self::POINTS_MULTIPLIER[self::TIER_REGULAR];
    }

    /**
     * Update user tier based on total_spent
     */
    public function updateUserTier(int $userId): bool
    {
        $user = $this->usersModel->findById($userId);
        if (!$user) {
            return false;
        }

        $newTier = self::calculateTierFromSpent($user['total_spent']);
        
        if ($newTier !== $user['loyalty_stage']) {
            $stmt = $this->db->prepare('UPDATE users SET loyalty_stage = :tier WHERE user_id = :id');
            return $stmt->execute([':tier' => $newTier, ':id' => $userId]);
        }

        return true;
    }

    /**
     * Add points to user (with tier multiplier)
     */
    public function addPoints(int $userId, int $basePoints): bool
    {
        $user = $this->usersModel->findById($userId);
        if (!$user) {
            return false;
        }

        $multiplier = self::getPointsMultiplier($user['loyalty_stage']);
        $pointsToAdd = (int) ($basePoints * $multiplier);

        $stmt = $this->db->prepare(
            'UPDATE users SET reward_points = reward_points + :points WHERE user_id = :id'
        );
        return $stmt->execute([':points' => $pointsToAdd, ':id' => $userId]);
    }

    /**
     * Spend points for reward redemption
     */
    public function spendPoints(int $userId, int $points): bool
    {
        $user = $this->usersModel->findById($userId);
        if (!$user || $user['reward_points'] < $points) {
            return false;
        }

        $stmt = $this->db->prepare(
            'UPDATE users SET reward_points = reward_points - :points WHERE user_id = :id'
        );
        return $stmt->execute([':points' => $points, ':id' => $userId]);
    }

    /**
     * Add spending (transaction) and update tier
     */
    public function recordSpending(int $userId, float $amount): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET total_spent = total_spent + :amount WHERE user_id = :id'
        );
        $result = $stmt->execute([':amount' => $amount, ':id' => $userId]);

        // Update tier after spending
        if ($result) {
            $this->updateUserTier($userId);
        }

        return $result;
    }

    /**
     * Get loyalty progress for user (towards next tier)
     */
    public function getProgressToNextTier(int $userId): array
    {
        $user = $this->usersModel->findById($userId);
        if (!$user) {
            return [];
        }

        $currentTier = $user['loyalty_stage'];
        $totalSpent = $user['total_spent'];
        $rewardPoints = $user['reward_points'];

        $progress = [
            'current_tier' => $currentTier,
            'current_tier_name' => self::getTierName($currentTier),
            'current_tier_icon' => self::getTierIcon($currentTier),
            'total_spent' => $totalSpent,
            'reward_points' => $rewardPoints,
            'booking_window_days' => self::getBookingWindow($currentTier),
            'points_multiplier' => self::getPointsMultiplier($currentTier),
            'is_vip' => $currentTier === self::TIER_VIP,
            'is_loyal' => $currentTier >= self::TIER_LOYAL
        ];

        // Add progress to next tier
        if ($currentTier === self::TIER_REGULAR) {
            $needed = self::THRESHOLD_LOYAL - $totalSpent;
            $progress['next_tier'] = self::TIER_LOYAL;
            $progress['next_tier_name'] = 'Loyal Member';
            $progress['spent_towards_next'] = $totalSpent;
            $progress['needed_for_next'] = max(0, $needed);
            $progress['progress_percent'] = min(100, ($totalSpent / self::THRESHOLD_LOYAL) * 100);
        } elseif ($currentTier === self::TIER_LOYAL) {
            $needed = self::THRESHOLD_VIP - $totalSpent;
            $progress['next_tier'] = self::TIER_VIP;
            $progress['next_tier_name'] = 'VIP Member';
            $progress['spent_towards_next'] = $totalSpent - self::THRESHOLD_LOYAL;
            $progress['needed_for_next'] = max(0, $needed);
            $progress['progress_percent'] = min(100, (($totalSpent - self::THRESHOLD_LOYAL) / (self::THRESHOLD_VIP - self::THRESHOLD_LOYAL)) * 100);
        } else {
            // Already VIP
            $progress['next_tier'] = null;
            $progress['next_tier_name'] = 'Max Tier Achieved';
            $progress['spent_towards_next'] = null;
            $progress['needed_for_next'] = 0;
            $progress['progress_percent'] = 100;
        }

        return $progress;
    }

    /**
     * Get all tiers with thresholds (for admin/info)
     */
    public static function getAllTiers(): array
    {
        return [
            [
                'stage' => self::TIER_REGULAR,
                'name' => 'Regular Member',
                'icon' => '💳',
                'min_spent' => 0,
                'max_spent' => self::THRESHOLD_LOYAL,
                'booking_window' => self::BOOKING_WINDOW[self::TIER_REGULAR],
                'points_multiplier' => self::POINTS_MULTIPLIER[self::TIER_REGULAR],
                'perks' => ['Standar pemesanan', 'Poin reward 1x']
            ],
            [
                'stage' => self::TIER_LOYAL,
                'name' => 'Loyal Member',
                'icon' => '⭐',
                'min_spent' => self::THRESHOLD_LOYAL,
                'max_spent' => self::THRESHOLD_VIP,
                'booking_window' => self::BOOKING_WINDOW[self::TIER_LOYAL],
                'points_multiplier' => self::POINTS_MULTIPLIER[self::TIER_LOYAL],
                'perks' => ['Booking besok (H-1)', 'Poin reward 1.5x', 'Akses reward eksklusif']
            ],
            [
                'stage' => self::TIER_VIP,
                'name' => 'VIP Member',
                'icon' => '👑',
                'min_spent' => self::THRESHOLD_VIP,
                'max_spent' => null,
                'booking_window' => self::BOOKING_WINDOW[self::TIER_VIP],
                'points_multiplier' => self::POINTS_MULTIPLIER[self::TIER_VIP],
                'perks' => ['Booking hingga 14 hari ke depan', 'Poin reward 2x', 'Priority scheduling', 'Exclusive VIP perks']
            ]
        ];
    }

    /**
     * Check if user is eligible for VIP features
     */
    public function isVIP(int $userId): bool
    {
        $user = $this->usersModel->findById($userId);
        return $user && $user['loyalty_stage'] === self::TIER_VIP;
    }

    /**
     * Check if user is at least Loyal
     */
    public function isLoyal(int $userId): bool
    {
        $user = $this->usersModel->findById($userId);
        return $user && $user['loyalty_stage'] >= self::TIER_LOYAL;
    }

    /**
     * Get leaderboard (top spenders)
     */
    public function getLeaderboard(int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            'SELECT user_id, NAME, total_spent, loyalty_stage, reward_points
             FROM users
             WHERE ROLE = "Customer"
             ORDER BY total_spent DESC
             LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
