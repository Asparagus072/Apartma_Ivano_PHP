<style>
    .hero {
        background: linear-gradient(135deg, rgba(46,125,50,.85), rgba(25,118,210,.85)),
                    url('static/img/jezero.jpg') center/cover no-repeat;
        background-attachment: fixed;
    }

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
                <h1 class="display-3 fw-bold mb-4"><?php echo __('hero.title'); ?></h1>
                <p class="lead mb-4"><?php echo __('hero.subtitle'); ?></p>
                <p class="fs-5 mb-4"><?php echo __('hero.tagline'); ?></p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="?page=booking" class="btn btn-light btn-lg">
                        <i class="fas fa-calendar-check"></i> <?php echo __('hero.book_btn'); ?>
                    </a>
                    <a href="?page=about" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-info-circle"></i> <?php echo __('hero.learn_btn'); ?>
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
                    <h3 class="text-center mb-4"><?php echo __('home.check_avail'); ?></h3>
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label for="quick_checkin" class="form-label"><?php echo __('home.checkin'); ?></label>
                            <input type="text" class="form-control" id="quick_checkin" placeholder="<?php echo __('home.checkin'); ?>">
                        </div>
                        <div class="col-md-5">
                            <label for="quick_checkout" class="form-label"><?php echo __('home.checkout'); ?></label>
                            <input type="text" class="form-control" id="quick_checkout" placeholder="<?php echo __('home.checkout'); ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <a href="?page=booking" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> <?php echo __('home.check_btn'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features -->
    <div class="row mb-5">
        <div class="col-12 text-center mb-4">
            <h2 class="display-5 fw-bold"><?php echo __('home.why_title'); ?></h2>
            <p class="text-muted"><?php echo __('home.why_sub'); ?></p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card h-100 text-center border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="feature-icon bg-primary bg-gradient text-white rounded-circle mb-3 mx-auto" style="width:80px;height:80px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-mountain fa-2x"></i>
                    </div>
                    <h4><?php echo __('home.feat1_title'); ?></h4>
                    <p class="text-muted"><?php echo __('home.feat1_text'); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 text-center border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="feature-icon bg-success bg-gradient text-white rounded-circle mb-3 mx-auto" style="width:80px;height:80px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-home fa-2x"></i>
                    </div>
                    <h4><?php echo __('home.feat2_title'); ?></h4>
                    <p class="text-muted"><?php echo __('home.feat2_text'); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 text-center border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="feature-icon bg-info bg-gradient text-white rounded-circle mb-3 mx-auto" style="width:80px;height:80px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-star fa-2x"></i>
                    </div>
                    <h4><?php echo __('home.feat3_title'); ?></h4>
                    <p class="text-muted"><?php echo __('home.feat3_text'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Amenities -->
    <div class="row mb-5">
        <div class="col-12 text-center mb-4">
            <h2 class="display-6 fw-bold"><?php echo __('home.included'); ?></h2>
        </div>
    </div>

    <div class="row g-3 mb-5">
        <?php
        $amenities = [
            ['fa-wifi',      'amenity.wifi'],
            ['fa-car',       'amenity.parking'],
            ['fa-snowflake', 'amenity.ac'],
            ['fa-tv',        'amenity.tv'],
            ['fa-utensils',  'amenity.kitchen'],
            ['fa-tshirt',    'amenity.washer'],
            ['fa-mug-hot',   'amenity.coffee'],
            ['fa-door-open', 'amenity.balcony'],
        ];
        foreach ($amenities as [$icon, $key]):
        ?>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 h-100 border-0 bg-light">
                <i class="fas <?php echo $icon; ?> text-primary fa-2x mb-2"></i>
                <small class="fw-bold"><?php echo __($key); ?></small>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Pricing -->
    <div class="row mb-5">
        <div class="col-lg-10 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4"><?php echo __('home.pricing'); ?></h2>
                    <div class="row text-center justify-content-center">
                        <div class="col-md-5">
                            <div class="p-3">
                                <div class="price-badge mb-2">€130/night</div>
                                <h6><?php echo __('home.prepost'); ?></h6>
                                <small class="text-muted"><?php echo __('home.prepost_months'); ?></small>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="p-3">
                                <div class="price-badge mb-2">€150/night</div>
                                <h6><?php echo __('home.peak'); ?></h6>
                                <small class="text-muted"><?php echo __('home.peak_months'); ?></small>
                            </div>
                        </div>
                    </div>
                    <hr class="my-4">
                    <div class="text-center">
                        <h5 class="mb-3"><i class="fas fa-percent text-success"></i> <?php echo __('home.discounts'); ?></h5>
                        <div class="row justify-content-center">
                            <div class="col-md-4"><p class="mb-0"><strong><?php echo __('home.disc_7'); ?></strong></p></div>
                            <div class="col-md-4"><p class="mb-0"><strong><?php echo __('home.disc_14'); ?></strong></p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Location -->
    <div class="row mb-5">
        <div class="col-12 text-center mb-4">
            <h2 class="display-6 fw-bold"><?php echo __('home.explore'); ?></h2>
            <p class="text-muted"><?php echo __('home.explore_sub'); ?></p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h5><i class="fas fa-map-marker-alt text-info"></i> <?php echo __('home.nearby'); ?></h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Lake Bohinj - 4 km (5 min)</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Ski Center Vogel - 8 km (10 min)</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Waterfall Savica - 7 km</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Cable car Vogel - 4.9 km</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Triglav National Park</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Restaurant Pri Hrvatu - 300 m</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h5><i class="fas fa-car text-primary"></i> <?php echo __('home.daytrips'); ?></h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Lake Bled - 30 min</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Ljubljana - 1 hour</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Ljubljana Airport - 63 km</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Kranjska Gora - 45 min</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Italy (Tarvisio) - 1 hour</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Austria - 1.5 hours</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Summer Activities -->
    <div class="row mb-5">
        <div class="col-12 text-center mb-4">
            <h2 class="display-6 fw-bold"><?php echo __('home.summer_act'); ?></h2>
            <p class="text-muted"><?php echo __('home.summer_sub'); ?></p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="text-center mb-4"><i class="fas fa-sun text-warning"></i> <?php echo __('act.title'); ?></h5>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">🏊 <?php echo __('act.swim'); ?></li>
                                <li class="mb-2">🥾 <?php echo __('act.hike'); ?></li>
                                <li class="mb-2">🚴 <?php echo __('act.bike'); ?></li>
                                <li class="mb-2">🛶 <?php echo __('act.kayak'); ?></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">🧗 <?php echo __('act.climb'); ?></li>
                                <li class="mb-2">🎣 <?php echo __('act.fish'); ?></li>
                                <li class="mb-2">🚡 <?php echo __('act.cable'); ?></li>
                                <li class="mb-2">📸 <?php echo __('act.photo'); ?></li>
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
                    <h2 class="text-center mb-4"><i class="fas fa-images text-primary"></i> <?php echo __('home.gallery'); ?></h2>
                    <p class="text-center text-muted mb-4"><?php echo __('home.gallery_sub'); ?></p>
                    <div class="gallery-container">
                        <?php
                        $photos = [
                            ['static/img/hisa.jpg',    'House Exterior'],
                            ['static/img/dnevna.jpg',  'Living Room'],
                            ['static/img/dnevna2.jpg', 'Living Area'],
                            ['static/img/dnevna3.jpg', 'Living Space'],
                            ['static/img/kopalnica.jpg','Bathroom'],
                            ['static/img/kuhinja.jpg', 'Kitchen'],
                            ['static/img/pogled.jpg',  'View'],
                            ['static/img/soba1.jpg',   'Bedroom 1'],
                            ['static/img/soba2.jpg',   'Bedroom 2'],
                        ];
                        foreach ($photos as [$src, $alt]):
                        ?>
                        <div class="gallery-item" onclick="openLightbox('<?php echo $src; ?>')">
                            <img src="<?php echo $src; ?>" alt="<?php echo $alt; ?> - Apartma Ivano">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <p class="scroll-hint">
                        <i class="fas fa-arrows-alt-h"></i> <?php echo __('home.gallery_hint'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card bg-primary text-white border-0 shadow-lg">
                <div class="card-body p-5 text-center">
                    <h2 class="display-6 mb-3"><?php echo __('home.cta_title'); ?></h2>
                    <p class="lead mb-4"><?php echo __('home.cta_sub'); ?></p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="?page=booking" class="btn btn-light btn-lg">
                            <i class="fas fa-calendar-alt"></i> <?php echo __('home.cta_avail'); ?>
                        </a>
                        <a href="?page=contact" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-envelope"></i> <?php echo __('home.cta_contact'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lightbox -->
<div id="lightbox" class="lightbox" onclick="closeLightbox()">
    <span class="lightbox-close">&times;</span>
    <img id="lightbox-img" src="" alt="Full size image">
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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

function openLightbox(src) {
    document.getElementById('lightbox-img').src = src;
    document.getElementById('lightbox').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('lightbox').classList.remove('active');
    document.body.style.overflow = 'auto';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });
</script>