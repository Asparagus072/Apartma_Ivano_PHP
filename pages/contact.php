<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4"><?php echo __('contact.title'); ?></h1>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h4><?php echo __('contact.touch'); ?></h4>
                            <form method="POST" action="index.php">
                                <input type="hidden" name="action" value="contact">

                                <div class="mb-3">
                                    <label for="name" class="form-label"><?php echo __('contact.name'); ?></label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label"><?php echo __('contact.email'); ?></label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>

                                <div class="mb-3">
                                    <label for="message" class="form-label"><?php echo __('contact.message'); ?></label>
                                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> <?php echo __('contact.send'); ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4><?php echo __('contact.info'); ?></h4>
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <i class="fas fa-user text-primary me-2"></i>
                                    <strong><?php echo __('contact.owner'); ?>:</strong> Martina Mašić Sabalić
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-phone text-primary me-2"></i>
                                    <strong><?php echo __('book.phone'); ?>:</strong> +386 40 395 807
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-envelope text-primary me-2"></i>
                                    <strong><?php echo __('book.email'); ?>:</strong> mmsabalic@gmail.com
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                    <strong><?php echo __('about.location'); ?>:</strong> Srednja vas v Bohinju, Slovenia
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h4><?php echo __('contact.response'); ?></h4>
                            <p><?php echo __('contact.resp_text'); ?></p>

                            <h5 class="mt-3"><?php echo __('contact.langs'); ?></h5>
                            <p><?php echo __('about.langs_v'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <h4><?php echo __('contact.faq'); ?></h4>
                    <div class="accordion" id="faqAccordion">

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    <?php echo __('faq.q1'); ?>
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body"><?php echo __('faq.a1'); ?></div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    <?php echo __('faq.q2'); ?>
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body"><?php echo __('faq.a2'); ?></div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    <?php echo __('faq.q3'); ?>
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body"><?php echo __('faq.a3'); ?></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>