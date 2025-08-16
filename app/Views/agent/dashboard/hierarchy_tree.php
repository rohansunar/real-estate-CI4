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
                    <!-- <a href="<?= base_url('agent/commissions') ?>" class="btn btn-outline-info">
                        <i class="fas fa-dollar-sign me-2"></i>
                        View Commissions
                    </a> -->
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
        <?= view('agent/dashboard/partials/hierarchy_row.css.php') ?>

    <div class="card-body">
        <?php if (isset($hierarchyTree) && !empty($hierarchyTree)): ?>
            <!-- Level 1 row: render as grid of cards -->
            <div id="levelRows">
              <?= view('agent/dashboard/partials/hierarchy_row', [
                // For the level-based view, use current page's nodes as Level 1
                'agents' => $hierarchyTree,
                'level' => 1,
                'parentId' => (int)($currentAgent['id'] ?? session()->get('agent_id')),
                'pagination' => $pagination ?? null,
              ]) ?>
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
    <?php if (isset($pagination)): ?>
        <nav aria-label="Downline pagination" class="mt-3">
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

<script>
// Level-based interactions for hierarchy
(function(){
  const rowsContainer = document.getElementById('levelRows');

  // Bootstrap 5 tooltips/popovers for hover summaries (desktop) and focus (accessibility)
  async function fetchSummary(agentId) {
    try {
      const controller = new AbortController();
      const timeoutId = setTimeout(() => controller.abort(), 5000); // 5 second timeout

      const res = await fetch('<?= base_url('agent/hierarchy/summary/') ?>'+agentId, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        signal: controller.signal
      });

      clearTimeout(timeoutId);

      if (!res.ok) {
        console.warn('Failed to fetch agent summary:', res.status, res.statusText);
        return '<div class="small text-muted">Unable to load details</div>';
      }

      return await res.text();
    } catch(e) {
      console.error('Error fetching agent summary:', e);
      if (e.name === 'AbortError') {
        return '<div class="small text-muted">Request timed out</div>';
      }
      return '<div class="small text-muted">No details available</div>';
    }
  }

  // Delegate hover/focus to show popover
  rowsContainer?.addEventListener('mouseover', async (e) => {
    const card = e.target.closest('.agent-card');
    if (!card || card.getAttribute('data-popover-loaded')) return;
    const id = card.getAttribute('data-agent-id');
    const content = await fetchSummary(id);

    // Add close button to the content
    const contentWithCloseBtn = `
      <div class="d-flex justify-content-between align-items-start">
        <div class="flex-grow-1">${content}</div>
        <button type="button" class="btn-close btn-close-sm ms-2" aria-label="Close tooltip" onclick="this.closest('.popover').style.display='none'"></button>
      </div>
    `;

    card.setAttribute('data-bs-toggle','popover');
    card.setAttribute('data-bs-html','true');
    card.setAttribute('data-bs-placement','top');
    card.setAttribute('title','Details');
    card.setAttribute('data-bs-content', contentWithCloseBtn);

    const pop = bootstrap.Popover.getOrCreateInstance(card, {
      trigger: 'hover focus',
      delay: { show: 300, hide: 100 },
      customClass: 'hierarchy-popover'
    });

    // Clean up any existing event listeners to prevent memory leaks
    card.removeEventListener('shown.bs.popover', card._popoverShownHandler);

    // Add event listener for when popover is shown
    card._popoverShownHandler = () => {
      const popoverElement = document.querySelector('.popover.show');
      if (popoverElement) {
        const closeBtn = popoverElement.querySelector('.btn-close');
        if (closeBtn && !closeBtn._clickHandlerAdded) {
          closeBtn.addEventListener('click', (event) => {
            event.stopPropagation();
            pop.hide();
          });
          closeBtn._clickHandlerAdded = true;
        }
      }
    };

    card.addEventListener('shown.bs.popover', card._popoverShownHandler);

    // Add cleanup when popover is hidden to prevent memory leaks
    card.addEventListener('hidden.bs.popover', () => {
      const popoverElement = document.querySelector('.popover');
      if (popoverElement) {
        const closeBtn = popoverElement.querySelector('.btn-close');
        if (closeBtn && closeBtn._clickHandlerAdded) {
          closeBtn._clickHandlerAdded = false;
        }
      }
    });

    pop.show();
    card.setAttribute('data-popover-loaded','1');
  });

  // Expand/collapse next level on button click
  rowsContainer?.addEventListener('click', async (e) => {
    const btn = e.target.closest('.expand-btn, .pagination .page-link');
    if (!btn) return;

    // Handle pagination inside a level row
    if (btn.classList.contains('page-link')) {
      e.preventDefault();
      const row = btn.closest('[data-level-row]');
      const parentId = row?.getAttribute('data-parent-id');
      const level = row?.getAttribute('data-level-row');
      const page = btn.getAttribute('data-page') || 1;
      const per = btn.getAttribute('data-per-page') || 12;
      if (!parentId || !level) return;
      row.classList.add('opacity-50');
      try {
        const url = `<?= base_url('agent/hierarchy/children/') ?>${parentId}?level=${level}&page=${page}&perPage=${per}`;
        const html = await (await fetch(url, { headers: { 'X-Requested-With':'XMLHttpRequest' } })).text();
        row.outerHTML = html; // replace current row with paged version
      } finally {
        // no-op; row replaced
      }
      return;
    }

    // Expand to show children row under clicked card
    e.preventDefault();
    const parentId = btn.getAttribute('data-parent-id');
    const nextLevel = btn.getAttribute('data-target-level');
    if (!parentId || !nextLevel) return;

    const levelRow = btn.closest('[data-level-row]');

    // Check if children row already exists for this parent to prevent duplicates
    const existingChildrenRow = levelRow.nextElementSibling;
    if (existingChildrenRow && existingChildrenRow.getAttribute('data-level-row') === nextLevel &&
        existingChildrenRow.getAttribute('data-parent-id') === parentId) {
      // Children row already exists, toggle visibility instead of creating duplicate
      if (existingChildrenRow.style.display === 'none') {
        existingChildrenRow.style.display = '';
        btn.innerHTML = '<i class="fas fa-chevron-up me-1"></i> Close';
        btn.setAttribute('aria-expanded', 'true');
      } else {
        existingChildrenRow.style.display = 'none';
        btn.innerHTML = '<i class="fas fa-chevron-down me-1"></i> Open';
        btn.setAttribute('aria-expanded', 'false');
      }
      return;
    }

    // Disable button to prevent multiple clicks while loading
    btn.disabled = true;
    const originalContent = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Loading...';

    // Show loading placeholder row
    const loadingRow = document.createElement('div');
    loadingRow.className = 'text-center py-3';
    loadingRow.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
    levelRow.after(loadingRow);

    try {
      const url = `<?= base_url('agent/hierarchy/children/') ?>${parentId}?level=${nextLevel}`;
      const html = await (await fetch(url, { headers: { 'X-Requested-With':'XMLHttpRequest' } })).text();
      loadingRow.outerHTML = html; // insert next level row

      // Update button to show "Close" state
      btn.innerHTML = '<i class="fas fa-chevron-up me-1"></i> Close';
      btn.setAttribute('aria-expanded', 'true');
    } catch (err) {
      console.error('Error loading children:', err);
      loadingRow.outerHTML = '<div class="text-center text-danger small py-3">Failed to load. Please try again.</div>';

      // Reset button to original state on error
      btn.innerHTML = originalContent;
      btn.setAttribute('aria-expanded', 'false');
    } finally {
      // Re-enable button
      btn.disabled = false;
    }
  });
})();
</script>

                        <span class="visually-hidden">Next</span>
                    </a>
                </li>
            </ul>
        </nav>
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

/* Enhanced popover styles */
.hierarchy-popover {
    max-width: 300px;
    font-size: 0.875rem;
}

.hierarchy-popover .popover-body {
    padding: 0.75rem;
}

.hierarchy-popover .btn-close {
    font-size: 0.75rem;
    padding: 0.25rem;
    opacity: 0.6;
    transition: opacity 0.2s ease;
}

.hierarchy-popover .btn-close:hover {
    opacity: 1;
}

/* Improve button states for expand/collapse */
.expand-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.expand-btn .fa-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
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
}


/* Simplified interactive tree styles to mirror admin page, mobile-friendly */
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

<script>
// Collapse/expand behavior for hierarchy nodes
(function(){
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
})();
</script>


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
