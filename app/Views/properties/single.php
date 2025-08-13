<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb Section -->
<section class="bg-light py-3">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="animate__animated animate__fadeInDown">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="<?= base_url() ?>" class="text-decoration-none">
                        <i class="fas fa-home me-1"></i>Home
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= base_url('properties') ?>" class="text-decoration-none">Properties</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= base_url('properties?location=' . urlencode($property['location'])) ?>" class="text-decoration-none">
                        <?= esc($property['location']) ?>
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <?= esc($property['title']) ?>
                </li>
            </ol>
        </nav>
    </div>
</section>

<!-- Property Hero Section -->
<section class="property-hero-section position-relative overflow-hidden">
    <?php
    // Get the first image for hero background
    $images = is_string($property['images']) ? json_decode($property['images'], true) : $property['images'];
    $heroImage = !empty($images[0]) ? base_url($images[0]) : base_url('assets/images/default-property.svg');
    ?>

    <!-- Hero Background Image -->
    <div class="hero-background position-absolute top-0 start-0 w-100 h-100">
        <img src="<?= $heroImage ?>"
             alt="<?= esc($property['title']) ?>"
             class="w-100 h-100 object-fit-cover">
        <div class="hero-overlay position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50"></div>
    </div>

    <!-- Hero Content -->
    <div class="hero-content position-relative d-flex align-items-center" style="min-height: 60vh; z-index: 2;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-8">
                    <div class="text-center text-white animate__animated animate__fadeInUp">
                        <!-- Property Title -->
                        <h1 class="display-4 fw-bold mb-4 text-shadow">
                            <?= esc($property['title']) ?>
                        </h1>

                        <!-- Location -->
                        <div class="d-flex justify-content-center align-items-center mb-4">
                            <i class="fas fa-map-marker-alt me-2 text-warning fs-5"></i>
                            <span class="fs-4 fw-medium"><?= esc($property['location']) ?></span>
                        </div>

                        <!-- Property Details -->
                        <div class="d-flex flex-wrap justify-content-center gap-4 mb-5">
                            <div class="hero-detail-item">
                                <i class="fas fa-home text-primary fs-5 mb-2 d-block"></i>
                                <span class="fw-semibold"><?= ucfirst(esc($property['type'])) ?></span>
                            </div>
                            <?php if ($property['area']): ?>
                                <div class="hero-detail-item">
                                    <i class="fas fa-ruler-combined text-success fs-5 mb-2 d-block"></i>
                                    <span class="fw-semibold"><?= number_format($property['area']) ?> sq ft</span>
                                </div>
                            <?php endif; ?>
                            <div class="hero-detail-item">
                                <i class="fas fa-calendar text-info fs-5 mb-2 d-block"></i>
                                <span class="fw-semibold">Listed <?= date('M j, Y', strtotime($property['created_at'])) ?></span>
                            </div>
                            <?php if (isset($property['is_featured']) && $property['is_featured']): ?>
                                <div class="hero-detail-item">
                                    <i class="fas fa-star text-warning fs-5 mb-2 d-block"></i>
                                    <span class="fw-semibold">Featured</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Call to Action -->
                        <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                            <button class="btn btn-primary btn-lg px-4 py-3 rounded-pill" onclick="scrollToContact()">
                                <i class="fas fa-phone me-2"></i>
                                Contact Agent
                            </button>
                            <button class="btn btn-outline-light btn-lg px-4 py-3 rounded-pill" onclick="scrollToGallery()">
                                <i class="fas fa-images me-2"></i>
                                View Gallery
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll Down Indicator -->
    <div class="position-absolute bottom-0 start-50 translate-middle-x mb-4 animate__animated animate__bounce animate__infinite">
        <button class="btn btn-link text-white p-0" onclick="scrollToGallery()" aria-label="Scroll to gallery">
            <i class="fas fa-chevron-down fs-3"></i>
        </button>
    </div>
</section>

