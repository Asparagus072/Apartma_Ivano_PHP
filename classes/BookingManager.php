<?php
class BookingManager {
    private $db;
    private $pricingManager;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->pricingManager = new PricingManager();
    }

    public function getAllBookings() {
        $stmt = $this->db->query("
            SELECT *, 
                   (julianday(checkout) - julianday(checkin)) as nights,
                   CASE 
                       WHEN checkin > date('now') THEN 'upcoming'
                       WHEN checkout < date('now') THEN 'past'
                       ELSE 'current'
                   END as status
            FROM bookings 
            ORDER BY checkin DESC, id DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBookingById($id) {
        $stmt = $this->db->prepare("
            SELECT *, 
                   (julianday(checkout) - julianday(checkin)) as nights
            FROM bookings 
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateBooking($id, $checkin, $checkout, $guests, $name, $email, $phone, $notes = '') {
        // Validate dates
        if (!$this->isDateAvailable($checkin, $checkout, $id)) {
            throw new Exception('Selected dates are not available');
        }

        // Calculate new total price
        $pricing = $this->pricingManager->calculateStayTotal($checkin, $checkout);
        
        $stmt = $this->db->prepare("
            UPDATE bookings 
            SET checkin = ?, checkout = ?, guests = ?, name = ?, email = ?, phone = ?, 
                total_price = ?, notes = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        return $stmt->execute([
            $checkin, $checkout, $guests, $name, $email, $phone, 
            $pricing['total'], $notes, $id
        ]);
    }

    public function deleteBooking($id) {
        $stmt = $this->db->prepare("DELETE FROM bookings WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getBookingStats() {
        $stats = [];
        
        // Total bookings
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM bookings");
        $stats['total_bookings'] = $stmt->fetchColumn();
        
        // Monthly bookings
        $stmt = $this->db->query("
            SELECT COUNT(*) as monthly 
            FROM bookings 
            WHERE strftime('%Y-%m', created_at) = strftime('%Y-%m', 'now')
        ");
        $stats['monthly_bookings'] = $stmt->fetchColumn();
        
        // Upcoming bookings
        $stmt = $this->db->query("
            SELECT COUNT(*) as upcoming 
            FROM bookings 
            WHERE checkin >= date('now')
        ");
        $stats['upcoming_bookings'] = $stmt->fetchColumn();
        
        // Current guests
        $stmt = $this->db->query("
            SELECT COALESCE(SUM(guests), 0) as current_guests
            FROM bookings 
            WHERE checkin <= date('now') AND checkout > date('now')
        ");
        $stats['current_guests'] = $stmt->fetchColumn();
        
        // Revenue calculations
        $stmt = $this->db->query("
            SELECT 
                COALESCE(SUM(total_price), 0) as total_revenue,
                COALESCE(SUM(CASE WHEN strftime('%Y-%m', created_at) = strftime('%Y-%m', 'now') 
                    THEN total_price ELSE 0 END), 0) as monthly_revenue,
                COALESCE(SUM(CASE WHEN checkin >= date('now') 
                    THEN total_price ELSE 0 END), 0) as upcoming_revenue
            FROM bookings
        ");
        $revenue = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats = array_merge($stats, $revenue);
        
        // Occupancy rate (last 30 days)
        $stmt = $this->db->query("
            SELECT 
                COUNT(DISTINCT date(checkin, '+' || (julianday(checkout) - julianday(checkin)) || ' days')) as occupied_days
            FROM bookings 
            WHERE checkin >= date('now', '-30 days') AND checkin < date('now')
        ");
        $occupiedDays = $stmt->fetchColumn();
        $stats['occupancy_rate'] = round(($occupiedDays / 30) * 100, 1);
        
        // Average booking value
        $stats['avg_booking_value'] = $stats['total_bookings'] > 0 
            ? round($stats['total_revenue'] / $stats['total_bookings'], 2) 
            : 0;
        
        return $stats;
    }

    public function getDisabledDates() {
        $stmt = $this->db->query("SELECT checkin, checkout FROM bookings WHERE checkout >= date('now')");
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $disabledDates = [];
        foreach ($bookings as $booking) {
            $start = new DateTime($booking['checkin']);
            $end = new DateTime($booking['checkout']);
            
            // Include all dates from checkin to checkout (inclusive)
            while ($start < $end) {
                $disabledDates[] = $start->format('Y-m-d');
                $start->add(new DateInterval('P1D'));
            }
        }
        
        return array_unique($disabledDates);
    }

    public function createBooking($checkin, $checkout, $guests, $name, $email, $phone, $notes = '', $source = 'direct') {
        // Validate input
        $this->validateBookingData($checkin, $checkout, $guests, $name, $email, $phone);
        
        // Check availability
        if (!$this->isDateAvailable($checkin, $checkout)) {
            throw new Exception('Selected dates are not available');
        }

        // Calculate pricing
        $pricing = $this->pricingManager->calculateStayTotal($checkin, $checkout);
        
        $stmt = $this->db->prepare("
            INSERT INTO bookings (checkin, checkout, guests, name, email, phone, notes, source, total_price) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $checkin, $checkout, $guests, $name, $email, $phone, 
            $notes, $source, $pricing['total']
        ]);
        
        if ($result) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }

    private function validateBookingData($checkin, $checkout, $guests, $name, $email, $phone) {
        $errors = [];
        
        // Date validation
        $checkinDate = new DateTime($checkin);
        $checkoutDate = new DateTime($checkout);
        $today = new DateTime();
        
        if ($checkinDate < $today) {
            $errors[] = 'Check-in date cannot be in the past';
        }
        
        if ($checkoutDate <= $checkinDate) {
            $errors[] = 'Check-out date must be after check-in date';
        }
        
        $nights = $checkinDate->diff($checkoutDate)->days;
        if ($nights > 30) {
            $errors[] = 'Maximum stay is 30 nights';
        }
        
        // Guest validation
        if ($guests < 1 || $guests > 6) {
            $errors[] = 'Number of guests must be between 1 and 6';
        }
        
        // Contact validation
        if (empty(trim($name))) {
            $errors[] = 'Name is required';
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required';
        }
        
        if (empty(trim($phone))) {
            $errors[] = 'Phone number is required';
        }
        
        if (!empty($errors)) {
            throw new Exception(implode(', ', $errors));
        }
    }

    public function isDateAvailable($checkin, $checkout, $excludeId = null) {
        $sql = "
            SELECT COUNT(*) FROM bookings 
            WHERE ((checkin < ? AND checkout > ?) 
               OR (checkin < ? AND checkout > ?)
               OR (checkin >= ? AND checkout <= ?))
        ";
        $params = [$checkout, $checkin, $checkout, $checkout, $checkin, $checkout];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() == 0;
    }

    public function getMonthlyBookings() {
        $stmt = $this->db->query("
            SELECT 
                strftime('%Y-%m', checkin) as month,
                COUNT(*) as count,
                COALESCE(SUM(total_price), 0) as revenue
            FROM bookings 
            WHERE checkin >= date('now', '-12 months')
            GROUP BY strftime('%Y-%m', checkin)
            ORDER BY month
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUpcomingBookings($limit = 5) {
        $stmt = $this->db->prepare("
            SELECT *, 
                   (julianday(checkout) - julianday(checkin)) as nights
            FROM bookings 
            WHERE checkin >= date('now')
            ORDER BY checkin ASC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCurrentBookings() {
        $stmt = $this->db->query("
            SELECT *, 
                   (julianday(checkout) - julianday(checkin)) as nights
            FROM bookings 
            WHERE checkin <= date('now') AND checkout > date('now')
            ORDER BY checkin ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchBookings($query) {
        $stmt = $this->db->prepare("
            SELECT *, 
                   (julianday(checkout) - julianday(checkin)) as nights
            FROM bookings 
            WHERE name LIKE ? OR email LIKE ? OR phone LIKE ?
            ORDER BY checkin DESC
        ");
        $searchTerm = '%' . $query . '%';
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAvailabilityCalendar($month = null, $year = null) {
        if (!$month) $month = date('m');
        if (!$year) $year = date('Y');
        
        $startDate = new DateTime("$year-$month-01");
        $endDate = clone $startDate;
        $endDate->add(new DateInterval('P1M'));
        
        $stmt = $this->db->prepare("
            SELECT checkin, checkout, name, guests
            FROM bookings 
            WHERE (checkin < ? AND checkout > ?)
            ORDER BY checkin
        ");
        $stmt->execute([$endDate->format('Y-m-d'), $startDate->format('Y-m-d')]);
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $calendar = [];
        $current = clone $startDate;
        
        while ($current < $endDate) {
            $dateStr = $current->format('Y-m-d');
            $calendar[$dateStr] = [
                'available' => true,
                'bookings' => []
            ];
            
            foreach ($bookings as $booking) {
                if ($dateStr >= $booking['checkin'] && $dateStr < $booking['checkout']) {
                    $calendar[$dateStr]['available'] = false;
                    $calendar[$dateStr]['bookings'][] = $booking;
                }
            }
            
            $current->add(new DateInterval('P1D'));
        }
        
        return $calendar;
    }

    public function cancelBooking($id, $reason = '') {
        $booking = $this->getBookingById($id);
        if (!$booking) {
            throw new Exception('Booking not found');
        }
        
        // Archive the booking before deletion
        $stmt = $this->db->prepare("
            INSERT INTO cancelled_bookings 
            (original_id, checkin, checkout, guests, name, email, phone, total_price, 
             cancellation_reason, cancelled_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
        ");
        $stmt->execute([
            $id, $booking['checkin'], $booking['checkout'], $booking['guests'],
            $booking['name'], $booking['email'], $booking['phone'], 
            $booking['total_price'], $reason
        ]);
        
        // Delete original booking
        return $this->deleteBooking($id);
    }
}
?>