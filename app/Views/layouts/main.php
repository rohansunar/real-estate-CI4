<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Gold Properties' ?></title>

    <!-- Bootstrap 5 CSS with Cache-Busting -->
    <?= css_link('https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css') ?>

    <!-- Icons with Cache-Busting -->
    <?= css_link('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css') ?>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Custom Styles with Automatic Cache-Busting -->
    <?= css_link('assets/css/website.css') ?>
</head>
<body class="bg-light">
    <!-- Top Header Section - Mobile Optimized -->
    <div class="top-header bg-primary text-white py-2 fixed-top">
        <div class="container">
            <div class="row align-items-center">
                <!-- Contact Info - Full width on mobile, 8 cols on desktop -->
                <div class="col-12 col-md-8">
                    <div class="d-flex align-items-center justify-content-center justify-content-md-start flex-wrap">
                        <div class="d-flex align-items-center me-3 me-md-4">
                            <i class="fas fa-phone me-1 me-md-2"></i>
                            <span class="small">+91 94342 55059</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-envelope me-1 me-md-2"></i>
                            <span class="small">connect@goldproperties</span>
                        </div>
                    </div>
                </div>
                <!-- Social Links - Hidden on mobile, visible on desktop -->
                <div class="col-md-4 d-none d-md-block">
                    <div class="d-flex justify-content-end align-items-center gap-2">
                        <span class="small me-2">Follow Us:</span>
                        <a href="#" class="text-white hover-opacity" title="Facebook" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-white hover-opacity ms-2" title="Instagram" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-white hover-opacity ms-2" title="YouTube" aria-label="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="#" class="text-white hover-opacity ms-2" title="LinkedIn" aria-label="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

    <!-- Modern Mobile Slide-out Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-menu-header">
            <div class="mobile-menu-brand">
                <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo" class="clip-img" width="130" height="60">
                <!-- <span>Gold Properties</span> -->
            </div>
            <button class="mobile-menu-close" id="mobileMenuClose" aria-label="Close menu">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="mobile-menu-content">
            <nav class="mobile-nav">
                <a href="<?= base_url() ?>" class="mobile-nav-link">
                    <i class="fas fa-home me-3"></i>
                    <span>Home</span>
                </a>
                <a href="<?= base_url('properties') ?>" class="mobile-nav-link">
                    <i class="fas fa-building me-3"></i>
                    <span>Properties</span>
                </a>
                <a href="<?= base_url('about') ?>" class="mobile-nav-link">
                    <i class="fas fa-info-circle me-3"></i>
                    <span>About Us</span>
                </a>
                <a href="<?= base_url('blog') ?>" class="mobile-nav-link">
                    <i class="fas fa-blog me-3"></i>
                    <span>Blog</span>
                </a>
            </nav>

            <div class="mobile-menu-actions">
                <!-- Mobile Theme Toggle -->
                <button type="button" class="theme-toggle mobile-theme-toggle w-100 mb-3" id="mobileThemeToggle"
                        aria-label="Switch to dark theme">
                    <i class="fas fa-sun sun-icon"></i>
                    <i class="fas fa-moon moon-icon"></i>
                    <span class="theme-text">Dark Theme</span>
                </button>

                <button type="button" class="btn btn-primary w-100 mb-3" data-bs-toggle="modal" data-bs-target="#contactModal">
                    <i class="fas fa-envelope me-2"></i>Contact Us
                </button>

                <?php if (isset($user) && $user): ?>
                    <div class="mobile-user-section">
                        <div class="mobile-user-info">
                            <i class="fas fa-user-circle me-2"></i>
                            <span><?= esc($user['name'] ?? $user['email']) ?></span>
                        </div>
                        <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-primary w-100 mb-2">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a href="<?= base_url('auth/logout') ?>" class="btn btn-outline-danger w-100">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </div>
                <?php endif; ?>
                <!-- Login button removed from mobile menu as requested -->
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top" style="top: 45px;">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="<?= base_url() ?>">
                <img src="<?= base_url('assets/images/logo.png') ?>" class="clip-img" alt="Logo" width="130" height="60">
                <!-- Gold Properties -->
            </a>

            <!-- Modern Mobile Menu Toggle -->
            <button class="navbar-toggler modern-hamburger" type="button" id="mobileMenuToggle" aria-label="Toggle navigation menu">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </button>

            <!-- Desktop Navigation (hidden on mobile) -->
            <div class="collapse navbar-collapse d-none d-lg-flex" id="navbarNav">
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
                    <!-- Theme Toggle Button -->
                    <li class="nav-item me-2">
                        <button type="button" class="theme-toggle" id="themeToggle"
                                aria-label="Switch to dark theme"
                                title="Switch to dark theme">
                            <i class="fas fa-sun sun-icon"></i>
                            <i class="fas fa-moon moon-icon"></i>
                        </button>
                    </li>

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

    <!-- Client Testimonials Section - Only displayed on home page -->
    <?php
    /**
     * Testimonials Section Display Logic
     *
     * This section is conditionally displayed only on the home page to improve
     * user experience and reduce content repetition across the site.
     *
     * The logic checks if the current URL matches the base URL (home page)
     * by comparing various URL formats to ensure accurate detection.
     *
     * Modified: 2025-08-04 - Limited to exactly 3 testimonials and home page only
     * Fixed: 2025-08-04 - Improved URL detection logic for better reliability
     */
    // Simple and reliable home page detection using CodeIgniter's router
    $router = service('router');
    $controllerName = $router->controllerName();
    $methodName = $router->methodName();

    // Check if we're on Home controller's index method (most reliable method)
    $isHomePage = ($controllerName === '\App\Controllers\Home' && $methodName === 'index');

    // Fallback URL-based detection for additional reliability
    if (!$isHomePage) {
        $currentUrl = current_url();
        $baseUrl = base_url();
        $currentPath = parse_url($currentUrl, PHP_URL_PATH);
        $basePath = parse_url($baseUrl, PHP_URL_PATH);

        $isHomePage = (
            $currentUrl === $baseUrl ||
            $currentUrl === $baseUrl . '/' ||
            $currentPath === $basePath ||
            $currentPath === $basePath . '/' ||
            $currentPath === '/' ||
            $currentPath === '' ||
            $currentPath === '/index.php'
        );
    }
    ?>
    <?php if ($isHomePage): ?>
    <section class="testimonials-section py-5" id="testimonials" aria-labelledby="testimonials-heading">
        <div class="container">
            <!-- Section Header -->
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <h2 id="testimonials-heading" class="display-5 fw-bold text-dark mb-4">
                        What Our <span class="text-primary">Clients Say</span>
                    </h2>
                    <p class="lead text-muted mb-0">
                        Don't just take our word for it. Here's what our satisfied clients have to say about their experience with us.
                    </p>
                </div>
            </div>

            <!-- Testimonials Grid - Limited to exactly 3 testimonials -->
            <div class="testimonials-grid">
                <!-- Testimonial 1 -->
                <article class="testimonial-card animate-fade-in" role="article" aria-labelledby="testimonial-1-author">
                    <div class="testimonial-stars mb-3" role="img" aria-label="5 out of 5 stars">
                        <i class="fas fa-star" aria-hidden="true"></i>
                        <i class="fas fa-star" aria-hidden="true"></i>
                        <i class="fas fa-star" aria-hidden="true"></i>
                        <i class="fas fa-star" aria-hidden="true"></i>
                        <i class="fas fa-star" aria-hidden="true"></i>
                    </div>
                    <blockquote class="testimonial-quote">
                        The team at Real Estate helped us find our dream home in Siliguri. Their professionalism and attention to detail made the entire process smooth and stress-free. Highly recommended!
                    </blockquote>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar-wrapper">
                            <div class="testimonial-avatar testimonial-avatar-initials" data-initials="RK">
                                <span>RK</span>
                            </div>
                        </div>
                        <div class="testimonial-info">
                            <h5 id="testimonial-1-author">Rajesh Kumar</h5>
                            <p class="testimonial-role mb-0">Property Buyer, Champasari</p>
                        </div>
                    </div>
                </article>

                <!-- Testimonial 2 -->
                <article class="testimonial-card animate-fade-in" role="article" aria-labelledby="testimonial-2-author">
                    <div class="testimonial-stars mb-3" role="img" aria-label="5 out of 5 stars">
                        <i class="fas fa-star" aria-hidden="true"></i>
                        <i class="fas fa-star" aria-hidden="true"></i>
                        <i class="fas fa-star" aria-hidden="true"></i>
                        <i class="fas fa-star" aria-hidden="true"></i>
                        <i class="fas fa-star" aria-hidden="true"></i>
                    </div>
                    <blockquote class="testimonial-quote">
                        Excellent service! They understood exactly what we were looking for and found us the perfect apartment in Bagdogra. The entire team was very supportive throughout the process.
                    </blockquote>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar-wrapper">
                            <div class="testimonial-avatar testimonial-avatar-initials" data-initials="PS">
                                <span>PS</span>
                            </div>
                        </div>
                        <div class="testimonial-info">
                            <h5 id="testimonial-2-author">Priya Sharma</h5>
                            <p class="testimonial-role mb-0">First-time Buyer, Bagdogra</p>
                        </div>
                    </div>
                </article>

                <!-- Testimonial 3 -->
                <article class="testimonial-card animate-fade-in" role="article" aria-labelledby="testimonial-3-author">
                    <div class="testimonial-stars mb-3" role="img" aria-label="5 out of 5 stars">
                        <i class="fas fa-star" aria-hidden="true"></i>
                        <i class="fas fa-star" aria-hidden="true"></i>
                        <i class="fas fa-star" aria-hidden="true"></i>
                        <i class="fas fa-star" aria-hidden="true"></i>
                        <i class="fas fa-star" aria-hidden="true"></i>
                    </div>
                    <blockquote class="testimonial-quote">
                        Professional, reliable, and trustworthy. They helped us sell our property in Jalpaiguri at a great price and made the whole transaction seamless. Thank you for the excellent service!
                    </blockquote>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar-wrapper">
                            <div class="testimonial-avatar testimonial-avatar-initials" data-initials="AD">
                                <span>AD</span>
                            </div>
                        </div>
                        <div class="testimonial-info">
                            <h5 id="testimonial-3-author">Amit Das</h5>
                            <p class="testimonial-role mb-0">Property Seller, Jalpaiguri</p>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Call to Action -->
            <div class="row justify-content-center mt-5">
                <div class="col-lg-6 text-center">
                    <p class="text-muted mb-4">Ready to join our satisfied clients?</p>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                        <a href="<?= base_url('properties') ?>" class="btn btn-primary btn-lg">
                            <i class="fas fa-search me-2"></i>
                            Browse Properties
                        </a>
                        <a href="<?= base_url('contact') ?>" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-phone me-2"></i>
                            Contact Us
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="bg-dark text-white mt-5">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center mb-4">
                       <img src="<?= base_url('assets/images/logo.png') ?>" class="clip-img" alt="Logo" width="130" height="60">
                        <!-- <h3 class="h4 mb-0 fw-bold">Gold Properties</h3> -->
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
                                <i class="fas fa-chevron-right me-2"></i>Properties
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?= base_url('blog') ?>" class="text-light text-decoration-none hover-primary transition-all">
                                <i class="fas fa-chevron-right me-2"></i>Blog
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?= base_url('contact') ?>" class="text-light text-decoration-none hover-primary transition-all">
                                <i class="fas fa-chevron-right me-2"></i>Contact
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/about" class="text-light text-decoration-none hover-primary transition-all">
                                <i class="fas fa-chevron-right me-2"></i>About Us
                            </a>
                        </li>
                        <?php if (!isset($user) || !$user): ?>
                            <li class="mb-2">
                                <a href="<?= base_url('auth/login') ?>" class="text-light text-decoration-none hover-primary transition-all">
                                    <i class="fas fa-sign-in-alt me-2"></i>Admin Login
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="mb-2">
                                <a href="<?= base_url('dashboard') ?>" class="text-light text-decoration-none hover-primary transition-all">
                                    <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="mb-2">
                            <a href="<?= base_url('agent/login') ?>" class="text-light text-decoration-none hover-primary transition-all">
                                <i class="fas fa-user-tie me-2"></i>Business Associate Login
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h5 class="fw-semibold mb-4">Property Types</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="#" class="text-light text-decoration-none hover-primary transition-all">
                                <i class="fas fa-chevron-right me-2"></i>Houses
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-light text-decoration-none hover-primary transition-all">
                                <i class="fas fa-chevron-right me-2"></i>Apartments
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-light text-decoration-none hover-primary transition-all">
                                <i class="fas fa-chevron-right me-2"></i>Villas
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-light text-decoration-none hover-primary transition-all">
                                <!-- <i class="fas fa-chevron-right me-2 small"></i> -->
                                 <i class="fas fa-chevron-right me-2"></i>
                                Land
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-6">
                    <h5 class="fw-semibold mb-4">Contact Information</h5>
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-map-marker-alt text-primary me-3"></i>
                        <div>
                            <div class="fw-semibold">Address</div>
                            <div class="text-light small">Siliguri, West Bengal, India</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-phone text-primary me-3"></i>
                        <div>
                            <div class="fw-semibold">Phone</div>
                            <div class="text-light small">+91 94342 55059</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-envelope text-primary me-3"></i>
                        <div>
                            <div class="fw-semibold">Email</div>
                            <div class="text-light small">connect@goldproperties.in</div>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="border-secondary my-4">
            <div class="text-center">
                <p class="text-muted mb-0 small">
                    &copy; <?= date('Y') ?> Gold Properties. All rights reserved.
                    <!-- Built with <i class="fas fa-heart text-danger"></i>. -->
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

    <!-- Bootstrap 5 JS with Cache-Busting -->
    <?= js_script('https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js') ?>

    <!-- Custom JavaScript with Automatic Cache-Busting -->
    <?= js_script('assets/js/website.js') ?>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
