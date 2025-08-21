<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!--
Properties Page - Content Modifications Log:
- Removed "Skip to property search" accessibility link
- Removed "Discover Your Perfect Property" heading from hero section
- Removed "Start Your Search" button from hero section
- Removed phone icon buttons from property cards
- Maintained all existing functionality and responsive design
- Preserved WCAG 2.1 AA accessibility compliance
-->



<!-- Property Hero Section -->
<section class="property-hero-section position-relative overflow-hidden"
         role="banner"
         aria-label="Featured Properties Showcase">
    <?php
    // Determine if we have search context (location or property type filters)
    $hasSearchContext = !empty($searchParams['location']) || !empty($searchParams['type']);

    // Get a single featured property for background image (only if search context exists)
    $heroProperty = null;
    $heroImage = null;

    if ($hasSearchContext) {
        $propertyModel = new \App\Models\PropertyModel();
        $featuredProperty = $propertyModel->getFeaturedProperties(1);

        // If no featured property, get the latest property
        if (empty($featuredProperty)) {
            $featuredProperty = $propertyModel->orderBy('created_at', 'DESC')->limit(1)->findAll();
        }

        if (!empty($featuredProperty)) {
            $heroProperty = $featuredProperty[0];
            $imageDisplayService = new \App\Services\ImageDisplayService();
            $heroImages = is_string($heroProperty['images']) ? json_decode($heroProperty['images'], true) : $heroProperty['images'];
            $firstHeroImage = !empty($heroImages) ? $heroImages[0] : null;
            $heroImage = $imageDisplayService->getOptimizedImageUrl($firstHeroImage, 'hero');
        }
    }
    ?>

    <?php if ($hasSearchContext && $heroImage): ?>
        <!-- Hero Background Image (for search results) -->
        <div class="hero-background-container position-absolute top-0 start-0 w-100 h-100">
            <div class="hero-image-container position-relative w-100 h-100">
                <img src="<?= $heroImage ?>"
                     alt="<?= esc($heroProperty['title']) ?>"
                     class="hero-background-image w-100 h-100"
                     style="object-fit: cover;"
                     loading="eager">
                <div class="hero-overlay position-absolute top-0 start-0 w-100 h-100"></div>
            </div>
        </div>
    <?php else: ?>
        <!-- Fallback Background (no search context) -->
        <div class="hero-fallback-background position-absolute top-0 start-0 w-100 h-100">
            <div class="hero-overlay position-absolute top-0 start-0 w-100 h-100"></div>
        </div>
    <?php endif; ?>

    <!-- Hero Content Overlay -->
    <div class="hero-content-container position-relative d-flex align-items-center justify-content-center text-center text-white" style="z-index: 10;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-8">
                    <?php if ($hasSearchContext): ?>
                        <!-- Search Results Hero Content -->
                        <div class="hero-search-content">
                            <!-- Search Context Badge -->
                            <div class="mb-3">
                                <?php if (!empty($searchParams['location'])): ?>
                                    <span class="badge bg-primary bg-opacity-90 fs-6 px-3 py-2 rounded-pill me-2">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        <?= esc($searchParams['location']) ?>
                                    </span>
                                <?php endif; ?>
                                <?php if (!empty($searchParams['type'])): ?>
                                    <span class="badge bg-success bg-opacity-90 fs-6 px-3 py-2 rounded-pill">
                                        <i class="fas fa-<?= $searchParams['type'] === 'house' ? 'home' : ($searchParams['type'] === 'apartment' ? 'building' : ($searchParams['type'] === 'villa' ? 'crown' : 'map')) ?> me-2"></i>
                                        <?= esc(ucfirst($searchParams['type'])) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Main Headline -->
                            <h1 class="display-4 fw-bold mb-4 text-shadow">
                                <?php if (!empty($searchParams['location']) && !empty($searchParams['type'])): ?>
                                    <?= esc(ucfirst($searchParams['type'])) ?>s in <?= esc($searchParams['location']) ?>
                                <?php elseif (!empty($searchParams['location'])): ?>
                                    Properties in <?= esc($searchParams['location']) ?>
                                <?php elseif (!empty($searchParams['type'])): ?>
                                    <?= esc(ucfirst($searchParams['type'])) ?> Properties
                                <?php else: ?>
                                    Search Results
                                <?php endif; ?>
                            </h1>

                            <!-- Subheadline -->
                            <p class="fs-5 text-light mb-4 mx-auto" style="max-width: 600px;">
                                Discover <?= $totalProperties ?> amazing properties that match your search criteria.
                                Find your perfect home in the best locations.
                            </p>

                            <!-- Action Buttons -->
                            <div class="hero-actions">
                                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                                    <a href="#search-section" class="btn btn-primary btn-lg px-4 py-3 smooth-scroll">
                                        <i class="fas fa-search me-2"></i>
                                        Refine Search
                                    </a>
                                    <a href="<?= base_url('properties') ?>" class="btn btn-outline-light btn-lg px-4 py-3">
                                        <i class="fas fa-list me-2"></i>
                                        View All Properties
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Default Hero Content (no search context) -->
                        <div class="hero-default-content">



                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll Down Indicator -->
    <div class="hero-scroll-indicator position-absolute bottom-0 start-50 translate-middle-x mb-4">
        <a href="#search-section" class="text-white text-decoration-none smooth-scroll">
            <div class="scroll-indicator-container text-center">
                <div class="small mb-2 text-light">Explore Properties</div>
                <div class="scroll-arrow">
                    <i class="fas fa-chevron-down fa-lg"></i>
                </div>
            </div>
        </a>
    </div>
