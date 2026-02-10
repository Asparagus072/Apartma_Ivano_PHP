<?php
function handleRoutes($route, $method, $userManager, $bookingManager) {
    global $flashMessages, $currentUser, $isLoggedIn, $isAdmin;
    
    switch ($route) {
        case 'home':
            include 'pages/index.php';
            break;
        case 'about':
            include 'pages/about.php';
            break;
        case 'contact':
            include 'pages/contact.php';
            break;
        case 'login':
            include 'pages/login.php';
            break;
        case 'register':
            include 'pages/register.php';
            break;
        case 'logout':
            logout();
            setFlash('success', 'You have been logged out.');
            header('Location: ?page=home');
            exit;
        case 'booking':
            include 'pages/booking.php';
            break;
        case 'booking-summary':
            include 'pages/booking_summary.php';
            break;
        case 'admin':
            include 'pages/admin.php';
            break;
        case 'calculate-price':
            include 'pages/calculate-price.php';
            break;
        default:
            http_response_code(404);
            include 'pages/404.php';
            break;
    }
}
?>