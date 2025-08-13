<?= $this->extend('layouts/agent_dashboard') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-4">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Agent Hierarchy</h2>
            <p class="text-muted">View your complete organizational structure</p>
        </div>
        <div class="mt-3 mt-sm-0">
            <a href="<?= base_url('agent/sub-agents/create') ?>" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>
                Add Sub-Agent
            </a>
        </div>
    </div>
</div>

<!-- Error Messages -->
<?php if (isset($hasErrors) && $hasErrors && !empty($errorMessages)): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Some data could not be loaded:</strong>
        <ul class="mb-0 mt-2">
            <?php foreach ($errorMessages as $message): ?>
                <li><?= esc($message) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Hierarchy Position Card -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-transparent border-0 pb-0">
                <h5 class="card-title mb-0">
                    <i class="fas fa-sitemap me-2 text-primary"></i>
                    Your Position in Hierarchy
                </h5>
            </div>
            <div class="card-body">
                <?php if ($hierarchyPosition['is_top_level']): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-crown me-2"></i>
                        <strong>Top-Level Agent</strong> - You are at the top of your hierarchy
                    </div>
                <?php else: ?>
                    <div class="mb-3">
                        <h6 class="fw-semibold">Upline Structure:</h6>
                        <div class="hierarchy-path">
                            <?php foreach ($hierarchyPosition['upline'] as $index => $uplineAgent): ?>
                                <span class="hierarchy-item">
                                    <i class="fas fa-user-tie text-primary"></i>
                                    <?= esc($uplineAgent['name']) ?>
                                    <small class="text-muted">(Level <?= $uplineAgent['hierarchy_level'] ?>)</small>
                                </span>
                                <?php if ($index < count($hierarchyPosition['upline']) - 1): ?>
                                    <i class="fas fa-arrow-right text-muted mx-2"></i>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <i class="fas fa-arrow-right text-success mx-2"></i>
                            <span class="hierarchy-item current">
                                <i class="fas fa-user text-success"></i>
                                <strong>You</strong>
                            </span>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="row g-3 mt-3">
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-light rounded">
                            <div class="h4 text-primary mb-1"><?= $hierarchyPosition['hierarchy_level'] ?></div>
                            <div class="small text-muted">Your Level</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-light rounded">
                            <div class="h4 text-success mb-1"><?= $hierarchyPosition['direct_sub_agents'] ?></div>
                            <div class="small text-muted">Direct Sub-Agents</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-light rounded">
                            <div class="h4 text-info mb-1"><?= $hierarchyPosition['total_downline'] ?></div>
                            <div class="small text-muted">Total Downline</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-transparent border-0 pb-0">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2 text-success"></i>
                    Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= base_url('agent/sub-agents/create') ?>" class="btn btn-success">
                        <i class="fas fa-user-plus me-2"></i>
                        Add New Sub-Agent
                    </a>
                    <a href="<?= base_url('agent/downline') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-users me-2"></i>
                        Manage Downline
                    </a>
                    <a href="<?= base_url('agent/commissions') ?>" class="btn btn-outline-info">
                        <i class="fas fa-dollar-sign me-2"></i>
                        View Commissions
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hierarchy Tree -->
<div class="card">
    <div class="card-header bg-transparent border-0 pb-0">
        <h5 class="card-title mb-0">
            <i class="fas fa-project-diagram me-2 text-success"></i>
            Your Downline Structure
            <?php if (isset($hasErrors) && $hasErrors): ?>
                <span class="badge bg-warning ms-2">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Partial Data
                </span>
            <?php endif; ?>
        </h5>
    </div>
    <div class="card-body">
        <?php if (isset($hierarchyTree) && !empty($hierarchyTree)): ?>
            <div class="hierarchy-tree" id="hierarchyTreeContainer">
                <?= $this->include('agent/dashboard/partials/hierarchy_node', ['nodes' => $hierarchyTree, 'level' => 1]) ?>
            </div>
        <?php elseif (isset($hasErrors) && $hasErrors): ?>
            <div class="text-center py-5">
                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                <h5 class="text-warning">Unable to Load Hierarchy Data</h5>
                <p class="text-muted">There was an error loading your hierarchy information. Please try refreshing the page.</p>
                <div class="mt-3">
                    <button onclick="location.reload()" class="btn btn-primary me-2">
                        <i class="fas fa-sync-alt me-2"></i>
                        Refresh Page
                    </button>
                    <a href="<?= base_url('agent/dashboard') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Sub-Agents Yet</h5>
                <p class="text-muted">Start building your team by adding your first sub-agent.</p>
                <a href="<?= base_url('agent/sub-agents/create') ?>" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>
                    Add Your First Sub-Agent
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Hierarchy Tree Styles -->
<style>
.hierarchy-path {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 0.5rem;
    border-left: 4px solid #0d6efd;
}