</section>

<!-- Compact Property Search Section -->
<section id="search-section" class="compact-search-section py-3 bg-white border-bottom">
    <div class="container">
        <!-- Compact Search Form -->
        <form action="<?= base_url('properties') ?>" method="get" class="compact-search-form">
            <div class="row g-3 align-items-end">
                <!-- Location Search -->
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <label class="form-label small fw-medium text-muted mb-1">
                        <i class="fas fa-map-marker-alt me-1"></i>Location
                    </label>
                    <select name="location" class="form-select form-select-sm compact-select">
                        <option value="">All Locations</option>
                        <option value="Siliguri" <?= (isset($searchParams['location']) && $searchParams['location'] === 'Siliguri') ? 'selected' : '' ?>>Siliguri</option>
                        <option value="Champasari" <?= (isset($searchParams['location']) && $searchParams['location'] === 'Champasari') ? 'selected' : '' ?>>Champasari</option>
                        <option value="Bagdogra" <?= (isset($searchParams['location']) && $searchParams['location'] === 'Bagdogra') ? 'selected' : '' ?>>Bagdogra</option>
                        <option value="Jalpaiguri" <?= (isset($searchParams['location']) && $searchParams['location'] === 'Jalpaiguri') ? 'selected' : '' ?>>Jalpaiguri</option>
                        <option value="Pradhan Nagar" <?= (isset($searchParams['location']) && $searchParams['location'] === 'Pradhan Nagar') ? 'selected' : '' ?>>Pradhan Nagar</option>
                        <option value="Milan More" <?= (isset($searchParams['location']) && $searchParams['location'] === 'Milan More') ? 'selected' : '' ?>>Milan More</option>
                        <option value="Khaprail" <?= (isset($searchParams['location']) && $searchParams['location'] === 'Khaprail') ? 'selected' : '' ?>>Khaprail</option>
                        <option value="Matigara" <?= (isset($searchParams['location']) && $searchParams['location'] === 'Matigara') ? 'selected' : '' ?>>Matigara</option>
                    </select>
                </div>

                <!-- Property Type -->
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <label class="form-label small fw-medium text-muted mb-1">
                        <i class="fas fa-building me-1"></i>Type
                    </label>
                    <select name="type" class="form-select form-select-sm compact-select">
                        <option value="">All Types</option>
                        <option value="house" <?= (isset($searchParams['type']) && $searchParams['type'] === 'house') ? 'selected' : '' ?>>House</option>
                        <option value="apartment" <?= (isset($searchParams['type']) && $searchParams['type'] === 'apartment') ? 'selected' : '' ?>>Apartment</option>
                        <option value="villa" <?= (isset($searchParams['type']) && $searchParams['type'] === 'villa') ? 'selected' : '' ?>>Villa</option>
                        <option value="land" <?= (isset($searchParams['type']) && $searchParams['type'] === 'land') ? 'selected' : '' ?>>Land</option>
                    </select>
                </div>

                <!-- Area Filter -->
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <label class="form-label small fw-medium text-muted mb-1">
                        <i class="fas fa-expand-arrows-alt me-1"></i>Min Area
                    </label>
                    <input type="number"
                           name="min_area"
                           class="form-control form-control-sm compact-input"
                           placeholder="sq ft"
                           value="<?= isset($searchParams['min_area']) ? esc($searchParams['min_area']) : '' ?>">
                </div>

                <!-- Featured Filter -->
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input"
                               type="checkbox"
                               role="switch"
                               id="featured"
                               name="featured"
                               value="1"
                               <?= (isset($searchParams['featured']) && $searchParams['featured']) ? 'checked' : '' ?>>
                        <label class="form-check-label small fw-medium text-muted" for="featured">
                            <i class="fas fa-star text-warning me-1"></i>Featured
                        </label>
                    </div>
                </div>

                <!-- Search Actions -->
                <div class="col-lg-4 col-md-8 col-sm-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm px-3">
                            <i class="fas fa-search me-1"></i>Search
                        </button>
                        <?php if (isset($isSearchResults) && $isSearchResults): ?>
                            <a href="<?= base_url('properties') ?>" class="btn btn-outline-secondary btn-sm px-3">
                                <i class="fas fa-times me-1"></i>Clear
                            </a>
                        <?php endif; ?>
                        <button type="button" class="btn btn-outline-primary btn-sm px-3" data-bs-toggle="collapse" data-bs-target="#quickFilters">
                            <i class="fas fa-filter me-1"></i>Quick
                        </button>
                    </div>
                </div>
            </div>

            <!-- Quick Filters (Collapsible) -->
            <div class="collapse mt-3" id="quickFilters">
                <div class="card card-body py-2 bg-light border-0">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <span class="small text-muted me-2">Quick filters:</span>
                        <button type="button" class="btn btn-outline-warning btn-sm quick-filter" data-featured="1">
                            <i class="fas fa-star me-1"></i>Featured
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm quick-filter" data-location="Siliguri">
                            Siliguri
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm quick-filter" data-type="house">
                            Houses
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm quick-filter" data-type="apartment">
                            Apartments
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm quick-filter" data-type="villa">
                            Villas
                        </button>
                        <button type="button" class="btn btn-outline-dark btn-sm quick-filter" data-type="land">
                            Land
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Search Results Summary -->
        <?php if (isset($isSearchResults) && $isSearchResults): ?>
            <div class="mt-3">
                <div class="alert alert-info alert-dismissible fade show py-2 mb-0" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong><?= $totalProperties ?></strong> properties found matching your search criteria
                    <?php if ($totalPages > 1): ?>
                        • Page <strong><?= $currentPage ?></strong> of <strong><?= $totalPages ?></strong>
                    <?php endif; ?>
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Properties Section -->
<section class="py-4 bg-light">
    <div class="container py-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h2 fw-bold text-dark mb-2">
                    <?= isset($isSearchResults) && $isSearchResults ? 'Search Results' : 'Available Properties' ?>
                </h2>
                <p class="text-muted">
                    <?= $totalProperties ?> properties found
                    <?php if ($totalPages > 1): ?>
                        • Page <?= $currentPage ?> of <?= $totalPages ?>
                    <?php endif; ?>
                </p>
            </div>
            <?php if (isset($user) && $user): ?>
                <a href="<?= base_url('properties/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Add Property
                </a>
            <?php endif; ?>
        </div>

        <?php if (empty($properties)): ?>
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-home display-1 text-muted"></i>
                </div>
                <h3 class="h3 fw-bold text-dark mb-3">No Properties Found</h3>
                <p class="fs-5 text-muted mb-4 mx-auto" style="max-width: 400px;">
                    We couldn't find any properties matching your criteria. Try adjusting your search filters.
                </p>
                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                    <a href="<?= base_url('properties') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-refresh me-2"></i>
                        View All Properties
                    </a>
                    <?php if (isset($user) && $user): ?>
                        <a href="<?= base_url('properties/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Add First Property
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <!-- Properties Grid -->
            <div class="row g-4">
                <?php foreach ($properties as $index => $property): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 shadow-sm card-hover animate-on-scroll property-card-clickable"
                             style="animation-delay: <?= $index * 0.1 ?>s; cursor: pointer;"
                             data-property-url="<?= base_url('properties/' . urlencode($property['location']) . '/' . $property['id']) ?>">
                            <?php
                            // Use ImageDisplayService for optimized image display with lazy loading
                            $imageDisplayService = new \App\Services\ImageDisplayService();
                            $images = is_string($property['images']) ? json_decode($property['images'], true) : $property['images'];
                            $firstImage = !empty($images) ? $images[0] : null;

                            // Get optimized image URL for card display (thumbnail size)
                            $imageUrl = $imageDisplayService->getOptimizedImageUrl($firstImage, 'card');
                            $imageDimensions = $imageDisplayService->getImageDimensions($firstImage, 'thumbnail');

                            // Generate srcset for responsive images
                            $srcset = $imageDisplayService->generateSrcset($firstImage);
                            ?>

                            <!-- Property Image with Lazy Loading -->
                            <div class="position-relative overflow-hidden">
                                <img src="<?= base_url('assets/images/placeholder.svg') ?>"
                                     data-lazy-src="<?= $imageUrl ?>"
                                     <?php if ($srcset): ?>data-lazy-srcset="<?= $srcset ?>"<?php endif; ?>
                                     data-fallback="<?= base_url('assets/images/default-property.svg') ?>"
                                     alt="<?= esc($property['title']) ?>"
                                     class="card-img-top property-image-hover"
                                     style="height: 250px; object-fit: cover;"
                                     <?php if ($imageDimensions): ?>
                                     width="<?= $imageDimensions['width'] ?>"
                                     height="<?= $imageDimensions['height'] ?>"
                                     <?php endif; ?>>

                                <!-- Fallback placeholder -->
                                <div class="property-image-placeholder d-none align-items-center justify-content-center bg-light text-muted"
                                     style="height: 250px; border-radius: 0.5rem 0.5rem 0 0;">
                                    <div class="text-center">
                                        <i class="fas fa-image fs-1 mb-2 opacity-50"></i>
                                        <div class="small">Image not available</div>
                                    </div>
                                </div>

                                <!-- Property Type Badge -->
                                <div class="position-absolute top-0 start-0 m-3">
                                    <span class="badge bg-primary fs-6">
                                        <i class="fas fa-<?= $property['type'] === 'house' ? 'home' : ($property['type'] === 'apartment' ? 'building' : ($property['type'] === 'villa' ? 'crown' : 'map')) ?> me-1"></i>
                                        <?= esc(ucfirst($property['type'])) ?>
                                    </span>
                                </div>

                                <!-- Quick Actions -->
                                <div class="position-absolute top-0 end-0 m-3 property-actions">
                                    <div class="d-flex flex-column gap-2">
                                        <button class="btn btn-light btn-sm rounded-circle shadow-sm" title="Add to Favorites">
                                            <i class="fas fa-heart"></i>
                                        </button>
                                        <button class="btn btn-light btn-sm rounded-circle shadow-sm" title="Share">
                                            <i class="fas fa-share-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Property Details -->
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold text-dark mb-2">
                                    <?= esc($property['title']) ?>
                                </h5>

                                <p class="card-text text-muted mb-3">
                                    <?= esc(substr($property['description'], 0, 120)) ?>...
                                </p>

                                <!-- Property Info -->
                                <div class="mb-3">
                                    <div class="d-flex align-items-center text-muted mb-2">
                                        <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                        <span class="fw-medium"><?= esc($property['location']) ?></span>
                                    </div>

                                    <?php if ($property['area']): ?>
                                        <div class="d-flex align-items-center text-muted mb-2">
                                            <i class="fas fa-ruler-combined me-2 text-primary"></i>
                                            <span><?= number_format($property['area']) ?> sq ft</span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="d-flex align-items-center text-muted">
                                        <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                        <span><?= date('M j, Y', strtotime($property['created_at'])) ?></span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex gap-2 mt-auto">
                                    <a href="<?= base_url('properties/' . urlencode($property['location']) . '/' . $property['id']) ?>"
                                       class="btn btn-primary flex-fill">
                                        <i class="fas fa-eye me-2"></i>
                                        View Details
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Modern Pagination -->
            <?php if ($totalPages > 1): ?>
                <?php
                // Build base URL for pagination links
                $baseUrl = isset($isSearchResults) && $isSearchResults ? 'properties/search' : 'properties';
                $searchQuery = isset($searchQuery) ? $searchQuery : '';
                ?>
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mt-5 gap-3">
                    <!-- Pagination Info -->
                    <div class="text-muted small">
                        Showing <strong><?= (($currentPage - 1) * $perPage) + 1 ?></strong> to
                        <strong><?= min($currentPage * $perPage, $totalProperties) ?></strong> of
                        <strong><?= $totalProperties ?></strong> properties
                    </div>

                    <!-- Pagination Navigation -->
                    <nav aria-label="Properties pagination" class="d-flex align-items-center gap-2">
                        <!-- Previous Button -->
                        <?php if ($hasPrevPage): ?>
                            <a class="btn btn-outline-primary btn-sm d-flex align-items-center"
                               href="<?= base_url($baseUrl . '?page=' . ($currentPage - 1) . $searchQuery) ?>"
                               aria-label="Previous page">
                                <i class="fas fa-chevron-left me-1"></i>
                                <span class="d-none d-sm-inline">Previous</span>
                            </a>
                        <?php else: ?>
                            <button class="btn btn-outline-secondary btn-sm d-flex align-items-center" disabled>
                                <i class="fas fa-chevron-left me-1"></i>
                                <span class="d-none d-sm-inline">Previous</span>
                            </button>
                        <?php endif; ?>

                        <!-- Page Numbers -->
                        <div class="d-flex align-items-center gap-1">
                            <?php
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($totalPages, $currentPage + 2);

                            // Show first page if not in range
                            if ($startPage > 1): ?>
                                <a class="btn btn-outline-primary btn-sm"
                                   href="<?= base_url($baseUrl . '?page=1' . $searchQuery) ?>">1</a>
                                <?php if ($startPage > 2): ?>
                                    <span class="text-muted px-2">...</span>
                                <?php endif; ?>
                            <?php endif; ?>

                            <!-- Current page range -->
                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <?php if ($i === $currentPage): ?>
                                    <button class="btn btn-primary btn-sm" disabled>
                                        <?= $i ?>
                                        <span class="visually-hidden">(current)</span>
                                    </button>
                                <?php else: ?>
                                    <a class="btn btn-outline-primary btn-sm"
                                       href="<?= base_url($baseUrl . '?page=' . $i . $searchQuery) ?>"><?= $i ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <!-- Show last page if not in range -->
                            <?php if ($endPage < $totalPages): ?>
                                <?php if ($endPage < $totalPages - 1): ?>
                                    <span class="text-muted px-2">...</span>
                                <?php endif; ?>
                                <a class="btn btn-outline-primary btn-sm"
                                   href="<?= base_url($baseUrl . '?page=' . $totalPages . $searchQuery) ?>"><?= $totalPages ?></a>
                            <?php endif; ?>
                        </div>

                        <!-- Next Button -->
                        <?php if ($hasNextPage): ?>
                            <a class="btn btn-outline-primary btn-sm d-flex align-items-center"
                               href="<?= base_url($baseUrl . '?page=' . ($currentPage + 1) . $searchQuery) ?>"
                               aria-label="Next page">
                                <span class="d-none d-sm-inline">Next</span>
                                <i class="fas fa-chevron-right ms-1"></i>
                            </a>
                        <?php else: ?>
                            <button class="btn btn-outline-secondary btn-sm d-flex align-items-center" disabled>
                                <span class="d-none d-sm-inline">Next</span>
                                <i class="fas fa-chevron-right ms-1"></i>
                            </button>
                        <?php endif; ?>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Newsletter Subscription Section -->
