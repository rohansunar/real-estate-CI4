<?php
// Agent summary for popover/tooltip content
// Variables: $agent (array), $levels (array: counts,total_levels,total_agents)
?>
<div class="text-start">
  <div class="fw-semibold mb-1">
    <i class="fas fa-user text-primary me-1"></i> <?= esc($agent['name']) ?>
  </div>
  <div class="small text-muted mb-2">ID: <?= esc($agent['unique_agent_id'] ?? ('#'.$agent['id'])) ?></div>
  <div class="small mb-1"><span class="badge bg-light text-secondary border">Total levels: <?= (int)($levels['total_levels'] ?? 0) ?></span></div>
  <?php if (!empty($levels['counts'])): ?>
    <ul class="small ps-3 mb-0">
      <?php foreach ($levels['counts'] as $lvl=>$cnt): ?>
        <li>Level <?= (int)$lvl+0 ?>: <?= (int)$cnt ?> agents</li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <div class="small text-muted">No downline</div>
  <?php endif; ?>
</div>