<!-- Property Media Gallery -->
<section class="py-4">
    <div class="container">
        <?php
        $images = is_string($property['images']) ? json_decode($property['images'], true) : $property['images'];
        $hasImages = !empty($images);

        // Handle both old single video format and new multiple videos format
        $videos = [];
        if (!empty($property['youtube_video'])) {
            if (is_string($property['youtube_video'])) {
                // Try to decode as JSON first (new format)
                $decodedVideos = json_decode($property['youtube_video'], true);
                if (is_array($decodedVideos)) {
                    $videos = $decodedVideos;
                } else {
                    // Old single video format
                    $videos = [$property['youtube_video']];
                }
            } elseif (is_array($property['youtube_video'])) {
                $videos = $property['youtube_video'];
            }
        }
        $hasVideo = !empty($videos);
        ?>

        <div class="row g-4 animate__animated animate__fadeInUp">
            <!-- Main Media Display -->
            <div class="col-lg-8">
                <?php if ($hasImages || $hasVideo): ?>
                    <!-- Media Carousel -->
                    <div id="propertyMediaCarousel" class="carousel slide shadow-lg rounded-4 overflow-hidden" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <!-- Video Slides (if available) -->
                            <?php if ($hasVideo): ?>
                                <?php foreach ($videos as $index => $videoUrl): ?>
                                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                        <div class="ratio ratio-16x9 bg-dark">
                                            <iframe src="<?= getYouTubeEmbedUrl($videoUrl) ?>"
                                                    title="Property Video Tour <?= $index + 1 ?>"
                                                    allowfullscreen
                                                    class="rounded-4"></iframe>
                                        </div>
                                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-75 rounded-3 p-3">
                                            <h5 class="mb-1">
                                                <i class="fas fa-play-circle me-2"></i>
                                                Virtual Property Tour <?= count($videos) > 1 ? ($index + 1) : '' ?>
                                            </h5>
                                            <p class="mb-0">Take a virtual tour of this beautiful property</p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- Image Slides -->
                            <?php if ($hasImages): ?>
                                <?php foreach ($images as $index => $image): ?>
                                    <div class="carousel-item <?= !$hasVideo && $index === 0 ? 'active' : '' ?>">
                                        <img src="<?= base_url($image) ?>"
                                             class="d-block w-100"
                                             alt="<?= esc($property['title']) ?> - Image <?= $index + 1 ?>"
                                             style="height: 500px; object-fit: cover;"
                                             onclick="openLightbox(<?= $hasVideo ? $index + count($videos) : $index ?>)">
                                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-75 rounded-3 p-3">
                                            <h5 class="mb-1"><?= esc($property['title']) ?></h5>
                                            <p class="mb-0">
                                                <i class="fas fa-map-marker-alt me-2"></i>
                                                <?= esc($property['location']) ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Carousel Controls -->
                        <?php
                        $totalSlides = ($hasImages ? count($images) : 0) + ($hasVideo ? count($videos) : 0);
                        if ($totalSlides > 1):
                        ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#propertyMediaCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#propertyMediaCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        <?php endif; ?>

                        <!-- Media Counter -->
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-dark bg-opacity-75 fs-6 px-3 py-2 rounded-pill">
                                <i class="fas fa-images me-2"></i>
                                <span id="mediaCounter">1</span> / <span id="mediaTotal"><?= ($hasVideo ? 1 : 0) + ($hasImages ? count($images) : 0) ?></span>
                            </span>
                        </div>

                        <!-- View Gallery Button -->
                        <div class="position-absolute bottom-0 end-0 m-3">
                            <button class="btn btn-primary btn-sm rounded-pill px-3" onclick="openLightbox(0)">
                                <i class="fas fa-expand me-2"></i>
                                View Gallery
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Default Image -->
                    <div class="bg-light rounded-4 shadow-lg d-flex align-items-center justify-content-center" style="height: 500px;">
                        <div class="text-center text-muted">
                            <i class="fas fa-home display-1 mb-3"></i>
                            <h5>No images available</h5>
                            <p class="mb-0">Property images will be displayed here</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Thumbnail Grid -->
            <div class="col-lg-4">
                <?php if ($hasImages || $hasVideo): ?>
                    <div class="row g-2 h-100">
                        <div class="col-12">
                            <h6 class="fw-bold text-dark mb-3">
                                <i class="fas fa-images me-2 text-primary"></i>
                                Property Media
                                <?php if ($hasImages && $hasVideo): ?>
                                    (<?= count($images) ?> photos, <?= count($videos) ?> videos)
                                <?php elseif ($hasImages): ?>
                                    (<?= count($images) ?> photos)
                                <?php else: ?>
                                    (<?= count($videos) ?> videos)
                                <?php endif; ?>
                            </h6>
                        </div>

                        <?php
                        $thumbnailIndex = 0;
                        $maxThumbnails = 6;
                        ?>

                        <!-- Video Thumbnails -->
                        <?php if ($hasVideo): ?>
                            <?php foreach ($videos as $index => $videoUrl): ?>
                                <?php if ($thumbnailIndex < $maxThumbnails): ?>
                                    <div class="col-6 mb-2">
                                        <div class="position-relative">
                                            <div class="bg-dark rounded-3 shadow-sm d-flex align-items-center justify-content-center thumbnail-hover"
                                                 style="height: 80px; width: 100%; cursor: pointer;"
                                                 onclick="changeMainImage(<?= $index ?>)"
                                                 data-bs-target="#propertyMediaCarousel"
                                                 data-bs-slide-to="<?= $index ?>">
                                                <i class="fas fa-play-circle text-white" style="font-size: 1.5rem;"></i>
                                            </div>
                                            <div class="position-absolute top-0 end-0 m-1">
                                                <span class="badge bg-danger">Video <?= $index + 1 ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php $thumbnailIndex++; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Image Thumbnails -->
                        <?php if ($hasImages): ?>
                            <?php foreach ($images as $index => $image): ?>
                                <?php if ($thumbnailIndex < $maxThumbnails): ?>
                                    <div class="col-6 mb-2">
                                        <div class="position-relative">
                                            <img src="<?= base_url($image) ?>"
                                                 class="img-fluid rounded-3 shadow-sm thumbnail-hover"
                                                 alt="Thumbnail <?= $index + 1 ?>"
                                                 style="height: 80px; width: 100%; object-fit: cover; cursor: pointer;"
                                                 onclick="changeMainImage(<?= count($videos) + $index ?>)"
                                                 data-bs-target="#propertyMediaCarousel"
                                                 data-bs-slide-to="<?= count($videos) + $index ?>">
                                            <?php
                                            $remainingMedia = (count($images) + count($videos)) - $maxThumbnails;
                                            if ($thumbnailIndex === $maxThumbnails - 1 && $remainingMedia > 0):
                                            ?>
                                                <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-75 d-flex align-items-center justify-content-center rounded-3">
                                                    <span class="text-white fw-bold">+<?= $remainingMedia ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php $thumbnailIndex++; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if ($hasVideo): ?>
                            <div class="col-6 mb-2">
                                <div class="position-relative">
                                    <div class="bg-dark rounded-3 shadow-sm d-flex align-items-center justify-content-center thumbnail-hover"
                                         style="height: 80px; cursor: pointer;"
                                         onclick="changeMainImage(0)"
                                         data-bs-target="#propertyMediaCarousel"
                                         data-bs-slide-to="0">
                                        <i class="fas fa-play-circle text-white fs-3"></i>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Property Details Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Property Header -->
                <div class="mb-5 animate__animated animate__fadeInLeft">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4">
                        <div class="flex-grow-1">
                            <h1 class="display-5 fw-bold text-dark mb-3"><?= esc($property['title']) ?></h1>
                            <div class="d-flex align-items-center text-muted mb-3">
                                <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                <span class="fs-5"><?= esc($property['location']) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Property Badges -->
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <span class="badge bg-primary bg-opacity-10 text-primary fs-6 px-3 py-2 rounded-pill">
                            <i class="fas fa-home me-2"></i>
                            <?= ucfirst(esc($property['type'])) ?>
                        </span>
                        <?php if ($property['area']): ?>
                            <span class="badge bg-success bg-opacity-10 text-success fs-6 px-3 py-2 rounded-pill">
                                <i class="fas fa-ruler-combined me-2"></i>
                                <?= number_format($property['area']) ?> sq ft
                            </span>
                        <?php endif; ?>
                        <span class="badge bg-info bg-opacity-10 text-info fs-6 px-3 py-2 rounded-pill">
                            <i class="fas fa-calendar me-2"></i>
                            Listed <?= date('M j, Y', strtotime($property['created_at'])) ?>
                        </span>

                    </div>

                    <!-- Quick Actions - Simplified to only include essential actions -->
                    <!--
                        Modified: 2025-08-04
                        Removed: "Save to Favorites" and "Print Details" buttons as per requirements
                        Kept: Share Property functionality for social sharing
                    -->
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-outline-primary btn-sm rounded-pill" onclick="shareProperty()">
                            <i class="fas fa-share-alt me-2"></i>
                            Share Property
                        </button>
                    </div>
                </div>

                <!-- Property Description -->
                <div class="mb-5 animate__animated animate__fadeInUp">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h2 class="h4 fw-bold text-dark mb-4">
                                <i class="fas fa-align-left me-2 text-primary"></i>
                                Property Description
                            </h2>
                            <div class="text-muted lh-lg">
                                <?= nl2br(esc($property['description'])) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Property Features -->
                <div class="mb-5 animate__animated animate__fadeInUp">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h2 class="h4 fw-bold text-dark mb-4">
                                <i class="fas fa-list-check me-2 text-primary"></i>
                                Property Features
                            </h2>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                                <i class="fas fa-home text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <div class="fw-medium">Property Type</div>
                                            <div class="text-muted small"><?= ucfirst(esc($property['type'])) ?></div>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($property['area']): ?>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                            <div class="flex-shrink-0">
                                                <div class="bg-success bg-opacity-10 rounded-circle p-2">
                                                    <i class="fas fa-ruler-combined text-success"></i>
                                                </div>
                                            </div>
                                            <div class="ms-3">
                                                <div class="fw-medium">Total Area</div>
                                                <div class="text-muted small"><?= number_format($property['area']) ?> sq ft</div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                        <div class="flex-shrink-0">
                                            <div class="bg-warning bg-opacity-10 rounded-circle p-2">
                                                <i class="fas fa-calendar text-warning"></i>
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <div class="fw-medium">Listed Date</div>
                                            <div class="text-muted small"><?= date('M j, Y', strtotime($property['created_at'])) ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                                <i class="fas fa-shield-alt text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <div class="fw-medium">Verification</div>
                                            <div class="text-muted small">Verified Property</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                        <div class="flex-shrink-0">
                                            <div class="bg-success bg-opacity-10 rounded-circle p-2">
                                                <i class="fas fa-car text-success"></i>
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <div class="fw-medium">Parking</div>
                                            <div class="text-muted small">Available</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location Map -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Location</h2>
                    <div class="bg-gray-200 h-64 rounded-xl flex items-center justify-center">
                        <div class="text-center">
                            <i class="fas fa-map text-4xl text-gray-400 mb-2"></i>
                            <p class="text-gray-600">Interactive map would be integrated here</p>
                            <p class="text-sm text-gray-500"><?= esc($property['location']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Enhanced Contact Form -->
                <div class="card border-0 shadow-lg sticky-top mb-4 animate__animated animate__fadeInRight" style="top: 2rem;">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h3 class="h5 mb-2 fw-bold">
                            <i class="fas fa-user-tie me-2"></i>
                            Contact Our Agent
                        </h3>
                        <p class="mb-0 small opacity-75">Get expert assistance for this property</p>
                    </div>
                    <div class="card-body p-4">
                        <form action="<?= base_url('contact/submit') ?>" method="post" id="propertyContactForm" class="needs-validation" novalidate>
                            <?= csrf_field() ?>
                            <input type="hidden" name="property_id" value="<?= $property['id'] ?>">

                            <!-- Full Name -->
                            <div class="mb-3">
                                <label for="contact_name" class="form-label fw-medium">
                                    <i class="fas fa-user me-2 text-primary"></i>
                                    Full Name <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       id="contact_name"
                                       name="name"
                                       class="form-control form-control-lg"
                                       placeholder="Enter your full name"
                                       required>
                                <div class="invalid-feedback">Please provide your full name.</div>
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="contact_email" class="form-label fw-medium">
                                    <i class="fas fa-envelope me-2 text-primary"></i>
                                    Email Address <span class="text-danger">*</span>
                                </label>
                                <input type="email"
                                       id="contact_email"
                                       name="email"
                                       class="form-control form-control-lg"
                                       placeholder="your.email@example.com"
                                       required>
                                <div class="invalid-feedback">Please provide a valid email address.</div>
                            </div>

                            <!-- Phone -->
                            <div class="mb-3">
                                <label for="contact_phone" class="form-label fw-medium">
                                    <i class="fas fa-phone me-2 text-primary"></i>
                                    Phone Number <span class="text-danger">*</span>
                                </label>
                                <input type="tel"
                                       id="contact_phone"
                                       name="phone"
                                       class="form-control form-control-lg"
                                       placeholder="+91 98765 43210"
                                       required>
                                <div class="invalid-feedback">Please provide your phone number.</div>
                            </div>

                            <!-- Property Interest Type -->
                            <div class="mb-3">
                                <label for="property_interest" class="form-label fw-medium">
                                    <i class="fas fa-home me-2 text-primary"></i>
                                    Property Interest
                                </label>
                                <select id="property_interest" name="properties_in" class="form-select form-select-lg">
                                    <option value="<?= esc($property['type']) ?>" selected><?= ucfirst(esc($property['type'])) ?></option>
                                    <option value="house">House</option>
                                    <option value="apartment">Apartment</option>
                                    <option value="villa">Villa</option>
                                    <option value="land">Land</option>
                                </select>
                            </div>

                            <!-- Pre-filled Message -->
                            <div class="mb-4">
                                <label for="contact_message" class="form-label fw-medium">
                                    <i class="fas fa-comment-dots me-2 text-primary"></i>
                                    Message <span class="text-danger">*</span>
                                </label>
                                <textarea id="contact_message"
                                          name="message"
                                          rows="4"
                                          class="form-control"
                                          placeholder="Your message..."
                                          required>Hi, I'm interested in the <?= esc($property['type']) ?> "<?= esc($property['title']) ?>" located in <?= esc($property['location']) ?>. Could you please provide more details about this property including pricing, availability, and viewing arrangements? I would appreciate your prompt response. Thank you!</textarea>
                                <div class="invalid-feedback">Please enter your message.</div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Feel free to modify the message above or add specific questions
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg" id="contactSubmitBtn">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Send Inquiry
                                </button>
                            </div>

                            <!-- Contact Info - Simplified contact options -->
                            <!--
                                Modified: 2025-08-04
                                Removed: "Call" button as per requirements
                                Kept: WhatsApp contact for modern communication preference
                            -->
                            <div class="text-center mt-4 pt-3 border-top">
                                <div class="small text-muted mb-2">Or contact us directly:</div>
                                <div class="d-flex justify-content-center gap-3">
                                    <a href="https://wa.me/919876543210" class="btn btn-outline-success btn-sm" target="_blank">
                                        <i class="fab fa-whatsapp me-1"></i>
                                        WhatsApp
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Property Statistics -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2 text-primary"></i>
                            Property Statistics
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 text-center">

                            <div class="col-4">
                                <div class="border rounded-3 p-3">
                                    <div class="h4 fw-bold text-success mb-1">45</div>
                                    <div class="small text-muted">Inquiries</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded-3 p-3">
                                    <div class="h6 fw-bold text-info mb-1"><?= date('M j', strtotime($property['created_at'])) ?></div>
                                    <div class="small text-muted">Listed</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded-3 p-3">
                                    <div class="h6 fw-bold text-warning mb-1">#<?= $property['id'] ?></div>
                                    <div class="small text-muted">Property ID</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Similar Properties Section - Fixed with Bootstrap 5 classes -->
<?php
/**
 * Similar Properties Display Section
 *
 * This section displays related properties from the same location to help users
 * discover other options. The layout has been updated to use Bootstrap 5 classes
 * instead of Tailwind CSS for consistency with the rest of the application.
 *
 * Features:
 * - Responsive grid layout (1 column on mobile, 2 on tablet, 3 on desktop)
 * - Property image with type badge overlay
 * - Truncated description with "View Details" call-to-action
 * - Consistent card styling with hover effects
 *
 * Modified: 2025-08-04 - Converted from Tailwind CSS to Bootstrap 5
 */
if (!empty($similarProperties)): ?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <h2 class="display-6 fw-bold text-dark mb-4">
                    Similar Properties in <span class="text-primary"><?= esc($property['location']) ?></span>
                </h2>
                <p class="text-muted">Discover other amazing properties in the same area</p>
            </div>
        </div>

        <div class="row g-4">
            <?php foreach ($similarProperties as $similarProperty): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm border-0 property-card">
                        <?php
                        $similarImages = is_string($similarProperty['images']) ? json_decode($similarProperty['images'], true) : $similarProperty['images'];
                        $similarImage = !empty($similarImages[0]) ? base_url($similarImages[0]) : base_url('assets/images/default-property.svg');
                        ?>

                        <!-- Property Image -->
                        <div class="position-relative overflow-hidden">
                            <img src="<?= $similarImage ?>"
                                 alt="<?= esc($similarProperty['title']) ?>"
                                 class="card-img-top property-image"
                                 style="height: 200px; object-fit: cover;">

                            <!-- Property Type Badge -->
                            <div class="position-absolute top-0 start-0 m-3">
                                <span class="badge bg-primary rounded-pill px-3 py-2">
                                    <?= esc(ucfirst($similarProperty['type'])) ?>
                                </span>
                            </div>
                        </div>

                        <!-- Property Details -->
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold text-dark mb-2">
                                <?= esc($similarProperty['title']) ?>
                            </h5>

                            <p class="card-text text-muted mb-3 flex-grow-1">
                                <?= esc(substr($similarProperty['description'], 0, 80)) ?>...
                            </p>

                            <!-- Location -->
                            <div class="d-flex align-items-center text-muted mb-3">
                                <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                <span class="small"><?= esc($similarProperty['location']) ?></span>
                            </div>

                            <!-- View Details Button -->
                            <a href="<?= base_url('properties/' . urlencode($similarProperty['location']) . '/' . $similarProperty['id']) ?>"
                               class="btn btn-primary w-100 mt-auto">
                                <i class="fas fa-eye me-2"></i>
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>



<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<style>
/* Property Hero Section Styles */
.property-hero-section {
    position: relative;
    min-height: 60vh;
    display: flex;
    align-items: center;
}

.hero-background img {
    object-fit: cover;
    filter: brightness(0.7);
    transition: all 0.3s ease;
}

.hero-overlay {
    background: linear-gradient(135deg, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0.3) 100%);
}

