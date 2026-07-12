<?php
// Set session cookie lifetime to 0 to destroy session when browser closes
if (ini_get('session.cookie_lifetime') !== '0') {
    ini_set('session.cookie_lifetime', 0);
}

session_start();

define('LOGOUT_URL', 'index.php?page=login&action=logout');

// Session activity timeout in seconds (600 seconds / 10 minutes)
$sessionTimeout = 600;

if (isset($_SESSION['user_id'])) {
    $now = time();
    if (isset($_SESSION['last_activity']) && ($now - $_SESSION['last_activity'] > $sessionTimeout)) {
        // Clear and destroy session
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();

        // Detect if API request
        $isApi = (strpos($_SERVER['SCRIPT_NAME'], 'api.php') !== false) || (strpos($_SERVER['REQUEST_URI'], '/api/') !== false);
        
        if ($isApi) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'code' => 401,
                'message' => 'Session expired due to inactivity. Please log in again.',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            exit;
        } else {
            session_start();
            $_SESSION['error'] = 'Sesi Anda telah berakhir karena tidak ada aktivitas selama beberapa saat.';
            header('Location: index.php?page=login');
            exit;
        }
    }
    $_SESSION['last_activity'] = $now;
}

// Inject JavaScript inactivity tracker on HTML responses when user is logged in
$isApiRequest = (strpos($_SERVER['SCRIPT_NAME'], 'api.php') !== false) || (strpos($_SERVER['REQUEST_URI'], '/api/') !== false);
if (!$isApiRequest && isset($_SESSION['user_id'])) {
    ob_start(function ($buffer) use ($sessionTimeout) {
        if (stripos($buffer, '</body>') !== false) {
            $jsTimeoutMs = $sessionTimeout * 1000;
            $jsSnippet = '
<script>
(function() {
    const idleLimit = ' . $jsTimeoutMs . '; // Inactivity limit in milliseconds
    let idleTimeout;

    function getLocalStorageLastActivity() {
        const val = localStorage.getItem("merish_last_activity");
        return val ? parseInt(val, 10) : 0;
    }

    function updateLastActivity() {
        const now = Date.now();
        localStorage.setItem("merish_last_activity", now);
        resetIdleTimer();
    }

    function checkSession() {
        const now = Date.now();
        const lastActivity = getLocalStorageLastActivity();
        const elapsed = now - lastActivity;

        if (elapsed >= idleLimit) {
            window.location.href = "index.php?page=login&action=logout&reason=timeout";
        } else {
            // Reschedule for the remaining time
            clearTimeout(idleTimeout);
            idleTimeout = setTimeout(checkSession, idleLimit - elapsed);
        }
    }

    function resetIdleTimer() {
        clearTimeout(idleTimeout);
        idleTimeout = setTimeout(checkSession, idleLimit);
    }

    // Track user activity in this tab
    const activityEvents = [
        "mousedown", "mousemove", "keypress",
        "scroll", "touchstart", "click"
    ];

    activityEvents.forEach(function(event) {
        let lastTrigger = 0;
        document.addEventListener(event, function() {
            const now = Date.now();
            if (now - lastTrigger > 5000) { // update at most every 5 seconds
                lastTrigger = now;
                updateLastActivity();
            }
        }, true);
    });

    // Set initial activity on page load if not set or if we just had activity
    const lastActivity = getLocalStorageLastActivity();
    if (!lastActivity || Date.now() - lastActivity > 60000) {
        localStorage.setItem("merish_last_activity", Date.now());
    }
    resetIdleTimer();
})();
</script>';
            return str_ireplace('</body>', $jsSnippet . "\n</body>", $buffer);
        }
        return $buffer;
    });
}


$autoloadFile = __DIR__ . '/vendor/autoload.php';

if (file_exists($autoloadFile)) {
    require_once $autoloadFile;
} else {
    // Fallback sederhana jika composer dump-autoload belum dijalankan.
    spl_autoload_register(function (string $class): void {
        $prefix = 'App\\';
        $baseDir = __DIR__ . '/app/';

        if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
            return;
        }

        $relativeClass = substr($class, strlen($prefix));
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require_once $file;
        }
    });
}
