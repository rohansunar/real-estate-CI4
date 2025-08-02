<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="hero-gradient py-4">
    <div class="container py-3 text-center">
        <!-- <h1 class="display-3 fw-bold text-white mb-3 animate-fade-in">
            <?= isset($isSearchResults) && $isSearchResults ? 'Search Results' : 'Discover Your Perfect Property' ?>
        </h1>
        <p class="fs-4 text-light mb-4 mx-auto animate-slide-up" style="max-width: 600px;">
            <?= isset($isSearchResults) && $isSearchResults ?
                'Found ' . $totalProperties . ' properties matching your criteria.' :
                'Browse through our extensive collection of premium properties and find the one that matches your dreams and budget.' ?>
        </p> -->

        <!-- Search Bar -->
        <div class="mx-auto animate-slide-up" style="max-width: 800px;">
            <form action="<?= base_url('properties/search') ?>" method="get" class="bg-white rounded-4 p-4 shadow-lg">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-semibold text-dark small">Location</label>
                        <select name="location" class="form-select">
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
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-semibold text-dark small">Property Type</label>
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="house" <?= (isset($searchParams['type']) && $searchParams['type'] === 'house') ? 'selected' : '' ?>>House</option>
                            <option value="apartment" <?= (isset($searchParams['type']) && $searchParams['type'] === 'apartment') ? 'selected' : '' ?>>Apartment</option>
                            <option value="villa" <?= (isset($searchParams['type']) && $searchParams['type'] === 'villa') ? 'selected' : '' ?>>Villa</option>
                            <option value="land" <?= (isset($searchParams['type']) && $searchParams['type'] === 'land') ? 'selected' : '' ?>>Land</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-semibold text-dark small">Min Area (sq ft)</label>
                        <input type="number" name="min_area" class="form-control" placeholder="Any"
                               value="<?= isset($searchParams['min_area']) ? esc($searchParams['min_area']) : '' ?>">
                    </div>
                    <div class="col-lg-3 col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>
                            Search 
                        </button>
                    </div>
                </div>

                <!-- Clear Search Button for Search Results -->
                <?php if (isset($isSearchResults) && $isSearchResults): ?>
                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <a href="<?= base_url('properties') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>
                                Clear Search
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </form>
        </div>
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
                        <div class="card h-100 shadow-sm card-hover animate-on-scroll" style="animation-delay: <?= $index * 0.1 ?>s">
                            <?php
                            $images = is_string($property['images']) ? json_decode($property['images'], true) : $property['images'];
                            $propertyImage = !empty($images[0]) ? base_url($images[0]) : base_url('assets/images/default-property.svg');
                            ?>

                            <!-- Property Image -->
                            <div class="position-relative overflow-hidden">
                                <img src="<?= $propertyImage ?>" alt="<?= esc($property['title']) ?>"
                                     class="card-img-top property-image-hover" style="height: 250px; object-fit: cover;">

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
                                    <button class="btn btn-outline-primary" title="Contact Agent">
                                        <i class="fas fa-phone"></i>
                                    </button>
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

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
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

/* Search section improvements */
.hero-gradient {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
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

    // Property card interactions using event delegation (more efficient)
    // This prevents memory leaks and improves performance with many cards
    document.addEventListener('mouseenter', function(e) {
        const card = e.target.closest('.card');
        if (card) {
            card.style.transform = 'translateY(-8px)';
            card.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease';
        }
    }, true);

    document.addEventListener('mouseleave', function(e) {
        const card = e.target.closest('.card');
        if (card) {
            card.style.transform = 'translateY(0)';
        }
    }, true);
});

// Pagination functionality is now handled by direct links for better performance

// Add to favorites function
function addToFavorites(propertyId) {
    // Implementation for adding to favorites
    console.log('Adding property to favorites:', propertyId);

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
                console.log('Share cancelled or failed:', err);
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
                console.error('Failed to copy to clipboard:', err);
                // Final fallback
                prompt('Copy this link:', window.location.href);
            });
        } else {
            // Final fallback for older browsers
            prompt('Copy this link:', window.location.href);
        }
    } catch (error) {
        console.error('Share function error:', error);
        // Final fallback
        prompt('Copy this link:', window.location.href);
    }
}
</script>
<?= $this->endSection() ?>
