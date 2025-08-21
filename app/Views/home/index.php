<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Hero Section with Modern Carousel -->
<section class="position-relative hero-section d-flex align-items-center justify-content-center overflow-hidden">
    <!-- Background Carousel -->
    <div class="position-absolute top-0 start-0 w-100 h-100 modern-carousel" id="heroCarousel">
        <!-- Carousel Slides using house images -->
        <div class="carousel-slide position-absolute top-0 start-0 w-100 h-100 active">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background-image: url('<?= base_url('assets/images/house1.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark" style="opacity: 0.4;"></div>
        </div>

        <div class="carousel-slide position-absolute top-0 start-0 w-100 h-100">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background-image: url('<?= base_url('assets/images/house2.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark" style="opacity: 0.4;"></div>
        </div>

        <div class="carousel-slide position-absolute top-0 start-0 w-100 h-100">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background-image: url('<?= base_url('assets/images/house3.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark" style="opacity: 0.4;"></div>
        </div>

        <div class="carousel-slide position-absolute top-0 start-0 w-100 h-100">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background-image: url('<?= base_url('assets/images/house5.png') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark" style="opacity: 0.4;"></div>
        </div>


        <!-- Carousel Dots (Bottom Navigation) -->
        <div class="position-absolute bottom-0 start-50 translate-middle-x d-flex gap-3 mb-4" style="z-index: 10;">
            <button class="carousel-dot active" data-slide="0" aria-label="Go to slide 1"></button>
            <button class="carousel-dot" data-slide="1" aria-label="Go to slide 2"></button>
            <button class="carousel-dot" data-slide="2" aria-label="Go to slide 3"></button>
            <button class="carousel-dot" data-slide="4" aria-label="Go to slide 4"></button>
        </div>
    </div>
    
    <!-- Hero Content -->
    <div class="position-relative container" style="z-index: 10;">
        <div class="row g-5 align-items-center">
            <!-- Left Content -->
            <div class="col-lg-8 text-white animate-slide-in-left">
                <h1 class="display-2 fw-bold mb-4 lh-1">
                    Find Your
                    <span class="text-gradient d-block">
                        Dream Property
                    </span>
                </h1>
                <p class="fs-4 mb-5 text-light">
                    Discover the perfect property that matches your lifestyle and budget. From cozy apartments to luxury villas, we have it all.
                </p>
                <div class="d-flex flex-column flex-sm-row gap-3 mb-5">
                    <a href="<?= base_url('properties') ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-search me-2"></i>
                        Browse Properties
                    </a>
                    <a href="#properties" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-play me-2"></i>
                        Learn More
                    </a>
                </div>

                <!-- Stats -->
                <div class="row g-4 mt-4">
                    <div class="col-4 text-center">
                        <div class="display-5 fw-bold text-warning">500+</div>
                        <div class="small text-light">Properties</div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="display-5 fw-bold text-warning">1000+</div>
                        <div class="small text-light">Happy Clients</div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="display-5 fw-bold text-warning">50+</div>
                        <div class="small text-light">Locations</div>
                    </div>
                </div>
            </div>

            <!-- Right Content - Smaller Contact Form -->
            <div class="col-lg-4 animate-slide-in-right">
                <div class="bg-white rounded-4 shadow-lg p-3 p-md-4">
                    <h4 class="h5 fw-bold text-dark mb-3">Get In Touch</h4>
                    <form id="getInTouchForm" action="<?= base_url('contact/submit') ?>" method="post" data-validate>
                        <?= csrf_field() ?>

                        <div class="mb-2">
                            <input type="text" id="name" name="name" class="form-control" placeholder="Full Name" required>
                        </div>

                        <div class="mb-2">
                            <input type="email" id="email" name="email" class="form-control" placeholder="Email Address" required>
                        </div>

                        <div class="mb-2">
                            <input type="tel" id="phone" name="phone" class="form-control" placeholder="Phone Number" required>
                        </div>

                        <div class="mb-2">
                            <select id="properties_in" name="properties_in" class="form-select" required>
                                <option value="">Property Interest</option>
                                <option value="Siliguri">Siliguri</option>
                                <option value="Champasari">Champasari</option>
                                <option value="Bagdogra">Bagdogra</option>
                                <option value="Jalpaiguri">Jalpaiguri</option>
                                <option value="Pradhan Nagar">Pradhan Nagar</option>
                                <option value="Milan More">Milan More</option>
                                <option value="Khaprail">Khaprail</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <textarea id="message" name="message" rows="3" class="form-control" placeholder="Your message..." required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane me-1"></i>
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Properties Section -->
<?php if (!empty($featuredPropertiesSection)): ?>
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="text-center mb-5 animate-on-scroll">
            <div class="d-flex align-items-center justify-content-center mb-3">
                <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                    <i class="fas fa-star text-warning fs-3"></i>
                </div>
                <div>
                    <h2 class="display-5 fw-bold text-dark mb-1">Featured Properties</h2>
                    <p class="text-muted mb-0">Handpicked premium properties just for you</p>
                </div>
            </div>
            <p class="fs-5 text-muted mx-auto" style="max-width: 600px;">
                Discover our carefully selected featured properties that offer exceptional value, prime locations, and outstanding amenities.
            </p>
        </div>

        <div class="row g-4 mb-5">
            <?php foreach ($featuredPropertiesSection as $index => $property): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm card-hover animate-on-scroll position-relative" style="animation-delay: <?= $index * 0.1 ?>s;">
                        <?php
                        // Use ImageDisplayService for consistent image handling
                        $imageDisplayService = new \App\Services\ImageDisplayService();
                        $images = is_string($property['images']) ? json_decode($property['images'], true) : $property['images'];
                        $firstImage = !empty($images) ? $images[0] : null;
                        $propertyImage = $imageDisplayService->getOptimizedImageUrl($firstImage, 'card');
                        ?>

                        <!-- Featured Badge -->
                        <div class="position-absolute top-0 end-0 m-3" style="z-index: 10;">
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-star me-1"></i>Featured
                            </span>
                        </div>

                        <!-- Property Image with Lazy Loading -->
                        <div class="position-relative overflow-hidden">
                            <?php
                            // Use ImageDisplayService for optimized image display
                            $imageDisplayService = new \App\Services\ImageDisplayService();
                            $images = is_string($property['images']) ? json_decode($property['images'], true) : $property['images'];
                            $firstImage = !empty($images) ? $images[0] : null;

                            // Get optimized image URL for card display
                            $optimizedImageUrl = $imageDisplayService->getOptimizedImageUrl($firstImage, 'card');
                            $srcset = $imageDisplayService->generateSrcset($firstImage);
                            ?>
                            <img src="<?= base_url('assets/images/placeholder.svg') ?>"
                                 data-lazy-src="<?= $optimizedImageUrl ?>"
                                 <?php if ($srcset): ?>data-lazy-srcset="<?= $srcset ?>"<?php endif; ?>
                                 data-fallback="<?= base_url('assets/images/default-property.svg') ?>"
                                 class="card-img-top property-image"
                                 alt="<?= esc($property['title']) ?>"
                                 style="height: 250px; object-fit: cover;">
                            <div class="position-absolute bottom-0 start-0 m-3">
                                <span class="badge bg-primary">
                                    <?= esc(ucfirst($property['type'])) ?>
                                </span>
                            </div>
                        </div>

                        <!-- Property Details -->
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold text-dark mb-2"><?= esc($property['title']) ?></h5>
                            <p class="card-text text-muted mb-3 flex-grow-1">
                                <?= esc(substr($property['description'], 0, 100)) ?>...
                            </p>

                            <!-- Property Info -->
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center text-muted">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    <small><?= esc($property['location']) ?></small>
                                </div>
                                <?php if ($property['area']): ?>
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="fas fa-expand-arrows-alt me-2"></i>
                                        <small><?= number_format($property['area']) ?> sq ft</small>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- View Details Button -->
                            <a href="<?= base_url('properties/' . urlencode($property['location']) . '/' . $property['id']) ?>"
                               class="btn btn-primary">
                                <i class="fas fa-eye me-2"></i>
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- View All Featured Properties Button -->
        <div class="text-center animate-on-scroll">
            <a href="<?= base_url('properties?featured=1') ?>" class="btn btn-outline-warning btn-lg">
                <i class="fas fa-star me-2"></i>
                View All Featured Properties
                <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Properties Section -->
<section id="properties" class="py-5 bg-light">
    <div class="container py-5">
        <div class="text-center mb-5 animate-on-scroll">
            <h2 class="display-4 fw-bold text-dark mb-4">Explore Properties by Type</h2>
            <p class="fs-5 text-muted mx-auto" style="max-width: 600px;">
                Discover our diverse collection of properties, from cozy homes to luxury villas, organized by type to help you find exactly what you're looking for.
            </p>
        </div>

        <?php
        $propertyTypes = [
            'house' => ['title' => 'Houses', 'icon' => 'fas fa-home', 'description' => 'Comfortable family homes with modern amenities'],
            'villa' => ['title' => 'Villas', 'icon' => 'fas fa-building', 'description' => 'Luxury villas with premium features and spacious layouts'],
            'land' => ['title' => 'Land', 'icon' => 'fas fa-map', 'description' => 'Prime land plots for your dream construction projects'],
            'apartment' => ['title' => 'Apartments', 'icon' => 'fas fa-city', 'description' => 'Modern apartments in convenient urban locations']
        ];
        ?>

        <?php foreach ($propertyTypes as $type => $typeInfo): ?>
            <?php if (!empty($propertiesByType[$type])): ?>
                <!-- <?= $typeInfo['title'] ?> Section -->
                <div class="mb-5">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="<?= $typeInfo['icon'] ?> text-primary fs-4"></i>
                            </div>
                            <div>
                                <h3 class="h3 fw-bold text-dark mb-1"><?= $typeInfo['title'] ?></h3>
                                <p class="text-muted mb-0"><?= $typeInfo['description'] ?></p>
                            </div>
                        </div>
                        <a href="<?= base_url('properties?type=' . $type) ?>" class="btn btn-outline-primary">
                            View All <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>

                    <div class="row g-4">
                        <?php foreach ($propertiesByType[$type] as $index => $property): ?>
                            <div class="col-lg-4 col-md-6">
                                <div class="card h-100 shadow-sm card-hover animate-on-scroll position-relative" style="animation-delay: <?= $index * 0.1 ?>s;">
                                    <?php
                                    // Use ImageDisplayService for consistent image handling
                                    $imageDisplayService = new \App\Services\ImageDisplayService();
                                    $images = is_string($property['images']) ? json_decode($property['images'], true) : $property['images'];
                                    $firstImage = !empty($images) ? $images[0] : null;
                                    $propertyImage = $imageDisplayService->getOptimizedImageUrl($firstImage, 'card');
                                    ?>

                                    <!-- Featured Badge -->
                                    <?php if ($property['is_featured']): ?>
                                        <div class="position-absolute top-0 end-0 m-3" style="z-index: 10;">
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-star me-1"></i>Featured
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="position-relative overflow-hidden">
                                        <?php
                                        // Use ImageDisplayService for optimized image display
                                        $imageDisplayService = new \App\Services\ImageDisplayService();
                                        $images = is_string($property['images']) ? json_decode($property['images'], true) : $property['images'];
                                        $firstImage = !empty($images) ? $images[0] : null;

                                        // Get optimized image URL for card display
                                        $optimizedImageUrl = $imageDisplayService->getOptimizedImageUrl($firstImage, 'card');
                                        $srcset = $imageDisplayService->generateSrcset($firstImage);
                                        ?>
                                        <img src="<?= base_url('assets/images/placeholder.svg') ?>"
                                             data-lazy-src="<?= $optimizedImageUrl ?>"
                                             <?php if ($srcset): ?>data-lazy-srcset="<?= $srcset ?>"<?php endif; ?>
                                             data-fallback="<?= base_url('assets/images/default-property.svg') ?>"
                                             alt="<?= esc($property['title']) ?>"
                                             class="card-img-top property-image-hover"
                                             style="height: 250px; object-fit: cover;">
                                        <div class="position-absolute top-0 start-0 m-3">
                                            <!-- <span class="badge bg-primary fs-6">
                                                <?= esc(ucfirst($property['type'])) ?>
                                            </span> -->
                                        </div>
                                    </div>

                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title fw-bold text-dark mb-2"><?= esc($property['title']) ?></h5>
                                        <p class="card-text text-muted mb-3"><?= esc(substr($property['description'], 0, 100)) ?>...</p>

                                        <div class="d-flex align-items-center text-muted mb-3">
                                            <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                            <span><?= esc($property['location']) ?></span>
                                        </div>

                                        <?php if ($property['area']): ?>
                                            <div class="d-flex align-items-center text-muted mb-3">
                                                <i class="fas fa-ruler-combined me-2 text-primary"></i>
                                                <span><?= esc($property['area']) ?> sq ft</span>
                                            </div>
                                        <?php endif; ?>

                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                            <a href="<?= base_url('properties/' . urlencode($property['location']) . '/' . $property['id']) ?>" class="btn btn-primary">
                                                View Details
                                            </a>
                                            <small class="text-muted">
                                                <?= date('M j, Y', strtotime($property['created_at'])) ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <!-- Show message if no properties exist -->
        <?php if (empty($propertiesByType['house']) && empty($propertiesByType['villa']) && empty($propertiesByType['land']) && empty($propertiesByType['apartment'])): ?>
            <div class="text-center py-5">
                <i class="fas fa-home display-1 text-muted mb-4"></i>
                <h3 class="h3 fw-semibold text-dark mb-3">No Properties Available</h3>
                <p class="text-muted mb-4">We're working on adding amazing properties. Check back soon!</p>
                <?php if (isset($user) && $user): ?>
                    <a href="<?= base_url('properties/create') ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>
                        Add First Property
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- View All Properties Button -->
        <div class="text-center mt-5">
            <a href="<?= base_url('properties') ?>" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-th-large me-2"></i>
                View All Properties
            </a>
        </div>
    </div>
</section>

<!-- Newsletter Subscription Section -->
<?= $this->include('components/newsletter_section') ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Initialize homepage animations
document.addEventListener('DOMContentLoaded', function() {
    // Animate elements on scroll
    const animateOnScroll = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in-up');
            }
        });
    }, { threshold: 0.1 });
    
    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        animateOnScroll.observe(el);
    });
});
</script>
<?= $this->endSection() ?>
