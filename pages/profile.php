<?php
// pages/profile.php - User Profile Management Page

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

// Get user data from database
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Note: Profile update handling is now done in index.php before any HTML output
// This prevents "headers already sent" errors

// Get user's bookings
$bookings = $bookingManager->searchBookings($user['email']);
$totalBookings = count($bookings);
$upcomingBookings = array_filter($bookings, function($b) {
    return strtotime($b['checkin']) > time();
});
$pastBookings = array_filter($bookings, function($b) {
    return strtotime($b['checkout']) < time();
});
?>

<div class="container py-4">
    <div class="row">
        <!-- Profile Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-circle" style="font-size: 5rem; color: #6c757d;"></i>
                    </div>
                    <h5><?php echo htmlspecialchars($user['full_name'] ?: $user['username']); ?></h5>
                    <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                    <?php if ($user['role'] === 'admin'): ?>
                        <span class="badge bg-danger">Administrator</span>
                    <?php else: ?>
                        <span class="badge bg-primary">Guest</span>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <div class="text-start">
                        <p class="mb-2"><small class="text-muted">Member since</small><br>
                        <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
                        
                        <?php if ($user['last_login']): ?>
                        <p class="mb-2"><small class="text-muted">Last login</small><br>
                        <?php echo date('M d, Y H:i', strtotime($user['last_login'])); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title">Booking Statistics</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Bookings:</span>
                        <strong><?php echo $totalBookings; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Upcoming:</span>
                        <strong><?php echo count($upcomingBookings); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Past Stays:</span>
                        <strong><?php echo count($pastBookings); ?></strong>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Profile Tabs -->
            <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button">
                        <i class="fas fa-user"></i> Overview
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="edit-tab" data-bs-toggle="tab" data-bs-target="#edit" type="button">
                        <i class="fas fa-edit"></i> Edit Profile
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button">
                        <i class="fas fa-lock"></i> Change Password
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="bookings-tab" data-bs-toggle="tab" data-bs-target="#bookings" type="button">
                        <i class="fas fa-calendar-check"></i> My Bookings
                    </button>
                </li>
            </ul>
            
            <!-- Tab Content -->
            <div class="tab-content" id="profileTabContent">
                <!-- Overview Tab -->
                <div class="tab-pane fade show active" id="overview" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Profile Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Username</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <?php echo htmlspecialchars($user['username']); ?>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Full Name</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <?php echo htmlspecialchars($user['full_name'] ?: 'Not set'); ?>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Email</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <?php echo htmlspecialchars($user['email']); ?>
                                    <?php if ($user['email_verified']): ?>
                                        <span class="badge bg-success ms-2">Verified</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning ms-2">Unverified</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Phone</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <?php echo htmlspecialchars($user['phone'] ?: 'Not set'); ?>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Account Type</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <?php echo ucfirst($user['role']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title">Quick Actions</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <a href="index.php?page=booking" class="btn btn-primary w-100">
                                        <i class="fas fa-calendar-plus"></i> New Booking
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-outline-primary w-100" data-bs-toggle="tab" data-bs-target="#edit">
                                        <i class="fas fa-user-edit"></i> Edit Profile
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <a href="index.php?page=logout" class="btn btn-outline-danger w-100" onclick="return confirm('Are you sure you want to logout?')">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Edit Profile Tab -->
                <div class="tab-pane fade" id="edit" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Edit Profile Information</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="index.php?page=profile">
                                <input type="hidden" name="action" value="update_profile">
                                
                                <div class="mb-3">
                                    <label for="edit_username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="edit_username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                                    <small class="text-muted">Username cannot be changed</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="edit_full_name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="edit_full_name" name="full_name" 
                                           value="<?php echo htmlspecialchars($user['full_name'] ?: ''); ?>" 
                                           placeholder="Enter your full name">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="edit_email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="edit_email" name="email" 
                                           value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="edit_phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="edit_phone" name="phone" 
                                           value="<?php echo htmlspecialchars($user['phone'] ?: ''); ?>" 
                                           placeholder="+386 XX XXX XXX">
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                                <button type="button" class="btn btn-outline-secondary ms-2" data-bs-toggle="tab" data-bs-target="#overview">
                                    Cancel
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Change Password Tab -->
                <div class="tab-pane fade" id="password" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Change Password</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="index.php?page=profile">
                                <input type="hidden" name="action" value="change_password">
                                
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                                    <small class="text-muted">Minimum 6 characters</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-lock"></i> Change Password
                                </button>
                                <button type="button" class="btn btn-outline-secondary ms-2" data-bs-toggle="tab" data-bs-target="#overview">
                                    Cancel
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- My Bookings Tab -->
                <div class="tab-pane fade" id="bookings" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">My Bookings</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($bookings)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-calendar-times" style="font-size: 3rem; color: #dee2e6;"></i>
                                    <p class="mt-3">You haven't made any bookings yet.</p>
                                    <a href="index.php?page=booking" class="btn btn-primary">Make Your First Booking</a>
                                </div>
                            <?php else: ?>
                                <!-- Upcoming Bookings -->
                                <?php if (!empty($upcomingBookings)): ?>
                                    <h6 class="text-success mb-3"><i class="fas fa-calendar-check"></i> Upcoming Bookings</h6>
                                    <div class="table-responsive mb-4">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Booking ID</th>
                                                    <th>Check-in</th>
                                                    <th>Check-out</th>
                                                    <th>Guests</th>
                                                    <th>Total</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($upcomingBookings as $booking): ?>
                                                <tr>
                                                    <td>#<?php echo $booking['id']; ?></td>
                                                    <td><?php echo date('M d, Y', strtotime($booking['checkin'])); ?></td>
                                                    <td><?php echo date('M d, Y', strtotime($booking['checkout'])); ?></td>
                                                    <td><?php echo $booking['guests']; ?></td>
                                                    <td>€<?php echo number_format($booking['total_price'], 2); ?></td>
                                                    <td>
                                                        <a href="index.php?page=booking-summary&id=<?php echo $booking['id']; ?>" 
                                                           class="btn btn-sm btn-outline-primary">View</a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Past Bookings -->
                                <?php if (!empty($pastBookings)): ?>
                                    <h6 class="text-muted mb-3"><i class="fas fa-history"></i> Past Bookings</h6>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Booking ID</th>
                                                    <th>Check-in</th>
                                                    <th>Check-out</th>
                                                    <th>Guests</th>
                                                    <th>Total</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($pastBookings as $booking): ?>
                                                <tr>
                                                    <td>#<?php echo $booking['id']; ?></td>
                                                    <td><?php echo date('M d, Y', strtotime($booking['checkin'])); ?></td>
                                                    <td><?php echo date('M d, Y', strtotime($booking['checkout'])); ?></td>
                                                    <td><?php echo $booking['guests']; ?></td>
                                                    <td>€<?php echo number_format($booking['total_price'], 2); ?></td>
                                                    <td>
                                                        <a href="index.php?page=booking-summary&id=<?php echo $booking['id']; ?>" 
                                                           class="btn btn-sm btn-outline-secondary">View</a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.nav-tabs .nav-link {
    color: #6c757d;
    border: none;
    padding: 0.75rem 1.25rem;
}

.nav-tabs .nav-link.active {
    color: #fff;
    background: linear-gradient(135deg, #2E7D32, #1976D2);
    border-radius: 5px 5px 0 0;
}

.nav-tabs .nav-link:hover:not(.active) {
    color: #495057;
    background-color: #f8f9fa;
}

.card {
    border: none;
    box-shadow: 0 0 20px rgba(0,0,0,0.08);
}

.badge {
    padding: 0.5em 0.75em;
}
</style>