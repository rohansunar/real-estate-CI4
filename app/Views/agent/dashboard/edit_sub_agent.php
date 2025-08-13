<?= $this->extend('layouts/agent_dashboard') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-4">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Edit Sub-Agent</h2>
            <p class="text-muted">Update sub-agent information</p>
        </div>
        <div class="mt-3 mt-sm-0">
            <a href="<?= base_url('agent/sub-agents') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Sub-Agents
            </a>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Edit Sub-Agent Form -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-transparent border-0 pb-0">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-edit me-2 text-success"></i>
                    Sub-Agent Information
                </h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('agent/sub-agents/edit/' . $subAgent['id']) ?>" method="post" id="editSubAgentForm">
                    <?= csrf_field() ?>
                    
                    <div class="row g-3">
                        <!-- Name -->
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">
                                Full Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control <?= session()->getFlashdata('errors')['name'] ?? false ? 'is-invalid' : '' ?>" 
                                   id="name" 
                                   name="name" 
                                   value="<?= old('name', esc($subAgent['name'])) ?>" 
                                   required>
                            <?php if (session()->getFlashdata('errors')['name'] ?? false): ?>
                                <div class="invalid-feedback">
                                    <?= session()->getFlashdata('errors')['name'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">
                                Email Address <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control <?= session()->getFlashdata('errors')['email'] ?? false ? 'is-invalid' : '' ?>" 
                                   id="email" 
                                   name="email" 
                                   value="<?= old('email', esc($subAgent['email'])) ?>" 
                                   required>
                            <?php if (session()->getFlashdata('errors')['email'] ?? false): ?>
                                <div class="invalid-feedback">
                                    <?= session()->getFlashdata('errors')['email'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold">
                                Phone Number <span class="text-danger">*</span>
                            </label>
                            <input type="tel" 
                                   class="form-control <?= session()->getFlashdata('errors')['phone'] ?? false ? 'is-invalid' : '' ?>" 
                                   id="phone" 
                                   name="phone" 
                                   value="<?= old('phone', esc($subAgent['phone'])) ?>" 
                                   required>
                            <?php if (session()->getFlashdata('errors')['phone'] ?? false): ?>
                                <div class="invalid-feedback">
                                    <?= session()->getFlashdata('errors')['phone'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Qualification -->
                        <div class="col-md-6">
                            <label for="qualification" class="form-label fw-semibold">
                                Qualification
                            </label>
                            <input type="text" 
                                   class="form-control <?= session()->getFlashdata('errors')['qualification'] ?? false ? 'is-invalid' : '' ?>" 
                                   id="qualification" 
                                   name="qualification" 
                                   value="<?= old('qualification', esc($subAgent['qualification'])) ?>" 
                                   placeholder="e.g., MBA, Real Estate License">
                            <?php if (session()->getFlashdata('errors')['qualification'] ?? false): ?>
                                <div class="invalid-feedback">
                                    <?= session()->getFlashdata('errors')['qualification'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Address -->
                        <div class="col-12">
                            <label for="address" class="form-label fw-semibold">
                                Address
                            </label>
                            <textarea class="form-control <?= session()->getFlashdata('errors')['address'] ?? false ? 'is-invalid' : '' ?>" 
                                      id="address" 
                                      name="address" 
                                      rows="3" 
                                      placeholder="Enter complete address"><?= old('address', esc($subAgent['address'])) ?></textarea>
                            <?php if (session()->getFlashdata('errors')['address'] ?? false): ?>
                                <div class="invalid-feedback">
                                    <?= session()->getFlashdata('errors')['address'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Password Change Section -->
                        <div class="col-12">
                            <hr class="my-4">
                            <h6 class="fw-semibold mb-3">
                                <i class="fas fa-lock me-2"></i>
                                Change Password (Optional)
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-semibold">
                                        New Password
                                    </label>
                                    <input type="password" 
                                           class="form-control <?= session()->getFlashdata('errors')['password'] ?? false ? 'is-invalid' : '' ?>" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Leave blank to keep current password">
                                    <?php if (session()->getFlashdata('errors')['password'] ?? false): ?>
                                        <div class="invalid-feedback">
                                            <?= session()->getFlashdata('errors')['password'] ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="form-text">
                                        <small>Password must be at least 6 characters long</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirm" class="form-label fw-semibold">
                                        Confirm New Password
                                    </label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirm" 
                                           name="password_confirm" 
                                           placeholder="Confirm your new password">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex flex-column flex-sm-row gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>
                                Update Sub-Agent
                            </button>
                            <a href="<?= base_url('agent/sub-agents') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>
                                Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sub-Agent Details Card -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-transparent border-0 pb-0">
                <h5 class="card-title mb-0">
                    <i class="fas fa-id-card me-2 text-success"></i>
                    Sub-Agent Details
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="agent-avatar bg-success mx-auto mb-3" style="width: 80px; height: 80px; line-height: 80px; font-size: 2rem;">
                        <?= strtoupper(substr($subAgent['name'], 0, 1)) ?>
                    </div>
                    <h6 class="mb-1"><?= esc($subAgent['name']) ?></h6>
                    <small class="text-muted"><?= esc($subAgent['email']) ?></small>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Unique Agent ID</label>
                    <p class="mb-0 font-monospace"><?= esc($subAgent['unique_agent_id']) ?></p>
                </div>

                <?php if ($subAgent['referral_id']): ?>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Referral ID</label>
                        <p class="mb-0 font-monospace"><?= esc($subAgent['referral_id']) ?></p>
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Account Status</label>
                    <p class="mb-0">
                        <?php if ($subAgent['is_active']): ?>
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle me-1"></i>
                                Active
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger">
                                <i class="fas fa-times-circle me-1"></i>
                                Inactive
                            </span>
                        <?php endif; ?>
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Member Since</label>
                    <p class="mb-0"><?= date('F j, Y', strtotime($subAgent['created_at'])) ?></p>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Last Updated</label>
                    <p class="mb-0"><?= date('F j, Y g:i A', strtotime($subAgent['updated_at'])) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form Validation Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editSubAgentForm');
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirm');

    // Password confirmation validation
    function validatePasswordConfirmation() {
        if (password.value && passwordConfirm.value) {
            if (password.value !== passwordConfirm.value) {
                passwordConfirm.setCustomValidity('Passwords do not match');
                passwordConfirm.classList.add('is-invalid');
            } else {
                passwordConfirm.setCustomValidity('');
                passwordConfirm.classList.remove('is-invalid');
            }
        } else {
            passwordConfirm.setCustomValidity('');
            passwordConfirm.classList.remove('is-invalid');
        }
    }

    password.addEventListener('input', validatePasswordConfirmation);
    passwordConfirm.addEventListener('input', validatePasswordConfirmation);

    // Form submission validation
    form.addEventListener('submit', function(e) {
        validatePasswordConfirmation();
        
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        form.classList.add('was-validated');
    });

    // Email validation
    const emailInput = document.getElementById('email');
    emailInput.addEventListener('blur', function() {
        const email = this.value;
        if (email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                this.setCustomValidity('Please enter a valid email address');
                this.classList.add('is-invalid');
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
            }
        }
    });

    // Phone validation
    const phoneInput = document.getElementById('phone');
    phoneInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^+\-\(\)\s\d]/g, '');
    });
});
</script>

<?= $this->endSection() ?>
