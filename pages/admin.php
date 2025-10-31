<?php
// pages/admin.php - Complete Admin Dashboard with Booking.com Import

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    setFlash('error', 'Access denied. Admin privileges required.');
    header('Location: index.php');
    exit;
}

// Initialize managers and database
$bookingManager = new BookingManager();
$database = Database::getInstance();
$db = $database->getConnection();

// Get statistics
$stats = $bookingManager->getBookingStats();
$allBookings = $bookingManager->getAllBookings();
$upcomingBookings = $bookingManager->getUpcomingBookings(10);
$monthlyData = $bookingManager->getMonthlyBookings();

// Get saved sync settings
$savedIcalUrl = $database->getSetting('booking_com_ical_url') ?? '';
$lastSync = $database->getSetting('booking_com_last_sync') ?? 'Never';
$syncEnabled = $database->getSetting('booking_com_sync_enabled') ?? '0';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 bg-dark text-white min-vh-100 p-3">
            <h4 class="mb-4"><i class="fas fa-cog"></i> Admin Panel</h4>
            <nav class="nav flex-column">
                <a class="nav-link text-white active" href="#dashboard" data-bs-toggle="tab">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a class="nav-link text-white" href="#bookings" data-bs-toggle="tab">
                    <i class="fas fa-calendar-check"></i> All Bookings
                </a>
                <a class="nav-link text-white" href="#booking-import" data-bs-toggle="tab">
                    <i class="fas fa-sync"></i> Booking.com Sync
                </a>
                <a class="nav-link text-white" href="#analytics" data-bs-toggle="tab">
                    <i class="fas fa-chart-bar"></i> Analytics
                </a>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-10">
            <div class="tab-content p-4">
                <!-- Dashboard Tab -->
                <div class="tab-pane fade show active" id="dashboard">
                    <h2 class="mb-4">Dashboard Overview</h2>
                    
                    <!-- Stats Cards -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <div class="card text-white bg-primary">
                                <div class="card-body">
                                    <h5 class="card-title">Total Bookings</h5>
                                    <h2><?php echo $stats['total_bookings']; ?></h2>
                                    <small><?php echo $stats['monthly_bookings']; ?> this month</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card text-white bg-success">
                                <div class="card-body">
                                    <h5 class="card-title">Total Revenue</h5>
                                    <h2>€<?php echo number_format($stats['total_revenue'], 2); ?></h2>
                                    <small>€<?php echo number_format($stats['monthly_revenue'], 2); ?> this month</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card text-white bg-warning">
                                <div class="card-body">
                                    <h5 class="card-title">Occupancy Rate</h5>
                                    <h2><?php echo $stats['occupancy_rate']; ?>%</h2>
                                    <small>Last 30 days</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card text-white bg-info">
                                <div class="card-body">
                                    <h5 class="card-title">Upcoming</h5>
                                    <h2><?php echo $stats['upcoming_bookings']; ?></h2>
                                    <small>Future bookings</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Bookings -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Recent Bookings</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Guest</th>
                                            <th>Check-in</th>
                                            <th>Check-out</th>
                                            <th>Source</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach(array_slice($allBookings, 0, 10) as $booking): ?>
                                        <tr>
                                            <td>#<?php echo $booking['id']; ?></td>
                                            <td><?php echo htmlspecialchars($booking['name']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($booking['checkin'])); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($booking['checkout'])); ?></td>
                                            <td>
                                                <?php if($booking['source'] === 'booking.com'): ?>
                                                    <span class="badge bg-info">Booking.com</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Direct</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>€<?php echo number_format($booking['total_price'], 2); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $booking['status'] === 'upcoming' ? 'primary' : 
                                                        ($booking['status'] === 'current' ? 'success' : 'secondary'); 
                                                ?>">
                                                    <?php echo ucfirst($booking['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- All Bookings Tab -->
                <div class="tab-pane fade" id="bookings">
                    <h2 class="mb-4">All Bookings</h2>
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Guest Name</th>
                                            <th>Email</th>
                                            <th>Check-in</th>
                                            <th>Check-out</th>
                                            <th>Guests</th>
                                            <th>Source</th>
                                            <th>Total</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($allBookings as $booking): ?>
                                        <tr>
                                            <td>#<?php echo $booking['id']; ?></td>
                                            <td><?php echo htmlspecialchars($booking['name']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['email']); ?></td>
                                            <td><?php echo $booking['checkin']; ?></td>
                                            <td><?php echo $booking['checkout']; ?></td>
                                            <td><?php echo $booking['guests']; ?></td>
                                            <td>
                                                <?php if($booking['source'] === 'booking.com'): ?>
                                                    <span class="badge bg-info">Booking.com</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Direct</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>€<?php echo number_format($booking['total_price'], 2); ?></td>
                                            <td><?php echo date('Y-m-d H:i', strtotime($booking['created_at'])); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-danger" onclick="deleteBooking(<?php echo $booking['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Booking.com Import Tab -->
                <div class="tab-pane fade" id="booking-import">
                    <h2 class="mb-4">Booking.com Synchronization</h2>
                    
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0"><i class="fas fa-download"></i> Import Settings</h5>
                                </div>
                                <div class="card-body">
                                    <h6>Step 1: Get your Booking.com Calendar URL</h6>
                                    <ol class="small">
                                        <li>Login to <a href="https://admin.booking.com" target="_blank">Booking.com Extranet</a></li>
                                        <li>Go to <strong>Rates & Availability</strong> → <strong>Calendar</strong></li>
                                        <li>Click <strong>"Sync calendars"</strong></li>
                                        <li>Copy the <strong>"Export calendar"</strong> URL</li>
                                    </ol>
                                    
                                    <h6>Step 2: Paste URL and Import</h6>
                                    <form id="importBookingForm">
                                        <div class="mb-3">
                                            <label class="form-label">Booking.com iCal URL:</label>
                                            <input type="url" class="form-control" id="ical_url" name="ical_url" 
                                                   value="<?php echo htmlspecialchars($savedIcalUrl); ?>"
                                                   placeholder="https://admin.booking.com/hotel/xxxxxxxxx.ics?t=xxxxx" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-sync"></i> Import Now
                                        </button>
                                        <?php if($syncEnabled === '1'): ?>
                                            <button type="button" class="btn btn-warning" onclick="disableSync()">
                                                <i class="fas fa-stop"></i> Disable Auto-Sync
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-success" onclick="enableSync()">
                                                <i class="fas fa-play"></i> Enable Auto-Sync
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                    
                                    <div id="importResult" class="mt-3"></div>
                                    
                                    <?php if($syncEnabled === '1'): ?>
                                    <div class="alert alert-success mt-3">
                                        <strong>Auto-sync is enabled!</strong><br>
                                        Last sync: <?php echo $lastSync; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header bg-warning">
                                    <h5 class="mb-0">Import Status</h5>
                                </div>
                                <div class="card-body">
                                    <?php
                                    // Get Booking.com bookings count
                                    $stmt = $db->prepare("SELECT COUNT(*) FROM bookings WHERE source = 'booking.com'");
                                    $stmt->execute();
                                    $bookingComCount = $stmt->fetchColumn();
                                    
                                    // Get revenue from Booking.com
                                    $stmt = $db->prepare("SELECT SUM(total_price) FROM bookings WHERE source = 'booking.com'");
                                    $stmt->execute();
                                    $bookingComRevenue = $stmt->fetchColumn() ?: 0;
                                    ?>
                                    
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h3><?php echo $bookingComCount; ?></h3>
                                            <p>Booking.com Reservations</p>
                                        </div>
                                        <div class="col-6">
                                            <h3>€<?php echo number_format($bookingComRevenue, 2); ?></h3>
                                            <p>Revenue (before commission)</p>
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    
                                    <h6>Recent Booking.com Imports</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Check-in</th>
                                                    <th>Check-out</th>
                                                    <th>Guest</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $stmt = $db->prepare("
                                                    SELECT * FROM bookings 
                                                    WHERE source = 'booking.com' 
                                                    ORDER BY created_at DESC 
                                                    LIMIT 5
                                                ");
                                                $stmt->execute();
                                                $recentImports = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                
                                                foreach($recentImports as $import): 
                                                ?>
                                                <tr>
                                                    <td><?php echo $import['checkin']; ?></td>
                                                    <td><?php echo $import['checkout']; ?></td>
                                                    <td><?php echo htmlspecialchars($import['name']); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Analytics Tab -->
                <div class="tab-pane fade" id="analytics">
                    <h2 class="mb-4">Analytics</h2>
                    
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Monthly Revenue</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="revenueChart"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Booking Sources</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="sourceChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Import form handler
document.getElementById('importBookingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const url = document.getElementById('ical_url').value;
    const resultDiv = document.getElementById('importResult');
    
    resultDiv.innerHTML = '<div class="alert alert-info">Importing... Please wait.</div>';
    
    fetch('index.php?page=admin&action=import_booking_com', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'ical_url=' + encodeURIComponent(url)
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            resultDiv.innerHTML = `
                <div class="alert alert-success">
                    <strong>Success!</strong><br>
                    Imported: ${data.imported} new bookings<br>
                    Total events processed: ${data.total}
                </div>`;
            setTimeout(() => location.reload(), 2000);
        } else {
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <strong>Error:</strong> ${data.message}
                </div>`;
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `
            <div class="alert alert-danger">
                <strong>Error:</strong> Failed to import. Please check your URL and try again.
            </div>`;
    });
});

function enableSync() {
    const url = document.getElementById('ical_url').value;
    if(!url) {
        alert('Please enter a URL first');
        return;
    }
    
    fetch('index.php?page=admin&action=save_booking_sync', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'ical_url=' + encodeURIComponent(url)
    })
    .then(() => location.reload());
}

function disableSync() {
    fetch('index.php?page=admin&action=disable_booking_sync', {
        method: 'POST'
    })
    .then(() => location.reload());
}

function deleteBooking(id) {
    if(confirm('Delete this booking?')) {
        fetch('index.php?page=admin&action=delete_booking', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'booking_id=' + id
        })
        .then(() => location.reload());
    }
}

// Charts
const monthlyData = <?php echo json_encode($monthlyData); ?>;

// Revenue Chart
if(document.getElementById('revenueChart')) {
    new Chart(document.getElementById('revenueChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: monthlyData.map(d => d.month),
            datasets: [{
                label: 'Revenue (€)',
                data: monthlyData.map(d => d.revenue),
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '€' + value;
                        }
                    }
                }
            }
        }
    });
}

// Source Chart
if(document.getElementById('sourceChart')) {
    <?php
    $stmt = $db->prepare("SELECT source, COUNT(*) as count FROM bookings GROUP BY source");
    $stmt->execute();
    $sources = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    
    new Chart(document.getElementById('sourceChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode(array_column($sources, 'source')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($sources, 'count')); ?>,
                backgroundColor: ['#28a745', '#17a2b8']
            }]
        }
    });
}
</script>