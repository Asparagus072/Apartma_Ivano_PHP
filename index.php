<?php
// Enhanced index.php - With Booking.com import functionality
session_start();

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base paths
define('BASE_PATH', __DIR__);
define('CLASSES_PATH', BASE_PATH . '/classes');
define('PAGES_PATH', BASE_PATH . '/pages');
define('INCLUDES_PATH', BASE_PATH . '/includes');
define('CONFIG_PATH', BASE_PATH . '/config');

// Include configuration
require_once CONFIG_PATH . '/config.php';

// Include required classes
require_once CLASSES_PATH . '/Database.php';
require_once CLASSES_PATH . '/User.php';
require_once CLASSES_PATH . '/BookingManager.php';
require_once CLASSES_PATH . '/PricingManager.php';

// Include functions
require_once INCLUDES_PATH . '/functions.php';

// Initialize database
$database = Database::getInstance();
$db = $database->getConnection();

// Initialize managers
$userManager = new User();
$bookingManager = new BookingManager();
$pricingManager = new PricingManager();

// Get current page
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Check login status
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Handle admin AJAX requests including Booking.com import
if ($page === 'admin' && isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    if (!$isAdmin) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    
    // Handle Booking.com import
    if ($_GET['action'] === 'import_booking_com') {
        $icalUrl = $_POST['ical_url'] ?? '';
        
        if (empty($icalUrl)) {
            echo json_encode(['success' => false, 'message' => 'No URL provided']);
            exit;
        }
        
        // Fetch iCal data with context options for SSL
        $context = stream_context_create([
            "http" => [
                "header" => "User-Agent: Mozilla/5.0 (compatible; ApartmaIvano/1.0)\r\n"
            ],
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ]
        ]);
        
        $icalData = @file_get_contents($icalUrl, false, $context);
        
        if (!$icalData) {
            echo json_encode(['success' => false, 'message' => 'Could not fetch calendar data. Please check the URL.']);
            exit;
        }
        
        // Parse iCal data
        $lines = explode("\n", $icalData);
        $events = [];
        $event = null;
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            if ($line === 'BEGIN:VEVENT') {
                $event = [];
            } elseif ($line === 'END:VEVENT' && $event) {
                if (isset($event['DTSTART']) && isset($event['DTEND'])) {
                    $events[] = $event;
                }
                $event = null;
            } elseif ($event !== null) {
                if (strpos($line, ':') !== false) {
                    list($key, $value) = explode(':', $line, 2);
                    $key = trim($key);
                    
                    // Handle date values
                    if (strpos($key, 'DTSTART') === 0 || strpos($key, 'DTEND') === 0) {
                        $key = substr($key, 0, strpos($key, ';') ?: strlen($key));
                        // Parse date format YYYYMMDD
                        if (preg_match('/(\d{8})/', $value, $matches)) {
                            $value = substr($matches[1], 0, 4) . '-' . 
                                    substr($matches[1], 4, 2) . '-' . 
                                    substr($matches[1], 6, 2);
                        }
                    }
                    
                    $event[$key] = $value;
                }
            }
        }
        
        // Import events as bookings
        $imported = 0;
        $total = 0;
        
        foreach ($events as $event) {
            $uid = $event['UID'] ?? md5($event['DTSTART'] . $event['DTEND']);
            
            // Check if booking already exists
            $stmt = $db->getConnection()->prepare("SELECT id FROM bookings WHERE source_id = ? AND source = 'booking.com'");
            $stmt->execute([$uid]);
            
            if (!$stmt->fetch()) {
                // Parse guest info
                $summary = $event['SUMMARY'] ?? 'Booking.com Guest';
                $description = $event['DESCRIPTION'] ?? '';
                
                // Clean up the summary
                $guestName = 'Booking.com Guest';
                if (strpos($summary, 'BLOCKED') === false && strpos($summary, 'Not available') === false) {
                    $guestName = trim($summary) ?: 'Booking.com Guest';
                }
                
                // Insert new booking
                $stmt = $db->getConnection()->prepare("
                    INSERT INTO bookings (
                        checkin, checkout, guests, name, email, phone,
                        source, source_id, notes, total_price, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
                ");
                
                $stmt->execute([
                    $event['DTSTART'],
                    $event['DTEND'],
                    2, // Default guests
                    $guestName,
                    'via@booking.com',
                    'See Booking.com',
                    'booking.com',
                    $uid,
                    $description,
                    0 // Price updated manually
                ]);
                
                $imported++;
            }
            $total++;
        }
        
        // Save the URL for future use
        $database->setSetting('booking_com_ical_url', $icalUrl);
        $database->setSetting('booking_com_last_sync', date('Y-m-d H:i:s'));
        
        echo json_encode([
            'success' => true,
            'imported' => $imported,
            'total' => $total,
            'message' => "Successfully imported $imported new bookings out of $total total events"
        ]);
        exit;
    }
    
    // Save sync settings
    if ($_GET['action'] === 'save_booking_sync') {
        $icalUrl = $_POST['ical_url'] ?? '';
        $database->setSetting('booking_com_ical_url', $icalUrl);
        $database->setSetting('booking_com_sync_enabled', '1');
        echo json_encode(['success' => true, 'message' => 'Auto-sync enabled']);
        exit;
    }
    
    // Disable sync
    if ($_GET['action'] === 'disable_booking_sync') {
        $database->setSetting('booking_com_sync_enabled', '0');
        echo json_encode(['success' => true, 'message' => 'Auto-sync disabled']);
        exit;
    }
    
    // Get statistics
    if ($_GET['action'] === 'get_stats') {
        $stats = $bookingManager->getBookingStats();
        echo json_encode(['success' => true, 'stats' => $stats]);
        exit;
    }
    
    // Delete booking
    if ($_GET['action'] === 'delete_booking') {
        $bookingId = $_POST['booking_id'] ?? 0;
        if ($bookingManager->deleteBooking($bookingId)) {
            echo json_encode(['success' => true, 'message' => 'Booking deleted']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete booking']);
        }
        exit;
    }
}

// Handle AJAX requests for booking page
if ($page === 'booking' && $_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        switch ($_POST['action']) {
            case 'check_availability':
                $checkin = $_POST['checkin'] ?? '';
                $checkout = $_POST['checkout'] ?? '';
                
                if (empty($checkin) || empty($checkout)) {
                    throw new Exception('Check-in and check-out dates are required');
                }
                
                $available = $bookingManager->isDateAvailable($checkin, $checkout);
                $pricing = $available ? $pricingManager->calculateStayTotal($checkin, $checkout) : null;
                
                echo json_encode([
                    'success' => true,
                    'available' => $available,
                    'pricing' => $pricing
                ]);
                exit;
                
            case 'get_disabled_dates':
                $disabledDates = $bookingManager->getDisabledDates();
                echo json_encode([
                    'success' => true,
                    'disabled_dates' => $disabledDates
                ]);
                exit;
                
            case 'create_booking':
                if (!$isLoggedIn) {
                    throw new Exception('Please login to create a booking');
                }
                
                $required = ['checkin', 'checkout', 'guests', 'name', 'email', 'phone'];
                foreach ($required as $field) {
                    if (empty($_POST[$field])) {
                        throw new Exception("Field '$field' is required");
                    }
                }
                
                $bookingId = $bookingManager->createBooking(
                    $_POST['checkin'],
                    $_POST['checkout'],
                    $_POST['guests'],
                    $_POST['name'],
                    $_POST['email'],
                    $_POST['phone'],
                    $_POST['notes'] ?? '',
                    'direct' // Source
                );
                
                if ($bookingId) {
                    echo json_encode([
                        'success' => true,
                        'booking_id' => $bookingId,
                        'message' => 'Booking created successfully!'
                    ]);
                } else {
                    throw new Exception('Failed to create booking');
                }
                exit;
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
        exit;
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($page) {
        case 'login':
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $userData = $userManager->login($username, $password);
            if ($userData) {
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['username'] = $userData['username'];
                $_SESSION['role'] = $userData['role'] ?? 'user';
                
                // Update last login
                $stmt = $db->getConnection()->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
                $stmt->execute([$userData['id']]);
                
                setFlash('success', 'Login successful! Welcome back, ' . $username);
                header('Location: index.php?page=' . ($userData['role'] === 'admin' ? 'admin' : 'booking'));
                exit;
            } else {
                setFlash('error', 'Invalid username or password');
            }
            break;
            
        case 'register':
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            if ($password !== $confirm_password) {
                setFlash('error', 'Passwords do not match');
            } elseif (strlen($password) < 6) {
                setFlash('error', 'Password must be at least 6 characters');
            } else {
                if ($userManager->register($username, $email, $password)) {
                    setFlash('success', 'Registration successful! Please login.');
                    header('Location: index.php?page=login');
                    exit;
                } else {
                    setFlash('error', 'Registration failed. Username or email may already exist.');
                }
            }
            break;
            
        case 'contact':
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $message = $_POST['message'] ?? '';
            
            if (!empty($name) && !empty($email) && !empty($message)) {
                $stmt = $db->getConnection()->prepare("
                    INSERT INTO contact_messages (name, email, message, created_at) 
                    VALUES (?, ?, ?, CURRENT_TIMESTAMP)
                ");
                if ($stmt->execute([$name, $email, $message])) {
                    setFlash('success', 'Thank you for your message. We will contact you soon!');
                } else {
                    setFlash('error', 'Failed to send message. Please try again.');
                }
            } else {
                setFlash('error', 'Please fill in all fields');
            }
            break;
    }
}

// Handle logout
if ($page === 'logout') {
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
    }
    
    session_destroy();
    session_start();
    setFlash('success', 'You have been logged out successfully');
    header('Location: index.php');
    exit;
}

// Handle profile updates BEFORE any HTML output
if ($page === 'profile' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!$isLoggedIn) {
        setFlash('error', 'Please login to update profile');
        header('Location: index.php?page=login');
        exit;
    }
    
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($_POST['action'] === 'update_profile') {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $full_name = htmlspecialchars($_POST['full_name']);
        $phone = htmlspecialchars($_POST['phone']);
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Please enter a valid email address');
        } else {
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $_SESSION['user_id']]);
            if ($stmt->fetch()) {
                setFlash('error', 'This email is already registered to another account');
            } else {
                $stmt = $db->prepare("UPDATE users SET email = ?, full_name = ?, phone = ? WHERE id = ?");
                if ($stmt->execute([$email, $full_name, $phone, $_SESSION['user_id']])) {
                    setFlash('success', 'Profile updated successfully!');
                } else {
                    setFlash('error', 'Failed to update profile');
                }
            }
        }
        header('Location: index.php?page=profile');
        exit;
    } elseif ($_POST['action'] === 'change_password') {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (!password_verify($current_password, $user['password'])) {
            setFlash('error', 'Current password is incorrect');
        } elseif (strlen($new_password) < 6) {
            setFlash('error', 'New password must be at least 6 characters long');
        } elseif ($new_password !== $confirm_password) {
            setFlash('error', 'New passwords do not match');
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($stmt->execute([$hashed_password, $_SESSION['user_id']])) {
                setFlash('success', 'Password changed successfully!');
            } else {
                setFlash('error', 'Failed to change password');
            }
        }
        header('Location: index.php?page=profile');
        exit;
    }
}

