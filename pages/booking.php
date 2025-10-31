<?php

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    setFlash('error', 'Please login to make a booking');
    header('Location: index.php?page=login');
    exit;
}

// Get disabled dates from booking manager
$disabledDates = $bookingManager->getDisabledDates();
$pricingManager = new PricingManager();
?>

<div class="container my-5">
    <div class="booking-container">
        <div class="booking-header">
            <h1><i class="fas fa-home"></i> Book Your Stay at Apartma Ivano</h1>
            <p class="mb-0">Experience the comfort of home in beautiful Kranj, Slovenia</p>
        </div>
        
        <div class="step-indicator">
            <div class="step active" id="step-1">
                <div class="step-number">1</div>
                <span>Dates & Guests</span>
            </div>
            <div class="step" id="step-2">
                <div class="step-number">2</div>
                <span>Your Details</span>
            </div>
            <div class="step" id="step-3">
                <div class="step-number">3</div>
                <span>Confirmation</span>
            </div>
        </div>
        
        <div class="p-4">
            <form id="bookingForm" method="POST">
                <!-- Step 1: Dates and Guests -->
                <div class="booking-step" id="booking-step-1">
                    <h3 class="mb-4"><i class="fas fa-calendar-alt text-primary"></i> Select Your Dates & Guests</h3>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="checkin" name="checkin" 
                                       placeholder="Check-in date" required>
                                <label for="checkin"><i class="fas fa-calendar-check"></i> Check-in Date</label>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="checkout" name="checkout" 
                                       placeholder="Check-out date" required>
                                <label for="checkout"><i class="fas fa-calendar-times"></i> Check-out Date</label>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-control" id="guests" name="guests" required>
                                    <option value="">Select guests</option>
                                    <?php for($i = 1; $i <= 6; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?> guest<?php echo $i > 1 ? 's' : ''; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <label for="guests"><i class="fas fa-users"></i> Number of Guests</label>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-grid">
                                <button type="button" class="btn btn-primary btn-lg" onclick="checkAvailability()">
                                    <i class="fas fa-search"></i> Check Availability
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="priceBreakdown" class="price-breakdown" style="display: none;">
                        <h5><i class="fas fa-receipt text-primary"></i> Price Breakdown</h5>
                        <div id="priceDetails"></div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-success btn-lg" id="continueToStep2" 
                                onclick="goToStep(2)" style="display: none;">
                            Continue to Guest Details <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Step 2: Guest Details -->
                <div class="booking-step" id="booking-step-2" style="display: none;">
                    <h3 class="mb-4"><i class="fas fa-user text-primary"></i> Your Details</h3>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($_SESSION['username']); ?>" required>
                                <label for="name"><i class="fas fa-user"></i> Full Name</label>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="Email address" required>
                                <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       placeholder="Phone number" required>
                                <label for="phone"><i class="fas fa-phone"></i> Phone Number</label>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control" id="notes" name="notes" 
                                          placeholder="Special requests" style="height: 120px;"></textarea>
                                <label for="notes"><i class="fas fa-comment"></i> Special Requests or Notes</label>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the Terms and Conditions and Cancellation Policy
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-outline-secondary me-3" onclick="goToStep(1)">
                            <i class="fas fa-arrow-left"></i> Back to Dates
                        </button>
                        <button type="button" class="btn btn-success btn-lg" onclick="goToStep(3)">
                            Review Booking <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Step 3: Confirmation -->
                <div class="booking-step" id="booking-step-3" style="display: none;">
                    <h3 class="mb-4"><i class="fas fa-check-circle text-success"></i> Confirm Your Booking</h3>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> Booking Summary</h5>
                                </div>
                                <div class="card-body">
                                    <div id="bookingSummary"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-success text-white text-center">
                                    <h5 class="mb-0"><i class="fas fa-euro-sign"></i> Final Price</h5>
                                </div>
                                <div class="card-body">
                                    <div id="finalPriceBreakdown"></div>
                                    <div class="d-grid mt-3">
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-credit-card"></i> Confirm Booking
                                        </button>
                                    </div>
                                    <p class="text-center small text-muted mt-2">
                                        You will receive a confirmation email
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-outline-secondary" onclick="goToStep(2)">
                            <i class="fas fa-arrow-left"></i> Back to Details
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const DISABLED_DATES = <?php echo json_encode($disabledDates); ?>;
let bookingData = {};
let currentStep = 1;

document.addEventListener("DOMContentLoaded", function() {
    initializeDatePickers();
    
    document.getElementById("bookingForm").addEventListener("submit", function(e) {
        e.preventDefault();
        submitBooking();
    });
});

function initializeDatePickers() {
    const checkinPicker = flatpickr("#checkin", {
        mode: "single",
        dateFormat: "Y-m-d",
        minDate: "today",
        maxDate: new Date().fp_incr(365),
        disable: DISABLED_DATES,
        onChange: function(selectedDates, dateStr) {
            bookingData.checkin = dateStr;
            checkoutPicker.set("minDate", new Date(dateStr).fp_incr(1));
            if (bookingData.checkout && bookingData.checkout <= dateStr) {
                checkoutPicker.clear();
                bookingData.checkout = null;
            }
        }
    });
    
    const checkoutPicker = flatpickr("#checkout", {
        mode: "single", 
        dateFormat: "Y-m-d",
        minDate: new Date().fp_incr(1),
        maxDate: new Date().fp_incr(365),
        disable: DISABLED_DATES,
        onChange: function(selectedDates, dateStr) {
            bookingData.checkout = dateStr;
        }
    });
}

function checkAvailability() {
    const checkin = document.getElementById("checkin").value;
    const checkout = document.getElementById("checkout").value; 
    const guests = document.getElementById("guests").value;
    
    if (!checkin || !checkout || !guests) {
        alert("Please fill in all fields");
        return;
    }
    
    const formData = new FormData();
    formData.append("action", "check_availability");
    formData.append("checkin", checkin);
    formData.append("checkout", checkout);
    formData.append("guests", guests);
    
    fetch("index.php?page=booking", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.available) {
                bookingData = {
                    checkin,
                    checkout, 
                    guests,
                    pricing: data.pricing
                };
                showPriceBreakdown(data.pricing);
                document.getElementById("continueToStep2").style.display = "block";
            } else {
                alert("Selected dates are not available. Please choose different dates.");
            }
        } else {
            alert(data.message || "Error checking availability");
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("An error occurred. Please try again.");
    });
}

