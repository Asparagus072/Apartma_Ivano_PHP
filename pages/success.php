<?php
$bookingId   = $_GET['id'] ?? 0;
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
                        <i class="fas fa-check-circle text-success" style="font-size:5rem;"></i>
                    </div>

                    <h1 class="mb-3"><?php echo __('success.title'); ?></h1>
                    <p class="lead mb-4"><?php echo __('success.lead'); ?></p>

                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h5><?php echo __('success.details'); ?></h5>
                            <div class="row mt-3">
                                <div class="col-sm-6 text-start">
                                    <p><strong><?php echo __('success.id'); ?>:</strong> #<?php echo $bookingData['id']; ?></p>
                                    <p><strong><?php echo __('success.name'); ?>:</strong> <?php echo htmlspecialchars($bookingData['name']); ?></p>
                                    <p><strong><?php echo __('book.email'); ?>:</strong> <?php echo htmlspecialchars($bookingData['email']); ?></p>
                                    <p><strong><?php echo __('book.phone'); ?>:</strong> <?php echo htmlspecialchars($bookingData['phone']); ?></p>
                                </div>
                                <div class="col-sm-6 text-start">
                                    <p><strong><?php echo __('success.checkin'); ?>:</strong> <?php echo formatDate($bookingData['checkin']); ?></p>
                                    <p><strong><?php echo __('success.checkout'); ?>:</strong> <?php echo formatDate($bookingData['checkout']); ?></p>
                                    <p><strong><?php echo __('success.guests'); ?>:</strong> <?php echo $bookingData['guests']; ?></p>
                                    <p><strong><?php echo __('success.total'); ?>:</strong> <?php echo formatPrice($bookingData['total_price']); ?></p>
                                </div>
                            </div>
                            <?php if (!empty($bookingData['notes'])): ?>
                            <p class="text-start"><strong><?php echo __('success.requests'); ?>:</strong><br>
                                <?php echo htmlspecialchars($bookingData['notes']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <h5><?php echo __('success.next'); ?></h5>
                        <p class="mb-2"><?php echo __('success.step1'); ?></p>
                        <p class="mb-2"><?php echo __('success.step2'); ?></p>
                        <p class="mb-0"><?php echo __('success.step3'); ?></p>
                    </div>

                    <div class="mt-4">
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-home"></i> <?php echo __('success.home'); ?>
                        </a>
                        <button onclick="window.print()" class="btn btn-outline-secondary">
                            <i class="fas fa-print"></i> <?php echo __('success.print'); ?>
                        </button>
                    </div>

                    <hr class="my-4">
                    <p class="text-muted">
                        <?php echo __('contact.resp_text'); ?><br>
                        <strong>mmsabalic@gmail.com</strong> or <strong>+386 40 695 807</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .navbar, .footer, .btn { display: none !important; }
}
</style>