<?php
/**
 * Helper.php - Global helper functions for views
 */

/**
 * Generate URL for routing via router.php
 * @param string $route Route name
 * @return string Full URL
 */
function url($route = '')
{
    $base = '/SIB/PROJECT-APLIN/index.php';
    if (empty($route)) {
        return $base;
    }
    return $base . '?route=' . urlencode($route);
}

/**
 * Generate POST form URL
 * @param string $route Route name
 * @return string Full URL for form action
 */
function form_url($route)
{
    return url($route);
}

/**
 * Check if user is authenticated
 * @return bool
 */
function is_authenticated()
{
    return isset($_SESSION['user_id']);
}

/**
 * Get current user data
 * @return array|null
 */
function current_user()
{
    return isset($_SESSION['user_id']) ? [
        'id' => $_SESSION['user_id'],
        'email' => $_SESSION['email'] ?? null,
        'role' => $_SESSION['role'] ?? null,
        'full_name' => $_SESSION['full_name'] ?? null,
    ] : null;
}

/**
 * Get current user role
 * @return string|null
 */
function user_role()
{
    return $_SESSION['role'] ?? null;
}

/**
 * Get dashboard route based on user role
 * @return string
 */
function get_dashboard_route()
{
    $role = user_role();
    
    return match($role) {
        'admin' => 'admin',
        'receptionist' => 'receptionist',
        'barista' => 'barista',
        'beautician' => 'beautician',
        'customer' => 'customer',
        default => 'home',
    };
}

/**
 * Check if user has specific role
 * @param string|array $roles
 * @return bool
 */
function has_role($roles)
{
    if (!is_authenticated()) {
        return false;
    }
    
    $userRole = user_role();
    $roles = (array) $roles;
    
    return in_array($userRole, $roles);
}

/**
 * Format currency (IDR)
 * @param int|float $amount
 * @return string
 */
function format_currency($amount)
{
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Format date (Indonesian)
 * @param string $date
 * @return string
 */
function format_date($date)
{
    $months = [
        'January' => 'Januari',
        'February' => 'Februari',
        'March' => 'Maret',
        'April' => 'April',
        'May' => 'Mei',
        'June' => 'Juni',
        'July' => 'Juli',
        'August' => 'Agustus',
        'September' => 'September',
        'October' => 'Oktober',
        'November' => 'November',
        'December' => 'Desember',
    ];
    
    $dateObj = new DateTime($date);
    $monthName = $dateObj->format('F');
    $indonesianMonth = $months[$monthName] ?? $monthName;
    
    return $dateObj->format('d') . ' ' . $indonesianMonth . ' ' . $dateObj->format('Y');
}

/**
 * Format time
 * @param string $time
 * @return string
 */
function format_time($time)
{
    $timeObj = new DateTime($time);
    return $timeObj->format('H:i');
}

/**
 * Get status badge HTML
 * @param string $status
 * @return string
 */
function get_status_badge($status)
{
    $statusMap = [
        'pending' => ['bg' => '#f39c12', 'text' => 'Pending'],
        'in-progress' => ['bg' => '#3498db', 'text' => 'In Progress'],
        'completed' => ['bg' => '#27ae60', 'text' => 'Completed'],
        'cancelled' => ['bg' => '#e74c3c', 'text' => 'Cancelled'],
        'active' => ['bg' => '#27ae60', 'text' => 'Active'],
        'inactive' => ['bg' => '#95a5a6', 'text' => 'Inactive'],
    ];
    
    $statusLower = strtolower($status);
    $statusData = $statusMap[$statusLower] ?? ['bg' => '#95a5a6', 'text' => $status];
    
    return sprintf(
        '<span style="background-color: %s; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500;">%s</span>',
        $statusData['bg'],
        $statusData['text']
    );
}

/**
 * Sanitize user input
 * @param string $input
 * @return string
 */
function sanitize($input)
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to URL
 * @param string $url
 * @param int $statusCode
 * @return void
 */
function redirect($url, $statusCode = 302)
{
    header("Location: $url", true, $statusCode);
    exit;
}

/**
 * Set session message
 * @param string $key
 * @param string $message
 * @param string $type (success, error, info, warning)
 * @return void
 */
function set_message($key, $message, $type = 'info')
{
    $_SESSION['message'][$key] = [
        'text' => $message,
        'type' => $type,
    ];
}

/**
 * Get and clear session message
 * @param string $key
 * @return array|null
 */
function get_message($key)
{
    if (isset($_SESSION['message'][$key])) {
        $message = $_SESSION['message'][$key];
        unset($_SESSION['message'][$key]);
        return $message;
    }
    return null;
}