function showPriceBreakdown(pricing) {
    const breakdown = document.getElementById("priceBreakdown");
    const details = document.getElementById("priceDetails");
    
    let html = `
        <div class="price-item">
            <span>€${pricing.avg_per_night.toFixed(2)} × ${pricing.nights} nights</span>
            <span>€${pricing.subtotal.toFixed(2)}</span>
        </div>`;
    
    if (pricing.discount > 0) {
        html += `
        <div class="price-item text-success">
            <span>Discount</span>
            <span>-€${pricing.discount.toFixed(2)}</span>
        </div>`;
    }
    
    html += `
        <div class="price-item price-total">
            <span>Total</span>
            <span>€${pricing.total.toFixed(2)}</span>
        </div>`;
    
    details.innerHTML = html;
    breakdown.style.display = "block";
}

function goToStep(step) {
    document.querySelectorAll(".booking-step").forEach(el => {
        el.style.display = "none";
    });
    
    document.getElementById(`booking-step-${step}`).style.display = "block";
    
    document.querySelectorAll(".step").forEach((el, index) => {
        el.classList.remove("active", "completed");
        if (index + 1 < step) {
            el.classList.add("completed");
        } else if (index + 1 === step) {
            el.classList.add("active");
        }
    });
    
    currentStep = step;
    
    if (step === 3) {
        updateBookingSummary();
    }
    
    window.scrollTo({ top: 0, behavior: "smooth" });
}

function updateBookingSummary() {
    const summary = document.getElementById("bookingSummary");
    const finalPrice = document.getElementById("finalPriceBreakdown");
    
    const name = document.getElementById("name").value;
    const email = document.getElementById("email").value;
    const phone = document.getElementById("phone").value;
    const notes = document.getElementById("notes").value;
    
    const { checkin, checkout, guests, pricing } = bookingData;
    
    summary.innerHTML = `
        <div class="row mb-3">
            <div class="col-sm-4"><strong>Dates:</strong></div>
            <div class="col-sm-8">${checkin} to ${checkout}</div>
        </div>
        <div class="row mb-3">
            <div class="col-sm-4"><strong>Duration:</strong></div>
            <div class="col-sm-8">${pricing.nights} nights</div>
        </div>
        <div class="row mb-3">
            <div class="col-sm-4"><strong>Guests:</strong></div>
            <div class="col-sm-8">${guests}</div>
        </div>
        <div class="row mb-3">
            <div class="col-sm-4"><strong>Name:</strong></div>
            <div class="col-sm-8">${name}</div>
        </div>
        <div class="row mb-3">
            <div class="col-sm-4"><strong>Email:</strong></div>
            <div class="col-sm-8">${email}</div>
        </div>
        <div class="row mb-3">
            <div class="col-sm-4"><strong>Phone:</strong></div>
            <div class="col-sm-8">${phone}</div>
        </div>
        ${notes ? `
        <div class="row mb-3">
            <div class="col-sm-4"><strong>Notes:</strong></div>
            <div class="col-sm-8">${notes}</div>
        </div>
        ` : ""}`;
    
    finalPrice.innerHTML = `
        <div class="price-item">
            <span>Subtotal</span>
            <span>€${pricing.subtotal.toFixed(2)}</span>
        </div>
        ${pricing.discount > 0 ? `
        <div class="price-item text-success">
            <span>Discount</span>
            <span>-€${pricing.discount.toFixed(2)}</span>
        </div>` : ''}
        <div class="price-item price-total">
            <span>Total</span>
            <span>€${pricing.total.toFixed(2)}</span>
        </div>`;
}

function submitBooking() {
    const form = document.getElementById("bookingForm");
    if (!form.checkValidity()) {
        form.classList.add("was-validated");
        return;
    }
    
    const formData = new FormData(form);
    formData.append("action", "create_booking");
    
    Object.keys(bookingData).forEach(key => {
        if (key !== "pricing") {
            formData.append(key, bookingData[key]);
        }
    });
    
    fetch("index.php?page=booking", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Booking confirmed!");
            window.location.href = `index.php?page=booking-summary&id=${data.booking_id}`;
        } else {
            alert(data.message || "Error creating booking");
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("An error occurred. Please try again.");
    });
}
</script>