<?= $this->include('components/newsletter_section') ?>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
/* Property Hero Section Styles */
.property-hero-section {
    height: 70vh;
    min-height: 500px;
    max-height: 800px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    position: relative;
}

.hero-background-container {
    z-index: 1;
}

.hero-background-image {
    filter: brightness(0.7);
    transition: transform 0.3s ease;
}

.hero-background-image:hover {
    transform: scale(1.02);
}

.hero-overlay {
    background: linear-gradient(
        135deg,
        rgba(13, 110, 253, 0.4) 0%,
        rgba(0, 0, 0, 0.6) 50%,
        rgba(13, 110, 253, 0.3) 100%
    );
    z-index: 2;
}

.hero-content-container {
    height: 100%;
    z-index: 10;
}

/* Hero Content Styles */
.hero-search-content,
.hero-default-content {
    animation: fadeInUp 0.8s ease-out;
}

.hero-search-content .badge {
    animation: fadeInDown 0.6s ease-out 0.2s both;
}

.hero-search-content h1,
.hero-default-content h1 {
    animation: fadeInUp 0.8s ease-out 0.4s both;
}

.hero-search-content p {
    animation: fadeInUp 0.8s ease-out 0.6s both;
}

.hero-actions {
    animation: fadeInUp 0.8s ease-out 0.8s both;
}

