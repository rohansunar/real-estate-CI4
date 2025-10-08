<?= $this->extend('layouts/agent_dashboard') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-4">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Create New Sub-Agent</h2>
            <p class="text-muted">Add a new sub-agent to your team</p>
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
    <!-- Create Sub-Agent Form -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-transparent border-0 pb-0">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-plus me-2 text-success"></i>
                    Sub-Agent Information
                </h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('agent/sub-agents/create') ?>" method="post" id="createSubAgentForm">
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
                                   value="<?= old('name') ?>" 
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
                                   value="<?= old('email') ?>" 
                                   required>
                            <?php if (session()->getFlashdata('errors')['email'] ?? false): ?>
                                <div class="invalid-feedback">
                                    <?= session()->getFlashdata('errors')['email'] ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-text">
                                <small>The sub-agent will use this email address to log in and receive system notifications</small>
                            </div>
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
                                   value="<?= old('phone') ?>" 
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
                                   value="<?= old('qualification') ?>" 
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
                                      placeholder="Enter complete address"><?= old('address') ?></textarea>
                            <?php if (session()->getFlashdata('errors')['address'] ?? false): ?>
                                <div class="invalid-feedback">
                                    <?= session()->getFlashdata('errors')['address'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex flex-column flex-sm-row gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-user-plus me-2"></i>
                                Create Sub-Agent
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

    <!-- Information Card -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-transparent border-0 pb-0">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2 text-info"></i>
                    Important Information
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6 class="alert-heading">
                        <i class="fas fa-lightbulb me-2"></i>
                        Automatic Setup
                    </h6>
                    <p class="mb-0">When you create a sub-agent:</p>
                    <ul class="mb-0 mt-2">
                        <li>A unique Agent ID will be generated automatically</li>
                        <li>Login credentials will be generated and sent via email</li>
                        <li>The sub-agent will be linked to your account</li>
                    </ul>
                </div>

                <div class="alert alert-warning">
                    <h6 class="alert-heading">
                        <i class="fas fa-shield-alt me-2"></i>
                        Security Note
                    </h6>
                    <p class="mb-0">
                        A secure password will be automatically generated and sent to the sub-agent's email address. 
                        They can change it after their first login.
                    </p>
                </div>

                <div class="alert alert-success">
                    <h6 class="alert-heading">
                        <i class="fas fa-users me-2"></i>
                        Team Management
                    </h6>
                    <p class="mb-0">
                        Sub-agents will have access to their own dashboard where they can manage their profile 
                        and view their assigned properties.
                    </p>
                </div>
            </div>
        </div>

        <!-- Quick Stats Card -->
        <div class="card mt-4">
            <div class="card-header bg-transparent border-0 pb-0">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2 text-primary"></i>
                    Your Team Stats
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3 text-center">
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <div class="h4 text-primary mb-1">0</div>
                            <div class="small text-muted">Current Sub-Agents</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <div class="h4 text-success mb-1">0</div>
                            <div class="small text-muted">Active This Month</div>
                        </div>
                    </div>
                </div>
                <div class="mt-3 text-center">
                    <small class="text-muted">Stats will update after creating your first sub-agent</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form Validation Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createSubAgentForm');
    
    // Form submission validation
    form.addEventListener('submit', function(e) {
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
            // Basic email validation
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
        // Remove non-numeric characters except +, -, (, ), and spaces
        this.value = this.value.replace(/[^+\-\(\)\s\d]/g, '');
    });
});
</script>

<?= $this->endSection() ?>