.text-shadow {
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
}

.hero-detail-item {
    text-align: center;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
    min-width: 120px;
}

.hero-detail-item:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
}

.hero-content .btn {
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.hero-content .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
}

.hero-content .btn-outline-light:hover {
    background: rgba(255, 255, 255, 0.9);
    color: var(--bs-dark);
}

/* Responsive adjustments for hero section */
@media (max-width: 768px) {
    .property-hero-section {
        min-height: 50vh;
    }

    .display-4 {
        font-size: 2.5rem;
    }

    .hero-detail-item {
        min-width: 100px;
        padding: 0.75rem;
    }

    .fs-4 {
        font-size: 1.25rem !important;
    }
}

@media (max-width: 576px) {
    .property-hero-section {
        min-height: 45vh;
    }

    .display-4 {
        font-size: 2rem;
    }

    .hero-detail-item {
        min-width: 80px;
        padding: 0.5rem;
    }
}

/* Custom animations and styles for property single view */
.thumbnail-hover {
    transition: all 0.3s ease;
}

.thumbnail-hover:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.sticky-top {
    top: 2rem !important;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.carousel-item img {
    cursor: pointer;
    transition: all 0.3s ease;
}

.carousel-item img:hover {
    transform: scale(1.02);
}

.badge {
    transition: all 0.3s ease;
}

.badge:hover {
    transform: scale(1.05);
}

/* Lightbox styles */
.lightbox {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.lightbox.show {
    opacity: 1;
    visibility: visible;
}

.lightbox img {
    max-width: 90%;
    max-height: 90%;
    object-fit: contain;
    border-radius: 8px;
}

.lightbox-close {
    position: absolute;
    top: 20px;
    right: 30px;
    color: white;
    font-size: 2rem;
    cursor: pointer;
    z-index: 10000;
}

.lightbox-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    color: white;
    font-size: 2rem;
    cursor: pointer;
    padding: 10px;
    background: rgba(0, 0, 0, 0.5);
    border-radius: 50%;
    transition: all 0.3s ease;
}

.lightbox-nav:hover {
    background: rgba(0, 0, 0, 0.8);
}

.lightbox-prev {
    left: 30px;
}

.lightbox-next {
    right: 30px;
}

/* Enhanced Media Gallery Styles */
.thumbnail-hover {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.thumbnail-hover:hover {
    transform: scale(1.05);
    border-color: var(--bs-primary);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.carousel-item img {
    transition: transform 0.3s ease;
}

.carousel-item:hover img {
    transform: scale(1.02);
}

/* Enhanced lightbox video support */
#lightboxVideo {
    max-width: 90%;
    max-height: 90%;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.7);
}

.lightbox img {
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.7);
}