.text-shadow {
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
}

.hero-detail-item {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 25px;
    padding: 0.5rem 1rem;
    display: inline-flex;
    align-items: center;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.hero-detail-item:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
}

.hero-actions .btn {
    border-radius: 50px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.hero-actions .btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
}

.hero-actions .btn:focus {
    outline: 3px solid #ffffff;
    outline-offset: 2px;
    box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.5);
}

.hero-actions .btn-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
    border: none;
}

.hero-actions .btn-outline-light {
    border: 2px solid rgba(255, 255, 255, 0.8);
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
}

.hero-actions .btn-outline-light:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: white;
}

/* Animation Keyframes */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Scroll Indicator */
.hero-scroll-indicator {
    z-index: 10;
    animation: bounce 2s infinite;
}

.scroll-indicator-container {
    opacity: 0.8;
    transition: all 0.3s ease;
}

.scroll-indicator-container:hover {
    opacity: 1;
    transform: translateY(-5px);
}

.scroll-arrow {
    animation: float 2s ease-in-out infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateX(-50%) translateY(0);
    }
    40% {
        transform: translateX(-50%) translateY(-10px);
    }
    60% {
        transform: translateX(-50%) translateY(-5px);
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-8px);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .property-hero-section {
        height: 60vh;
        min-height: 400px;
    }

    .hero-content-slide h1 {
        font-size: 2rem;
    }

    .hero-property-details .row {
        flex-direction: column;
        align-items: center;
    }

    .hero-detail-item {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
    }

    .hero-actions .btn {
        font-size: 0.9rem;
        padding: 0.75rem 1.5rem;
    }

    .carousel-control-prev,
    .carousel-control-next {
        width: 50px;
        height: 50px;
    }

    .carousel-control-prev {
        left: 1rem;
    }

    .carousel-control-next {
        right: 1rem;
    }
}

