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
    $base = '/SIB/PROJECT-APLIN/router.php';
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
?>
