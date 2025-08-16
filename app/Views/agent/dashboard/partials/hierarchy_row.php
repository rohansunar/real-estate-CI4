<?php
// Renders a single horizontal row of agent cards for a given level
// Variables: $agents (array), $level (int), $parentId (int), $pagination (array)
?>
<div class="level-row py-2" data-level-row="<?= (int)$level ?>" data-parent-id="<?= (int)$parentId ?>">
  <div class="row g-3 justify-content-center">
    <?php if (empty($agents)): ?>
      <div class="col-12 text-center text-muted small">No agents at this level.</div>
    <?php else: ?>
      <?php foreach ($agents as $agent): ?>
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
          <div class="card h-100 shadow-sm border-0 hover-lift agent-card" tabindex="0"
               data-agent-id="<?= (int)$agent['id'] ?>" data-level="<?= (int)$level ?>"
               aria-label="Agent <?= esc($agent['name']) ?> Level <?= (int)$level ?>">
            <div class="card-body d-flex flex-column align-items-center text-center p-3">
              <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mb-2"
                   style="width:48px;height:48px;">
                <span class="fw-bold"><?= strtoupper(substr($agent['name'],0,1)) ?></span>
              </div>
              <div class="small fw-semibold text-dark text-truncate w-100" title="<?= esc($agent['name']) ?>">
                <?= esc($agent['name']) ?>
              </div>
              <div class="small text-muted">L<?= (int)$level ?></div>
              <?php if (!empty($agent['has_children'])): ?>
                <button class="btn btn-sm btn-outline-primary mt-2 expand-btn" data-bs-toggle="collapse"
                        data-target-level="<?= (int)($level+1) ?>" data-parent-id="<?= (int)$agent['id'] ?>">
                  <i class="fas fa-chevron-down me-1"></i> Open
                </button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <?php if (!empty($pagination) && ($pagination['total'] ?? 0) > ($pagination['perPage'] ?? 0)): ?>
    <nav aria-label="Level <?= (int)$level ?> pagination" class="mt-2">
      <?php $current = (int)($pagination['page'] ?? 1); $per = (int)($pagination['perPage'] ?? 12); $total = (int)($pagination['total'] ?? 0); $pages = max(1, (int)ceil($total / $per)); ?>
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