@media (max-width: 576px) {
    .property-hero-section {
        height: 50vh;
        min-height: 350px;
    }

    .hero-content-slide h1 {
        font-size: 1.75rem;
    }

    .hero-content-slide p {
        font-size: 1rem;
    }

    .hero-actions .d-flex {
        flex-direction: column;
    }

    .hero-actions .btn {
        width: 100%;
        max-width: 280px;
        margin: 0 auto;
    }

    .carousel-indicators {
        bottom: 1rem;
    }

    .hero-scroll-indicator {
        display: none;
    }
}

/* Smooth Scrolling */
.smooth-scroll {
    scroll-behavior: smooth;
}

/* Accessibility: Respect user's motion preferences */
@media (prefers-reduced-motion: reduce) {
    .property-hero-section *,
    .hero-content-slide,
    .hero-background-image,
    .carousel-item,
    .hero-actions .btn,
    .carousel-control-prev,
    .carousel-control-next,
    .carousel-indicators button {
        animation: none !important;
        transition: none !important;
        transform: none !important;
    }

    .smooth-scroll {
        scroll-behavior: auto;
    }

    .hero-scroll-indicator {
        animation: none;
    }

    .scroll-arrow {
        animation: none;
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .hero-overlay {
        background: rgba(0, 0, 0, 0.8);
    }

    .hero-detail-item {
        background: rgba(0, 0, 0, 0.8);
        border: 2px solid white;
    }

    .carousel-control-prev,
    .carousel-control-next {
        background: rgba(0, 0, 0, 0.9);
        border: 3px solid white;
    }

    .carousel-indicators button {
        border: 3px solid white;
        background: rgba(0, 0, 0, 0.8);
    }
}

/* Skip link for keyboard navigation */
.skip-to-content {
    position: absolute;
    top: -40px;
    left: 6px;
    background: #000;
    color: #fff;
    padding: 8px;
    text-decoration: none;
    z-index: 1000;
    border-radius: 4px;
    transition: top 0.3s;
}

.skip-to-content:focus {
    top: 6px;
    outline: 2px solid #fff;
}

/* Compact Search Section Styles */
.compact-search-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-bottom: 1px solid #e9ecef;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.compact-select,
.compact-input {
    border: 1px solid #dee2e6;
    border-radius: 6px;
    transition: all 0.2s ease;
    font-size: 0.875rem;
}

.compact-select:focus,
.compact-input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
}

