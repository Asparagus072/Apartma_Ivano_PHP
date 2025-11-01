<?php
$bookingId = $_GET['id'] ?? 0;
$bookingData = $booking->getBooking($bookingId);

if (!$bookingData) {
    header('Location: index.php');
    exit;
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                    </div>
                    
                    <h1 class="mb-3">Booking Confirmed!</h1>
                    <p class="lead mb-4">Thank you for choosing Apartma Ivano. Your booking has been successfully received.</p>
                    
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h5>Booking Details</h5>
                            <div class="row mt-3">
                                <div class="col-sm-6 text-start">
                                    <p><strong>Booking ID:</strong> #<?php echo $bookingData['id']; ?></p>
                                    <p><strong>Guest Name:</strong> <?php echo htmlspecialchars($bookingData['name']); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($bookingData['email']); ?></p>
                                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($bookingData['phone']); ?></p>
                                </div>
                                <div class="col-sm-6 text-start">
                                    <p><strong>Check-in:</strong> <?php echo formatDate($bookingData['checkin']); ?></p>
                                    <p><strong>Check-out:</strong> <?php echo formatDate($bookingData['checkout']); ?></p>
                                    <p><strong>Guests:</strong> <?php echo $bookingData['guests']; ?></p>
                                    <p><strong>Total Price:</strong> <?php echo formatPrice($bookingData['total_price']); ?></p>
                                </div>
                            </div>
                            <?php if (!empty($bookingData['notes'])): ?>
                            <p class="text-start"><strong>Special Requests:</strong><br><?php echo htmlspecialchars($bookingData['notes']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <h5>What happens next?</h5>
                        <p class="mb-2">1. You will receive a confirmation email shortly</p>
                        <p class="mb-2">2. We will contact you within 24 hours with check-in instructions</p>
                        <p class="mb-0">3. Payment details will be provided via email</p>
                    </div>
                    
                    <div class="mt-4">
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-home"></i> Return to Home
                        </a>
                        <button onclick="window.print()" class="btn btn-outline-secondary">
                            <i class="fas fa-print"></i> Print Confirmation
                        </button>
                    </div>
                    
                    <hr class="my-4">
                    
                    <p class="text-muted">
                        If you have any questions, please contact us at<br>
                        <strong>info@apartmaivano.com</strong> or <strong>+386 XX XXX XXX</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .navbar, .footer, .btn {
        display: none !important;
    }
}
</style>