<?php
/**
 * Helper Functions
 */

function isAdmin() {
    return isset($_SESSION['admin']) && $_SESSION['admin'] === true;
}

function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function formatPrice($price) {
    return '€' . number_format($price, 2);
}

function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
    // Simple validation for international phone numbers
    return preg_match('/^[+]?[0-9\s\-()]+$/', $phone) && strlen($phone) >= 7;
}

function validateDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

function redirect($page = 'home', $params = []) {
    $url = 'index.php?page=' . $page;
    if (!empty($params)) {
        $url .= '&' . http_build_query($params);
    }
    header('Location: ' . $url);
    exit;
}

function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function sendEmail($to, $subject, $message) {
    // In production, use PHPMailer or similar
    // For now, just return true
    $headers = "From: noreply@apartmaivano.com\r\n";
    $headers .= "Reply-To: mmsabalic@gmail.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    // In development, log emails instead of sending
    error_log("Email to: $to, Subject: $subject");
    return true;
}

function getSeasonName($date) {
    $month = (int)date('n', strtotime($date));
    
    if ($month >= 6 && $month <= 8) {
        return 'High Season';
    } elseif (($month >= 4 && $month <= 5) || ($month >= 9 && $month <= 10)) {
        return 'Mid Season';
    } else {
        return 'Low Season';
    }
}

function calculateAge($date) {
    $now = new DateTime();
    $past = new DateTime($date);
    $interval = $now->diff($past);
    
    if ($interval->days == 0) {
        return 'Today';
    } elseif ($interval->days == 1) {
        return 'Yesterday';
    } elseif ($interval->days < 7) {
        return $interval->days . ' days ago';
    } elseif ($interval->days < 30) {
        $weeks = floor($interval->days / 7);
        return $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
    } elseif ($interval->days < 365) {
        $months = floor($interval->days / 30);
        return $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
    } else {
        $years = floor($interval->days / 365);
        return $years . ' year' . ($years > 1 ? 's' : '') . ' ago';
    }
}
