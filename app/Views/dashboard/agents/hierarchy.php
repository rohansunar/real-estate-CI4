<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Agents Hierarchy</h2>
            <p class="text-muted">View all agents and their sub-agents in a unified tree</p>
        </div>
        <div class="mt-3 mt-sm-0">
            <a href="<?= base_url('dashboard/agents') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Agents
            </a>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="p-3 bg-light rounded">
                    <div class="h4 text-primary mb-1"><?= esc($stats['total'] ?? 0) ?></div>
                    <div class="small text-muted">Total Agents</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-light rounded">
                    <div class="h4 text-success mb-1"><?= esc($stats['active'] ?? 0) ?></div>
                    <div class="small text-muted">Active</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-light rounded">
                    <div class="h4 text-secondary mb-1"><?= esc($stats['inactive'] ?? 0) ?></div>
                    <div class="small text-muted">Inactive</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-transparent border-0 pb-0">
        <h5 class="card-title mb-0">
            <i class="fas fa-sitemap me-2 text-primary"></i>
            Organization Tree
        </h5>
    </div>
    <div class="card-body">
        <?php if (!empty($tree)): ?>
            <div class="hierarchy-tree" id="adminHierarchyTree">
                <?= view('agent/dashboard/partials/hierarchy_node', ['nodes' => $tree, 'level' => 1, 'adminContext' => true]) ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No agents found</h5>
                <p class="text-muted">Create agents to populate the organization tree.</p>
            </div>
        <?php endif; ?>
    </div>
    <?php if (isset($pagination)): ?>
        <nav aria-label="Hierarchy pagination" class="mt-3">
            <ul class="pagination pagination-sm justify-content-center">
                <?php $current = (int) ($pagination['page'] ?? 1); $totalPages = (int) ($pagination['totalPages'] ?? 1); ?>
                <li class="page-item <?= $current <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= esc($pagination['baseUrl']) ?>?page=<?= max(1, $current-1) ?>&perPage=<?= (int)$pagination['perPage'] ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                        <span class="visually-hidden">Previous</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $current ? 'active' : '' ?>">
                        <a class="page-link" href="<?= esc($pagination['baseUrl']) ?>?page=<?= $i ?>&perPage=<?= (int)$pagination['perPage'] ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $current >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= esc($pagination['baseUrl']) ?>?page=<?= min($totalPages, $current+1) ?>&perPage=<?= (int)$pagination['perPage'] ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                        <span class="visually-hidden">Next</span>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<style>
/* Clean, interactive tree lines */
.hierarchy-tree { position: relative; padding-left: 0.5rem; }
.hierarchy-tree .tree-node { position: relative; margin: 0.25rem 0 0.5rem 0.5rem; }
.hierarchy-tree .tree-node::before { content: ''; position: absolute; left: -0.5rem; top: 0.9rem; width: 0.5rem; height: 1px; background: #e5e7eb; }
.hierarchy-tree .tree-children { border-left: 1px solid #e5e7eb; margin-left: 1.1rem; padding-left: 0.75rem; }
.hierarchy-tree .tree-node-content { display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.5rem; border-radius: 8px; transition: background-color .2s ease; }
.hierarchy-tree .tree-node-content:hover { background-color: #f8fafc; }
.hierarchy-tree .tree-node-avatar { width: 28px; height: 28px; border-radius: 50%; background: #e0ecff; color: #1d4ed8; display: flex; align-items: center; justify-content: center; font-weight: 600; }
.hierarchy-tree .tree-node-title { cursor: pointer; user-select: none; }
.hierarchy-tree .toggle-children { padding: 0.1rem 0.35rem; }
@media (max-width: 576px) {
  .hierarchy-tree .tree-node-content { padding: 0.35rem 0.5rem; }
}
</style>

<!-- Logs Modal -->
<div class="modal fade" id="logsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-clipboard-list me-2"></i>Agent Logs</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="logsModalBody">
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
            <p class="mt-2 text-muted">Loading logs...</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
async function viewAgentLogs(agentId) {
    try {
        const modal = new bootstrap.Modal(document.getElementById('logsModal'));
        const body = document.getElementById('logsModalBody');
        body.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
            <p class="mt-2 text-muted">Loading logs...</p>
        </div>`;
        modal.show();
        const res = await fetch('<?= base_url('dashboard/agents/logs/') ?>' + agentId, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});

    // Collapse/expand behavior for admin tree
    document.addEventListener('click', function(e){
        const btn = e.target.closest('.toggle-children');
        if (btn) {
            const node = btn.closest('.tree-node');
            const children = node?.querySelector(':scope > .tree-children');
            if (children) {
                children.classList.toggle('show');
                children.classList.toggle('collapse');
                const icon = btn.querySelector('i');
                if (icon) icon.classList.toggle('fa-chevron-right');
                if (icon) icon.classList.toggle('fa-chevron-down');
                // Accessibility: reflect expanded state
                const expanded = btn.getAttribute('aria-expanded') === 'true';
                btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            }
        }
    });

        if (!res.ok) throw new Error('Failed to load logs');
        const html = await res.text();
        body.innerHTML = html;
    } catch (e) {
        document.getElementById('logsModalBody').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>Unable to load logs. Please try again.
            </div>`;
        console.error(e);
    }
}
</script>
<?= $this->endSection() ?>

