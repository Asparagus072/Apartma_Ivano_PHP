<?php
/**
 * Simplified Database Class
 */
class Database {
    private $pdo;
    
    public function __construct() {
        try {
            // Create database directory if it doesn't exist
            $dbDir = dirname(DB_PATH);
            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0755, true);
            }
            
            $this->pdo = new PDO('sqlite:' . DB_PATH);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            $this->initDatabase();
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }
    
    private function initDatabase() {
        // Create bookings table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS bookings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                checkin DATE NOT NULL,
                checkout DATE NOT NULL,
                guests INTEGER NOT NULL,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                phone TEXT NOT NULL,
                notes TEXT,
                total_price REAL DEFAULT 0,
                status TEXT DEFAULT 'confirmed',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Create contact messages table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS messages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                message TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Create indices for better performance
        $this->pdo->exec("CREATE INDEX IF NOT EXISTS idx_bookings_dates ON bookings (checkin, checkout)");
    }
    
    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }
    
    public function fetchOne($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }
    
    public function insert($table, $data) {
        $columns = array_keys($data);
        $values = array_map(function($col) { return ':' . $col; }, $columns);
        
        $sql = "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ")";
        $this->query($sql, $data);
        
        return $this->pdo->lastInsertId();
    }
    
    public function update($table, $data, $where, $whereParams = []) {
        $setParts = [];
        foreach ($data as $column => $value) {
            $setParts[] = "$column = :$column";
        }
        
        $sql = "UPDATE $table SET " . implode(', ', $setParts) . " WHERE $where";
        return $this->query($sql, array_merge($data, $whereParams));
    }
    
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM $table WHERE $where";
        return $this->query($sql, $params);
    }
    
    public function count($table, $where = '1=1', $params = []) {
        $sql = "SELECT COUNT(*) as count FROM $table WHERE $where";
        $result = $this->fetchOne($sql, $params);
        return $result['count'];
    }
}