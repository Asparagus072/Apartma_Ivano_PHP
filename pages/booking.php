<?php
$disabledDates = $booking->getDisabledDates();
?>

<div class="container my-5">
    <h1 class="text-center mb-4"><?php echo __('book.title'); ?></h1>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="booking-calendar">
                <form method="POST" id="bookingForm" action="index.php">
                    <input type="hidden" name="action" value="book">

                    <!-- Date Selection -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="checkin" class="form-label"><?php echo __('book.checkin'); ?></label>
                            <input type="text" class="form-control" id="checkin" name="checkin" required>
                        </div>
                        <div class="col-md-6">
                            <label for="checkout" class="form-label"><?php echo __('book.checkout'); ?></label>
                            <input type="text" class="form-control" id="checkout" name="checkout" required>
                        </div>
                    </div>

                    <!-- Guests -->
                    <div class="mb-4">
                        <label for="guests" class="form-label"><?php echo __('book.guests'); ?></label>
                        <select class="form-control" id="guests" name="guests" required>
                            <option value=""><?php echo __('book.guests_sel'); ?></option>
                            <?php for ($i = 1; $i <= MAX_GUESTS; $i++): ?>
                            <option value="<?php echo $i; ?>">
                                <?php echo $i . ' ' . ($i > 1 ? __('book.guests_pl') : __('book.guest')); ?>
                            </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <!-- Price Display -->
                    <div id="priceDisplay" class="alert alert-info d-none">
                        <h5><?php echo __('book.price_title'); ?></h5>
                        <div id="priceDetails"></div>
                    </div>

                    <!-- Guest Details -->
                    <h4 class="mb-3"><?php echo __('book.details'); ?></h4>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label"><?php echo __('book.name'); ?></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label"><?php echo __('book.email'); ?></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label"><?php echo __('book.phone'); ?></label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="col-md-6">
                            <label for="notes" class="form-label"><?php echo __('book.notes'); ?></label>
                            <textarea class="form-control" id="notes" name="notes" rows="1"></textarea>
                        </div>
                    </div>

                    <!-- Terms -->
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="terms" required>
                        <label class="form-check-label" for="terms">
                            <?php echo __('book.terms'); ?>
                        </label>
                    </div>

                    <!-- Submit -->
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                            <i class="fas fa-check-circle"></i> <?php echo __('book.confirm'); ?>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Info Cards -->
            <div class="row mt-5">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-clock text-primary fa-2x mb-2"></i>
                            <h6><?php echo __('book.checkinout'); ?></h6>
                            <p class="small mb-0"><?php echo __('book.checkinout_v'); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-ban text-danger fa-2x mb-2"></i>
                            <h6><?php echo __('book.cancel'); ?></h6>
                            <p class="small mb-0"><?php echo __('book.cancel_v'); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-shield-alt text-success fa-2x mb-2"></i>
                            <h6><?php echo __('book.secure'); ?></h6>
                            <p class="small mb-0"><?php echo __('book.secure_v'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const disabledDates = <?php echo json_encode($disabledDates); ?>;
    const unavailMsg    = <?php echo json_encode(__('book.unavail')); ?>;
    const totalLabel    = <?php echo json_encode(__('book.total')); ?>;
    const checkinInput  = document.getElementById('checkin');
    const checkoutInput = document.getElementById('checkout');
    const priceDisplay  = document.getElementById('priceDisplay');
    const priceDetails  = document.getElementById('priceDetails');
    const submitBtn     = document.getElementById('submitBtn');

    const checkinPicker = flatpickr(checkinInput, {
        minDate: 'today',
        dateFormat: 'Y-m-d',
        disable: disabledDates,
        onChange: function(selectedDates) {
            if (selectedDates.length > 0) {
                checkoutPicker.set('minDate', new Date(selectedDates[0].getTime() + 86400000));
                checkoutPicker.clear();
                checkAvailability();
            }
        }
    });

    const checkoutPicker = flatpickr(checkoutInput, {
        minDate: new Date().fp_incr(1),
        dateFormat: 'Y-m-d',
        disable: disabledDates,
        onChange: checkAvailability
    });

    function checkAvailability() {
        const checkin  = checkinInput.value;
        const checkout = checkoutInput.value;
        if (!checkin || !checkout) return;

        fetch('index.php?ajax=check_availability', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `checkin=${checkin}&checkout=${checkout}`
        })
        .then(r => r.json())
        .then(data => {
            if (data.available) {
                priceDisplay.classList.remove('d-none', 'alert-danger');
                priceDisplay.classList.add('alert-info');

                let html = `
                    <div class="d-flex justify-content-between mb-2">
                        <span>€${data.price.per_night.toFixed(2)} × ${data.nights} nights</span>
                        <span>€${data.price.subtotal.toFixed(2)}</span>
                    </div>`;

                if (data.price.discount > 0) {
                    const pct = (data.price.discount / data.price.subtotal * 100).toFixed(0);
                    html += `
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Discount (${pct}% off)</span>
                        <span>-€${data.price.discount.toFixed(2)}</span>
                    </div>`;
                }

                html += `
                    <div class="d-flex justify-content-between border-top pt-2">
                        <strong>${totalLabel}</strong>
                        <strong>€${data.price.total.toFixed(2)}</strong>
                    </div>`;

                priceDetails.innerHTML = html;
                submitBtn.disabled = false;
            } else {
                priceDisplay.classList.remove('d-none', 'alert-info');
                priceDisplay.classList.add('alert-danger');
                priceDetails.innerHTML = `<p class="mb-0">${unavailMsg}</p>`;
                submitBtn.disabled = true;
            }
        });
    }

    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        if (!document.getElementById('terms').checked) {
            e.preventDefault();
            alert('<?php echo __('book.terms'); ?>');
        }
    });
});
</script>