<?php
/**
 * Enhanced Agent Summary Partial for Bootstrap 5 Popovers
 *
 * Provides detailed agent information for Level 3+ agents in hover popovers.
 * This partial is loaded via AJAX and displayed in Bootstrap 5 popovers with:
 *
 * - Comprehensive agent details (name, ID, contact info, status)
 * - Hierarchy information (levels, sub-agent counts, breakdown)
 * - Commission information (if available)
 * - Professional styling with Bootstrap 5 components
 * - Mobile-optimized layout for smaller screens
 *
 * @param array $agent Agent data including name, email, phone, status, etc.
 * @param array $levels Hierarchy level data with counts, total_levels, total_agents
 *
 * @author Real Estate Team
 * @version 2.0 - Enhanced popover content for progressive disclosure
 * @since 2025-08-16
 */
?>
<div class="text-start agent-summary-popover">
  <!-- Agent Header -->
  <div class="d-flex align-items-center mb-2">
    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
         style="width:32px;height:32px;">
      <span class="fw-bold small"><?= strtoupper(substr($agent['name'],0,1)) ?></span>
    </div>
    <div>
      <div class="fw-semibold text-dark">
        <?= esc($agent['name']) ?>
      </div>
      <div class="small text-muted">
        ID: <?= esc($agent['unique_agent_id'] ?? '#'.$agent['id']) ?>
      </div>
    </div>
  </div>

  <!-- Agent Status -->
  <div class="mb-2">
    <span class="badge <?= $agent['is_active'] ? 'bg-success' : 'bg-danger' ?> me-1">
      <i class="fas <?= $agent['is_active'] ? 'fa-check-circle' : 'fa-times-circle' ?> me-1"></i>
      <?= $agent['is_active'] ? 'Active' : 'Inactive' ?>
    </span>
    <?php if (!empty($agent['created_at'])): ?>
      <span class="badge bg-light text-secondary border">
        <i class="fas fa-calendar me-1"></i>
        Joined <?= date('M Y', strtotime($agent['created_at'])) ?>
      </span>
    <?php endif; ?>
  </div>

  <!-- Contact Information -->
  <?php if (!empty($agent['email']) || !empty($agent['phone'])): ?>
    <div class="mb-2">
      <div class="small text-muted mb-1">
        <i class="fas fa-address-card me-1"></i> Contact Info
      </div>
      <?php if (!empty($agent['email'])): ?>
        <div class="small">
          <i class="fas fa-envelope text-primary me-1"></i>
          <?= esc($agent['email']) ?>
        </div>
      <?php endif; ?>
      <?php if (!empty($agent['phone'])): ?>
        <div class="small">
          <i class="fas fa-phone text-success me-1"></i>
          <?= esc($agent['phone']) ?>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <!-- Hierarchy Information -->
  <div class="mb-2">
    <div class="small text-muted mb-1">
      <i class="fas fa-sitemap me-1"></i> Hierarchy Details
    </div>
    <div class="row g-1">
      <div class="col-6">
        <div class="small">
          <span class="badge bg-light text-secondary border w-100">
            Levels: <?= (int)($levels['total_levels'] ?? 0) ?>
          </span>
        </div>
      </div>
      <div class="col-6">
        <div class="small">
          <span class="badge bg-light text-secondary border w-100">
            Total: <?= (int)($levels['total_agents'] ?? 0) ?>
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- Level Breakdown -->
  <?php if (!empty($levels['counts'])): ?>
    <div class="mb-0">
      <div class="small text-muted mb-1">
        <i class="fas fa-layer-group me-1"></i> Level Breakdown
      </div>
      <div class="small">
        <?php foreach ($levels['counts'] as $lvl => $cnt): ?>
          <div class="d-flex justify-content-between align-items-center py-1">
            <span>Level <?= (int)$lvl ?></span>
            <span class="badge bg-primary"><?= (int)$cnt ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php else: ?>
    <div class="text-center py-2">
      <i class="fas fa-users text-muted"></i>
      <div class="small text-muted">No downline agents</div>
    </div>
  <?php endif; ?>

  <!-- Commission Information (if available) -->
  <?php if (!empty($agent['commission_rate'])): ?>
    <div class="mt-2 pt-2 border-top">
      <div class="small text-muted mb-1">
        <i class="fas fa-percentage me-1"></i> Commission Rate
      </div>
      <span class="badge bg-warning text-dark">
        <?= number_format($agent['commission_rate'], 1) ?>%
      </span>
    </div>
  <?php endif; ?>
</div>

