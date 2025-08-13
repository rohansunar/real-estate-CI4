<?php foreach ($nodes as $node): ?>
    <div class="tree-node" data-level="<?= $level ?>">
        <div class="tree-node-content">
            <div class="tree-node-avatar">
                <?= strtoupper(substr($node['name'], 0, 1)) ?>
            </div>
            
            <div class="tree-node-info">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h6 class="mb-0"><?= esc($node['name']) ?></h6>
                    <span class="level-indicator">Level <?= $level ?></span>
                    <?php if ($node['is_active']): ?>
                        <span class="badge bg-success">Active</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Inactive</span>
                    <?php endif; ?>
                </div>
                
                <div class="small text-muted mb-1">
                    <i class="fas fa-envelope me-1"></i>
                    <?= esc($node['email']) ?>
                    <span class="mx-2">|</span>
                    <i class="fas fa-phone me-1"></i>
                    <?= esc($node['phone']) ?>
                </div>
                
                <div class="small text-muted">
                    <i class="fas fa-id-card me-1"></i>
                    <?= esc($node['unique_agent_id']) ?>
                    <?php if ($node['referral_id']): ?>
                        <span class="mx-2">|</span>
                        <i class="fas fa-link me-1"></i>
                        Ref: <?= esc($node['referral_id']) ?>
                    <?php endif; ?>
                </div>
                
                <?php if ($node['has_children']): ?>
                    <div class="small text-info mt-1">
                        <i class="fas fa-users me-1"></i>
                        <?= $node['total_downline'] ?> agents in downline
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="tree-node-actions">
                <a href="<?= base_url('agent/sub-agents/view/' . $node['id']) ?>" 
                   class="btn btn-sm btn-outline-primary"
                   onclick="viewSubAgent(<?= $node['id'] ?>); return false;">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="<?= base_url('agent/sub-agents/edit/' . $node['id']) ?>" 
                   class="btn btn-sm btn-outline-success">
                    <i class="fas fa-edit"></i>
                </a>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                            type="button" 
                            data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="<?= base_url('agent/sub-agents/edit/' . $node['id']) ?>">
                                <i class="fas fa-edit me-2"></i>
                                Edit Agent
                            </a>
                        </li>
                        <li>
                            <button class="dropdown-item" onclick="viewSubAgent(<?= $node['id'] ?>)">
                                <i class="fas fa-eye me-2"></i>
                                View Details
                            </button>
                        </li>
                        <?php if ($node['has_children']): ?>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('agent/downline?parent=' . $node['id']) ?>">
                                    <i class="fas fa-sitemap me-2"></i>
                                    View Downline
                                </a>
                            </li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <button class="dropdown-item text-danger" 
                                    onclick="deleteSubAgent(<?= $node['id'] ?>, '<?= esc($node['name']) ?>')">
                                <i class="fas fa-trash me-2"></i>
                                Delete Agent
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <?php if ($node['has_children'] && !empty($node['children'])): ?>
            <div class="tree-children">
                <?= $this->include('agent/dashboard/partials/hierarchy_node', ['nodes' => $node['children'], 'level' => $level + 1]) ?>
            </div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
