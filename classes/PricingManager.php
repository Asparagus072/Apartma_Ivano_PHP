<?php
class PricingManager {
    private $db;
    
    private $basePrices = [
        'low_season' => 80,
        'mid_season' => 100,
        'high_season' => 130,
        'peak_season' => 150
    ];
    
    private $peakDates = [
        '2024-12-20' => '2025-01-07',
        '2025-12-20' => '2026-01-07',
        '2025-04-18' => '2025-04-21',
        '2026-04-03' => '2026-04-06',
    ];
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->initPricingTable();
    }
    
    private function initPricingTable() {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS pricing_rules (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                start_date TEXT NOT NULL,
                end_date TEXT NOT NULL,
                price_per_night REAL NOT NULL,
                season TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM pricing_rules");
        if ($stmt->fetchColumn() == 0) {
            $this->setupDefaultSeasonalPricing();
        }
    }
    
    private function setupDefaultSeasonalPricing() {
        $currentYear = date('Y');
        
        $defaultRules = [
            [$currentYear . '-01-08', $currentYear . '-03-31', $this->basePrices['low_season'], 'low_season'],
            [$currentYear . '-11-01', $currentYear . '-12-19', $this->basePrices['low_season'], 'low_season'],
            [$currentYear . '-04-01', $currentYear . '-05-31', $this->basePrices['mid_season'], 'mid_season'],
            [$currentYear . '-09-01', $currentYear . '-10-31', $this->basePrices['mid_season'], 'mid_season'],
            [$currentYear . '-06-01', $currentYear . '-08-31', $this->basePrices['high_season'], 'high_season'],
        ];
        
        foreach ($defaultRules as $rule) {
            $stmt = $this->db->prepare("INSERT INTO pricing_rules (start_date, end_date, price_per_night, season) VALUES (?, ?, ?, ?)");
            $stmt->execute($rule);
        }
    }
    
    public function getPriceForDate($date) {
        $stmt = $this->db->prepare("
            SELECT price_per_night FROM pricing_rules 
            WHERE ? BETWEEN start_date AND end_date 
            ORDER BY id DESC LIMIT 1
        ");
        $stmt->execute([$date]);
        $customPrice = $stmt->fetchColumn();
        
        if ($customPrice !== false) {
            return floatval($customPrice);
        }
        
        foreach ($this->peakDates as $start => $end) {
            if ($date >= $start && $date <= $end) {
                return $this->basePrices['peak_season'];
            }
        }
        
        $month = date('n', strtotime($date));
        
        if (in_array($month, [1, 2, 3, 11, 12])) {
            return $this->basePrices['low_season'];
        } elseif (in_array($month, [4, 5, 9, 10])) {
            return $this->basePrices['mid_season'];
        } else {
            return $this->basePrices['high_season'];
        }
    }
    
    public function calculateStayTotal($checkin, $checkout) {
        $start = new DateTime($checkin);
        $end = new DateTime($checkout);
        $total = 0;
        $breakdown = [];
        
        while ($start < $end) {
            $dateStr = $start->format('Y-m-d');
            $price = $this->getPriceForDate($dateStr);
            $total += $price;
            $breakdown[] = [
                'date' => $dateStr,
                'price' => $price
            ];
            $start->add(new DateInterval('P1D'));
        }
        
        $nights = count($breakdown);
        $discount = 0;
        $discountReason = '';
        
        if ($nights >= 14) {
            $discount = $total * 0.15;
            $discountReason = '15% discount for stays 14+ nights';
        } elseif ($nights >= 7) {
            $discount = $total * 0.10;
            $discountReason = '10% discount for stays 7+ nights';
        } elseif ($nights >= 3) {
            $discount = $total * 0.05;
            $discountReason = '5% discount for stays 3+ nights';
        }
        
        return [
            'subtotal' => $total,
            'discount' => $discount,
            'discount_reason' => $discountReason,
            'total' => $total - $discount,
            'nights' => $nights,
            'avg_per_night' => ($total - $discount) / $nights,
            'breakdown' => $breakdown
        ];
    }
    
    public function getSeasonalPrices() {
        return $this->basePrices;
    }
}
?>