.lightbox-close:hover {
    color: #ff6b6b;
    transform: scale(1.1);
}

.lightbox-nav:hover {
    transform: translateY(-50%) scale(1.1);
}

/* Form enhancements */
.form-control:focus {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .sticky-top {
        position: relative !important;
        top: 0 !important;
    }

    .display-5 {
        font-size: 2rem;
    }

    .display-6 {
        font-size: 1.5rem;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Property data
const propertyData = {
    images: <?= json_encode($hasImages ? array_map(function($img) { return base_url($img); }, $images) : []) ?>,
    videos: <?= json_encode($hasVideo ? $videos : []) ?>,
    hasVideo: <?= $hasVideo ? 'true' : 'false' ?>,
    title: '<?= esc($property['title']) ?>',
    location: '<?= esc($property['location']) ?>',
    type: '<?= esc($property['type']) ?>'
};

let currentImageIndex = 0;
let lightboxImages = [];

// Initialize lightbox images (include videos as first items if available)
if (propertyData.hasVideo) {
    // Add null placeholders for videos, then add images
    const videoPlaceholders = new Array(propertyData.videos.length).fill(null);
    lightboxImages = [...videoPlaceholders, ...propertyData.images];
} else {
    lightboxImages = propertyData.images;
}

// Lightbox functionality
function openLightbox(index) {
    if (lightboxImages.length === 0) return;

    currentImageIndex = index;

    // Create lightbox if it doesn't exist
    let lightbox = document.getElementById('propertyLightbox');
    if (!lightbox) {
        createLightbox();
        lightbox = document.getElementById('propertyLightbox');
    }

    updateLightboxImage();
    lightbox.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function createLightbox() {
    const lightboxHTML = `
        <div id="propertyLightbox" class="lightbox">
            <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
            <span class="lightbox-nav lightbox-prev" onclick="prevLightboxImage()">&#10094;</span>
            <span class="lightbox-nav lightbox-next" onclick="nextLightboxImage()">&#10095;</span>
            <div id="lightboxContent">
                <img id="lightboxImage" src="" alt="Property Image" style="display: none;">
                <div id="lightboxVideo" class="ratio ratio-16x9" style="display: none;">
                    <iframe id="lightboxVideoFrame" src="" allowfullscreen></iframe>
                </div>
            </div>
            <div class="position-absolute bottom-0 start-50 translate-middle-x mb-4">
                <span class="badge bg-dark bg-opacity-75 fs-6 px-3 py-2">
                    <span id="lightboxCounter">1</span> / ${lightboxImages.length}
                </span>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', lightboxHTML);
}

function updateLightboxImage() {
    const lightboxImage = document.getElementById('lightboxImage');
    const lightboxVideo = document.getElementById('lightboxVideo');
    const lightboxVideoFrame = document.getElementById('lightboxVideoFrame');
    const lightboxCounter = document.getElementById('lightboxCounter');

    if (currentImageIndex < propertyData.videos.length && propertyData.hasVideo) {
        // Show video
        lightboxImage.style.display = 'none';
        lightboxVideo.style.display = 'block';

        // Get YouTube embed URL
        const videoUrl = propertyData.videos[currentImageIndex];
        const embedUrl = getYouTubeEmbedUrl(videoUrl);
        lightboxVideoFrame.src = embedUrl;
    } else {
        // Show image
        lightboxVideo.style.display = 'none';
        lightboxImage.style.display = 'block';
        lightboxVideoFrame.src = ''; // Stop video

        const imageIndex = currentImageIndex - propertyData.videos.length;
        lightboxImage.src = propertyData.images[imageIndex];
    }

    if (lightboxCounter) {
        lightboxCounter.textContent = currentImageIndex + 1;
    }
}

// Helper function to convert YouTube URL to embed URL
function getYouTubeEmbedUrl(url) {
    let videoId = '';

    if (url.includes('youtube.com/watch?v=')) {
        videoId = url.split('v=')[1].split('&')[0];
    } else if (url.includes('youtube.com/embed/')) {
        videoId = url.split('embed/')[1].split('?')[0];
    } else if (url.includes('youtu.be/')) {
        videoId = url.split('youtu.be/')[1].split('?')[0];
    }

    if (videoId) {
        return `https://www.youtube.com/embed/${videoId}?rel=0&showinfo=0&modestbranding=1`;
    }

    return url;
}

function closeLightbox() {
    const lightbox = document.getElementById('propertyLightbox');
    const lightboxVideoFrame = document.getElementById('lightboxVideoFrame');

    if (lightbox) {
        // Stop video if playing
        if (lightboxVideoFrame) {
            lightboxVideoFrame.src = '';
        }

        lightbox.classList.remove('show');
        document.body.style.overflow = 'auto';
    }
}

function nextLightboxImage() {
    currentImageIndex = (currentImageIndex + 1) % lightboxImages.length;
    updateLightboxImage();
}

function prevLightboxImage() {
    currentImageIndex = (currentImageIndex - 1 + lightboxImages.length) % lightboxImages.length;
    updateLightboxImage();
}

// Carousel functionality
function changeMainImage(index) {
    const carousel = document.getElementById('propertyMediaCarousel');
    if (carousel) {
        const bsCarousel = new bootstrap.Carousel(carousel);
        bsCarousel.to(index);
    }
}

// Update media counter when carousel changes
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('propertyMediaCarousel');
    const mediaCounter = document.getElementById('mediaCounter');

    if (carousel && mediaCounter) {
        carousel.addEventListener('slide.bs.carousel', function(e) {
            mediaCounter.textContent = e.to + 1;
        });
    }
});

