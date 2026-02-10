<?php
if (!isAdmin()) {
    die('Access denied');
}

$allBookings = $booking->getAllBookings();
$stats = $booking->getStats();
?>

<div class="container my-4">
    <h1 class="mb-4">Admin Dashboard</h1>
    
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Bookings</h5>
                    <h2><?php echo $stats['total_bookings']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Revenue</h5>
                    <h2><?php echo formatPrice($stats['total_revenue']); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Upcoming</h5>
                    <h2><?php echo $stats['upcoming_bookings']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Occupancy Rate</h5>
                    <h2><?php echo $stats['occupancy_rate']; ?>%</h2>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bookings Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">All Bookings</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Guest Name</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Guests</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($allBookings as $b): ?>
                        <tr>
                            <td>#<?php echo $b['id']; ?></td>
                            <td>
                                <?php echo htmlspecialchars($b['name']); ?><br>
                                <small class="text-muted"><?php echo htmlspecialchars($b['email']); ?></small>
                            </td>
                            <td><?php echo formatDate($b['checkin']); ?></td>
                            <td><?php echo formatDate($b['checkout']); ?></td>
                            <td><?php echo $b['guests']; ?></td>
                            <td><?php echo formatPrice($b['total_price']); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $b['booking_status'] === 'upcoming' ? 'primary' : 
                                        ($b['booking_status'] === 'current' ? 'success' : 'secondary'); 
                                ?>">
                                    <?php echo ucfirst($b['booking_status']); ?>
                                </span>
                            </td>
                            <td><?php echo formatDate($b['created_at'], 'd M Y'); ?></td>
                            <td>
                                <button class="btn btn-sm btn-danger" onclick="deleteBooking(<?php echo $b['id']; ?>)">
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

<script>
function deleteBooking(id) {
    if (confirm('Are you sure you want to delete this booking?')) {
        fetch('index.php?ajax=delete_booking', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + id
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to delete booking');
            }
        });
    }
}
</script>