// Rest of your existing HTML structure remains the same...
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst($page); ?> - Apartma Ivano</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Flatpickr for date picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="static/css/style.css">
    <?php if ($page === 'booking'): ?>
    <link rel="stylesheet" href="static/css/booking.css">
    <?php endif; ?>
    
    <style>
        :root {
            --primary-color: #2E7D32;
            --secondary-color: #1976D2;
            --success-color: #28a745;
            --danger-color: #dc3545;
        }
        
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .navbar {
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color)) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .main-content {
            flex: 1;
            padding: 2rem 0;
        }
        
        .footer {
            background-color: #343a40;
            color: white;
            padding: 2rem 0;
            margin-top: auto;
        }
        
        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-home"></i> Apartma Ivano
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page === 'home' ? 'active' : ''; ?>" href="index.php">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page === 'about' ? 'active' : ''; ?>" href="index.php?page=about">
                            <i class="fas fa-info-circle"></i> About
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page === 'contact' ? 'active' : ''; ?>" href="index.php?page=contact">
                            <i class="fas fa-envelope"></i> Contact
                        </a>
                    </li>
                    
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page === 'booking' ? 'active' : ''; ?>" href="index.php?page=booking">
                                <i class="fas fa-calendar-check"></i> Book Now
                            </a>
                        </li>
                        <?php if ($isAdmin): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $page === 'admin' ? 'active' : ''; ?>" href="index.php?page=admin">
                                    <i class="fas fa-cog"></i> Admin
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="index.php?page=profile">
                                    <i class="fas fa-user-circle"></i> Profile
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="index.php?page=logout">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page === 'login' ? 'active' : ''; ?>" href="index.php?page=login">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page === 'register' ? 'active' : ''; ?>" href="index.php?page=register">
                                <i class="fas fa-user-plus"></i> Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php
    $flashMessages = getFlash();
    if (!empty($flashMessages)):
    ?>
    <div class="container mt-3">
        <?php foreach ($flashMessages as $type => $message): ?>
        <div class="alert alert-<?php echo $type === 'error' ? 'danger' : $type; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="main-content">
        <?php
        // Include the appropriate page
        $pageFile = PAGES_PATH . '/' . $page . '.php';
        
        if (file_exists($pageFile)) {
            // Special handling for admin page - check permissions
            if ($page === 'admin' && !$isAdmin) {
                setFlash('error', 'You must be an admin to access this page');
                header('Location: index.php');
                exit;
            }
            
            // Special handling for booking page - require login
            if ($page === 'booking' && !$isLoggedIn) {
                setFlash('error', 'Please login to make a booking');
                header('Location: index.php?page=login');
                exit;
            }
            
            include $pageFile;
        } else {
            // Show 404 page
            include PAGES_PATH . '/404.php';
        }
        ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="fas fa-home"></i> Apartma Ivano</h5>
                    <p>Your perfect holiday destination in beautiful Slovenia</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white-50">Home</a></li>
                        <li><a href="index.php?page=about" class="text-white-50">About</a></li>
                        <li><a href="index.php?page=contact" class="text-white-50">Contact</a></li>
                        <li><a href="index.php?page=booking" class="text-white-50">Book Now</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Info</h5>
                    <p class="text-white-50">
                        <i class="fas fa-map-marker-alt"></i> Kranj, Slovenia<br>
                        <i class="fas fa-phone"></i> +386 XX XXX XXX<br>
                        <i class="fas fa-envelope"></i> info@apartmaivano.com
                    </p>
                </div>
            </div>
            <hr class="bg-white">
            <div class="text-center">
                <p class="text-white-50">&copy; 2024 Apartma Ivano. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <?php if ($page === 'booking'): ?>
    <script src="static/js/booking.js"></script>
    <?php endif; ?>
    
    <?php if ($page === 'admin'): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="static/js/admin.js"></script>
    <?php endif; ?>
    
    <script src="static/js/script.js"></script>
</body>
</html>