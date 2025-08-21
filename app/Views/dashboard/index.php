<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-4">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Dashboard Overview</h2>
            <p class="text-muted">Welcome back! Here's what's happening with your real estate business.</p>
        </div>
        <div class="mt-3 mt-sm-0">
            <a href="<?= base_url('properties/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Add New Property
            </a>
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="row g-4 mb-4">
    <!-- Total Properties -->
    <div class="col-md-6 col-lg-3">
        <div class="card card-stats border-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="icon-stat bg-primary-light">
                            <i class="fas fa-home"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted mb-1 small fw-medium">Total Properties</p>
                        <p class="h3 fw-bold mb-0"><?= number_format($totalProperties) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Contacts -->
    <div class="col-md-6 col-lg-3">
        <div class="card card-stats border-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="icon-stat bg-success-light">
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted mb-1 small fw-medium">Total Enquiries</p>
                        <p class="h3 fw-bold mb-0"><?= number_format($totalContacts) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Unread Contacts -->
    <div class="col-md-6 col-lg-3">
        <div class="card card-stats border-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="icon-stat bg-warning-light">
                            <i class="fas fa-bell"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted mb-1 small fw-medium">Unread Messages</p>
                        <p class="h3 fw-bold mb-0"><?= number_format($unreadContacts) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Newsletter Subscribers -->
    <div class="col-md-6 col-lg-3">
        <div class="card card-stats border-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="icon-stat bg-info-light">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted mb-1 small fw-medium">Subscribers</p>
                        <p class="h3 fw-bold mb-0"><?= number_format($totalSubscribers) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Agent Management Widget -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-success agent-management-widget">
            <div class="card-header bg-success-light border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-1 text-success">
                            <i class="fas fa-user-tie me-2"></i>
                            Agent Management
                        </h5>
                        <p class="card-text text-muted small mb-0">Manage your real estate agents</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <!-- Agent Statistics -->
                    <div class="col-lg-8">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="h3 fw-bold text-success mb-1"><?= number_format($totalAgents) ?></div>
                                    <div class="small text-muted">Total Agents</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="h3 fw-bold text-primary mb-1"><?= number_format($activeAgents) ?></div>
                                    <div class="small text-muted">Active Agents</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="h3 fw-bold text-warning mb-1"><?= number_format($agentStats['inactive']) ?></div>
                                    <div class="small text-muted">Inactive Agents</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="h3 fw-bold text-info mb-1"><?= number_format($recentAgents) ?></div>
                                    <div class="small text-muted">New This Week</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="col-lg-4">
                        <div class="d-flex flex-column gap-2">
                            <a href="<?= base_url('dashboard/agents') ?>" class="btn btn-outline-success">
                                <i class="fas fa-list me-2"></i>
                                View All Agents
                            </a>
                            <a href="<?= base_url('dashboard/agents/create') ?>" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>
                                Add New Agent
                            </a>
                            <!-- <a href="<?= base_url('agent/login') ?>" class="btn btn-outline-primary" target="_blank">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Agent Login Portal
                            </a> -->
                        </div>
                    </div>
                </div>

                <!-- Recent Agents -->
                <!-- <?php if (!empty($recentAgentsList)): ?>
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="fw-semibold mb-3">Recent Agents</h6>
                        <div class="row g-2">
                            <?php foreach ($recentAgentsList as $agent): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="d-flex align-items-center p-2 bg-light rounded">
                                        <div class="flex-shrink-0">
                                            <div class="agent-avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                <?= strtoupper(substr($agent['name'], 0, 1)) ?>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <div class="fw-medium small"><?= esc($agent['name']) ?></div>
                                            <div class="text-muted" style="font-size: 0.75rem;"><?= esc($agent['unique_agent_id']) ?></div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <?php if ($agent['is_active']): ?>
                                                <span class="badge bg-success" style="font-size: 0.6rem;">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary" style="font-size: 0.6rem;">Inactive</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?> -->
            </div>
        </div>
    </div>
</div>

