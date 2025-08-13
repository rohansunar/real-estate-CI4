<?= $this->extend('layouts/agent_dashboard') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-4">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Welcome back, <?= esc($agent['name']) ?>!</h2>
            <p class="text-muted">Here's your agent dashboard overview</p>
        </div>
        <div class="mt-3 mt-sm-0">
            <a href="<?= base_url('agent/sub-agents/create') ?>" class="btn btn-agent-primary">
                <i class="fas fa-plus me-2"></i>
                Add Sub-Agent
            </a>
        </div>
    </div>
</div>

<!-- Agent Info Cards -->
<div class="row g-4 mb-4">
    <!-- Agent Details Card -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header bg-transparent border-0 pb-0">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-tie me-2 text-success"></i>
                    Agent Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="agent-avatar bg-success">
                                    <?= strtoupper(substr($agent['name'], 0, 1)) ?>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1"><?= esc($agent['name']) ?></h6>
                                <small class="text-muted"><?= esc($agent['email']) ?></small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Unique Agent ID</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="<?= esc($agent['unique_agent_id']) ?>" readonly>
                                <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('<?= esc($agent['unique_agent_id']) ?>')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        
                        <?php if ($agent['referral_id']): ?>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Referral ID</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="<?= esc($agent['referral_id']) ?>" readonly>
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('<?= esc($agent['referral_id']) ?>')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Phone</label>
                            <p class="mb-0"><?= esc($agent['phone']) ?></p>
                        </div>
                        
                        <?php if ($agent['address']): ?>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Address</label>
                                <p class="mb-0"><?= esc($agent['address']) ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($agent['qualification']): ?>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Qualification</label>
                                <p class="mb-0"><?= esc($agent['qualification']) ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Member Since</label>
                            <p class="mb-0"><?= date('F j, Y', strtotime($agent['created_at'])) ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 pt-3 border-top">
                    <a href="<?= base_url('agent/profile') ?>" class="btn btn-outline-success">
                        <i class="fas fa-edit me-2"></i>
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistics Card -->
    <div class="col-lg-4">
        <div class="card stat-card h-100">
            <div class="card-body text-center">
                <div class="stat-number"><?= $subAgentCount ?></div>
                <div class="stat-label">Sub-Agents</div>
                <div class="mt-3">
                    <i class="fas fa-users fa-3x opacity-50"></i>
                </div>
                <div class="mt-3">
                    <a href="<?= base_url('agent/sub-agents') ?>" class="btn btn-light btn-sm">
                        <i class="fas fa-eye me-2"></i>
                        View All
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sub-Agents Section -->
<?php if (!empty($subAgents)): ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-transparent border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-users me-2 text-success"></i>
                            Recent Sub-Agents
                        </h5>
                        <a href="<?= base_url('agent/sub-agents') ?>" class="btn btn-sm btn-outline-success">
                            View All
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Unique ID</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($subAgents, 0, 5) as $subAgent): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="agent-avatar bg-secondary me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                    <?= strtoupper(substr($subAgent['name'], 0, 1)) ?>
                                                </div>
                                                <?= esc($subAgent['name']) ?>
                                            </div>
                                        </td>
                                        <td><?= esc($subAgent['email']) ?></td>
                                        <td>
                                            <code><?= esc($subAgent['unique_agent_id']) ?></code>
                                        </td>
                                        <td>
                                            <?php if ($subAgent['is_active']): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('M j, Y', strtotime($subAgent['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- No Sub-Agents -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-users display-1 text-muted"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-3">No Sub-Agents Yet</h4>
                    <p class="text-muted mb-4 mx-auto" style="max-width: 400px;">
                        Start building your team by adding sub-agents. They'll be able to access their own dashboard and help grow your business.
                    </p>
                    <a href="<?= base_url('agent/sub-agents/create') ?>" class="btn btn-agent-primary">
                        <i class="fas fa-plus me-2"></i>
                        Add Your First Sub-Agent
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Copy to clipboard functionality
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('Copied to clipboard!', 'success');
        }).catch(err => {
            console.error('Failed to copy: ', err);
            fallbackCopyTextToClipboard(text);
        });
    } else {
        fallbackCopyTextToClipboard(text);
    }
}

function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showToast('Copied to clipboard!', 'success');
        } else {
            showToast('Failed to copy', 'error');
        }
    } catch (err) {
        console.error('Fallback: Oops, unable to copy', err);
        showToast('Failed to copy', 'error');
    }
    
    document.body.removeChild(textArea);
}

function showToast(message, type = 'info') {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle me-2"></i>${message}`;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
</script>
<?= $this->endSection() ?>
