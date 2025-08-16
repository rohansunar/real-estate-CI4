<?= $this->extend('layouts/agent_dashboard') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-4">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Sub-Agents Management</h2>
            <p class="text-muted">Manage your team of sub-agents</p>
        </div>
        <div class="mt-3 mt-sm-0">
            <a href="<?= base_url('agent/sub-agents/create') ?>" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>
                Add New Sub-Agent
            </a>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
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

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users fa-2x opacity-75"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fw-bold fs-4"><?= $totalSubAgents ?></div>
                        <div class="small">Total Sub-Agents</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-check fa-2x opacity-75"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fw-bold fs-4"><?= count(array_filter($subAgents, fn($agent) => $agent['is_active'])) ?></div>
                        <div class="small">Active Sub-Agents</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-plus fa-2x opacity-75"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fw-bold fs-4"><?= count(array_filter($subAgents, fn($agent) => strtotime($agent['created_at']) > strtotime('-30 days'))) ?></div>
                        <div class="small">New This Month</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-times fa-2x opacity-75"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fw-bold fs-4"><?= count(array_filter($subAgents, fn($agent) => !$agent['is_active'])) ?></div>
                        <div class="small">Inactive Sub-Agents</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter Section -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="<?= base_url('agent/sub-agents') ?>" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Search Sub-Agents</label>
                <input type="text"
                       class="form-control"
                       id="search"
                       name="search"
                       value="<?= esc($search ?? '') ?>"
                       placeholder="Search by name, email, or phone">
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="active" <?= ($status === 'active') ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= ($status === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="sort" class="form-label">Sort By</label>
                <select class="form-select" id="sort" name="sort">
                    <option value="created_at_desc" <?= ($sort === 'created_at_desc') ? 'selected' : '' ?>>Newest First</option>
                    <option value="created_at_asc" <?= ($sort === 'created_at_asc') ? 'selected' : '' ?>>Oldest First</option>
                    <option value="name_asc" <?= ($sort === 'name_asc') ? 'selected' : '' ?>>Name A-Z</option>
                    <option value="name_desc" <?= ($sort === 'name_desc') ? 'selected' : '' ?>>Name Z-A</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                    <a href="<?= base_url('agent/sub-agents') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Sub-Agents List -->
<div class="card">
    <div class="card-header bg-transparent border-0 pb-0">
        <h5 class="card-title mb-0">
            <i class="fas fa-users me-2 text-success"></i>
            Sub-Agents List
        </h5>
    </div>
    <div class="card-body">
        <?php if (empty($subAgents)): ?>
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Sub-Agents Found</h5>
                <p class="text-muted">You haven't created any sub-agents yet.</p>
                <a href="<?= base_url('agent/sub-agents/create') ?>" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>
                    Create Your First Sub-Agent
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Agent Details</th>
                            <th>Contact Information</th>
                            <th>Agent IDs</th>
                            <th>Status</th>
                            <th>Joined Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subAgents as $subAgent): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="agent-avatar bg-success">
                                                <?= strtoupper(substr($subAgent['name'], 0, 1)) ?>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1"><?= esc($subAgent['name']) ?></h6>
                                            <?php if ($subAgent['qualification']): ?>
                                                <small class="text-muted"><?= esc($subAgent['qualification']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        <div class="mb-1">
                                            <i class="fas fa-envelope me-1 text-muted"></i>
                                            <?= esc($subAgent['email']) ?>
                                        </div>
                                        <div>
                                            <i class="fas fa-phone me-1 text-muted"></i>
                                            <?= esc($subAgent['phone']) ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        <?php if ($subAgent['unique_agent_id']): ?>
                                            <div class="mb-1 font-monospace">
                                                <i class="fas fa-id-card me-1 text-muted"></i>
                                                <?= esc($subAgent['unique_agent_id']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($subAgent['is_active']): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>
                                            Active
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times-circle me-1"></i>
                                            Inactive
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="small">
                                        <?= date('M j, Y', strtotime($subAgent['created_at'])) ?>
                                        <br>
                                        <span class="text-muted"><?= date('g:i A', strtotime($subAgent['created_at'])) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                type="button" 
                                                data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="<?= base_url('agent/sub-agents/edit/' . $subAgent['id']) ?>">
                                                    <i class="fas fa-edit me-2"></i>
                                                    Edit
                                                </a>
                                            </li>
                                            <li>
                                                <button class="dropdown-item" 
                                                        onclick="viewSubAgent(<?= $subAgent['id'] ?>)">
                                                    <i class="fas fa-eye me-2"></i>
                                                    View Details
                                                </button>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button class="dropdown-item text-danger" 
                                                        onclick="deleteSubAgent(<?= $subAgent['id'] ?>, '<?= esc($subAgent['name']) ?>')">
                                                    <i class="fas fa-trash me-2"></i>
                                                    Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($pager->getPageCount() > 1): ?>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing <?= ($pager->getCurrentPage() - 1) * $pager->getPerPage() + 1 ?> to
                            <?= min($pager->getCurrentPage() * $pager->getPerPage(), $totalSubAgents) ?>
                            of <?= $totalSubAgents ?> sub-agents
                        </div>
                        <div>
                            <?= $pager->links('sub_agents', 'bootstrap_pagination') ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                    Confirm Deletion
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the sub-agent <strong id="deleteAgentName"></strong>?</p>
                <p class="text-muted small">This action cannot be undone. All data associated with this sub-agent will be permanently removed.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="post" style="display: inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>
                        Delete Sub-Agent
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Sub-Agent Modal -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user me-2"></i>
                    Sub-Agent Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewModalBody">
                <!-- Content will be loaded via AJAX -->
                <div class="text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Delete sub-agent function
function deleteSubAgent(agentId, agentName) {
    document.getElementById('deleteAgentName').textContent = agentName;
    document.getElementById('deleteForm').action = '<?= base_url('agent/sub-agents/delete/') ?>' + agentId;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// View sub-agent function
function viewSubAgent(agentId) {
    const modal = new bootstrap.Modal(document.getElementById('viewModal'));
    const modalBody = document.getElementById('viewModalBody');
    
    // Show loading spinner
    modalBody.innerHTML = `
        <div class="text-center py-3">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    modal.show();
    
    // Load sub-agent details via AJAX
    fetch('<?= base_url('agent/sub-agents/view/') ?>' + agentId)
        .then(response => response.text())
        .then(html => {
            modalBody.innerHTML = html;
        })
        .catch(error => {
            modalBody.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Failed to load sub-agent details. Please try again.
                </div>
            `;
        });
}
</script>

<?= $this->endSection() ?>