.compact-select:hover,
.compact-input:hover {
    border-color: #adb5bd;
}

.form-check-input {
    margin-top: 0.125rem;
}

.quick-filter {
    transition: all 0.2s ease;
    border-radius: 20px;
    font-size: 0.8rem;
    padding: 0.25rem 0.75rem;
}

.quick-filter:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.quick-filter.active {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    color: white;
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    border: 1px solid #b6d4da;
    color: #0c5460;
}

.btn-close-sm {
    font-size: 0.75rem;
    padding: 0.25rem;
}

/* Responsive adjustments for compact search */
@media (max-width: 768px) {
    .compact-search-section {
        padding: 1rem 0;
    }

    .compact-select,
    .compact-input {
        font-size: 0.8rem;
    }

    .form-label {
        font-size: 0.75rem;
    }

    .quick-filter {
        font-size: 0.75rem;
        padding: 0.2rem 0.6rem;
    }
}

@media (max-width: 576px) {
    .d-flex.gap-2 {
        flex-direction: column;
        gap: 0.5rem !important;
    }

    .btn-sm {
        width: 100%;
    }
}

/* Modern Pagination Styles */
.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 0.375rem;
    font-weight: 500;
    transition: all 0.2s ease;
    min-width: 40px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-outline-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
}

.btn-primary {
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.2);
}

/* Property card hover effects */
.card-hover {
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.card-hover:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.property-image-hover {
    transition: transform 0.3s ease;
}

.card-hover:hover .property-image-hover {
    transform: scale(1.05);
}

.property-actions {
    opacity: 0;
    transition: opacity 0.3s ease;
}

.card-hover:hover .property-actions {
    opacity: 1;
}

/* Modern Property Search Section */
.property-search-section {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    min-height: auto;
}

.bg-gradient-light {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%) !important;
}

.modern-search-card {
    border: 1px solid rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.modern-search-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1) !important;
}

.search-field {
    position: relative;
}

.modern-select,
.modern-input {
    border: 2px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 0.875rem 1rem;
    font-size: 1rem;
    transition: all 0.3s ease;
    background-color: #f8fafc;
}

.modern-select:focus,
.modern-input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.1);
    background-color: white;
    transform: translateY(-1px);
}

.modern-search-btn {
    border-radius: 0.75rem;
    padding: 0.875rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    border: none;
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
}

.modern-search-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
}

.modern-search-btn:active {
    transform: translateY(0);
}

.quick-filter {
    border-radius: 1.5rem;
    padding: 0.375rem 0.875rem;
    font-size: 0.875rem;
    transition: all 0.3s ease;
}

.quick-filter:hover {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
    transform: translateY(-1px);
}

