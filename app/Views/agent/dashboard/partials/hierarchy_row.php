<?php
/**
 * Enhanced Agent Hierarchy Row Partial with Parent Agent Display
 *
 * Renders a single horizontal row of agent cards for a given level with progressive disclosure UI
 * and enhanced parent agent information display. This partial implements the enhanced hierarchy
 * tree with the following features:
 *
 * CORE FUNCTIONALITY:
 * - Level 1-2: Click-to-expand functionality with smooth animations
 * - Level 3+: Hover-only information display with Bootstrap 5 popovers
 * - Single-expansion accordion behavior (only one agent expanded at a time)
 * - Parent agent information display with fallback to "Primary Agent" label
 * - Mobile-first responsive design with WCAG 2.1 AA accessibility compliance
 * - Server-side pagination support for performance optimization
 *
 * PARENT AGENT DISPLAY LOGIC:
 * - Shows parent agent name and ID prominently when parent-child relationship exists
 * - Displays "Primary Agent" label with crown icon for root level agents (no parent)
 * - Uses visual hierarchy with color-coded badges to distinguish parent vs primary agents
 * - Maintains clear visual distinction between agents with parents and root level agents
 *
 * VISUAL ENHANCEMENTS:
 * - Enhanced visual design with modern Bootstrap 5 components
 * - Color-coded card borders (blue for agents with parents, green for primary agents)
 * - Gradient backgrounds for parent/primary agent information sections
 * - Hover effects and smooth transitions for better user experience
 * - Responsive typography and spacing for mobile devices
 *
 * ACCESSIBILITY FEATURES:
 * - Proper ARIA labels and descriptions for screen readers
 * - Keyboard navigation support with focus management
 * - High contrast mode support for visually impaired users
 * - Semantic HTML structure for better accessibility
 *
 * @param array $agents Array of agent data for this level, each containing:
 *                      - id: Agent ID
 *                      - name: Agent name
 *                      - unique_agent_id: Unique agent identifier
 *                      - parent_agent: Parent agent data (if exists)
 *                      - is_root_level: Boolean indicating if agent is root level
 *                      - has_children: Boolean indicating if agent has sub-agents
 *                      - total_downline: Total number of sub-agents
 *                      - hierarchy_depth: Current depth in hierarchy
 * @param int $level Current hierarchy level (1-based)
 * @param int $parentId Parent agent ID for this level
 * @param array $pagination Pagination data (items, total, perPage, page, etc.)
 *
 * @author Real Estate Team
 * @version 3.0 - Enhanced with parent agent display and single-expansion accordion
 * @since 2025-08-16
 */
