<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration
define('DB_PATH', 'database/apartma.db');
define('SITE_NAME', 'Apartma Ivano');
define('MAX_GUESTS', 6);
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', '$2y$10$yOI0Ufb5GZXAuER6dBh.HOdEuw8vwMp.asGwHIoyQcqOIIOeIdDV6');

// Initialize classes
require_once 'classes/Database.php';
require_once 'includes/functions.php';
require_once 'includes/Lang.php';
require_once 'classes/BookingManager.php';

// Boot language (must happen before any output)
Lang::init();

$db = new Database();
$booking = new BookingSystem($db);

// Handle AJAX requests
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');

    switch ($_GET['ajax']) {
        case 'check_availability':
            $checkin  = $_POST['checkin']  ?? '';
            $checkout = $_POST['checkout'] ?? '';

            $available = $booking->checkAvailability($checkin, $checkout);
            $price     = $booking->calculatePrice($checkin, $checkout);

            echo json_encode([
                'available' => $available,
                'price'     => $price,
                'nights'    => $booking->getNights($checkin, $checkout),
            ]);
            exit;

        case 'get_bookings':
            echo json_encode($booking->getAllBookings());
            exit;

        case 'delete_booking':
            if (!isAdmin()) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }
            $id = $_POST['id'] ?? 0;
            echo json_encode(['success' => $booking->deleteBooking($id)]);
            exit;
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'book':
            $result = $booking->createBooking(
                $_POST['checkin'],
                $_POST['checkout'],
                $_POST['guests'],
                $_POST['name'],
                $_POST['email'],
                $_POST['phone'],
                $_POST['notes'] ?? ''
            );

            if ($result) {
                $_SESSION['flash'] = ['success' => __('flash.book_ok')];
                header('Location: index.php?page=success&id=' . $result);
            } else {
                $_SESSION['flash'] = ['error' => __('flash.book_fail')];
                header('Location: index.php?page=booking');
            }
            exit;

        case 'login':
            if ($_POST['username'] === ADMIN_USER && password_verify($_POST['password'], ADMIN_PASS)) {
                $_SESSION['admin'] = true;
                header('Location: index.php?page=admin');
            } else {
                $_SESSION['flash'] = ['error' => __('flash.login_fail')];
                header('Location: index.php?page=login');
            }
            exit;

        case 'logout':
            session_destroy();
            header('Location: index.php');
            exit;

        case 'contact':
            $name    = sanitize($_POST['name']);
            $email   = sanitize($_POST['email']);
            $message = sanitize($_POST['message']);

            $_SESSION['flash'] = ['success' => __('flash.msg_ok')];
            header('Location: index.php?page=contact');
            exit;
    }
}

// Current page
$page      = $_GET['page'] ?? 'home';
$pageTitle = ucfirst($page) . ' - ' . SITE_NAME;

