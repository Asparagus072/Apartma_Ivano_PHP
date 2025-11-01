<!-- Hero Section with hisa.jpg background -->
<style>
.hero {
    background: linear-gradient(135deg, rgba(46,125,50,.85), rgba(25,118,210,.85)), 
                url('static/img/jezero.jpg') center/cover no-repeat;
    background-attachment: fixed;
}

/* Image Gallery Styles */
.gallery-container {
    overflow-x: auto;
    overflow-y: hidden;
    white-space: nowrap;
    padding: 20px 0;
    scrollbar-width: thin;
    scrollbar-color: var(--primary) #f1f1f1;
}

.gallery-container::-webkit-scrollbar {
    height: 8px;
}

.gallery-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.gallery-container::-webkit-scrollbar-thumb {
    background: var(--primary);
    border-radius: 10px;
}

.gallery-container::-webkit-scrollbar-thumb:hover {
    background: var(--secondary);
}

.gallery-item {
    display: inline-block;
    margin-right: 15px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0,0,0,.1);
    transition: transform .3s, box-shadow .3s;
    cursor: pointer;
}

.gallery-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0,0,0,.2);
}

.gallery-item img {
    height: 250px;
    width: auto;
    display: block;
    object-fit: cover;
}

/* Lightbox for full-size images */
.lightbox {
    display: none;
    position: fixed;
    z-index: 9999;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,.9);
    justify-content: center;
    align-items: center;
}

.lightbox.active {
    display: flex;
}

.lightbox img {
    max-width: 90%;
    max-height: 90vh;
    object-fit: contain;
}

.lightbox-close {
    position: absolute;
    top: 20px;
    right: 40px;
    color: white;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
}

.scroll-hint {
    text-align: center;
    color: #666;
    font-size: 14px;
    margin-top: 10px;
}
</style>