// Property action functions
function shareProperty() {
    const propertyUrl = window.location.href;
    const shareData = {
        title: propertyData.title,
        text: `Check out this amazing ${propertyData.type} in ${propertyData.location}!`,
        url: propertyUrl
    };

    if (navigator.share) {
        navigator.share(shareData).catch((error) => {
            console.error('Share failed:', error);
            showToast('⚠️ Sharing not available. Property link copied to clipboard instead!', 'info');
            // Fallback to clipboard copy
            copyToClipboard(propertyUrl);
        });
    } else {
        // Fallback - copy to clipboard
        copyToClipboard(propertyUrl);
    }
}

// Helper function for clipboard operations with better error handling
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('📋 Property link copied to clipboard!', 'success');
        }).catch(() => {
            // Manual copy fallback for older browsers
            manualCopyFallback(text);
        });
    } else {
        // Manual copy fallback for browsers without clipboard API
        manualCopyFallback(text);
    }
}

// Manual copy fallback with user-friendly messaging
function manualCopyFallback(text) {
    try {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.opacity = '0';
        document.body.appendChild(textArea);
        textArea.select();
        const successful = document.execCommand('copy');
        document.body.removeChild(textArea);

        if (successful) {
            showToast('📋 Property link copied to clipboard!', 'success');
        } else {
            showToast('❌ Unable to copy link. Please copy the URL from your browser address bar.', 'error');
        }
    } catch (error) {
        console.error('Manual copy failed:', error);
        showToast('❌ Copy failed. Please manually copy the URL from your browser address bar.', 'error');
    }
}

