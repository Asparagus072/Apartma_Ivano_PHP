
<!-- Hero Section -->
<section class="hero text-white text-center py-5 mb-5" style="background: url('/static/img/bohinj.jpg') center/cover no-repeat;">
    <div class="container">
        <h1>Welcome to Apartma Ivano</h1>
        <p>Relax, enjoy nature, and experience Bohinj at its best</p>
        <a href="/booking" class="btn btn-primary btn-lg mt-3">Book Now</a>
    </div>
</section>

<!-- About Apartment Section -->
<section id="about" class="mb-5">
    <div class="container">
        <h2>About the Apartment</h2>
        <p>
            Apartma Ivano is a cozy and modern apartment in the heart of Bohinj, Slovenia. 
            Owned by Ivano Sabalič, the apartment offers comfort, tranquility, and direct access 
            to the stunning natural surroundings of the Julian Alps and Lake Bohinj. 
        </p>
        <p>
            The apartment is fully furnished with a modern kitchen, spacious living area, 
            comfortable bedrooms, and all the amenities needed for a perfect holiday. 
            Ideal for couples, families, and nature lovers.
        </p>
    </div>
</section>

<!-- Location Section -->
<section id="location" class="bg-light py-5 mb-5">
    <div class="container">
        <h2>Location</h2>
        <p>
            Apartma Ivano is located at <strong>Srednja Vas v Bohinju 101b, Bohinj, Slovenia</strong>. 
            Situated just minutes from Lake Bohinj, the apartment is close to hiking trails, 
            ski resorts, and local restaurants. Enjoy the serene natural beauty of Slovenia 
            while being conveniently connected to nearby towns.
        </p>
        <div class="ratio ratio-16x9 mt-3">
            <iframe 
                src="https://www.google.com/maps?q=Srednja+Vas+v+Bohinju+101b&output=embed" 
                style="border:0;" allowfullscreen="" loading="lazy">
            </iframe>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="mb-5">
    <div class="container">
        <h2>Features</h2>
        <div class="row mt-4">
            <div class="col-md-4 mb-3">
                <div class="card p-3 h-100">
                    <h5>Modern Kitchen</h5>
                    <p>Fully equipped for all your culinary needs.</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card p-3 h-100">
                    <h5>Comfortable Bedrooms</h5>
                    <p>Relax in cozy rooms with stunning views.</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card p-3 h-100">
                    <h5>Natural Surroundings</h5>
                    <p>Close to Lake Bohinj, hiking, and ski resorts.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
renderPage('Home', $content);