<div class="hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-3 fw-bold mb-4">Welcome to Apartma Ivano</h1>
                <p class="lead mb-4">Your Perfect Mountain Retreat in Bohinj, Slovenia</p>
                <p class="fs-5 mb-4">Cozy apartment • Up to 6 guests • Near Lake Bohinj & Vogel Ski Resort</p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="?page=booking" class="btn btn-light btn-lg">
                        <i class="fas fa-calendar-check"></i> Book Now
                    </a>
                    <a href="?page=about" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-info-circle"></i> Learn More
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container my-5">
    <!-- Quick Booking Check -->
    <div class="row mb-5">
        <div class="col-lg-10 mx-auto">
            <div class="card shadow-lg border-0">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4">Check Availability</h3>
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label for="quick_checkin" class="form-label">Check-in</label>
                            <input type="text" class="form-control" id="quick_checkin" placeholder="Select date">
                        </div>
                        <div class="col-md-5">
                            <label for="quick_checkout" class="form-label">Check-out</label>
                            <input type="text" class="form-control" id="quick_checkout" placeholder="Select date">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <a href="?page=booking" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Check
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="row mb-5">
        <div class="col-12 text-center mb-4">
            <h2 class="display-5 fw-bold">Why Choose Apartma Ivano?</h2>
            <p class="text-muted">Experience the beauty of Lake Bohinj and Julian Alps</p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card h-100 text-center border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="feature-icon bg-primary bg-gradient text-white rounded-circle mb-3 mx-auto" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-mountain fa-2x"></i>
                    </div>
                    <h4>Perfect Location</h4>
                    <p class="text-muted">Located in beautiful Srednja vas v Bohinju, just 4 km from Lake Bohinj and 8 km from Vogel Ski Resort</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100 text-center border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="feature-icon bg-success bg-gradient text-white rounded-circle mb-3 mx-auto" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-home fa-2x"></i>
                    </div>
                    <h4>Comfortable & Cozy</h4>
                    <p class="text-muted">60m² apartment with mountain views, fully equipped for a relaxing stay in the heart of nature</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100 text-center border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="feature-icon bg-info bg-gradient text-white rounded-circle mb-3 mx-auto" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-star fa-2x"></i>
                    </div>
                    <h4>Premium Amenities</h4>
                    <p class="text-muted">Free WiFi, Free Parking, Kitchen, Balcony, Washing Machine, and more</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Amenities Grid -->
    <div class="row mb-5">
        <div class="col-12 text-center mb-4">
            <h2 class="display-6 fw-bold">What's Included</h2>
        </div>
    </div>

    <div class="row g-3 mb-5">
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 h-100 border-0 bg-light">
                <i class="fas fa-wifi text-primary fa-2x mb-2"></i>
                <small class="fw-bold">Free WiFi</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 h-100 border-0 bg-light">
                <i class="fas fa-car text-primary fa-2x mb-2"></i>
                <small class="fw-bold">Free Parking</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 h-100 border-0 bg-light">
                <i class="fas fa-snowflake text-primary fa-2x mb-2"></i>
                <small class="fw-bold">Air Conditioning</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 h-100 border-0 bg-light">
                <i class="fas fa-tv text-primary fa-2x mb-2"></i>
                <small class="fw-bold">TV</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 h-100 border-0 bg-light">
                <i class="fas fa-utensils text-primary fa-2x mb-2"></i>
                <small class="fw-bold">Full Kitchen</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 h-100 border-0 bg-light">
                <i class="fas fa-tshirt text-primary fa-2x mb-2"></i>
                <small class="fw-bold">Washing Machine</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 h-100 border-0 bg-light">
                <i class="fas fa-mug-hot text-primary fa-2x mb-2"></i>
                <small class="fw-bold">Coffee/Tea</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 h-100 border-0 bg-light">
                <i class="fas fa-door-open text-primary fa-2x mb-2"></i>
                <small class="fw-bold">Balcony</small>
            </div>
        </div>
    </div>

    <!-- Pricing Section -->
    <div class="row mb-5">
        <div class="col-lg-10 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">Seasonal Pricing</h2>
                    <div class="row text-center justify-content-center">
                        <div class="col-md-5">
                            <div class="p-3">
                                <div class="price-badge mb-2">€120/night</div>
                                <h6>Pre-Post Season</h6>
                                <small class="text-muted">June, Sep, Oct</small>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="p-3">
                                <div class="price-badge mb-2">€150/night</div>
                                <h6>Peak Season</h6>
                                <small class="text-muted">Jul, Aug</small>
                            </div>
                        </div>
                    </div>
                    <hr class="my-4">
                    <div class="text-center">
                        <h5 class="mb-3"><i class="fas fa-percent text-success"></i> Special Discounts</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <p class="mb-0"><strong>7+ nights:</strong> 10% off</p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-0"><strong>14+ nights:</strong> 15% off</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Location Highlights -->
    <div class="row mb-5">
        <div class="col-12 text-center mb-4">
            <h2 class="display-6 fw-bold">Explore Bohinj & Beyond</h2>
            <p class="text-muted">Perfect base for outdoor adventures in the Julian Alps</p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h5><i class="fas fa-map-marker-alt text-info"></i> Nearby Attractions</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Lake Bohinj - 4 km (5 minutes)</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Ski Center Vogel - 8 km (10 minutes)</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Waterfall Savica - 7 km</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Cable car Vogel - 4.9 km</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Triglav National Park - In the area</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Restaurant Pri Hrvatu - 300 m</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h5><i class="fas fa-car text-primary"></i> Day Trips</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Lake Bled - 30 minutes</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Ljubljana - 1 hour</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Ljubljana Airport - 63 km (1 hour)</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Kranjska Gora - 45 minutes</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Italy (Tarvisio) - 1 hour</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Austria - 1.5 hours</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Activities Section (Summer Only) -->
    <div class="row mb-5">
        <div class="col-12 text-center mb-4">
            <h2 class="display-6 fw-bold">Summer Activities</h2>
            <p class="text-muted">Open June through October</p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="text-center mb-4"><i class="fas fa-sun text-warning"></i> Things to Do in Bohinj</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">🏊 Swimming in Lake Bohinj</li>
                                <li class="mb-2">🥾 Hiking in Triglav National Park</li>
                                <li class="mb-2">🚴 Mountain biking</li>
                                <li class="mb-2">🛶 Kayaking & paddleboarding</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">🧗 Rock climbing</li>
                                <li class="mb-2">🎣 Fishing</li>
                                <li class="mb-2">🚡 Cable car to Vogel</li>
                                <li class="mb-2">📸 Photography & nature walks</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Photo Gallery -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="text-center mb-4"><i class="fas fa-images text-primary"></i> Apartment Gallery</h2>
                    <p class="text-center text-muted mb-4">Scroll through our photos • Click to view full size</p>
                    
                    <div class="gallery-container">
                        <div class="gallery-item" onclick="openLightbox('static/img/hisa.jpg')">
                            <img src="static/img/hisa.jpg" alt="House Exterior - Apartma Ivano">
                        </div>
                        <div class="gallery-item" onclick="openLightbox('static/img/dnevna.jpg')">
                            <img src="static/img/dnevna.jpg" alt="Living Room - Apartma Ivano">
                        </div>
                        <div class="gallery-item" onclick="openLightbox('static/img/dnevna2.jpg')">
                            <img src="static/img/dnevna2.jpg" alt="Living Area - Apartma Ivano">
                        </div>
                        <div class="gallery-item" onclick="openLightbox('static/img/dnevna3.jpg')">
                            <img src="static/img/dnevna3.jpg" alt="Living Space - Apartma Ivano">
                        </div>
                        <div class="gallery-item" onclick="openLightbox('static/img/kopalnica.jpg')">
                            <img src="static/img/kopalnica.jpg" alt="Bathroom - Apartma Ivano">
                        </div>
                        <div class="gallery-item" onclick="openLightbox('static/img/kuhinja.jpg')">
                            <img src="static/img/kuhinja.jpg" alt="Kitchen - Apartma Ivano">
                        </div>
                        <div class="gallery-item" onclick="openLightbox('static/img/pogled.jpg')">
                            <img src="static/img/pogled.jpg" alt="View from Apartment - Apartma Ivano">
                        </div>
                        <div class="gallery-item" onclick="openLightbox('static/img/soba1.jpg')">
                            <img src="static/img/soba1.jpg" alt="Bedroom 1 - Apartma Ivano">
                        </div>
                        <div class="gallery-item" onclick="openLightbox('static/img/soba2.jpg')">
                            <img src="static/img/soba2.jpg" alt="Bedroom 2 - Apartma Ivano">
                        </div>
                    </div>
                    
                    <p class="scroll-hint">
                        <i class="fas fa-arrows-alt-h"></i> Scroll horizontally to see all photos
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card bg-primary text-white border-0 shadow-lg">
                <div class="card-body p-5 text-center">
                    <h2 class="display-6 mb-3">Ready to Experience Bohinj?</h2>
                    <p class="lead mb-4">Book your mountain getaway in the heart of the Julian Alps</p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="?page=booking" class="btn btn-light btn-lg">
                            <i class="fas fa-calendar-alt"></i> Check Availability
                        </a>
                        <a href="?page=contact" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-envelope"></i> Contact Us
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lightbox for full-size images -->
<div id="lightbox" class="lightbox" onclick="closeLightbox()">
    <span class="lightbox-close">&times;</span>
    <img id="lightbox-img" src="" alt="Full size image">
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize quick date pickers
    flatpickr("#quick_checkin", {
        minDate: 'today',
        dateFormat: 'Y-m-d',
        onChange: function(selectedDates) {
            if (selectedDates.length > 0) {
                flatpickr("#quick_checkout", {
                    minDate: new Date(selectedDates[0].getTime() + 86400000),
                    dateFormat: 'Y-m-d'
                });
            }
        }
    });
});

// Lightbox functions
function openLightbox(imageSrc) {
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    lightboxImg.src = imageSrc;
    lightbox.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    const lightbox = document.getElementById('lightbox');
    lightbox.classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Close lightbox with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeLightbox();
    }
});
</script>