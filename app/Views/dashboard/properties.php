<?php
/**
 * Dashboard Properties Management View
 *
 * This view provides a comprehensive property management interface with:
 * - Responsive design: Desktop table view and mobile card layout
 * - Server-side pagination with 10 properties per page
 * - Advanced filtering and search capabilities
 * - Touch-friendly mobile interface with optimized buttons
 * - Real-time property status management (featured/unfeatured)
 * - Secure CRUD operations with user confirmation
 *
 * Mobile Responsiveness Features:
 * - Card-based layout for screens < 992px (lg breakpoint)
 * - Touch-friendly buttons with minimum 44px touch targets
 * - Optimized image display and content truncation
 * - Accessible navigation and interaction elements
 *
 * @author White Rock Realtor Team
 * @version 2.0 - Enhanced with mobile responsiveness and improved UX
 * @since 2025-08-20
 */
?>
<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<!-- Page Header with responsive design -->
<div class="mb-4">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Properties Management</h2>
            <p class="text-muted">Manage all your property listings with mobile-friendly interface</p>
        </div>
        <div class="mt-3 mt-sm-0">
            <a href="<?= base_url('properties/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Add New Property
            </a>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-medium">Search</label>
                <input type="text" 
                       data-table-search="properties-table"
                       placeholder="Search properties..." 
                       class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium">Type</label>
                <select class="form-select">
                    <option value="">All Types</option>
                    <option value="house">House</option>
                    <option value="apartment">Apartment</option>
                    <option value="villa">Villa</option>
                    <option value="land">Land</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium">Location</label>
                <select class="form-select">
                    <option value="">All Locations</option>
                    <option value="Dublin">Dublin</option>
                    <option value="Cork">Cork</option>
                    <option value="Galway">Galway</option>
                    <option value="Limerick">Limerick</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-outline-primary w-100">
                    <i class="fas fa-filter me-2"></i>
                    Apply Filters
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Properties Table -->
<div class="card">
    <div class="card-header border-bottom">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="card-title mb-1">Properties</h5>
                <p class="card-text text-muted small mb-0">A list of all properties in your account</p>
            </div>
            <div class="text-muted small" data-table-info="properties-table">
                Showing <?= $startRecord ?? 1 ?>-<?= $endRecord ?? count($properties ?? []) ?> of <?= $totalProperties ?? count($properties ?? []) ?> results
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        <!-- Desktop Table View -->
        <div class="d-none d-lg-block">
            <div class="table-responsive">
                <table id="properties-table" data-table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="sortable" data-sort="0">
                                Property
                                <i class="fas fa-sort sort-indicator ms-1"></i>
                            </th>
                            <th class="sortable" data-sort="1">
                                Type
                                <i class="fas fa-sort sort-indicator ms-1"></i>
                            </th>
                            <th class="sortable" data-sort="2">
                                Location
                                <i class="fas fa-sort sort-indicator ms-1"></i>
                            </th>
                            <th class="sortable" data-sort="3">
                                Area
                                <i class="fas fa-sort sort-indicator ms-1"></i>
                            </th>
                            <th class="sortable" data-sort="4">
                                Created
                                <i class="fas fa-sort sort-indicator ms-1"></i>
                            </th>
                            <th class="sortable" data-sort="5">
                                Featured
                                <i class="fas fa-sort sort-indicator ms-1"></i>
                            </th>
                            <th class="text-end">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($properties)): ?>
                            <?php foreach ($properties as $property): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php
                                            $imageDisplayService = new \App\Services\ImageDisplayService();
                                            $images = is_string($property['images']) ? json_decode($property['images'], true) : $property['images'];
                                            $firstImage = !empty($images) ? $images[0] : null;
                                            $propertyImage = $imageDisplayService->getOptimizedImageUrl($firstImage, 'thumbnail');
                                            ?>
                                            <img src="<?= $propertyImage ?>" alt="<?= esc($property['title']) ?>" class="property-image me-3">
                                            <div>
                                                <div class="fw-medium"><?= esc($property['title']) ?></div>
                                                <div class="text-muted small"><?= esc(substr($property['description'], 0, 50)) ?>...</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-property-<?= strtolower($property['type']) ?>">
                                            <?= esc(ucfirst($property['type'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                            <?= esc($property['location']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?= $property['area'] ? number_format($property['area']) . ' sq ft' : 'N/A' ?>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= date('M j, Y', strtotime($property['created_at'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <button onclick="toggleFeatured(<?= $property['id'] ?>)"
                                                    class="btn btn-sm <?= $property['is_featured'] ? 'btn-warning' : 'btn-outline-secondary' ?>"
                                                    title="<?= $property['is_featured'] ? 'Remove from Featured' : 'Mark as Featured' ?>">
                                                <i class="fas fa-star"></i>
                                                <?= $property['is_featured'] ? 'Featured' : 'Feature' ?>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-end">
                                            <div class="btn-group btn-group-sm">
                                                <button data-property-quick-view data-property-id="<?= $property['id'] ?>"
                                                        class="btn btn-ghost"
                                                        title="Quick View">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <a href="<?= base_url('dashboard/properties/edit/' . $property['id']) ?>"
                                                   class="btn btn-ghost"
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button onclick="deleteProperty(<?= $property['id'] ?>)"
                                                        class="btn btn-ghost text-danger"
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-home text-muted mb-3" style="font-size: 4rem;"></i>
                                        <h5 class="text-dark mb-2">No properties found</h5>
                                        <p class="text-muted mb-3">Get started by adding your first property.</p>
                                        <a href="<?= base_url('properties/create') ?>" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>
                                            Add Property
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="d-lg-none">
            <?php if (!empty($properties)): ?>
                <div class="row g-3 p-3">
                    <?php foreach ($properties as $property): ?>
                        <?php
                        $imageDisplayService = new \App\Services\ImageDisplayService();
                        $images = is_string($property['images']) ? json_decode($property['images'], true) : $property['images'];
                        $firstImage = !empty($images) ? $images[0] : null;
                        $propertyImage = $imageDisplayService->getOptimizedImageUrl($firstImage, 'thumbnail');
                        ?>
                        <div class="col-12">
                            <div class="card property-mobile-card h-100 shadow-sm">
                                <div class="row g-0">
                                    <div class="col-4">
                                        <img src="<?= $propertyImage ?>"
                                             alt="<?= esc($property['title']) ?>"
                                             class="img-fluid rounded-start h-100 object-cover"
                                             style="min-height: 120px; max-height: 120px;">
                                    </div>
                                    <div class="col-8">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-1 fw-bold text-truncate" style="max-width: 70%;">
                                                    <?= esc($property['title']) ?>
                                                </h6>
                                                <?php if ($property['is_featured']): ?>
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-star"></i>
                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="mb-2">
                                                <span class="badge badge-property-<?= strtolower($property['type']) ?> me-2">
                                                    <?= esc(ucfirst($property['type'])) ?>
                                                </span>
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    <?= esc($property['location']) ?>
                                                </small>
                                            </div>

                                            <p class="card-text small text-muted mb-2">
                                                <?= esc(substr($property['description'], 0, 60)) ?>...
                                            </p>

                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    <?= $property['area'] ? number_format($property['area']) . ' sq ft' : 'N/A' ?>
                                                    <br>
                                                    <?= date('M j, Y', strtotime($property['created_at'])) ?>
                                                </small>

                                                <div class="btn-group btn-group-sm">
                                                    <button data-property-quick-view data-property-id="<?= $property['id'] ?>"
                                                            class="btn btn-outline-primary btn-sm"
                                                            title="Quick View">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <a href="<?= base_url('dashboard/properties/edit/' . $property['id']) ?>"
                                                       class="btn btn-outline-secondary btn-sm"
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button onclick="toggleFeatured(<?= $property['id'] ?>)"
                                                            class="btn btn-sm <?= $property['is_featured'] ? 'btn-warning' : 'btn-outline-warning' ?>"
                                                            title="<?= $property['is_featured'] ? 'Remove from Featured' : 'Mark as Featured' ?>">
                                                        <i class="fas fa-star"></i>
                                                    </button>
                                                    <button onclick="deleteProperty(<?= $property['id'] ?>)"
                                                            class="btn btn-outline-danger btn-sm"
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                        <i class="fas fa-home text-muted mb-3" style="font-size: 4rem;"></i>
                        <h5 class="text-dark mb-2">No properties found</h5>
                        <p class="text-muted mb-3">Get started by adding your first property.</p>
                        <a href="<?= base_url('properties/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Add Property
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pagination Controls -->
    <?php if (isset($totalPages) && $totalPages > 1): ?>
    <div class="card-footer bg-white border-top">
        <div class="d-flex justify-content-center">
            <nav aria-label="Properties pagination">
                <ul class="pagination pagination-sm mb-0">
                    <!-- Previous Button -->
                    <?php if ($currentPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= base_url('dashboard/properties?page=' . ($currentPage - 1)) ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
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
                            <a class="page-link" href="<?= base_url('dashboard/properties?page=1') ?>">1</a>
                        </li>
                        <?php if ($startPage > 2): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Current page range -->
                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                            <?php if ($i == $currentPage): ?>
                                <span class="page-link"><?= $i ?></span>
                            <?php else: ?>
                                <a class="page-link" href="<?= base_url('dashboard/properties?page=' . $i) ?>"><?= $i ?></a>
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
                            <a class="page-link" href="<?= base_url('dashboard/properties?page=' . $totalPages) ?>"><?= $totalPages ?></a>
                        </li>
                    <?php endif; ?>

                    <!-- Next Button -->
                    <?php if ($currentPage < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= base_url('dashboard/properties?page=' . ($currentPage + 1)) ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </span>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>

        <!-- Pagination Info -->
        <div class="text-center mt-3">
            <small class="text-muted">
                Page <?= $currentPage ?> of <?= $totalPages ?>
                (<?= $totalProperties ?> total properties)
            </small>
        </div>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Properties page specific functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize data table
    const table = document.getElementById('properties-table');
    if (table && window.DataTable) {
        new window.DataTable(table);
    }
});

// Delete property function
function deleteProperty(propertyId) {
    // Create confirmation modal
    const modalHtml = `
        <div class="modal fade" id="deletePropertyModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Property</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center py-3">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 4rem; height: 4rem;">
                                <i class="fas fa-exclamation-triangle text-danger fs-4"></i>
                            </div>
                            <h6 class="mb-2">Are you sure you want to delete this property?</h6>
                            <p class="text-muted small mb-0">
                                This action cannot be undone. This will permanently delete the property and remove all associated data.
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" onclick="confirmDelete(${propertyId})">
                            <i class="fas fa-trash me-2"></i>Delete Property
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Remove existing modal
    const existingModal = document.getElementById('deletePropertyModal');
    if (existingModal) {
        existingModal.remove();
    }

    // Add modal to DOM
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('deletePropertyModal'));
    modal.show();

    // Clean up when modal is hidden
    document.getElementById('deletePropertyModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

// Confirm delete function
function confirmDelete(propertyId) {
    // Hide the confirmation modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('deletePropertyModal'));
    modal.hide();

    // Show loading notification
    showNotification('Deleting property...', 'info');

    // Make AJAX call to delete property
    fetch(`<?= base_url('dashboard/properties/') ?>${propertyId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Property deleted successfully', 'success');
            // Reload page after short delay
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showNotification(data.message || 'Failed to delete property', 'danger');
        }
    })
    .catch(error => {
        showNotification('Error deleting property', 'danger');
    });
}

// Toggle featured status function
function toggleFeatured(propertyId) {
    // Show loading notification
    showNotification('Updating featured status...', 'info');

    // Make AJAX call to toggle featured status
    fetch(`<?= base_url('dashboard/properties/toggle-featured/') ?>${propertyId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message || 'Featured status updated successfully', 'success');
            // Reload page after short delay
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showNotification(data.message || 'Failed to update featured status', 'danger');
        }
    })
    .catch(error => {
        showNotification('Error updating featured status', 'danger');
    });
}
</script>
<?= $this->endSection() ?>
