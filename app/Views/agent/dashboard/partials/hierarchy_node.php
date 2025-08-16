<?php
// Determine context: admin vs agent
$isAdmin = isset($adminContext) && $adminContext === true;
?>
<?php foreach ($nodes as $node): ?>
    <div class="tree-node" data-level="<?= $level ?>">
        <div class="tree-node-content">
            <div class="tree-node-avatar">
                <?= strtoupper(substr($node['name'], 0, 1)) ?>
            </div>
            
            <div class="tree-node-info">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h6 class="mb-0 tree-node-title" role="button" aria-expanded="true"><?= esc($node['name']) ?></h6>
                    <span class="badge bg-light text-secondary border">L<?= $level ?></span>
                    <?php if (!$node['is_active']): ?>
                        <span class="badge bg-danger">Inactive</span>
                    <?php endif; ?>
                </div>
                <?php if ($node['has_children']): ?>
                    <div class="small text-muted mt-1">
                        <i class="fas fa-users me-1"></i>
                        <?= $node['total_downline'] ?> in downline
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="tree-node-actions">
                <button class="btn btn-sm btn-outline-secondary toggle-children" aria-label="Toggle children">
                    <i class="fas fa-chevron-down"></i>
                </button>
                <?php if (!$isAdmin): ?>
                    <a href="<?= base_url('agent/sub-agents/edit/' . $node['id']) ?>" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="<?= base_url('agent/sub-agents/view/' . $node['id']) ?>" class="btn btn-sm btn-outline-primary" onclick="viewSubAgent(<?= $node['id'] ?>); return false;">
                        <i class="fas fa-eye"></i>
                    </a>
                <?php endif; ?>
                <?php if ($isAdmin): ?>
                    <button class="btn btn-sm btn-outline-primary" onclick="viewAgentLogs(<?= $node['id'] ?>)">
                        <i class="fas fa-clipboard-list"></i>
                    </button>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if ($node['has_children'] && !empty($node['children'])): ?>
            <div class="tree-children collapse show">
                <?= view('agent/dashboard/partials/hierarchy_node', ['nodes' => $node['children'], 'level' => $level + 1, 'adminContext' => $isAdmin]) ?>
            </div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
