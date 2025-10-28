<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-4">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Enquiries Management</h2>
            <p class="text-muted">Manage customer enquiries and messages</p>
        </div>
        <div class="mt-3 mt-sm-0">
            <div class="text-muted small">
                <i class="fas fa-info-circle me-1"></i>
                Admin notifications are sent automatically for new enquiries
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card card-stats border-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="icon-stat bg-primary-light">
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted mb-1 small fw-medium">Total Enquiries</p>
                        <p class="h3 fw-bold mb-0"><?= number_format($totalContacts ?? 0) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-stats border-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="icon-stat bg-warning-light">
                            <i class="fas fa-bell"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted mb-1 small fw-medium">Unread</p>
                        <p class="h3 fw-bold mb-0"><?= number_format($unreadContacts ?? 0) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-stats border-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="icon-stat bg-success-light">
                            <i class="fas fa-reply"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted mb-1 small fw-medium">This Week</p>
                        <p class="h3 fw-bold mb-0"><?= number_format($weeklyContacts ?? 0) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Enquiries Table -->
<div class="card">
    <div class="card-header border-bottom">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="card-title mb-1">Customer Enquiries</h5>
                <p class="card-text text-muted small mb-0">All customer enquiries and messages</p>
            </div>
            <div class="text-muted small" data-table-info="enquiries-table">
                Showing 1-10 of <?= count($contacts ?? []) ?> results
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        <!-- Desktop Table View - Hidden on mobile devices -->
        <!-- Uses Bootstrap 5 responsive utilities: d-none d-lg-block shows only on large screens -->
        <!-- Traditional table layout works well on desktop with sufficient screen space -->
        <div class="d-none d-lg-block">
            <div class="table-responsive">
                <table id="enquiries-table" data-table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="sortable" data-sort="0">
                            Customer
                            <i class="fas fa-sort sort-indicator ms-1"></i>
                        </th>
                        <th class="sortable" data-sort="1">
                            Property Interest In
                            <i class="fas fa-sort sort-indicator ms-1"></i>
                        </th>
                        <th class="sortable" data-sort="2">
                            Message
                            <i class="fas fa-sort sort-indicator ms-1"></i>
                        </th>
                        <th class="sortable" data-sort="3">
                            Date
                            <i class="fas fa-sort sort-indicator ms-1"></i>
                        </th>
                        <th class="sortable" data-sort="4">
                            Status
                            <i class="fas fa-sort sort-indicator ms-1"></i>
                        </th>
                        <th class="text-end">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($contacts)): ?>
                        <?php foreach ($contacts as $contact): ?>
                            <tr class="<?= !$contact['is_read'] ? 'table-warning' : '' ?>">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 2.5rem; height: 2.5rem;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <div class="fw-medium"><?= esc($contact['name']) ?></div>
                                            <div class="text-muted small"><?= esc($contact['email']) ?></div>
                                            <div class="text-muted small"><?= esc($contact['phone']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?= esc(ucfirst($contact['properties_in'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="text-truncate-2" style="max-width: 200px;">
                                        <?= esc(substr($contact['message'], 0, 100)) ?><?= strlen($contact['message']) > 100 ? '...' : '' ?>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= date('M j, Y g:i A', strtotime($contact['created_at'])) ?>
                                    </small>
                                </td>
                                <td>
                                    <?php if ($contact['is_read']): ?>
                                        <span class="badge status-read">
                                            <i class="fas fa-check-circle me-1"></i>
                                            Read
                                        </span>
                                    <?php else: ?>
                                        <span class="badge status-unread">
                                            <i class="fas fa-bell me-1"></i>
                                            Unread
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-end">
                                        <div class="btn-group btn-group-sm">
                                            <button data-enquiry-quick-view data-enquiry-id="<?= $contact['id'] ?>" 
                                                    class="btn btn-ghost"
                                                    title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php if (!$contact['is_read']): ?>
                                                <button onclick="markAsRead(<?= $contact['id'] ?>)"
                                                        class="btn btn-ghost"
                                                        title="Mark as Read">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button onclick="deleteEnquiry(<?= $contact['id'] ?>)" 
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
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-envelope text-muted mb-3" style="font-size: 4rem;"></i>
                                    <h5 class="text-dark mb-2">No enquiries found</h5>
                                    <p class="text-muted">Customer enquiries will appear here when they contact you.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        </div>

        <!-- Mobile Card View - Responsive design for small screens -->
        <!-- Uses Bootstrap 5 responsive utilities: d-lg-none hides on large screens and up -->
        <!-- Card-based layout provides better mobile UX compared to cramped table rows -->
        <div class="d-lg-none">
            <?php if (!empty($contacts)): ?>
                <div class="row g-3 p-3">
                    <?php foreach ($contacts as $contact): ?>
                        <div class="col-12">
                            <div class="card enquiry-mobile-card h-100 shadow-sm <?= !$contact['is_read'] ? 'border-warning' : '' ?>">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 2.5rem; height: 2.5rem;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="card-title mb-1 fw-bold"><?= esc($contact['name']) ?></h6>
                                                <small class="text-muted"><?= esc($contact['email']) ?></small>
                                            </div>
                                        </div>
                                        <?php if (!$contact['is_read']): ?>
                                            <span class="badge bg-warning text-dark">New</span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mb-2">
                                        <small class="text-muted d-block">
                                            <i class="fas fa-phone me-1"></i><?= esc($contact['phone']) ?>
                                        </small>
                                        <span class="badge bg-secondary mt-1">
                                            <?= esc(ucfirst($contact['properties_in'])) ?>
                                        </span>
                                    </div>

                                    <p class="card-text small mb-2">
                                        <?= esc(substr($contact['message'], 0, 100)) ?><?= strlen($contact['message']) > 100 ? '...' : '' ?>
                                    </p>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <?= date('M j, Y g:i A', strtotime($contact['created_at'])) ?>
                                        </small>

                                        <div class="btn-group btn-group-sm">
                                            <?php if (!$contact['is_read']): ?>
                                                <button onclick="markAsRead(<?= $contact['id'] ?>)"
                                                        class="btn btn-outline-primary btn-sm"
                                                        title="Mark as Read">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button onclick="deleteEnquiry(<?= $contact['id'] ?>)"
                                                    class="btn btn-outline-danger btn-sm"
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
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
                        <i class="fas fa-envelope text-muted mb-3" style="font-size: 4rem;"></i>
                        <h5 class="text-dark mb-2">No enquiries found</h5>
                        <p class="text-muted">Customer enquiries will appear here when they contact you.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Pagination -->
<?php if (isset($totalPages) && $totalPages > 1): ?>
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted">
            Showing <?= ($currentPage - 1) * $perPage + 1 ?> to <?= min($currentPage * $perPage, $totalContacts) ?> of <?= $totalContacts ?> enquiries
        </div>
        <nav aria-label="Enquiries pagination">
            <ul class="pagination mb-0">
                <!-- Previous Page -->
                <?php if ($currentPage > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= base_url('dashboard/enquiries?page=' . ($currentPage - 1)) ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    </li>
                <?php endif; ?>

                <!-- Page Numbers -->
                <?php
                $startPage = max(1, $currentPage - 2);
                $endPage = min($totalPages, $currentPage + 2);

                if ($startPage > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= base_url('dashboard/enquiries?page=1') ?>">1</a>
                    </li>
                    <?php if ($startPage > 2): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                        <a class="page-link" href="<?= base_url('dashboard/enquiries?page=' . $i) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= base_url('dashboard/enquiries?page=' . $totalPages) ?>"><?= $totalPages ?></a>
                    </li>
                <?php endif; ?>

                <!-- Next Page -->
                <?php if ($currentPage < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= base_url('dashboard/enquiries?page=' . ($currentPage + 1)) ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Enquiries page functionality
function markAsRead(enquiryId) {
    fetch(`<?= base_url('dashboard/enquiries/mark-read/') ?>${enquiryId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Enquiry marked as read', 'success');
            setTimeout(() => location.reload(), 1000);
        }
    })
    .catch(error => {
        showNotification('Error marking enquiry as read', 'danger');
    });
}

// Mark All Read functionality removed - admin notifications are automatic

// Reply functionality removed - contact customers directly via their provided email/phone

function deleteEnquiry(enquiryId) {
    const modalHtml = `
        <div class="modal fade" id="deleteEnquiryModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Enquiry</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center py-3">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 4rem; height: 4rem;">
                                <i class="fas fa-exclamation-triangle text-danger fs-4"></i>
                            </div>
                            <h6 class="mb-2">Are you sure you want to delete this enquiry?</h6>
                            <p class="text-muted small mb-0">This action cannot be undone.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" onclick="confirmDeleteEnquiry(${enquiryId})">
                            <i class="fas fa-trash me-2"></i>Delete Enquiry
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('deleteEnquiryModal'));
    modal.show();

    document.getElementById('deleteEnquiryModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

function confirmDeleteEnquiry(enquiryId) {
    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteEnquiryModal'));
    modal.hide();

    // Delete enquiry functionality
    const url = `<?= base_url('dashboard/enquiries/') ?>${enquiryId}`;

    fetch(url, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        // Process response
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        // Handle response data
        if (data.success) {
            showNotification('Enquiry deleted successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Failed to delete enquiry', 'danger');
        }
    })
    .catch(error => {
        showNotification('Error deleting enquiry', 'danger');
    });
}

// Export functionality removed - use browser tools or database exports if needed
</script>
<?= $this->endSection() ?>
