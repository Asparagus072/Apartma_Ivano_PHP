<?php
/**
 * Simplified Booking System
 */
class BookingSystem {
    private $db;
    private $pricing = [
        'low' => 80,    // Jan-Mar, Nov-Dec
        'mid' => 100,   // Apr-May, Sep-Oct
        'high' => 130,  // Jun-Aug
        'peak' => 150   // Christmas/New Year
    ];
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    public function checkAvailability($checkin, $checkout, $excludeId = null) {
        $where = "((checkin <= ? AND checkout > ?) OR (checkin < ? AND checkout >= ?) OR (checkin >= ? AND checkout <= ?))";
        $params = [$checkout, $checkin, $checkout, $checkout, $checkin, $checkout];
        
        if ($excludeId) {
            $where .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $count = $this->db->count('bookings', $where, $params);
        return $count == 0;
    }
    
    public function calculatePrice($checkin, $checkout) {
        $start = new DateTime($checkin);
        $end = new DateTime($checkout);
        $nights = $start->diff($end)->days;
        $total = 0;
        
        while ($start < $end) {
            $month = (int)$start->format('n');
            $dateStr = $start->format('Y-m-d');
            
            // Peak season (Christmas/New Year)
            if (($start->format('m-d') >= '12-20') || ($start->format('m-d') <= '01-07')) {
                $total += $this->pricing['peak'];
            }
            // High season (Jun-Aug)
            elseif ($month >= 6 && $month <= 8) {
                $total += $this->pricing['high'];
            }
            // Mid season (Apr-May, Sep-Oct)
            elseif (($month >= 4 && $month <= 5) || ($month >= 9 && $month <= 10)) {
                $total += $this->pricing['mid'];
            }
            // Low season
            else {
                $total += $this->pricing['low'];
            }
            
            $start->add(new DateInterval('P1D'));
        }
        
        // Apply discounts
        $discount = 0;
        if ($nights >= 14) {
            $discount = $total * 0.15; // 15% off for 14+ nights
        } elseif ($nights >= 7) {
            $discount = $total * 0.10; // 10% off for 7+ nights
        } elseif ($nights >= 3) {
            $discount = $total * 0.05; // 5% off for 3+ nights
        }
        
        return [
            'subtotal' => $total,
            'discount' => $discount,
            'total' => $total - $discount,
            'nights' => $nights,
            'per_night' => ($total - $discount) / $nights
        ];
    }
    
    public function getNights($checkin, $checkout) {
        $start = new DateTime($checkin);
        $end = new DateTime($checkout);
        return $start->diff($end)->days;
    }
    
    public function createBooking($checkin, $checkout, $guests, $name, $email, $phone, $notes = '') {
        // Validate
        if (!$this->checkAvailability($checkin, $checkout)) {
            return false;
        }
        
        if ($guests < 1 || $guests > MAX_GUESTS) {
            return false;
        }
        
        $pricing = $this->calculatePrice($checkin, $checkout);
        
        return $this->db->insert('bookings', [
            'checkin' => $checkin,
            'checkout' => $checkout,
            'guests' => $guests,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'notes' => $notes,
            'total_price' => $pricing['total'],
            'status' => 'confirmed'
        ]);
    }
    
    public function getAllBookings() {
        return $this->db->fetchAll("
            SELECT *, 
                   (julianday(checkout) - julianday(checkin)) as nights,
                   CASE 
                       WHEN checkin > date('now') THEN 'upcoming'
                       WHEN checkout < date('now') THEN 'past'
                       ELSE 'current'
                   END as booking_status
            FROM bookings 
            ORDER BY checkin DESC
        ");
    }
    
    public function getBooking($id) {
        return $this->db->fetchOne("SELECT * FROM bookings WHERE id = ?", [$id]);
    }
    
    public function deleteBooking($id) {
        return $this->db->delete('bookings', 'id = ?', [$id]);
    }
    
    public function getDisabledDates() {
        $bookings = $this->db->fetchAll("
            SELECT checkin, checkout 
            FROM bookings 
            WHERE checkout >= date('now')
        ");
        
        $disabledDates = [];
        foreach ($bookings as $booking) {
            $start = new DateTime($booking['checkin']);
            $end = new DateTime($booking['checkout']);
            
            while ($start < $end) {
                $disabledDates[] = $start->format('Y-m-d');
                $start->add(new DateInterval('P1D'));
            }
        }
        
        return array_unique($disabledDates);
    }
    
    public function getStats() {
        $total = $this->db->count('bookings');
        
        $revenue = $this->db->fetchOne("
            SELECT 
                COALESCE(SUM(total_price), 0) as total_revenue,
                COALESCE(AVG(total_price), 0) as avg_booking
            FROM bookings
        ");
        
        $upcoming = $this->db->count('bookings', "checkin >= date('now')");
        
        $occupancyData = $this->db->fetchOne("
            SELECT 
                COUNT(DISTINCT date(checkin, '+' || (julianday(checkout) - julianday(checkin)) || ' days')) as occupied_days
            FROM bookings 
            WHERE checkin >= date('now', '-30 days') AND checkin < date('now')
        ");
        
        return [
            'total_bookings' => $total,
            'total_revenue' => $revenue['total_revenue'],
            'avg_booking' => $revenue['avg_booking'],
            'upcoming_bookings' => $upcoming,
            'occupancy_rate' => round(($occupancyData['occupied_days'] / 30) * 100, 1)
        ];
    }
}