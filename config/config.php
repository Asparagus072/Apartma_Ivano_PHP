<?php
// config/config.php - Main configuration file

// Database configuration
class Config {
    // Database settings
    const DB_NAME = 'database/apartma_ivano.db';  // SQLite database file
    const DB_TYPE = 'sqlite';
    
    // Site settings
    const SITE_NAME = 'Apartma Ivano';
    const SITE_URL = 'http://localhost/apartma_ivano';
    const ADMIN_EMAIL = 'admin@apartmaivano.com';
    
    // Booking settings
    const MAX_GUESTS = 6;
    const MIN_STAY_NIGHTS = 1;
    const MAX_STAY_NIGHTS = 30;
    const BOOKING_ADVANCE_DAYS = 365;  // How far in advance can bookings be made
    
    // Pricing settings (in EUR)
    const BASE_PRICE_LOW_SEASON = 80;
    const BASE_PRICE_MID_SEASON = 100;
    const BASE_PRICE_HIGH_SEASON = 130;
    const BASE_PRICE_PEAK_SEASON = 150;
    
    // Discounts
    const DISCOUNT_3_NIGHTS = 0.05;   // 5% discount for 3+ nights
    const DISCOUNT_7_NIGHTS = 0.10;   // 10% discount for 7+ nights
    const DISCOUNT_14_NIGHTS = 0.15;  // 15% discount for 14+ nights
    
    // Email settings (for PHPMailer or similar)
    const SMTP_HOST = 'smtp.gmail.com';
    const SMTP_PORT = 587;
    const SMTP_USERNAME = 'your-email@gmail.com';
    const SMTP_PASSWORD = 'your-app-password';
    const SMTP_FROM_EMAIL = 'noreply@apartmaivano.com';
    const SMTP_FROM_NAME = 'Apartma Ivano';
    
    // Session settings
    const SESSION_LIFETIME = 86400; // 24 hours in seconds
    const SESSION_NAME = 'apartma_ivano_session';
    
    // Upload settings
    const UPLOAD_MAX_SIZE = 5242880; // 5MB in bytes
    const UPLOAD_ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    const UPLOAD_PATH = 'uploads/';
    
    // Timezone
    const TIMEZONE = 'Europe/Ljubljana';
    
    // Development/Production mode
    const DEBUG_MODE = true; // Set to false in production
    
    // API Keys (if integrating with external services)
    const GOOGLE_MAPS_API_KEY = '';
    const BOOKING_COM_API_KEY = '';
    const STRIPE_PUBLIC_KEY = '';
    const STRIPE_SECRET_KEY = '';
    
    // Security
    const CSRF_TOKEN_NAME = 'csrf_token';
    const PASSWORD_MIN_LENGTH = 6;
    const LOGIN_MAX_ATTEMPTS = 5;
    const LOGIN_LOCKOUT_TIME = 900; // 15 minutes in seconds
    
    // Pagination
    const ITEMS_PER_PAGE = 10;
    
    // Cache settings
    const CACHE_ENABLED = false;
    const CACHE_LIFETIME = 3600; // 1 hour in seconds
    
    /**
     * Initialize configuration
     */
    public static function init() {
        // Set timezone
        date_default_timezone_set(self::TIMEZONE);
        
        // Set error reporting based on debug mode
        if (self::DEBUG_MODE) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        } else {
            error_reporting(0);
            ini_set('display_errors', 0);
        }
        
        // Create necessary directories if they don't exist
        $directories = [
            'database',
            'uploads',
            'uploads/rooms',
            'uploads/reviews',
            'logs',
            'cache',
            'backups'
        ];
        
        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
        
        // Create .htaccess for security in sensitive directories
        $protectedDirs = ['database', 'logs', 'cache', 'backups'];
        foreach ($protectedDirs as $dir) {
            $htaccessFile = $dir . '/.htaccess';
            if (!file_exists($htaccessFile)) {
                file_put_contents($htaccessFile, "Deny from all");
            }
        }
    }
    
    /**
     * Get configuration value
     */
    public static function get($key, $default = null) {
        if (defined('self::' . $key)) {
            return constant('self::' . $key);
        }
        return $default;
    }
    
    /**
     * Check if in debug mode
     */
    public static function isDebug() {
        return self::DEBUG_MODE;
    }
    
    /**
     * Get database connection string
     */
    public static function getDatabaseDSN() {
        if (self::DB_TYPE === 'sqlite') {
            return 'sqlite:' . self::DB_NAME;
        }
        // Add support for MySQL if needed
        // return 'mysql:host=localhost;dbname=apartma_ivano;charset=utf8mb4';
    }
    
    /**
     * Get season for a given date
     */
    public static function getSeason($date) {
        $month = date('n', strtotime($date));
        
        // Peak season: Christmas/New Year, Easter (approximate)
        $dateStr = date('m-d', strtotime($date));
        if ($dateStr >= '12-20' || $dateStr <= '01-07') {
            return 'peak';
        }
        
        // High season: June-August
        if ($month >= 6 && $month <= 8) {
            return 'high';
        }
        
        // Mid season: April-May, September-October
        if (($month >= 4 && $month <= 5) || ($month >= 9 && $month <= 10)) {
            return 'mid';
        }
        
        // Low season: January-March, November-December
        return 'low';
    }
    
    /**
     * Get price for a season
     */
    public static function getSeasonPrice($season) {
        $prices = [
            'low' => self::BASE_PRICE_LOW_SEASON,
            'mid' => self::BASE_PRICE_MID_SEASON,
            'high' => self::BASE_PRICE_HIGH_SEASON,
            'peak' => self::BASE_PRICE_PEAK_SEASON
        ];
        
        return $prices[$season] ?? self::BASE_PRICE_MID_SEASON;
    }
    
    /**
     * Calculate discount based on number of nights
     */
    public static function getDiscount($nights) {
        if ($nights >= 14) {
            return self::DISCOUNT_14_NIGHTS;
        } elseif ($nights >= 7) {
            return self::DISCOUNT_7_NIGHTS;
        } elseif ($nights >= 3) {
            return self::DISCOUNT_3_NIGHTS;
        }
        return 0;
    }
    
    /**
     * Format price for display
     */
    public static function formatPrice($price) {
        return '€' . number_format($price, 2);
    }
    
    /**
     * Validate email address
     */
    public static function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Generate random token
     */
    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }
    
    /**
     * Sanitize input
     */
    public static function sanitize($input, $type = 'string') {
        switch ($type) {
            case 'email':
                return filter_var($input, FILTER_SANITIZE_EMAIL);
            case 'int':
                return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
            case 'float':
                return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            case 'url':
                return filter_var($input, FILTER_SANITIZE_URL);
            default:
                return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        }
    }
}

// Initialize configuration when file is included
Config::init();