/* Mobile optimizations for search */
@media (max-width: 768px) {
    .modern-search-card {
        padding: 1.5rem !important;
        margin: 0 1rem;
    }

    .search-field .form-label {
        font-size: 0.9rem;
        margin-bottom: 0.5rem !important;
    }

    .modern-select,
    .modern-input {
        padding: 0.75rem;
        font-size: 0.95rem;
    }

    .modern-search-btn {
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
    }

    .quick-filter {
        font-size: 0.8rem;
        padding: 0.25rem 0.75rem;
    }
}

@media (max-width: 576px) {
    .property-search-section {
        padding: 2rem 0 !important;
    }

    .modern-search-card {
        padding: 1rem !important;
        margin: 0 0.5rem;
    }

    .row.g-4 {
        --bs-gutter-x: 1rem;
        --bs-gutter-y: 1rem;
    }
}

/* Responsive design improvements */
@media (max-width: 768px) {
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
        min-width: 36px;
        height: 32px;
    }

    .d-flex.gap-1 {
        gap: 0.25rem !important;
    }
}

@media (max-width: 576px) {
    .hero-gradient .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .property-actions {
        opacity: 1; /* Always visible on mobile */
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Properties page functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize hero section
    initializePropertyHeroSection();

    // Initialize animations
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

    // Initialize compact search functionality
    initializeCompactSearch();

    /**
     * Safe closest() function with fallback for maximum browser compatibility
     * @param {Element} element - The element to start searching from
     * @param {string} selector - The CSS selector to match
     * @returns {Element|null} - The closest matching element or null
     */
    function safeClosest(element, selector) {
        // Return null if element is not valid
        if (!element || !element.nodeType) return null;

        // Use native closest if available
        if (element.closest) {
            return element.closest(selector);
        }

        // Fallback implementation for older browsers
        var current = element;
        while (current && current.nodeType === 1) {
            if (current.matches && current.matches(selector)) {
                return current;
            }
            current = current.parentElement || current.parentNode;
        }
        return null;
    }

    // Property card interactions using event delegation (more efficient)
    // This prevents memory leaks and improves performance with many cards
    // Using safeClosest function to prevent "closest is not a function" errors
    document.addEventListener('mouseenter', function(e) {
        // Ensure e.target is a valid DOM element before calling safeClosest
        if (!e.target || !e.target.nodeType) return;

        const card = safeClosest(e.target, '.card');
        if (card) {
            card.style.transform = 'translateY(-8px)';
            card.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease';
        }
    }, true);

    document.addEventListener('mouseleave', function(e) {
        // Ensure e.target is a valid DOM element before calling safeClosest
        if (!e.target || !e.target.nodeType) return;

        const card = safeClosest(e.target, '.card');
        if (card) {
            card.style.transform = 'translateY(0)';
        }
    }, true);

    // Property card click navigation
    document.addEventListener('click', function(e) {
        // Ensure e.target is a valid DOM element before calling safeClosest
        if (!e.target || !e.target.nodeType) return;

        const clickableCard = safeClosest(e.target, '.property-card-clickable');
        if (clickableCard) {
            // Don't navigate if user clicked on a button or link
            if (safeClosest(e.target, 'button, a, .btn')) {
                return;
            }

            const propertyUrl = clickableCard.getAttribute('data-property-url');
            if (propertyUrl) {
                window.location.href = propertyUrl;
            }
        }
    });
});

/**
 * Initialize compact search functionality
 */
function initializeCompactSearch() {
    const searchForm = document.querySelector('.compact-search-form');
    const quickFilters = document.querySelectorAll('.quick-filter');
    const searchInputs = document.querySelectorAll('.compact-select, .compact-input');

    // Quick filter functionality with improved error handling
    quickFilters.forEach(filter => {
        filter.addEventListener('click', function(e) {
            e.preventDefault();

            try {
                const location = this.getAttribute('data-location');
                const type = this.getAttribute('data-type');
                const featured = this.getAttribute('data-featured');

                // Validate form exists
                if (!searchForm) {
                    showQuickFilterError('Search form not available. Please refresh the page.');
                    return;
                }

                // Update form fields
                if (location) {
                    const locationSelect = searchForm.querySelector('select[name="location"]');
                    if (locationSelect) {
                        locationSelect.value = location;
                        locationSelect.dispatchEvent(new Event('change'));
                    } else {
                        showQuickFilterError('Location filter not available.');
                        return;
                    }
                }

                if (type) {
                    const typeSelect = searchForm.querySelector('select[name="type"]');
                    if (typeSelect) {
                        typeSelect.value = type;
                        typeSelect.dispatchEvent(new Event('change'));
                    } else {
                        showQuickFilterError('Property type filter not available.');
                        return;
                    }
                }

                if (featured) {
                    const featuredCheckbox = searchForm.querySelector('input[name="featured"]');
                    if (featuredCheckbox) {
                        featuredCheckbox.checked = true;
                        featuredCheckbox.dispatchEvent(new Event('change'));
                    } else {
                        showQuickFilterError('Featured filter not available.');
                        return;
                    }
                }

                // Add visual feedback
                this.classList.add('active');
                setTimeout(() => {
                    this.classList.remove('active');
                }, 200);

                // Auto-submit form after a short delay with error handling
                setTimeout(() => {
                    try {
                        if (searchForm && typeof searchForm.submit === 'function') {
                            searchForm.submit();
                        } else {
                            // Fallback: redirect manually
                            const formData = new FormData(searchForm);
                            const params = new URLSearchParams();
                            for (let [key, value] of formData.entries()) {
                                if (value) params.append(key, value);
                            }
                            window.location.href = searchForm.action + '?' + params.toString();
                        }
                    } catch (submitError) {
                        showQuickFilterError('Unable to apply filter. Please try using the search form manually.');
                    }
                }, 300);

            } catch (error) {
                showQuickFilterError('An error occurred while applying the filter. Please try again.');
            }
        });
    });

    // Enhanced form interactions
    searchInputs.forEach(input => {
        // Add focus effects
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });

        // Add change effects for selects
        if (input.tagName === 'SELECT') {
            input.addEventListener('change', function() {
                if (this.value) {
                    this.classList.add('has-value');
                } else {
                    this.classList.remove('has-value');
                }
            });

            // Check initial state
            if (input.value) {
                input.classList.add('has-value');
            }
        }
    });

    // Form submission with loading state
    if (searchForm) {
        searchForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('.modern-search-btn');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Searching...';
            submitBtn.disabled = true;

            // Re-enable after a delay (in case of errors)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 5000);
        });
    }
}