// Flash messages
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Language switcher helper — builds a URL for a target language
function langUrl(string $code): string {
    $params = $_GET;
    $params['lang'] = $code;
    return 'index.php?' . http_build_query($params);
}
?>
<!DOCTYPE html>
<html lang="<?php echo Lang::current(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2E7D32;
            --secondary: #1976D2;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f8f9fa;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.3rem;
        }

        .hero {
            background: linear-gradient(135deg, rgba(46,125,50,.9), rgba(25,118,210,.9)),
                        url('https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=1600') center/cover;
            color: white;
            padding: 100px 0;
            margin-bottom: 3rem;
        }

        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,.1);
            transition: transform .3s;
        }

        .card:hover { transform: translateY(-5px); }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
        }

        .price-badge {
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 600;
        }

        .footer {
            background: #343a40;
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }

        .booking-calendar {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,.1);
        }

        .flatpickr-day.disabled {
            background: #ffcccc !important;
            color: #999 !important;
        }

        /* Language switcher */
        .lang-switcher .dropdown-toggle {
            color: rgba(255,255,255,.85);
            font-size: 0.9rem;
            padding: 0.375rem 0.5rem;
        }
        .lang-switcher .dropdown-toggle:hover,
        .lang-switcher .dropdown-toggle:focus {
            color: #fff;
        }
        .lang-switcher .dropdown-menu {
            min-width: 9rem;
            padding: 0.25rem 0;
        }
        .lang-switcher .dropdown-item {
            font-size: 0.875rem;
            padding: 0.4rem 1rem;
        }
        .lang-switcher .dropdown-item.active,
        .lang-switcher .dropdown-item:active {
            background-color: var(--primary);
        }
        .lang-flag { margin-right: 0.35rem; }
    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-home"></i> <?php echo SITE_NAME; ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link <?php echo $page === 'home'    ? 'active' : ''; ?>" href="index.php"><?php echo __('nav.home'); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $page === 'booking' ? 'active' : ''; ?>" href="?page=booking"><?php echo __('nav.book'); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $page === 'about'   ? 'active' : ''; ?>" href="?page=about"><?php echo __('nav.about'); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $page === 'contact' ? 'active' : ''; ?>" href="?page=contact"><?php echo __('nav.contact'); ?></a>
                </li>
                <?php if (isAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $page === 'admin' ? 'active' : ''; ?>" href="?page=admin"><?php echo __('nav.admin'); ?></a>
                </li>
                <li class="nav-item">
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="action" value="logout">
                        <button type="submit" class="nav-link btn btn-link"><?php echo __('nav.logout'); ?></button>
                    </form>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $page === 'login' ? 'active' : ''; ?>" href="?page=login"><?php echo __('nav.login'); ?></a>
                </li>
                <?php endif; ?>

                <!-- Language Switcher -->
                <li class="nav-item dropdown lang-switcher ms-lg-2">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo Lang::flag(Lang::current()); ?>
                        <span class="d-lg-none ms-1"><?php echo Lang::nativeName(Lang::current()); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php foreach (Lang::supported() as $code): ?>
                        <li>
                            <a class="dropdown-item <?php echo Lang::current() === $code ? 'active' : ''; ?>"
                               href="<?php echo htmlspecialchars(langUrl($code)); ?>">
                                <span class="lang-flag"><?php echo Lang::flag($code); ?></span>
                                <?php echo Lang::nativeName($code); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Flash Messages -->
<?php if ($flash): ?>
<div class="container mt-3">
    <?php foreach ($flash as $type => $message): ?>
    <div class="alert alert-<?php echo $type === 'error' ? 'danger' : $type; ?> alert-dismissible fade show">
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Main Content -->
<?php
switch ($page) {
    case 'home':
        include 'pages/home.php';
        break;
    case 'booking':
        include 'pages/booking.php';
        break;
    case 'about':
        include 'pages/about.php';
        break;
    case 'contact':
        include 'pages/contact.php';
        break;
    case 'admin':
        if (!isAdmin()) {
            echo '<div class="container mt-5"><div class="alert alert-danger">Access denied</div></div>';
        } else {
            include 'pages/admin.php';
        }
        break;
    case 'login':
        include 'pages/login.php';
        break;
    case 'success':
        include 'pages/success.php';
        break;
    default:
        echo '<div class="container mt-5 text-center"><h1>404 - Page Not Found</h1></div>';
}
?>

<!-- Footer -->
<?php if ($page !== 'login' && $page !== 'admin'): ?>
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><?php echo SITE_NAME; ?></h5>
                    <p><?php echo __('footer.tagline'); ?></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-1"><i class="fas fa-phone"></i> +386 40 695 807</p>
                    <p class="mb-1"><i class="fas fa-envelope"></i> mmsabalic@gmail.com</p>
                    <p><i class="fas fa-map-marker-alt"></i> Srednja vas v Bohinju, Slovenia</p>
                </div>
            </div>
            <hr class="bg-white">
            <div class="text-center">
                <small>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</small>
            </div>
        </div>
    </footer>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="static/js/main.js"></script>
</body>
</html>