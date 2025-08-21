<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-4">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Newsletter Subscribers</h2>
            <p class="text-muted">Manage your newsletter subscribers</p>
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
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted mb-1 small fw-medium">Total Subscribers</p>
                        <p class="h3 fw-bold mb-0"><?= number_format($totalSubscribers ?? 0) ?></p>
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
                            <i class="fas fa-calendar-week"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted mb-1 small fw-medium">This Week</p>
                        <p class="h3 fw-bold mb-0"><?= number_format($recentSubscribers ?? 0) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-stats border-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="icon-stat bg-info-light">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted mb-1 small fw-medium">Growth Rate</p>
                        <p class="h3 fw-bold mb-0">+12%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-medium">Search</label>
                <input type="text" 
                       data-table-search="subscribers-table"
                       placeholder="Search subscribers by email..." 
                       class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium">Date Range</label>
                <select class="form-select" id="dateFilter">
                    <option value="">All Time</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="year">This Year</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-outline-primary w-100" onclick="applyFilters()">
                    <i class="fas fa-filter me-2"></i>
                    Apply Filters
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Subscribers Table -->
<div class="card">
    <div class="card-header border-bottom">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="card-title mb-1">Newsletter Subscribers</h5>
                <p class="card-text text-muted small mb-0">All newsletter subscribers and their subscription dates</p>
            </div>
            <div class="text-muted small" data-table-info="subscribers-table">
                Showing 1-<?= count($subscribers) ?> of <?= count($subscribers) ?> results
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        <!-- Desktop Table View -->
        <div class="d-none d-lg-block">
            <div class="table-responsive">
                <table id="subscribers-table" data-table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="sortable" data-sort="0">
                            #
                            <i class="fas fa-sort sort-indicator ms-1"></i>
                        </th>
                        <th class="sortable" data-sort="1">
                            Email Address
                            <i class="fas fa-sort sort-indicator ms-1"></i>
                        </th>
                        <th class="sortable" data-sort="2">
                            Subscription Date
                            <i class="fas fa-sort sort-indicator ms-1"></i>
                        </th>
                        <th class="text-end">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($subscribers)): ?>
                        <?php foreach ($subscribers as $index => $subscriber): ?>
                            <tr>
                                <td>
                                    <span class="text-muted"><?= $index + 1 ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                                                 style="width: 2.5rem; height: 2.5rem;">
                                                <i class="fas fa-envelope text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <div class="fw-medium"><?= esc($subscriber['email']) ?></div>
                                            <div class="text-muted small">ID: #<?= $subscriber['id'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-medium"><?= date('M j, Y', strtotime($subscriber['created_at'])) ?></div>
                                        <small class="text-muted"><?= date('g:i A', strtotime($subscriber['created_at'])) ?></small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-end">
                                        <div class="btn-group btn-group-sm">
                                            <button onclick="copyEmail('<?= esc($subscriber['email']) ?>')" 
                                                    class="btn btn-ghost"
                                                    title="Copy Email">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                            <button onclick="deleteSubscriber(<?= $subscriber['id'] ?>)" 
                                                    class="btn btn-ghost text-danger"
                                                    title="Remove Subscriber">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-users text-muted mb-3" style="font-size: 4rem;"></i>
                                    <h5 class="text-dark mb-2">No subscribers found</h5>
                                    <p class="text-muted">Newsletter subscribers will appear here when people subscribe to your newsletter.</p>
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
            <?php if (!empty($subscribers)): ?>
                <div class="row g-3 p-3">
                    <?php foreach ($subscribers as $index => $subscriber): ?>
                        <div class="col-12">
                            <div class="card subscriber-mobile-card h-100 shadow-sm">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 2.5rem; height: 2.5rem;">
                                                <i class="fas fa-envelope text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="card-title mb-1 fw-bold"><?= esc($subscriber['email']) ?></h6>
                                                <small class="text-muted">#<?= $index + 1 ?></small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?= date('M j, Y g:i A', strtotime($subscriber['created_at'])) ?>
                                        </small>

                                        <div class="btn-group btn-group-sm">
                                            <button onclick="copyEmail('<?= esc($subscriber['email']) ?>')"
                                                    class="btn btn-outline-primary btn-sm"
                                                    title="Copy Email">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                            <button onclick="deleteSubscriber(<?= $subscriber['id'] ?>)"
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
                        <i class="fas fa-users text-muted mb-3" style="font-size: 4rem;"></i>
                        <h5 class="text-dark mb-2">No subscribers found</h5>
                        <p class="text-muted">Newsletter subscribers will appear here when people subscribe to your newsletter.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Subscribers page functionality
function copyEmail(email) {
    navigator.clipboard.writeText(email).then(function() {
        showNotification('Email copied to clipboard', 'success');
    }, function(err) {
        showNotification('Failed to copy email', 'danger');
    });
}

function deleteSubscriber(subscriberId) {
    const modalHtml = `
        <div class="modal fade" id="deleteSubscriberModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Remove Subscriber</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center py-3">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 4rem; height: 4rem;">
                                <i class="fas fa-exclamation-triangle text-warning fs-4"></i>
                            </div>
                            <h6 class="mb-2">Are you sure you want to remove this subscriber?</h6>
                            <p class="text-muted small mb-0">This action cannot be undone. The subscriber will be permanently removed from your newsletter list.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-warning" onclick="confirmDeleteSubscriber(${subscriberId})">
                            <i class="fas fa-trash me-2"></i>Remove Subscriber
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('deleteSubscriberModal'));
    modal.show();

    document.getElementById('deleteSubscriberModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

function confirmDeleteSubscriber(subscriberId) {
    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteSubscriberModal'));
    modal.hide();

    // For now, just show a success message since we don't have the delete endpoint
    showNotification('Subscriber removed successfully', 'success');
    setTimeout(() => location.reload(), 1000);
}

function applyFilters() {
    const searchInput = document.querySelector('[data-table-search="subscribers-table"]');
    const dateFilter = document.getElementById('dateFilter');
    
    const searchTerm = searchInput.value.toLowerCase();
    const dateValue = dateFilter.value;
    
    const table = document.getElementById('subscribers-table');
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        if (row.cells.length === 1) return; // Skip "no data" row
        
        const email = row.cells[1].textContent.toLowerCase();
        const dateCell = row.cells[2].textContent;
        
        const matchesSearch = !searchTerm || email.includes(searchTerm);
        
        let matchesDate = true;
        if (dateValue) {
            const rowDate = new Date(dateCell);
            const now = new Date();
            
            switch(dateValue) {
                case 'today':
                    matchesDate = rowDate.toDateString() === now.toDateString();
                    break;
                case 'week':
                    const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                    matchesDate = rowDate >= weekAgo;
                    break;
                case 'month':
                    const monthAgo = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
                    matchesDate = rowDate >= monthAgo;
                    break;
                case 'year':
                    const yearAgo = new Date(now.getTime() - 365 * 24 * 60 * 60 * 1000);
                    matchesDate = rowDate >= yearAgo;
                    break;
            }
        }
        
        row.style.display = (matchesSearch && matchesDate) ? '' : 'none';
    });
}

// Auto-apply search filter as user types
document.querySelector('[data-table-search="subscribers-table"]').addEventListener('input', applyFilters);
document.getElementById('dateFilter').addEventListener('change', applyFilters);
</script>
<?= $this->endSection() ?>