// Removed unused functions: saveProperty() and printProperty()
// These functions were removed as the corresponding UI elements were removed

// Contact form enhancements
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('propertyContactForm');
    const submitBtn = document.getElementById('contactSubmitBtn');

    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (contactForm.checkValidity()) {
                // Show loading state
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
                submitBtn.disabled = true;

                // Submit form data
                const formData = new FormData(contactForm);

                fetch(contactForm.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('✅ Your inquiry has been sent successfully! We\'ll get back to you soon.', 'success');
                        contactForm.reset();
                    } else {
                        // More specific error message based on response
                        const errorMsg = data.message || 'Unable to send your inquiry at the moment. Please try again or contact us directly via WhatsApp.';
                        showToast('❌ ' + errorMsg, 'error');
                    }
                })
                .catch(error => {
                    console.error('Contact form submission error:', error);
                    showToast('⚠️ Network error occurred. Please check your internet connection and try again, or contact us via WhatsApp.', 'error');
                })
                .finally(() => {
                    // Restore button state
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
            }

            contactForm.classList.add('was-validated');
        });
    }
});

// Toast notification function
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toastContainer') || createToastContainer();

    const toastId = 'toast-' + Date.now();
    const toastHTML = `
        <div id="${toastId}" class="toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'primary'} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;

    toastContainer.insertAdjacentHTML('beforeend', toastHTML);

    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, { delay: 5000 });
    toast.show();

    // Remove toast element after it's hidden
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
    });
}

function createToastContainer() {
    const containerHTML = `
        <div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 11000;"></div>
    `;
    document.body.insertAdjacentHTML('beforeend', containerHTML);
    return document.getElementById('toastContainer');
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    const lightbox = document.getElementById('propertyLightbox');
    if (lightbox && lightbox.classList.contains('show')) {
        switch(e.key) {
            case 'Escape':
                closeLightbox();
                break;
            case 'ArrowLeft':
                prevLightboxImage();
                break;
            case 'ArrowRight':
                nextLightboxImage();
                break;
        }
    }
});

// Close lightbox when clicking outside
document.addEventListener('click', function(e) {
    const lightbox = document.getElementById('propertyLightbox');
    if (lightbox && e.target === lightbox) {
        closeLightbox();
    }
});

// Initialize animations and cleanup
document.addEventListener('DOMContentLoaded', function() {
    // Add intersection observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe animated elements
    document.querySelectorAll('.animate__animated').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'all 0.6s ease';
        observer.observe(el);
    });

    // Hero Section Functions
    window.scrollToGallery = function() {
        const gallerySection = document.querySelector('.py-4');
        if (gallerySection) {
            gallerySection.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    };

    window.scrollToContact = function() {
        const contactSection = document.querySelector('#contactForm') ||
                              document.querySelector('.contact-section') ||
                              document.querySelector('[id*="contact"]');
        if (contactSection) {
            contactSection.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        } else {
            // If no contact section found, scroll to bottom of page
            window.scrollTo({
                top: document.body.scrollHeight,
                behavior: 'smooth'
            });
        }
    };

    // Enhanced cleanup function to prevent memory leaks
    window.addEventListener('beforeunload', function() {
        // Disconnect intersection observer to prevent memory leaks
        if (observer) {
            observer.disconnect();
            observer = null;
        }

        // Remove any dynamically created elements
        const lightbox = document.getElementById('propertyLightbox');
        if (lightbox) {
            lightbox.remove();
        }

        const toastContainer = document.getElementById('toastContainer');
        if (toastContainer) {
            toastContainer.remove();
        }

        // Clear any remaining timeouts or intervals
        // Note: This helps prevent memory leaks from unclosed timers
        const highestTimeoutId = setTimeout(";");
        for (let i = 0; i < highestTimeoutId; i++) {
            clearTimeout(i);
        }
    });
});
</script>
<?= $this->endSection() ?>
