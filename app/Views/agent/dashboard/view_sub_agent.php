<!-- Sub-Agent Details Modal Content -->
<div class="row g-4">
    <!-- Profile Section -->
    <div class="col-md-4">
        <div class="text-center">
            <div class="agent-avatar bg-success mx-auto mb-3" style="width: 100px; height: 100px; line-height: 100px; font-size: 2.5rem;">
                <?= strtoupper(substr($subAgent['name'], 0, 1)) ?>
            </div>
            <h5 class="mb-1"><?= esc($subAgent['name']) ?></h5>
            <p class="text-muted mb-3"><?= esc($subAgent['email']) ?></p>
            
            <?php if ($subAgent['is_active']): ?>
                <span class="badge bg-success fs-6">
                    <i class="fas fa-check-circle me-1"></i>
                    Active
                </span>
            <?php else: ?>
                <span class="badge bg-danger fs-6">
                    <i class="fas fa-times-circle me-1"></i>
                    Inactive
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Details Section -->
    <div class="col-md-8">
        <div class="row g-3">
            <!-- Contact Information -->
            <div class="col-12">
                <h6 class="fw-bold text-dark mb-3">
                    <i class="fas fa-address-book me-2 text-primary"></i>
                    Contact Information
                </h6>
                <div class="row g-2">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-envelope text-muted me-2" style="width: 20px;"></i>
                            <div>
                                <small class="text-muted d-block">Email</small>
                                <span><?= esc($subAgent['email']) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-phone text-muted me-2" style="width: 20px;"></i>
                            <div>
                                <small class="text-muted d-block">Phone</small>
                                <span><?= esc($subAgent['phone']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if ($subAgent['address']): ?>
                    <div class="d-flex align-items-start mb-2">
                        <i class="fas fa-map-marker-alt text-muted me-2 mt-1" style="width: 20px;"></i>
                        <div>
                            <small class="text-muted d-block">Address</small>
                            <span><?= esc($subAgent['address']) ?></span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Professional Information -->
            <div class="col-12">
                <hr class="my-3">
                <h6 class="fw-bold text-dark mb-3">
                    <i class="fas fa-briefcase me-2 text-success"></i>
                    Professional Information
                </h6>
                <div class="row g-2">
                    <?php if ($subAgent['qualification']): ?>
                        <div class="col-12">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-graduation-cap text-muted me-2" style="width: 20px;"></i>
                                <div>
                                    <small class="text-muted d-block">Qualification</small>
                                    <span><?= esc($subAgent['qualification']) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-id-card text-muted me-2" style="width: 20px;"></i>
                            <div>
                                <small class="text-muted d-block">Unique Agent ID</small>
                                <span class="font-monospace"><?= esc($subAgent['unique_agent_id']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="col-12">
                <hr class="my-3">
                <h6 class="fw-bold text-dark mb-3">
                    <i class="fas fa-user-cog me-2 text-info"></i>
                    Account Information
                </h6>
                <div class="row g-2">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-calendar-plus text-muted me-2" style="width: 20px;"></i>
                            <div>
                                <small class="text-muted d-block">Member Since</small>
                                <span><?= date('F j, Y', strtotime($subAgent['created_at'])) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-clock text-muted me-2" style="width: 20px;"></i>
                            <div>
                                <small class="text-muted d-block">Last Updated</small>
                                <span><?= date('M j, Y g:i A', strtotime($subAgent['updated_at'])) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="mt-4 pt-3 border-top">
    <div class="d-flex flex-wrap gap-2">
        <a href="<?= base_url('agent/sub-agents/edit/' . $subAgent['id']) ?>" class="btn btn-primary">
            <i class="fas fa-edit me-2"></i>
            Edit Sub-Agent
        </a>
        <button type="button" 
                class="btn btn-outline-danger" 
                onclick="deleteSubAgent(<?= $subAgent['id'] ?>, '<?= esc($subAgent['name']) ?>')"
                data-bs-dismiss="modal">
            <i class="fas fa-trash me-2"></i>
            Delete Sub-Agent
        </button>
        <button type="button" class="btn btn-outline-secondary ms-auto" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i>
            Close
        </button>
    </div>
</div>