/**
 * Show user-friendly error message for quick filter issues
 */
function showQuickFilterError(message) {
    // Remove any existing error messages
    const existingError = document.querySelector('.quick-filter-error');
    if (existingError) {
        existingError.remove();
    }

    // Create error message element
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-warning alert-dismissible fade show quick-filter-error mt-3';
    errorDiv.innerHTML = `
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Filter Error:</strong> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    // Insert error message after the search form
    const searchSection = document.querySelector('.compact-search-section');
    if (searchSection) {
        searchSection.parentNode.insertBefore(errorDiv, searchSection.nextSibling);

        // Auto-hide after 5 seconds
        setTimeout(() => {
            if (errorDiv && errorDiv.parentNode) {
                errorDiv.remove();
            }
        }, 5000);
    } else {
        // Fallback: show as browser alert
        alert('Filter Error: ' + message);
    }
}

// Pagination functionality is now handled by direct links for better performance

// Add to favorites function
function addToFavorites(propertyId) {
    // Implementation for adding to favorites
    // Add property to favorites functionality

    // Use safe notification function
    if (typeof showNotification === 'function') {
        showNotification('Property added to favorites!', 'success');
    } else {
        // Fallback notification
        alert('Property added to favorites!');
    }
}

// Share property function
function shareProperty(propertyId) {
    try {
        // Implementation for sharing property
        if (navigator.share) {
            navigator.share({
                title: 'Check out this property',
                url: window.location.href
            }).catch(err => {
                // Share cancelled or failed
            });
        } else if (navigator.clipboard) {
            // Fallback - copy to clipboard
            navigator.clipboard.writeText(window.location.href).then(() => {
                if (typeof showNotification === 'function') {
                    showNotification('Property link copied to clipboard!', 'success');
                } else {
                    alert('Property link copied to clipboard!');
                }
            }).catch(err => {
                // Final fallback
                prompt('Copy this link:', window.location.href);
            });
        } else {
            // Final fallback for older browsers
            prompt('Copy this link:', window.location.href);
        }
    } catch (error) {
        // Final fallback
        prompt('Copy this link:', window.location.href);
    }
}

/**
 * Initialize Property Hero Section
 *
 * Handles smooth scrolling and responsive interactions for the static hero section.
 */
function initializePropertyHeroSection() {
    // Initialize smooth scrolling for hero links
    const smoothScrollLinks = document.querySelectorAll('.smooth-scroll');
    smoothScrollLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');

            if (targetId.startsWith('#')) {
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    const headerOffset = 140; // Account for fixed header
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });

    // Add intersection observer for hero section animations
    const heroObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('hero-visible');

                // Trigger content animations
                const heroContent = entry.target.querySelector('.hero-search-content, .hero-default-content');
                if (heroContent) {
                    heroContent.style.animationPlayState = 'running';
                }
            }
        });
    }, { threshold: 0.3 });

    const heroSection = document.querySelector('.property-hero-section');
    if (heroSection) {
        heroObserver.observe(heroSection);
    }

    // Add subtle parallax effect to hero background image
    const heroImage = document.querySelector('.hero-background-image');
    if (heroImage) {
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const parallax = scrolled * 0.5;
            heroImage.style.transform = `translateY(${parallax}px) scale(1.1)`;
        });
    }
}
</script>
<?= $this->endSection() ?>
