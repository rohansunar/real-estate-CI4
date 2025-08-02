<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Real Estate' ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Custom Styles -->
    <link href="<?= base_url('assets/css/website.css') ?>" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Top Header Section -->
    <div class="top-header bg-primary text-white py-2 fixed-top">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-phone me-2"></i>
                        <span class="me-4">+91 98765 43210</span>
                        <i class="fas fa-envelope me-2"></i>
                        <span>info@realestate.com</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex justify-content-end align-items-center gap-2">
                        <span class="small me-2">Follow Us:</span>
                        <a href="#" class="text-white hover-opacity" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-white hover-opacity ms-2" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-white hover-opacity ms-2" title="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="#" class="text-white hover-opacity ms-2" title="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top" style="top: 45px;">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="<?= base_url() ?>">
                <i class="fas fa-building me-2"></i>
                Real Estate
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('properties') ?>">Properties</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('about') ?>">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('blog') ?>">Blog</a>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#contactModal">
                            <i class="fas fa-envelope me-1"></i>Contact Us
                        </button>
                    </li>
                    <?php if (isset($user) && $user): ?>
                        <li class="nav-item dropdown ms-2">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>
                                <?= esc($user['name'] ?? $user['email']) ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= base_url('dashboard') ?>">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= base_url('auth/logout') ?>">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>



    <!-- Flash Messages -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055; margin-top: 80px;">
        <!-- Session Error Messages -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="toast show" role="alert" data-bs-autohide="true" data-bs-delay="5000">
                <div class="toast-header bg-danger text-white">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong class="me-auto">Error</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body"><?= esc(session()->getFlashdata('error')) ?></div>
            </div>
        <?php endif; ?>

        <!-- Session Success Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="toast show" role="alert" data-bs-autohide="true" data-bs-delay="5000">
                <div class="toast-header bg-success text-white">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong class="me-auto">Success</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body"><?= esc(session()->getFlashdata('success')) ?></div>
            </div>
        <?php endif; ?>

        <!-- Session Info Messages -->
        <?php if (session()->getFlashdata('info')): ?>
            <div class="toast show" role="alert" data-bs-autohide="true" data-bs-delay="5000">
                <div class="toast-header bg-info text-white">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong class="me-auto">Information</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body"><?= esc(session()->getFlashdata('info')) ?></div>
            </div>
        <?php endif; ?>

        <!-- Session Warning Messages -->
        <?php if (session()->getFlashdata('warning')): ?>
            <div class="toast show" role="alert" data-bs-autohide="true" data-bs-delay="5000">
                <div class="toast-header bg-warning text-dark">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong class="me-auto">Warning</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body"><?= esc(session()->getFlashdata('warning')) ?></div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Main Content -->
    <main style="padding-top: 125px;">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white mt-5">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center mb-4">
                        <i class="fas fa-building fs-2 text-primary me-3"></i>
                        <h3 class="h4 mb-0 fw-bold">Real Estate</h3>
                    </div>
                    <p class="text-light mb-4">
                        Your trusted partner in finding the perfect property. We help you discover your dream home with personalized service and expert guidance.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-light hover-primary transition-all" title="Facebook">
                            <i class="fab fa-facebook-f fs-5"></i>
                        </a>
                        <a href="#" class="text-light hover-primary transition-all" title="Twitter">
                            <i class="fab fa-twitter fs-5"></i>
                        </a>
                        <a href="#" class="text-light hover-primary transition-all" title="Instagram">
                            <i class="fab fa-instagram fs-5"></i>
                        </a>
                        <a href="#" class="text-light hover-primary transition-all" title="LinkedIn">
                            <i class="fab fa-linkedin-in fs-5"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h5 class="fw-semibold mb-4">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="<?= base_url('properties') ?>" class="text-light text-decoration-none hover-primary transition-all">
                                <i class="fas fa-chevron-right me-2 small"></i>Properties
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?= base_url('blog') ?>" class="text-light text-decoration-none hover-primary transition-all">
                                <i class="fas fa-chevron-right me-2 small"></i>Blog
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?= base_url('contact') ?>" class="text-light text-decoration-none hover-primary transition-all">
                                <i class="fas fa-chevron-right me-2 small"></i>Contact
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-light text-decoration-none hover-primary transition-all">
                                <i class="fas fa-chevron-right me-2 small"></i>About Us
                            </a>
                        </li>
                        <?php if (!isset($user) || !$user): ?>
                            <li class="mb-2">
                                <a href="<?= base_url('auth/login') ?>" class="text-light text-decoration-none hover-primary transition-all">
                                    <i class="fas fa-sign-in-alt me-2 small"></i>Login
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="mb-2">
                                <a href="<?= base_url('dashboard') ?>" class="text-light text-decoration-none hover-primary transition-all">
                                    <i class="fas fa-tachometer-alt me-2 small"></i>Dashboard
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h5 class="fw-semibold mb-4">Property Types</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="#" class="text-light text-decoration-none hover-primary transition-all">
                                <i class="fas fa-chevron-right me-2 small"></i>Houses
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-light text-decoration-none hover-primary transition-all">
                                <i class="fas fa-chevron-right me-2 small"></i>Apartments
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-light text-decoration-none hover-primary transition-all">
                                <i class="fas fa-chevron-right me-2 small"></i>Villas
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-light text-decoration-none hover-primary transition-all">
                                <i class="fas fa-chevron-right me-2 small"></i>Land
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-6">
                    <h5 class="fw-semibold mb-4">Newsletter</h5>
                    <p class="text-light mb-4">Subscribe to get updates on new properties and market insights.</p>
                    <form id="newsletterForm" action="<?= base_url('newsletter/subscribe') ?>" method="post" class="newsletter-form">
                        <?= csrf_field() ?>
                        <div class="input-group mb-3">
                            <input type="email" name="email" class="form-control bg-secondary border-0 text-white"
                                   placeholder="Enter your email" required>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <hr class="border-secondary my-4">
            <div class="text-center">
                <p class="text-muted mb-0 small">
                    &copy; <?= date('Y') ?> Real Estate. All rights reserved.
                    Built with <i class="fas fa-heart text-danger"></i> using CodeIgniter 4 & Bootstrap 5.
                </p>
            </div>
        </div>
    </footer>

    <!-- Contact Us Modal -->
    <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="contactModalLabel">
                        <i class="fas fa-envelope me-2"></i>Contact Us
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Contact Form -->
                    <form id="contactModalForm" action="<?= base_url('contact/submit') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="modal_name" class="form-label fw-semibold">Full Name</label>
                                <input type="text" id="modal_name" name="name" class="form-control" placeholder="Enter your full name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="modal_email" class="form-label fw-semibold">Email Address</label>
                                <input type="email" id="modal_email" name="email" class="form-control" placeholder="Enter your email" required>
                            </div>
                            <div class="col-md-6">
                                <label for="modal_phone" class="form-label fw-semibold">Phone Number</label>
                                <input type="tel" id="modal_phone" name="phone" class="form-control" placeholder="Enter your phone number" required>
                            </div>
                            <div class="col-md-6">
                                <label for="modal_properties_in" class="form-label fw-semibold">Property Interest</label>
                                <select id="modal_properties_in" name="properties_in" class="form-select" required>
                                    <option value="">Select location</option>
                                    <option value="Siliguri">Siliguri</option>
                                    <option value="Champasari">Champasari</option>
                                    <option value="Bagdogra">Bagdogra</option>
                                    <option value="Jalpaiguri">Jalpaiguri</option>
                                    <option value="Pradhan Nagar">Pradhan Nagar</option>
                                    <option value="Milan More">Milan More</option>
                                    <option value="Khaprail">Khaprail</option>
                                    <option value="Matigara">Matigara</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="modal_message" class="form-label fw-semibold">Message</label>
                                <textarea id="modal_message" name="message" rows="4" class="form-control" placeholder="Tell us about your requirements..." required></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Close
                    </button>
                    <button type="submit" form="contactModalForm" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i>Send Message
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Modal -->
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" id="notificationModalHeader">
                    <h5 class="modal-title" id="notificationModalLabel">
                        <i class="fas fa-info-circle me-2" id="notificationModalIcon"></i>
                        <span id="notificationModalTitle">Notification</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="notificationModalMessage" class="mb-0"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    <script src="<?= base_url('assets/js/website.js') ?>"></script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