<!-- Content Grid -->
<div class="row g-4">
    <!-- Recent Properties -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title mb-1">Recent Properties</h5>
                    <p class="card-text text-muted small mb-0">Latest property listings</p>
                </div>
                <a href="<?= base_url('/dashboard/properties') ?>" class="btn btn-outline-primary btn-sm">
                    View All
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($recentProperties)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Property</th>
                                    <th>Type</th>
                                    <th>Location</th>
                                    <th>Date Added</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentProperties as $property): ?>
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
                                                    <small class="text-muted"><?= esc(substr($property['description'], 0, 50)) ?>...</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-property-<?= strtolower($property['type']) ?>">
                                                <?= esc(ucfirst($property['type'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                            <?= esc($property['location']) ?>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('M j, Y', strtotime($property['created_at'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-ghost" data-property-quick-view data-property-id="<?= $property['id'] ?>" title="Quick View">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <a href="<?= base_url('/dashboard/properties/edit/' . $property['id']) ?>" class="btn btn-ghost" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-home text-muted mb-3" style="font-size: 3rem;"></i>
                        <h6 class="text-muted">No properties found</h6>
                        <p class="text-muted small">Start by adding your first property listing.</p>
                        <a href="<?= base_url('properties/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-2"></i>Add Property
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Enquiries -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title mb-1">Recent Enquiries</h5>
                    <p class="card-text text-muted small mb-0">Latest customer messages</p>
                </div>
                <a href="<?= base_url('dashboard/enquiries') ?>" class="btn btn-outline-primary btn-sm">
                    View All
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($recentContacts)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentContacts as $contact): ?>
                            <div class="list-group-item <?= !$contact['is_read'] ? 'bg-light' : '' ?>">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 2.5rem; height: 2.5rem;">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h6 class="mb-1 fw-medium"><?= esc($contact['name']) ?></h6>
                                            <?php if (!$contact['is_read']): ?>
                                                <span class="badge bg-warning text-dark">New</span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="mb-1 small text-muted text-truncate-2">
                                            <?= esc($contact['message']) ?>
                                        </p>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            <?= date('M j, g:i A', strtotime($contact['created_at'])) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-envelope text-muted mb-3" style="font-size: 3rem;"></i>
                        <h6 class="text-muted">No enquiries yet</h6>
                        <p class="text-muted small">Customer enquiries will appear here.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-4 mt-2">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="<?= base_url('properties/create') ?>" class="btn btn-outline-primary w-100">
                            <i class="fas fa-plus mb-2 d-block"></i>
                            Add Property
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="<?= base_url('dashboard/enquiries') ?>" class="btn btn-outline-success w-100">
                            <i class="fas fa-envelope mb-2 d-block"></i>
                            View Enquiries
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="<?= base_url('dashboard/users') ?>" class="btn btn-outline-info w-100">
                            <i class="fas fa-users mb-2 d-block"></i>
                            Manage Users
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="<?= base_url('dashboard/settings') ?>" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-cog mb-2 d-block"></i>
                            Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
/* Agent Management Widget Styles */
.bg-success-light {
    background-color: rgba(25, 135, 84, 0.1) !important;
}

.agent-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.8rem;
}

.card.border-success {
    border-color: rgba(25, 135, 84, 0.3) !important;
}

.card.border-success .card-header {
    border-bottom-color: rgba(25, 135, 84, 0.2) !important;
}

/* Hover effects for agent cards */
.agent-management-widget .btn {
    transition: all 0.3s ease;
}

.agent-management-widget .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Statistics cards hover effect */
.agent-management-widget .bg-light.rounded {
    transition: all 0.3s ease;
    cursor: pointer;
}

.agent-management-widget .bg-light.rounded:hover {
    background-color: #e9ecef !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Recent agents list styling */
.agent-management-widget .d-flex.align-items-center.p-2 {
    transition: all 0.3s ease;
}

.agent-management-widget .d-flex.align-items-center.p-2:hover {
    background-color: #e9ecef !important;
    transform: translateX(5px);
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Dashboard specific functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize any dashboard-specific features
    console.log('Dashboard loaded');

    // Add click handlers for agent statistics
    document.querySelectorAll('.agent-management-widget .bg-light.rounded').forEach(function(card) {
        card.addEventListener('click', function() {
            // Navigate to agents page when clicking on statistics
            window.location.href = '<?= base_url('dashboard/agents') ?>';
        });
    });
});
</script>
<?= $this->endSection() ?>
