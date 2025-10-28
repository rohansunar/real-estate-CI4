<!-- Admin Agent Details Modal Content -->
<div class="row g-4">
    <!-- Profile Section -->
    <div class="col-md-4">
        <div class="text-center">
            <!-- Agent Avatar -->
            <div class="agent-avatar bg-primary mx-auto mb-3" style="width: 120px; height: 120px; line-height: 120px; font-size: 3rem; border-radius: 50%;">
                <?= strtoupper(substr($agent['name'], 0, 1)) ?>
            </div>
            
            <!-- Agent Name and Status -->
            <h4 class="mb-1 fw-bold"><?= esc($agent['name']) ?></h4>
            <p class="text-muted mb-2"><?= esc($agent['email']) ?></p>
            <p class="text-muted small mb-3">ID: <?= esc($agent['unique_agent_id']) ?></p>
            
            <!-- Status Badge -->
            <?php if ($agent['is_active']): ?>
                <span class="badge bg-success fs-6 px-3 py-2">
                    <i class="fas fa-check-circle me-1"></i>
                    Active Associate
                </span>
            <?php else: ?>
                <span class="badge bg-danger fs-6 px-3 py-2">
                    <i class="fas fa-times-circle me-1"></i>
                    Inactive Associate
                </span>
            <?php endif; ?>

            <!-- Hierarchy Position -->
            <?php if ($hierarchyPosition): ?>
                <div class="mt-3 p-3 bg-light rounded">
                    <h6 class="mb-2 text-primary">
                        <i class="fas fa-sitemap me-2"></i>
                        Hierarchy Position
                    </h6>
                    <div class="small">
                        <div class="mb-1">
                            <strong>Level:</strong> <?= (int)($hierarchyPosition['level'] ?? 0) ?>
                        </div>
                        <div class="mb-1">
                            <strong>Direct Sub-Associates:</strong> <?= (int)$directSubAgentCount ?>
                        </div>
                        <div>
                            <strong>Total Downline:</strong> <?= (int)$totalDownline ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Details Section -->
    <div class="col-md-8">
        <div class="row g-4">
            <!-- Contact Information -->
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-address-book me-2"></i>
                            Contact Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-envelope text-primary me-3" style="width: 20px;"></i>
                                    <div>
                                        <small class="text-muted d-block">Email Address</small>
                                        <span class="fw-medium"><?= esc($agent['email']) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-phone text-success me-3" style="width: 20px;"></i>
                                    <div>
                                        <small class="text-muted d-block">Phone Number</small>
                                        <span class="fw-medium"><?= esc($agent['phone']) ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php if ($agent['address']): ?>
                                <div class="col-12">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-map-marker-alt text-info me-3 mt-1" style="width: 20px;"></i>
                                        <div>
                                            <small class="text-muted d-block">Address</small>
                                            <span class="fw-medium"><?= esc($agent['address']) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professional Information -->
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-briefcase me-2"></i>
                            Professional Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <?php if ($agent['qualification']): ?>
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-graduation-cap text-warning me-3" style="width: 20px;"></i>
                                        <div>
                                            <small class="text-muted d-block">Qualification</small>
                                            <span class="fw-medium"><?= esc($agent['qualification']) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-id-card text-secondary me-3" style="width: 20px;"></i>
                                    <div>
                                        <small class="text-muted d-block">Unique Agent ID</small>
                                        <span class="font-monospace fw-medium"><?= esc($agent['unique_agent_id']) ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Parent Agent Information -->
                            <?php if ($parentAgent): ?>
                                <div class="col-12">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-tie text-primary me-3" style="width: 20px;"></i>
                                        <div>
                                            <small class="text-muted d-block">Reports To</small>
                                            <span class="fw-medium"><?= esc($parentAgent['name']) ?></span>
                                            <small class="text-muted ms-2">(<?= esc($parentAgent['unique_agent_id']) ?>)</small>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="col-12">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-crown text-warning me-3" style="width: 20px;"></i>
                                        <div>
                                            <small class="text-muted d-block">Hierarchy Status</small>
                                            <span class="fw-medium text-warning">Root Level Agent</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-user-cog me-2"></i>
                            Account Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar-plus text-success me-3" style="width: 20px;"></i>
                                    <div>
                                        <small class="text-muted d-block">Member Since</small>
                                        <span class="fw-medium"><?= date('F j, Y', strtotime($agent['created_at'])) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-clock text-warning me-3" style="width: 20px;"></i>
                                    <div>
                                        <small class="text-muted d-block">Last Updated</small>
                                        <span class="fw-medium"><?= date('M j, Y g:i A', strtotime($agent['updated_at'])) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Direct Sub-Agents -->
            <?php if (!empty($directSubAgents)): ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-users me-2"></i>
                                Direct Sub-Associates (<?= count($directSubAgents) ?>)
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <?php foreach ($directSubAgents as $subAgent): ?>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-2 bg-light rounded">
                                            <div class="agent-avatar bg-primary me-3" style="width: 40px; height: 40px; line-height: 40px; font-size: 1rem; border-radius: 50%;">
                                                <?= strtoupper(substr($subAgent['name'], 0, 1)) ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-medium"><?= esc($subAgent['name']) ?></div>
                                                <small class="text-muted"><?= esc($subAgent['unique_agent_id']) ?></small>
                                            </div>
                                            <span class="badge <?= $subAgent['is_active'] ? 'bg-success' : 'bg-secondary' ?> ms-2">
                                                <?= $subAgent['is_active'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="mt-4 pt-3 border-top">
    <div class="d-flex flex-wrap gap-2 justify-content-between">
        <div class="d-flex gap-2">
            <a href="<?= base_url('dashboard/agents/edit/' . $agent['id']) ?>" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>
                Edit Associate
            </a>
            <button type="button" class="btn btn-outline-info" onclick="viewAgentLogs(<?= $agent['id'] ?>)">
                <i class="fas fa-clipboard-list me-2"></i>
                View Logs
            </button>
            <?php if (!empty($directSubAgents)): ?>
                <button type="button" class="btn btn-outline-success" onclick="addSubAgent(<?= $agent['id'] ?>)">
                    <i class="fas fa-plus me-2"></i>
                    Add Sub-Associate
                </button>
            <?php endif; ?>
        </div>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i>
            Close
        </button>
    </div>
</div>
