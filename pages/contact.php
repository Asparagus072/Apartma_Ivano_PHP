<div class="container">
    <h1 class="mb-4">Contact Us</h1>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h3>Send us a message</h3>
                    <form method="POST" action="index.php?page=contact">
                        <div class="mb-3">
                            <label for="name" class="form-label">Your Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4>Contact Information</h4>
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <i class="fas fa-map-marker-alt text-primary"></i>
                            <strong>Address:</strong><br>
                            Kranj, Slovenia
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-phone text-primary"></i>
                            <strong>Phone:</strong><br>
                            +386 XX XXX XXX
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-envelope text-primary"></i>
                            <strong>Email:</strong><br>
                            info@apartmaivano.com
                        </li>
                    </ul>
                    
                    <h5>Check-in/Check-out Times</h5>
                    <ul class="list-unstyled">
                        <li>Check-in: 3:00 PM - 10:00 PM</li>
                        <li>Check-out: 11:00 AM</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>