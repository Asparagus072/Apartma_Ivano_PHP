<?php
// includes/functions.php - Helper functions with null handling fixed

function setFlash($type, $message) {
    if (!isset($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }
    $_SESSION['flash'][$type] = $message;
}

function getFlash($type = null) {
    if (!isset($_SESSION['flash'])) {
        return $type ? null : [];
    }
    
    if ($type) {
        $message = $_SESSION['flash'][$type] ?? null;
        unset($_SESSION['flash'][$type]);
        return $message;
    }
    
    $flash = $_SESSION['flash'] ?? [];
    $_SESSION['flash'] = [];
    return $flash;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function startSecureSession() {
    // Configure session settings
    ini_set('session.cookie_lifetime', 86400 * 30);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check for remember token
    if (isset($_COOKIE['remember_token']) && !isset($_SESSION['user_id'])) {
        autoLogin($_COOKIE['remember_token']);
    }
}

function createRememberToken($userId) {
    $token = bin2hex(random_bytes(32));
    $hashedToken = hash('sha256', $token);
    
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("
        INSERT OR REPLACE INTO remember_tokens (user_id, token_hash, expires_at) 
        VALUES (?, ?, ?)
    ");
    $expiresAt = date('Y-m-d H:i:s', time() + (86400 * 30));
    $stmt->execute([$userId, $hashedToken, $expiresAt]);
    
    setcookie('remember_token', $token, time() + (86400 * 30), '/', '', isset($_SERVER['HTTPS']), true);
    
    return $token;
}

function autoLogin($token) {
    $db = Database::getInstance()->getConnection();
    $hashedToken = hash('sha256', $token);
    
    $stmt = $db->prepare("
        SELECT u.id, u.username, u.email, u.role 
        FROM users u 
        JOIN remember_tokens rt ON u.id = rt.user_id 
        WHERE rt.token_hash = ? AND rt.expires_at > datetime('now')
    ");
    $stmt->execute([$hashedToken]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        return true;
    }
    
    return false;
}

function logout() {
    // Clear remember token if exists
    if (isset($_COOKIE['remember_token'])) {
        $db = Database::getInstance()->getConnection();
        $hashedToken = hash('sha256', $_COOKIE['remember_token']);
        $stmt = $db->prepare("DELETE FROM remember_tokens WHERE token_hash = ?");
        $stmt->execute([$hashedToken]);
        
        setcookie('remember_token', '', time() - 3600, '/');
    }
    
    // Clear session
    $_SESSION = array();
    
    // Destroy session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
}

/**
 * Safe htmlspecialchars wrapper that handles null values
 */
function safe_htmlspecialchars($string, $flags = ENT_QUOTES, $encoding = 'UTF-8', $double_encode = true) {
    // Handle null, false, and other non-string values
    if ($string === null || $string === false) {
        return '';
    }
    
    // Convert to string if it's not already
    $string = (string) $string;
    
    return htmlspecialchars($string, $flags, $encoding, $double_encode);
}

/**
 * Render a page with the main template
 * Fixed to handle null values properly
 */
function renderPage($title, $content) {
    global $flashMessages, $currentUser, $isLoggedIn, $isAdmin;
    
    // Ensure variables are not null
    $title = $title ?? '';
    $currentUser = $currentUser ?? '';
    
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo safe_htmlspecialchars($title); ?> – Apartma Ivano</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
        .hero { background: linear-gradient(rgba(46, 125, 50, 0.7), rgba(46, 125, 50, 0.7)), #4CAF50; }
        .navbar { background: linear-gradient(90deg, #2E7D32, #1976D2) !important; }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="?page=home">Apartma Ivano</a>
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="?page=home">Home</a>
                    <a class="nav-link" href="?page=about">About</a>
                    <a class="nav-link" href="?page=contact">Contact</a>
                    <a class="nav-link" href="?page=booking">Book</a>

                    <?php if ($isAdmin): ?>
                        <a class="nav-link" href="?page=admin">Admin</a>
                    <?php endif; ?>

                    <?php if ($isLoggedIn): ?>
                        <a class="nav-link" href="?page=profile">Profile</a>
                        <a class="nav-link" href="?page=logout">Logout<?php echo $currentUser ? ' (' . safe_htmlspecialchars($currentUser) . ')' : ''; ?></a>
                    <?php else: ?>
                        <a class="nav-link" href="?page=login">Login</a>
                        <a class="nav-link" href="?page=register">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

        <?php if (!empty($flashMessages)): ?>
            <div class="container mt-3">
                <?php foreach ($flashMessages as $type => $message): ?>
                    <div class="alert alert-<?php echo $type === 'error' ? 'danger' : safe_htmlspecialchars($type); ?> alert-dismissible fade show">
                        <?php echo safe_htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <main class="container my-4">
            <?php echo $content; ?>
        </main>

        <footer class="bg-light text-center py-3 mt-4">
            <p>&copy; 2025 Apartma Ivano – Ivano Sabalič</p>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    </body>
    </html>
    <?php
}

/**
 * Render a template from /templates directory
 * Fixed to handle null values properly
 */
function renderTemplate(string $template, $vars = [], bool $return = false) {
    // Normalize and check actual path
    $baseDir = __DIR__ . '/../templates/';
    $path = $baseDir . $template . '.php';
    
    if (!file_exists($path)) {
        $alt = $baseDir . strtolower($template) . '.php';
        if (file_exists($alt)) {
            $path = $alt;
        } else {
            error_log("Template not found: {$path}");
            if (function_exists('setFlash')) {
                setFlash('error', 'Requested page is temporarily unavailable.');
            }
            return $return ? '' : null;
        }
    }

    // Handle different input types
    if (is_string($vars)) {
        $vars = ['content' => $vars];
    } elseif (!is_array($vars)) {
        $vars = (array) $vars;
    }

    // Extract variables
    extract($vars, EXTR_SKIP);
    
    // Start output buffering
    ob_start();
    include $path;
    $output = ob_get_clean();

    if ($return) {
        return $output;
    }

    echo $output;
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    if ($data === null) {
        return '';
    }
    
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Validate email
 */
function validateEmail($email) {
    if ($email === null || $email === '') {
        return false;
    }
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Format date for display
 */
function formatDate($date, $format = 'M d, Y') {
    if (empty($date)) {
        return '';
    }
    return date($format, strtotime($date));
}

/**
 * Format price for display
 */
function formatPrice($price) {
    if ($price === null || $price === '') {
        return '€0.00';
    }
    return '€' . number_format((float)$price, 2);
}

/**
 * Get current page from URL
 */
function getCurrentPage() {
    return $_GET['page'] ?? 'home';
}

/**
 * Check if current page matches
 */
function isCurrentPage($page) {
    return getCurrentPage() === $page;
}

/**
 * Redirect to a page
 */
function redirect($page = 'home', $params = []) {
    $url = 'index.php?page=' . $page;
    if (!empty($params)) {
        $url .= '&' . http_build_query($params);
    }
    header('Location: ' . $url);
    exit;
}