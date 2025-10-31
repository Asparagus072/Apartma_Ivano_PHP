<?php
// classes/Database.php - Updated with settings management and import support

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            $this->connection = new PDO('sqlite:' . Config::DB_NAME);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->initTables();
            $this->initDefaultData();
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            die('Database connection failed. Please check the configuration.');
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    private function initTables() {
        $queries = [
            // Enhanced bookings table with source tracking
            "CREATE TABLE IF NOT EXISTS bookings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                checkin TEXT NOT NULL,
                checkout TEXT NOT NULL,
                guests INTEGER NOT NULL DEFAULT 1,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                phone TEXT,
                notes TEXT,
                source TEXT DEFAULT 'direct',
                source_id TEXT,
                total_price REAL DEFAULT 0,
                deposit_paid REAL DEFAULT 0,
                status TEXT DEFAULT 'confirmed',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )",
            
            // Users table with role support
            "CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                email TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                full_name TEXT,
                phone TEXT,
                role TEXT DEFAULT 'user',
                email_verified INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                last_login DATETIME
            )",
            
            // Remember tokens for persistent login
            "CREATE TABLE IF NOT EXISTS remember_tokens (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                token_hash TEXT NOT NULL,
                expires_at DATETIME NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
            )",
            
            // Enhanced pricing rules
            "CREATE TABLE IF NOT EXISTS pricing_rules (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                start_date TEXT NOT NULL,
                end_date TEXT NOT NULL,
                price_per_night REAL NOT NULL,
                season TEXT NOT NULL,
                description TEXT,
                is_special_event INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )",
            
            // Cancelled bookings for reporting
            "CREATE TABLE IF NOT EXISTS cancelled_bookings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                original_id INTEGER NOT NULL,
                checkin TEXT NOT NULL,
                checkout TEXT NOT NULL,
                guests INTEGER NOT NULL,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                phone TEXT,
                total_price REAL,
                cancellation_reason TEXT,
                cancelled_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )",
            
            // Contact messages
            "CREATE TABLE IF NOT EXISTS contact_messages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                phone TEXT,
                subject TEXT,
                message TEXT NOT NULL,
                is_read INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )",
            
            // Site settings - IMPORTANT for import functionality
            "CREATE TABLE IF NOT EXISTS site_settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                setting_key TEXT UNIQUE NOT NULL,
                setting_value TEXT,
                description TEXT,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )",
            
            // Activity log
            "CREATE TABLE IF NOT EXISTS activity_log (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                action TEXT NOT NULL,
                details TEXT,
                ip_address TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users (id)
            )",
            
            // Reviews table
            "CREATE TABLE IF NOT EXISTS reviews (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                booking_id INTEGER,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                rating INTEGER NOT NULL CHECK (rating >= 1 AND rating <= 5),
                title TEXT,
                comment TEXT,
                is_approved INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (booking_id) REFERENCES bookings (id)
            )",
            
            // Import log for tracking sync history
            "CREATE TABLE IF NOT EXISTS import_log (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                source TEXT NOT NULL,
                imported_count INTEGER DEFAULT 0,
                total_count INTEGER DEFAULT 0,
                status TEXT,
                error_message TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )"
        ];

        foreach ($queries as $query) {
            try {
                $this->connection->exec($query);
            } catch (PDOException $e) {
                error_log("Error creating table: " . $e->getMessage());
            }
        }
        
        // Add indices for better performance
        $this->addIndices();
        
        // Ensure required columns exist (for upgrades)
        $this->ensureColumns();
    }
    
    private function addIndices() {
        $indices = [
            "CREATE INDEX IF NOT EXISTS idx_bookings_dates ON bookings (checkin, checkout)",
            "CREATE INDEX IF NOT EXISTS idx_bookings_status ON bookings (status)",
            "CREATE INDEX IF NOT EXISTS idx_bookings_source ON bookings (source)",
            "CREATE INDEX IF NOT EXISTS idx_bookings_created ON bookings (created_at)",
            "CREATE INDEX IF NOT EXISTS idx_users_email ON users (email)",
            "CREATE INDEX IF NOT EXISTS idx_users_role ON users (role)",
            "CREATE INDEX IF NOT EXISTS idx_remember_tokens_hash ON remember_tokens (token_hash)",
            "CREATE INDEX IF NOT EXISTS idx_pricing_dates ON pricing_rules (start_date, end_date)",
            "CREATE INDEX IF NOT EXISTS idx_settings_key ON site_settings (setting_key)",
            "CREATE INDEX IF NOT EXISTS idx_activity_log_user ON activity_log (user_id, created_at)"
        ];
        
        foreach ($indices as $index) {
            try {
                $this->connection->exec($index);
            } catch (PDOException $e) {
                // Indices might already exist, which is fine
            }
        }
    }
    
    private function ensureColumns() {
        // Check and add missing columns for existing databases
        $alterQueries = [
            "ALTER TABLE bookings ADD COLUMN source TEXT DEFAULT 'direct'",
            "ALTER TABLE bookings ADD COLUMN source_id TEXT",
            "ALTER TABLE users ADD COLUMN role TEXT DEFAULT 'user'"
        ];
        
        foreach ($alterQueries as $query) {
            try {
                $this->connection->exec($query);
            } catch (PDOException $e) {
                // Column might already exist, which is fine
            }
        }
    }
    
    private function initDefaultData() {
        // Create default admin user if no users exist
        $stmt = $this->connection->query("SELECT COUNT(*) FROM users");
        if ($stmt->fetchColumn() == 0) {
            $this->createDefaultAdmin();
        }
        
        // Initialize default site settings
        $this->initDefaultSettings();
    }
    
    private function createDefaultAdmin() {
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $this->connection->prepare("
            INSERT INTO users (username, email, password, full_name, role) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute(['ADMIN', 'admin@apartmaivano.com', $hashedPassword, 'Administrator', 'admin']);
        
        // Log the creation
        $this->logActivity(null, 'system_init', 'Default admin user created');
    }
    
    private function initDefaultSettings() {
        $defaultSettings = [
            ['apartment_name', 'Apartma Ivano', 'Name of the apartment'],
            ['max_guests', '6', 'Maximum number of guests allowed'],
            ['min_stay', '1', 'Minimum nights required for booking'],
            ['max_stay', '30', 'Maximum nights allowed for booking'],
            ['check_in_time', '15:00', 'Standard check-in time'],
            ['check_out_time', '11:00', 'Standard check-out time'],
            ['contact_email', 'info@apartmaivano.com', 'Main contact email'],
            ['contact_phone', '+386 XX XXX XXX', 'Main contact phone'],
            ['address', 'Kranj, Slovenia', 'Apartment address'],
            ['cancellation_policy', 'Free cancellation up to 7 days before check-in', 'Cancellation policy'],
            ['house_rules', 'No smoking, No pets, Quiet hours 22:00-08:00', 'House rules'],
            ['wifi_password', 'apartmaivano2024', 'WiFi password for guests'],
            ['deposit_required', '0', 'Deposit amount required'],
            ['auto_approve_bookings', '1', 'Auto-approve new bookings (1=yes, 0=no)'],
            ['booking_com_ical_url', '', 'Booking.com iCal export URL'],
            ['booking_com_sync_enabled', '0', 'Enable automatic Booking.com sync'],
            ['booking_com_last_sync', '', 'Last successful sync timestamp']
        ];
        
        foreach ($defaultSettings as $setting) {
            $stmt = $this->connection->prepare("
                INSERT OR IGNORE INTO site_settings (setting_key, setting_value, description) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute($setting);
        }
    }
    
    /**
     * Log activity to the database
     */
    public function logActivity($userId, $action, $details = '', $ipAddress = null) {
        if (!$ipAddress) {
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        }
        
        $stmt = $this->connection->prepare("
            INSERT INTO activity_log (user_id, action, details, ip_address) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$userId, $action, $details, $ipAddress]);
    }
    
    /**
     * Get a setting value by key
     */
    public function getSetting($key, $default = null) {
        $stmt = $this->connection->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetchColumn();
        return $result !== false ? $result : $default;
    }
    
    /**
     * Set or update a setting value
     */
    public function setSetting($key, $value, $description = '') {
        // Check if setting exists
        $stmt = $this->connection->prepare("SELECT id FROM site_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $exists = $stmt->fetch();
        
        if ($exists) {
            // Update existing
            $stmt = $this->connection->prepare("
                UPDATE site_settings 
                SET setting_value = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE setting_key = ?
            ");
            return $stmt->execute([$value, $key]);
        } else {
            // Insert new
            $stmt = $this->connection->prepare("
                INSERT INTO site_settings (setting_key, setting_value, description, updated_at) 
                VALUES (?, ?, ?, CURRENT_TIMESTAMP)
            ");
            return $stmt->execute([$key, $value, $description]);
        }
    }
    
    /**
     * Get all settings
     */
    public function getAllSettings() {
        $stmt = $this->connection->query("SELECT * FROM site_settings ORDER BY setting_key");
        return $stmt->fetchAll();
    }
    
    /**
     * Log import activity
     */
    public function logImport($source, $imported, $total, $status = 'success', $error = null) {
        $stmt = $this->connection->prepare("
            INSERT INTO import_log (source, imported_count, total_count, status, error_message) 
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$source, $imported, $total, $status, $error]);
    }
    
    /**
     * Get import history
     */
    public function getImportHistory($limit = 10) {
        $stmt = $this->connection->prepare("
            SELECT * FROM import_log 
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Backup database
     */
    public function backup() {
        $backupDir = 'backups';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        $backupFile = $backupDir . '/backup_' . date('Y-m-d_H-i-s') . '.db';
        return copy(Config::DB_NAME, $backupFile);
    }
    
    /**
     * Get database statistics
     */
    public function getStats() {
        $stats = [];
        
        // Database file size
        $stats['db_size'] = file_exists(Config::DB_NAME) ? filesize(Config::DB_NAME) : 0;
        $stats['db_size_mb'] = round($stats['db_size'] / 1048576, 2);
        
        // Table counts
        $tables = ['bookings', 'users', 'pricing_rules', 'contact_messages', 'reviews', 'site_settings'];
        foreach ($tables as $table) {
            try {
                $stmt = $this->connection->query("SELECT COUNT(*) FROM $table");
                $stats["{$table}_count"] = $stmt->fetchColumn();
            } catch (PDOException $e) {
                $stats["{$table}_count"] = 0;
            }
        }
        
        // Source breakdown for bookings
        $stmt = $this->connection->query("
            SELECT source, COUNT(*) as count 
            FROM bookings 
            GROUP BY source
        ");
        $stats['bookings_by_source'] = $stmt->fetchAll();
        
        return $stats;
    }
    
    /**
     * Clean old data (maintenance)
     */
    public function cleanOldData($daysToKeep = 365) {
        // Clean old activity logs
        $stmt = $this->connection->prepare("
            DELETE FROM activity_log 
            WHERE created_at < date('now', '-' || ? || ' days')
        ");
        $stmt->execute([$daysToKeep]);
        
        // Clean old cancelled bookings
        $stmt = $this->connection->prepare("
            DELETE FROM cancelled_bookings 
            WHERE cancelled_at < date('now', '-' || ? || ' days')
        ");
        $stmt->execute([$daysToKeep]);
        
        return true;
    }
}