<?= $this->extend('layouts/agent_dashboard') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-4">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Downline Management</h2>
            <p class="text-muted">Manage all agents in your hierarchy</p>
        </div>
        <div class="mt-3 mt-sm-0">
            <a href="<?= base_url('agent/sub-agents/create') ?>" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>
                Add Sub-Agent
            </a>
        </div>
    </div>
</div>

<!-- Hierarchy Overview -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <div class="h3 mb-1"><?= $hierarchyPosition['hierarchy_level'] ?></div>
                <div class="small">Your Level</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <div class="h3 mb-1"><?= $hierarchyPosition['direct_sub_agents'] ?></div>
                <div class="small">Direct Sub-Agents</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <div class="h3 mb-1"><?= count($allSubAgents) ?></div>
                <div class="small">Total Downline</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <div class="h3 mb-1"><?= count(array_filter($allSubAgents, fn($agent) => $agent['is_active'])) ?></div>
                <div class="small">Active Agents</div>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="<?= base_url('agent/downline') ?>" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Search Agents</label>
                <input type="text" 
                       class="form-control" 
                       id="search" 
                       name="search" 
                       value="<?= esc($search ?? '') ?>" 
                       placeholder="Search by name, email, or phone">
            </div>
            <div class="col-md-3">
                <label for="level" class="form-label">Hierarchy Level</label>
                <select class="form-select" id="level" name="level">
                    <option value="">All Levels</option>
                    <?php for ($i = 1; $i <= $maxLevel; $i++): ?>
                        <option value="<?= $i ?>" <?= ($level == $i) ? 'selected' : '' ?>>
                            Level <?= $i ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="active" <?= ($status === 'active') ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= ($status === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                    <a href="<?= base_url('agent/downline') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Downline Agents List -->
<div class="card">
    <div class="card-header bg-transparent border-0 pb-0">
        <h5 class="card-title mb-0">
            <i class="fas fa-users me-2 text-success"></i>
            Downline Agents (<?= count($allSubAgents) ?> total)
        </h5>
    </div>
    <div class="card-body">
        <?php if (empty($allSubAgents)): ?>
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Agents in Downline</h5>
                <p class="text-muted">Start building your team by adding sub-agents.</p>
                <a href="<?= base_url('agent/sub-agents/create') ?>" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>
                    Add Your First Sub-Agent
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Agent Details</th>
                            <th>Contact Information</th>
                            <th>Hierarchy Level</th>
                            <th>Agent IDs</th>
                            <th>Status</th>
                            <th>Joined Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allSubAgents as $agent): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="agent-avatar bg-<?= $agent['is_active'] ? 'success' : 'secondary' ?>">
                                                <?= strtoupper(substr($agent['name'], 0, 1)) ?>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1"><?= esc($agent['name']) ?></h6>
                                            <?php if ($agent['qualification']): ?>
                                                <small class="text-muted"><?= esc($agent['qualification']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        <div class="mb-1">
                                            <i class="fas fa-envelope me-1 text-muted"></i>
                                            <?= esc($agent['email']) ?>
                                        </div>
                                        <div>
                                            <i class="fas fa-phone me-1 text-muted"></i>
                                            <?= esc($agent['phone']) ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary fs-6">
                                        Level <?= $agent['hierarchy_depth'] ?>
                                    </span>
                                    <div class="small text-muted mt-1">
                                        <?= str_repeat('└─ ', $agent['hierarchy_depth'] - 1) ?>
                                        <?php if ($agent['hierarchy_depth'] > 1): ?>
                                            Sub-level <?= $agent['hierarchy_depth'] ?>
                                        <?php else: ?>
                                            Direct Sub-Agent
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        <?php if ($agent['unique_agent_id']): ?>
                                            <div class="mb-1 font-monospace">
                                                <i class="fas fa-id-card me-1 text-muted"></i>
                                                <?= esc($agent['unique_agent_id']) ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($agent['referral_id']): ?>
                                            <div class="font-monospace">
                                                <i class="fas fa-link me-1 text-muted"></i>
                                                <?= esc($agent['referral_id']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($agent['is_active']): ?>
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
                                        <?= date('M j, Y', strtotime($agent['created_at'])) ?>
                                        <br>
                                        <span class="text-muted"><?= date('g:i A', strtotime($agent['created_at'])) ?></span>
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
                                                <button class="dropdown-item" onclick="viewSubAgent(<?= $agent['id'] ?>)">
                                                    <i class="fas fa-eye me-2"></i>
                                                    View Details
                                                </button>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="<?= base_url('agent/sub-agents/edit/' . $agent['id']) ?>">
                                                    <i class="fas fa-edit me-2"></i>
                                                    Edit Agent
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button class="dropdown-item text-danger" 
                                                        onclick="deleteSubAgent(<?= $agent['id'] ?>, '<?= esc($agent['name']) ?>')">
                                                    <i class="fas fa-trash me-2"></i>
                                                    Delete Agent
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
        <?php endif; ?>
    </div>
</div>

<!-- Include modals from sub_agents.php -->
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
                <p>Are you sure you want to delete the agent <strong id="deleteAgentName"></strong>?</p>
                <p class="text-muted small">This action cannot be undone. All data associated with this agent will be permanently removed.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="post" style="display: inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>
                        Delete Agent
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user me-2"></i>
                    Agent Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewModalBody">
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
// Delete agent function
function deleteSubAgent(agentId, agentName) {
    document.getElementById('deleteAgentName').textContent = agentName;
    document.getElementById('deleteForm').action = '<?= base_url('agent/sub-agents/delete/') ?>' + agentId;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// View agent function
function viewSubAgent(agentId) {
    const modal = new bootstrap.Modal(document.getElementById('viewModal'));
    const modalBody = document.getElementById('viewModalBody');
    
    modalBody.innerHTML = `
        <div class="text-center py-3">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    modal.show();
    
    fetch('<?= base_url('agent/sub-agents/view/') ?>' + agentId)
        .then(response => response.text())
        .then(html => {
            modalBody.innerHTML = html;
        })
        .catch(error => {
            modalBody.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Failed to load agent details. Please try again.
                </div>
            `;
        });
}
</script>

<?= $this->endSection() ?>
