<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="hero-gradient py-5">
    <div class="container py-5 text-center">
        <h1 class="display-3 fw-bold text-white mb-4 animate-fade-in">
            Discover Your Perfect Property
        </h1>
        <p class="fs-4 text-light mb-5 mx-auto animate-slide-up" style="max-width: 600px;">
            Browse through our extensive collection of premium properties and find the one that matches your dreams and budget.
        </p>

        <!-- Search Bar -->
        <div class="mx-auto animate-slide-up" style="max-width: 800px;">
            <form action="<?= base_url('properties/search') ?>" method="get" class="bg-white rounded-4 p-4 shadow-lg">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-semibold text-dark small">Location</label>
                        <select name="location" class="form-select">
                            <option value="">All Locations</option>
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
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-semibold text-dark small">Property Type</label>
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="house">House</option>
                            <option value="apartment">Apartment</option>
                            <option value="villa">Villa</option>
                            <option value="land">Land</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-semibold text-dark small">Min Area (sq ft)</label>
                        <input type="number" name="min_area" class="form-control" placeholder="Any">
                    </div>
                    <div class="col-lg-3 col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>
                            Search Properties
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Properties Section -->
<section class="py-5 bg-light">
    <div class="container py-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5">
            <div>
                <h2 class="h2 fw-bold text-dark mb-2">Available Properties</h2>
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

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="d-flex justify-content-center mt-5">
                    <nav aria-label="Properties pagination">
                        <ul class="pagination pagination-lg">
                            <!-- Previous Button -->
                            <?php if ($hasPrevPage): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= base_url('properties?page=' . ($currentPage - 1)) ?>" aria-label="Previous">
                                        <span aria-hidden="true">
                                            <i class="fas fa-chevron-left me-1"></i>
                                            Previous
                                        </span>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link" aria-label="Previous">
                                        <span aria-hidden="true">
                                            <i class="fas fa-chevron-left me-1"></i>
                                            Previous
                                        </span>
                                    </span>
                                </li>
                            <?php endif; ?>

                            <!-- Page Numbers -->
                            <?php
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($totalPages, $currentPage + 2);

                            // Show first page if not in range
                            if ($startPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= base_url('properties?page=1') ?>">1</a>
                                </li>
                                <?php if ($startPage > 2): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>

                            <!-- Current page range -->
                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                    <?php if ($i === $currentPage): ?>
                                        <span class="page-link">
                                            <?= $i ?>
                                            <span class="visually-hidden">(current)</span>
                                        </span>
                                    <?php else: ?>
                                        <a class="page-link" href="<?= base_url('properties?page=' . $i) ?>"><?= $i ?></a>
                                    <?php endif; ?>
                                </li>
                            <?php endfor; ?>

                            <!-- Show last page if not in range -->
                            <?php if ($endPage < $totalPages): ?>
                                <?php if ($endPage < $totalPages - 1): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= base_url('properties?page=' . $totalPages) ?>"><?= $totalPages ?></a>
                                </li>
                            <?php endif; ?>

                            <!-- Next Button -->
                            <?php if ($hasNextPage): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= base_url('properties?page=' . ($currentPage + 1)) ?>" aria-label="Next">
                                        <span aria-hidden="true">
                                            Next
                                            <i class="fas fa-chevron-right ms-1"></i>
                                        </span>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link" aria-label="Next">
                                        <span aria-hidden="true">
                                            Next
                                            <i class="fas fa-chevron-right ms-1"></i>
                                        </span>
                                    </span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>

                <!-- Pagination Info -->
                <div class="text-center mt-3">
                    <p class="text-muted mb-0">
                        Showing <?= (($currentPage - 1) * $perPage) + 1 ?> to <?= min($currentPage * $perPage, $totalProperties) ?> of <?= $totalProperties ?> properties
                    </p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
/* Modern Pagination Styles */
.pagination-lg .page-link {
    padding: 0.75rem 1.25rem;
    font-size: 1rem;
    border-radius: 0.5rem;
    border: 2px solid #e9ecef;
    color: #495057;
    font-weight: 500;
    transition: all 0.3s ease;
    margin: 0 0.25rem;
}

.pagination-lg .page-item:first-child .page-link,
.pagination-lg .page-item:last-child .page-link {
    border-radius: 0.5rem;
}

.pagination-lg .page-link:hover {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
}

.pagination-lg .page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
}

.pagination-lg .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #f8f9fa;
    border-color: #e9ecef;
    cursor: not-allowed;
}

.pagination-lg .page-item.disabled .page-link:hover {
    transform: none;
    box-shadow: none;
}

/* Responsive pagination */
@media (max-width: 576px) {
    .pagination-lg .page-link {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        margin: 0 0.125rem;
    }

    .pagination-lg .page-item:not(.active):not(:first-child):not(:last-child) {
        display: none;
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

// Pagination functionality
function goToPage(page) {
    const url = new URL(window.location);
    url.searchParams.set('page', page);
    window.location.href = url.toString();
}

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
