<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-4">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Agents Management</h2>
            <p class="text-muted">Manage your real estate agents</p>
        </div>
        <div class="mt-3 mt-sm-0">
            <a href="<?= base_url('dashboard/agents/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Add New Agent
            </a>
        </div>
    </div>
</div>

<!-- Alert Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card card-stats border-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="icon-stat bg-primary-light">
                            <i class="fas fa-user-tie"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted mb-1 small fw-medium">Total Agents</p>
                        <p class="h3 fw-bold mb-0"><?= number_format($statistics['total'] ?? 0) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="col-md-3">
        <div class="card card-stats border-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="icon-stat bg-info-light">
                            <i class="fas fa-calendar-week"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted mb-1 small fw-medium">This Week</p>
                        <p class="h3 fw-bold mb-0"><?= number_format($statistics['recent'] ?? 0) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Agents Table -->
<div class="card">
    <div class="card-header border-bottom">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="card-title mb-1">Real Estate Agents</h5>
                <p class="card-text text-muted small mb-0">All registered agents and their information</p>
            </div>
            <div class="text-muted small" data-table-info="agents-table">
                Showing 1-<?= count($agents) ?> of <?= count($agents) ?> results
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="agents-table" data-table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="sortable" data-sort="0">
                            Agent
                            <i class="fas fa-sort sort-indicator ms-1"></i>
                        </th>
                        <th class="sortable" data-sort="1">
                            Contact Info
                            <i class="fas fa-sort sort-indicator ms-1"></i>
                        </th>
                        <th class="sortable" data-sort="2">
                            Qualification
                            <i class="fas fa-sort sort-indicator ms-1"></i>
                        </th>
                        <th class="sortable" data-sort="3">
                            Joined Date
                            <i class="fas fa-sort sort-indicator ms-1"></i>
                        </th>
                        <th class="text-end">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($agents)): ?>
                        <?php foreach ($agents as $agent): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <?php if ($agent['profile_image']): ?>
                                                <img src="<?= base_url($agent['profile_image']) ?>" 
                                                     alt="<?= esc($agent['name']) ?>" 
                                                     class="rounded-circle" 
                                                     style="width: 2.5rem; height: 2.5rem; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 2.5rem; height: 2.5rem;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ms-3">
                                            <div class="fw-medium"><?= esc($agent['name']) ?></div>
                                            <div class="text-muted small">ID: #<?= $agent['id'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-muted small">
                                        <div><i class="fas fa-envelope me-1"></i><?= esc($agent['email']) ?></div>
                                        <div><i class="fas fa-phone me-1"></i><?= esc($agent['phone']) ?></div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($agent['qualification']): ?>
                                        <span class="badge bg-secondary"><?= esc($agent['qualification']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">Not specified</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= date('M j, Y', strtotime($agent['created_at'])) ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('dashboard/agents/edit/' . $agent['id']) ?>"
                                               class="btn btn-ghost"
                                               title="Edit Agent">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="deleteAgent(<?= $agent['id'] ?>)" 
                                                    class="btn btn-ghost text-danger"
                                                    title="Delete Agent">
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
                                    <i class="fas fa-user-tie text-muted mb-3" style="font-size: 4rem;"></i>
                                    <h5 class="text-dark mb-2">No agents found</h5>
                                    <p class="text-muted">Start by adding your first real estate agent.</p>
                                    <a href="<?= base_url('dashboard/agents/create') ?>" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>
                                        Add First Agent
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<?php if (isset($totalPages) && $totalPages > 1): ?>
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted">
            Showing <?= ($currentPage - 1) * $perPage + 1 ?> to <?= min($currentPage * $perPage, $totalAgents) ?> of <?= $totalAgents ?> agents
        </div>
        <nav aria-label="Agents pagination">
            <ul class="pagination mb-0">
                <!-- Previous Page -->
                <?php if ($currentPage > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= base_url('dashboard/agents?page=' . ($currentPage - 1)) ?>">
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
                        <a class="page-link" href="<?= base_url('dashboard/agents?page=1') ?>">1</a>
                    </li>
                    <?php if ($startPage > 2): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                        <a class="page-link" href="<?= base_url('dashboard/agents?page=' . $i) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= base_url('dashboard/agents?page=' . $totalPages) ?>"><?= $totalPages ?></a>
                    </li>
                <?php endif; ?>

                <!-- Next Page -->
                <?php if ($currentPage < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= base_url('dashboard/agents?page=' . ($currentPage + 1)) ?>">
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
// Agents page functionality


function deleteAgent(agentId) {
    const modalHtml = `
        <div class="modal fade" id="deleteAgentModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Agent</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center py-3">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 4rem; height: 4rem;">
                                <i class="fas fa-exclamation-triangle text-danger fs-4"></i>
                            </div>
                            <h6 class="mb-2">Are you sure you want to delete this agent?</h6>
                            <p class="text-muted small mb-0">This action cannot be undone. All agent data will be permanently removed.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" onclick="confirmDeleteAgent(${agentId})">
                            <i class="fas fa-trash me-2"></i>Delete Agent
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('deleteAgentModal'));
    modal.show();

    document.getElementById('deleteAgentModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

function confirmDeleteAgent(agentId) {
    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteAgentModal'));
    modal.hide();

    fetch(`<?= base_url('dashboard/agents/') ?>${agentId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error deleting agent', 'danger');
    });
}

// Search functionality removed as per requirements
</script>
<?= $this->endSection() ?>