.hierarchy-item {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem 0.5rem;
    background: white;
    border-radius: 0.25rem;
    border: 1px solid #dee2e6;
}

.hierarchy-item.current {
    background: #d1e7dd;
    border-color: #198754;
}

.hierarchy-tree {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.tree-node {
    margin: 0.5rem 0;
    padding-left: 1.5rem;
    position: relative;
}

.tree-node::before {
    content: '';
    position: absolute;
    left: 0;
    top: 1.2rem;
    width: 1rem;
    height: 1px;
    background: #dee2e6;
}

.tree-node::after {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 1px;
    height: 100%;
    background: #dee2e6;
}

.tree-node:last-child::after {
    height: 1.2rem;
}

.tree-node-content {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    margin-left: 1rem;
    transition: all 0.2s ease;
}

.tree-node-content:hover {
    border-color: #0d6efd;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.tree-node-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #0d6efd;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.1rem;
}

.tree-node-info {
    flex: 1;
}

.tree-node-actions {
    display: flex;
    gap: 0.5rem;
}

.level-indicator {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    background: #e9ecef;
    border-radius: 1rem;
    color: #6c757d;
}

@media (max-width: 768px) {
    .hierarchy-path {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .tree-node {
        padding-left: 1rem;
    }
    
    .tree-node-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .tree-node-actions {
        width: 100%;
        justify-content: center;
    }
}
</style>

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
document.addEventListener('DOMContentLoaded', function() {
    // Add loading states to action buttons
    const actionButtons = document.querySelectorAll('.btn[onclick]');
    actionButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Add loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
            this.disabled = true;

            // Restore button after 3 seconds (fallback)
            setTimeout(() => {
                this.innerHTML = originalText;
                this.disabled = false;
            }, 3000);
        });
    });

    // Add smooth animations to tree nodes
    const treeNodes = document.querySelectorAll('.tree-node-content');
    treeNodes.forEach((node, index) => {
        node.style.opacity = '0';
        node.style.transform = 'translateY(20px)';

        setTimeout(() => {
            node.style.transition = 'all 0.3s ease';
            node.style.opacity = '1';
            node.style.transform = 'translateY(0)';
        }, index * 100);
    });
});

// Delete sub-agent function with enhanced error handling
function deleteSubAgent(agentId, agentName) {
    try {
        document.getElementById('deleteAgentName').textContent = agentName;
        document.getElementById('deleteForm').action = '<?= base_url('agent/sub-agents/delete/') ?>' + agentId;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    } catch (error) {
        console.error('Error showing delete modal:', error);
        alert('Unable to show delete confirmation. Please refresh the page and try again.');
    }
}

// View sub-agent function with enhanced error handling
function viewSubAgent(agentId) {
    try {
        const modal = new bootstrap.Modal(document.getElementById('viewModal'));
        const modalBody = document.getElementById('viewModalBody');

        // Show loading spinner
        modalBody.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Loading agent details...</p>
            </div>
        `;

        modal.show();

        // Load sub-agent details via AJAX with timeout
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 second timeout

        fetch('<?= base_url('agent/sub-agents/view/') ?>' + agentId, {
            signal: controller.signal
        })
        .then(response => {
            clearTimeout(timeoutId);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(html => {
            modalBody.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading agent details:', error);
            let errorMessage = 'Failed to load sub-agent details. Please try again.';

            if (error.name === 'AbortError') {
                errorMessage = 'Request timed out. Please check your connection and try again.';
            }

            modalBody.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Error:</strong> ${errorMessage}
                    <div class="mt-3">
                        <button class="btn btn-primary btn-sm" onclick="viewSubAgent(${agentId})">
                            <i class="fas fa-sync-alt me-2"></i>
                            Retry
                        </button>
                        <button class="btn btn-secondary btn-sm ms-2" data-bs-dismiss="modal">
                            Close
                        </button>
                    </div>
                </div>
            `;
        });
    } catch (error) {
        console.error('Error showing view modal:', error);
        alert('Unable to show agent details. Please refresh the page and try again.');
    }
}

// Add keyboard navigation support
document.addEventListener('keydown', function(e) {
    // Close modals with Escape key
    if (e.key === 'Escape') {
        const openModals = document.querySelectorAll('.modal.show');
        openModals.forEach(modal => {
            const modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) {
                modalInstance.hide();
            }
        });
    }
});
</script>

<?= $this->endSection() ?>
