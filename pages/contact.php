<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4">Contact Us</h1>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h4>Get in Touch</h4>
                            <form method="POST" action="index.php">
                                <input type="hidden" name="action" value="contact">
                                
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
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Send Message
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4>Contact Information</h4>
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <i class="fas fa-user text-primary me-2"></i>
                                    <strong>Owner:</strong> Ivano Sabalič
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-phone text-primary me-2"></i>
                                    <strong>Phone:</strong> +386 XX XXX XXX
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-envelope text-primary me-2"></i>
                                    <strong>Email:</strong> info@apartmaivano.com
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                    <strong>Location:</strong> Kranj, Slovenia
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <h4>Response Time</h4>
                            <p>We typically respond within <strong>2-4 hours</strong> during business hours (9:00 - 20:00 CET).</p>
                            
                            <h5 class="mt-3">Languages</h5>
                            <p>We speak:</p>
                            <ul>
                                <li>Slovenian (native)</li>
                                <li>English</li>
                                <li>German</li>
                                <li>Croatian/Serbian</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-body">
                    <h4>Frequently Asked Questions</h4>
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    How do I make a reservation?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    You can make a reservation directly through our website by clicking "Book Now" and selecting your dates. We'll confirm your booking within 24 hours.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    What's included in the price?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    The price includes the apartment rental, all utilities, WiFi, parking, bed linens, towels, and basic toiletries. Cleaning fee is also included.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Is early check-in or late check-out possible?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Subject to availability, we can arrange early check-in (from 12:00) or late check-out (until 14:00) for an additional fee of €20. Please contact us in advance.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>