?>
<div class="level-row py-3" data-level-row="<?= (int)$level ?>" data-parent-id="<?= (int)$parentId ?>">
  <div class="row g-3 justify-content-center">
    <?php if (empty($agents)): ?>
      <div class="col-12 text-center text-muted py-4">
        <i class="fas fa-users fa-2x mb-2 opacity-50"></i>
        <div class="small">No agents at this level.</div>
      </div>
    <?php else: ?>
      <?php foreach ($agents as $agent): ?>
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
          <div class="card h-100 shadow-sm border-0 hover-lift agent-card position-relative <?= !empty($agent['parent_agent']) && !$agent['is_root_level'] ? 'has-parent' : 'is-primary' ?>" tabindex="0"
               data-agent-id="<?= (int)$agent['id'] ?>"
               data-level="<?= (int)$level ?>"
               data-has-children="<?= !empty($agent['has_children']) ? 'true' : 'false' ?>"
               data-total-downline="<?= (int)($agent['total_downline'] ?? 0) ?>"
               aria-label="Agent <?= esc($agent['name']) ?> at Level <?= (int)$level ?>, <?= (int)($agent['total_downline'] ?? 0) ?> sub-agents">

            <!-- Level indicator badge -->
            <div class="position-absolute top-0 start-0 m-2">
              <span class="badge bg-light text-secondary border small">L<?= (int)$level ?></span>
            </div>

            <!-- Sub-agent count indicator -->
            <?php if (!empty($agent['total_downline']) && $agent['total_downline'] > 0): ?>
              <div class="position-absolute top-0 end-0 m-2">
                <span class="badge bg-success text-white small" title="<?= (int)$agent['total_downline'] ?> sub-agents">
                  <i class="fas fa-users me-1"></i><?= (int)$agent['total_downline'] ?>
                </span>
              </div>
            <?php endif; ?>

            <div class="card-body d-flex flex-column align-items-center text-center p-3 pt-4">
              <!-- Agent avatar -->
              <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mb-3 shadow-sm"
                   style="width:56px;height:56px;">
                <span class="fw-bold fs-5"><?= strtoupper(substr($agent['name'],0,1)) ?></span>
              </div>

              <!-- Parent Agent Information or Primary Agent Label -->
              <?php if (!empty($agent['parent_agent']) && !$agent['is_root_level']): ?>
                <div class="parent-agent-info">
                  <div class="small fw-medium mb-1">
                    <i class="fas fa-user-tie me-1"></i>
                    <span class="parent-agent-name">Under: <?= esc($agent['parent_agent']['name']) ?></span>
                  </div>
                  <div class="small text-muted">
                    ID: <?= esc($agent['parent_agent']['unique_agent_id'] ?? '#'.$agent['parent_agent']['id']) ?>
                  </div>
                </div>
              <?php else: ?>
                <div class="primary-agent-badge">
                  <div class="small fw-medium">
                    <i class="fas fa-crown me-1"></i>
                    <span class="primary-agent-text">Primary Associate</span>
                  </div>
                </div>
              <?php endif; ?>

              <!-- Agent name -->
              <div class="fw-semibold text-dark text-truncate w-100 mb-1" title="<?= esc($agent['name']) ?>">
                <?= esc($agent['name']) ?>
              </div>

              <!-- Agent ID -->
              <div class="small text-muted mb-2">
                ID: <?= esc($agent['unique_agent_id'] ?? '#'.$agent['id']) ?>
              </div>

              <!-- Hierarchy depth indicator -->
              <div class="small text-info mb-2">
                <i class="fas fa-layer-group me-1"></i>
                Level: <?= (int)($agent['hierarchy_depth'] ?? $level) ?>
              </div>

              <!-- Expand button for levels 1-2 only with enhanced accessibility -->
              <?php if (!empty($agent['has_children']) && $level <= 2): ?>
                <button class="btn btn-sm btn-outline-primary mt-auto expand-btn"
                        data-target-level="<?= (int)($level+1) ?>"
                        data-parent-id="<?= (int)$agent['id'] ?>"
                        aria-expanded="false"
                        aria-controls="level-<?= (int)($level+1) ?>-parent-<?= (int)$agent['id'] ?>"
                        aria-describedby="expand-help-<?= (int)$agent['id'] ?>"
                        title="Expand to view <?= (int)($agent['total_downline'] ?? 0) ?> sub-agents">
                  <i class="fas fa-chevron-down me-1" aria-hidden="true"></i>
                  <span class="expand-text">Expand</span>
                  <span class="visually-hidden">
                    <?= (int)($agent['total_downline'] ?? 0) ?> sub-agents under <?= esc($agent['name']) ?>
                  </span>
                </button>
                <div id="expand-help-<?= (int)$agent['id'] ?>" class="visually-hidden">
                  Press Enter or Space to expand and view sub-agents at level <?= (int)($level+1) ?>
                </div>
              <?php elseif (!empty($agent['has_children']) && $level > 2): ?>
                <!-- For level 3+, show hover indicator only with accessibility support -->
                <div class="small text-muted mt-auto"
                     role="button"
                     tabindex="0"
                     aria-label="Hover or focus for detailed information about <?= esc($agent['name']) ?>"
                     title="Hover or focus for detailed information">
                  <i class="fas fa-info-circle me-1" aria-hidden="true"></i>
                  <span>Hover for details</span>
                  <span class="visually-hidden">
                    Use mouse hover or keyboard focus to view detailed information
                  </span>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <?php if (!empty($pagination) && ($pagination['total'] ?? 0) > ($pagination['perPage'] ?? 0)): ?>
    <nav aria-label="Level <?= (int)$level ?> pagination" class="mt-2">
      <?php $current = (int)($pagination['page'] ?? 1); $per = (int)($pagination['perPage'] ?? 100); $total = (int)($pagination['total'] ?? 0); $pages = max(1, (int)ceil($total / $per)); ?>
      <ul class="pagination pagination-sm justify-content-center">
        <li class="page-item <?= $current<=1?'disabled':'' ?>">
          <a class="page-link" href="#" data-page="<?= max(1,$current-1) ?>" data-per-page="<?= $per ?>">&laquo;</a>
        </li>
        <?php for ($i=1;$i<=$pages;$i++): ?>
          <li class="page-item <?= $i===$current?'active':'' ?>">
            <a class="page-link" href="#" data-page="<?= $i ?>" data-per-page="<?= $per ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
        <li class="page-item <?= $current>=$pages?'disabled':'' ?>">
          <a class="page-link" href="#" data-page="<?= min($pages,$current+1) ?>" data-per-page="<?= $per ?>">&raquo;</a>
        </li>
      </ul>
    </nav>
  <?php endif; ?>
</div>

