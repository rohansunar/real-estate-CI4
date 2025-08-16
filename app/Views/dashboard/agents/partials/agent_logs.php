<?php if (empty($lines)): ?>
<div class="alert alert-info mb-0">
    <i class="fas fa-info-circle me-2"></i>No recent hierarchy logs for this agent today.
</div>
<?php else: ?>
<div class="list-group small">
    <?php foreach ($lines as $line): ?>
        <div class="list-group-item py-2">
            <code class="text-muted" style="white-space: pre-wrap;"><?= esc($line) ?></code>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

