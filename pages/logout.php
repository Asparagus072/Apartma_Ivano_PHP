<?php

// If not logged in, redirect to home
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Handle logout confirmation
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    // Clear remember token if exists
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
        
        // Remove from database
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM remember_tokens WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    }
    
    // Update last login time
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    
    // Clear session data
    $username = $_SESSION['username'];
    $_SESSION = array();
    
    // Destroy the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
    
    // Start new session for flash message
    session_start();
    setFlash('success', 'You have been logged out successfully. Goodbye, ' . $username . '!');
    header('Location: index.php');
    exit;
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-sign-out-alt" style="font-size: 4rem; color: #dc3545;"></i>
                    </div>
                    
                    <h3 class="mb-3">Logout Confirmation</h3>
                    
                    <p class="text-muted mb-4">
                        Hi <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>,<br>
                        Are you sure you want to logout?
                    </p>
                    
                    <div class="alert alert-warning">
                        <small>
                            <i class="fas fa-info-circle"></i> 
                            Any unsaved changes will be lost when you logout.
                        </small>
                    </div>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="index.php?page=logout&confirm=yes" class="btn btn-danger btn-lg">
                            <i class="fas fa-sign-out-alt"></i> Yes, Logout
                        </a>
                        <a href="javascript:history.back()" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                    
                    <hr class="my-4">
                    
                    <p class="text-muted small mb-0">
                        Your session will remain active if you cancel.<br>
                        For security, always logout when using a shared computer.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.gap-3 {
    gap: 1rem !important;
}
</style>