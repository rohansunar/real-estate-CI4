<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="position-relative py-5 bg-primary text-white" style="margin-top: 76px;">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-4">Contact Us</h1>
                <p class="fs-5 mb-0">
                    Get in touch with our expert real estate team. We're here to help you find your perfect property in Siliguri and surrounding areas.
                </p>
            </div>
            <div class="col-lg-4">
                <div class="text-center">
                    <i class="fas fa-phone display-1 text-white opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-5">
    <div class="container py-5">
        <div class="row g-5">
            <!-- Contact Form -->
            <div class="col-lg-8">
                <div class="bg-white rounded-4 shadow-lg p-4 p-md-5">
                    <h2 class="h3 fw-bold text-dark mb-4">Send us a Message</h2>
                    
                    <!-- Display Success/Error Messages -->
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form id="contactPageForm" action="<?= base_url('contact/submit') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">Full Name *</label>
                                <input type="text" id="name" name="name" class="form-control" 
                                       value="<?= old('name') ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Email Address *</label>
                                <input type="email" id="email" name="email" class="form-control" 
                                       value="<?= old('email') ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">Phone Number *</label>
                                <input type="tel" id="phone" name="phone" class="form-control" 
                                       value="<?= old('phone') ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="properties_in" class="form-label fw-semibold">Property Interest *</label>
                                <select id="properties_in" name="properties_in" class="form-select" required>
                                    <option value="">Select your interest</option>
                                    <option value="Champasari" <?= old('properties_in') === 'Champasari' ? 'selected' : '' ?>>Champasari</option>
                                    <option value="Bagdogra" <?= old('properties_in') === 'Bagdogra' ? 'selected' : '' ?>>Bagdogra</option>
                                    <option value="Jalpaiguri" <?= old('properties_in') === 'Jalpaiguri' ? 'selected' : '' ?>>Jalpaiguri</option>
                                    <option value="Pradhan Nagar" <?= old('properties_in') === 'Pradhan Nagar' ? 'selected' : '' ?>>Pradhan Nagar</option>
                                    <option value="Milan More" <?= old('properties_in') === 'Milan More' ? 'selected' : '' ?>>Milan More</option>
                                    <option value="Khaprail" <?= old('properties_in') === 'Khaprail' ? 'selected' : '' ?>>Khaprail</option>
                                    <option value="Other" <?= old('properties_in') === 'Other' ? 'selected' : '' ?>>Other Location</option>
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <label for="message" class="form-label fw-semibold">Message *</label>
                                <textarea id="message" name="message" class="form-control" rows="5" 
                                          placeholder="Tell us about your property requirements..." required><?= old('message') ?></textarea>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Contact Information -->
            <div class="col-lg-4">
                <div class="bg-light rounded-4 p-4 p-md-5 h-100">
                    <h3 class="h4 fw-bold text-dark mb-4">Get in Touch</h3>
                    
                    <div class="mb-4">
                        <div class="d-flex align-items-start mb-3">
                            <div class="bg-primary text-white rounded-circle p-2 me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h5 class="fw-semibold mb-1">Office Address</h5>
                                <p class="text-muted mb-0">Siliguri, West Bengal, India</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-start mb-3">
                            <div class="bg-primary text-white rounded-circle p-2 me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <h5 class="fw-semibold mb-1">Phone</h5>
                                <p class="text-muted mb-0">+91 XXXXX XXXXX</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-start mb-3">
                            <div class="bg-primary text-white rounded-circle p-2 me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h5 class="fw-semibold mb-1">Email</h5>
                                <p class="text-muted mb-0">info@realestate.com</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-start">
                            <div class="bg-primary text-white rounded-circle p-2 me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h5 class="fw-semibold mb-1">Business Hours</h5>
                                <p class="text-muted mb-0">Mon - Sat: 9:00 AM - 6:00 PM</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-top pt-4">
                        <h5 class="fw-semibold mb-3">Follow Us</h5>
                        <div class="d-flex gap-2">
                            <a href="#" class="btn btn-outline-primary btn-sm">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="btn btn-outline-primary btn-sm">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="btn btn-outline-primary btn-sm">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="btn btn-outline-primary btn-sm">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Service Areas -->
<section class="py-5 bg-light">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="h2 fw-bold text-dark mb-3">Our Service Areas</h2>
            <p class="text-muted">We serve the following locations in and around Siliguri</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4 col-sm-6">
                <div class="text-center">
                    <div class="bg-primary text-white rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-map-marker-alt fs-4"></i>
                    </div>
                    <h5 class="fw-semibold">Champasari</h5>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="text-center">
                    <div class="bg-primary text-white rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-map-marker-alt fs-4"></i>
                    </div>
                    <h5 class="fw-semibold">Bagdogra</h5>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="text-center">
                    <div class="bg-primary text-white rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-map-marker-alt fs-4"></i>
                    </div>
                    <h5 class="fw-semibold">Jalpaiguri</h5>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="text-center">
                    <div class="bg-primary text-white rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-map-marker-alt fs-4"></i>
                    </div>
                    <h5 class="fw-semibold">Pradhan Nagar</h5>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="text-center">
                    <div class="bg-primary text-white rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-map-marker-alt fs-4"></i>
                    </div>
                    <h5 class="fw-semibold">Milan More</h5>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="text-center">
                    <div class="bg-primary text-white rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-map-marker-alt fs-4"></i>
                    </div>
                    <h5 class="fw-semibold">Khaprail</h5>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
