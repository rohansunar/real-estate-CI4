<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-4">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Create New Associate</h2>
            <p class="text-muted">Add a new real estate associate to your team</p>
        </div>
        <div class="mt-3 mt-sm-0">
            <a href="<?= base_url('dashboard/agents') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Assoicates
            </a>
        </div>
    </div>
</div>

<!-- Alert Messages -->
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Agent Creation Form -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Associate Information</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('dashboard/agents/create') ?>" method="post" enctype="multipart/form-data" id="agentForm">
                    <?= csrf_field() ?>
                    
                    <!-- Profile Image Upload -->
                    <div class="mb-4">
                        <label class="form-label fw-medium">Profile Image</label>
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div id="imagePreview" class="bg-light border rounded d-flex align-items-center justify-content-center" 
                                     style="width: 100px; height: 100px;">
                                    <i class="fas fa-user text-muted" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <input type="file" 
                                       class="form-control" 
                                       id="profile_image" 
                                       name="profile_image" 
                                       accept="image/*"
                                       onchange="previewImage(this)">
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Upload a professional profile photo. Maximum file size: 2MB. Supported formats: JPG, PNG, GIF
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Basic Information -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control <?= isset(session()->getFlashdata('errors')['name']) ? 'is-invalid' : '' ?>" 
                                   id="name" 
                                   name="name" 
                                   value="<?= old('name') ?>"
                                   placeholder="e.g., John Doe"
                                   required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-medium">Email Address <span class="text-danger">*</span></label>
                            <input type="email" 
                                   class="form-control <?= isset(session()->getFlashdata('errors')['email']) ? 'is-invalid' : '' ?>" 
                                   id="email" 
                                   name="email" 
                                   value="<?= old('email') ?>"
                                   placeholder="e.g., john.doe@example.com"
                                   required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-medium">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" 
                                   class="form-control <?= isset(session()->getFlashdata('errors')['phone']) ? 'is-invalid' : '' ?>" 
                                   id="phone" 
                                   name="phone" 
                                   value="<?= old('phone') ?>"
                                   placeholder="e.g., +91 98765 43210"
                                   required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="qualification" class="form-label fw-medium">Qualification</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="qualification" 
                                   name="qualification" 
                                   value="<?= old('qualification') ?>"
                                   placeholder="e.g., Real Estate License, MBA">
                            <div class="form-text">Professional qualifications or certifications (optional)</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="address" class="form-label fw-medium">Address</label>
                        <textarea class="form-control"
                                  id="address"
                                  name="address"
                                  rows="3"
                                  placeholder="Enter the assoicate's address..."><?= old('address') ?></textarea>
                        <div class="form-text">Complete address including city, state, and postal code (optional)</div>
                    </div>

                    <!-- Agent IDs and Hierarchy Section -->
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-id-card text-primary me-2"></i>
                                Assoicate IDs & Hierarchy
                            </h6>
                        </div>
                        <div class="card-body pt-3">
                            <div class="row g-3 mb-3">
                                <div class="col-md-12">
                                    <label for="parent_agent_id" class="form-label fw-medium">Parent Assoicate</label>
                                    <select class="form-select" id="parent_agent_id" name="parent_agent_id">
                                        <option value="">Select Parent Assoicate (Optional)</option>
                                        <?php
                                        $agentModel = new \App\Models\AgentModel();
                                        $allAgents = $agentModel->where('is_active', true)->findAll();
                                        ?>
                                        <?php foreach ($allAgents as $parentAgent): ?>
                                            <option value="<?= $parentAgent['id'] ?>"
                                                    <?= old('parent_agent_id') == $parentAgent['id'] ? 'selected' : '' ?>>
                                                <?= esc($parentAgent['name']) ?>
                                                <?php if ($parentAgent['unique_agent_id']): ?>
                                                    (<?= esc($parentAgent['unique_agent_id']) ?>)
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Choose a parent associate to establish the reporting hierarchy. Leave empty for top-level associates.
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note:</strong> A unique associate ID will be automatically generated when the associate is created.
                            </div>
                        </div>
                    </div>

                    <!-- Authentication Section -->
                    <!-- <div class="card bg-light border-0 mb-4">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-key text-warning me-2"></i>
                                Authentication Settings
                            </h6>
                        </div>
                        <div class="card-body pt-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-medium">Password</label>
                                    <input type="password"
                                           class="form-control <?= isset(session()->getFlashdata('errors')['password']) ? 'is-invalid' : '' ?>"
                                           id="password"
                                           name="password"
                                           placeholder="Enter password">
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Optional. If provided, associate can log in to the system. Minimum 6 characters.
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="password_confirm" class="form-label fw-medium">Confirm Password</label>
                                    <input type="password"
                                           class="form-control"
                                           id="password_confirm"
                                           name="password_confirm"
                                           placeholder="Confirm password">
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Re-enter the password to confirm
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                            <i class="fas fa-undo me-2"></i>
                            Reset Form
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save me-2"></i>
                            Create Associate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Sidebar with Information -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle text-info me-2"></i>
                    Associate Requirements
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-start mb-3">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                            <i class="fas fa-user text-primary"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-1">Personal Information</h6>
                        <p class="text-muted small mb-0">Full name, email, and phone number are required for all associates.</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-start mb-3">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-opacity-10 rounded-circle p-2">
                            <i class="fas fa-envelope text-success"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-1">Welcome Email</h6>
                        <p class="text-muted small mb-0">A welcome email will be automatically sent to the associate's email address.</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-start mb-3">
                    <div class="flex-shrink-0">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-2">
                            <i class="fas fa-camera text-warning"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-1">Profile Photo</h6>
                        <p class="text-muted small mb-0">Upload a professional headshot for better client trust and recognition.</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0">
                        <div class="bg-info bg-opacity-10 rounded-circle p-2">
                            <i class="fas fa-certificate text-info"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-1">Qualifications</h6>
                        <p class="text-muted small mb-0">Add relevant certifications, licenses, or educational background.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar text-primary me-2"></i>
                    Team Overview
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3 text-center">
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <div class="h4 fw-bold text-primary mb-1">0</div>
                            <div class="small text-muted">Total Associates</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <div class="h4 fw-bold text-success mb-1">0</div>
                            <div class="small text-muted">Active Associates</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Agent creation form functionality
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('agentForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Form submission handling
    form.addEventListener('submit', function(e) {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Associate...';
        submitBtn.disabled = true;
    });
});

// Image preview functionality
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded" style="width: 100px; height: 100px; object-fit: cover;">`;
        };
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.innerHTML = '<i class="fas fa-user text-muted" style="font-size: 2rem;"></i>';
    }
}

// Reset form functionality
function resetForm() {
    if (confirm('Are you sure you want to reset the form? All entered data will be lost.')) {
        document.getElementById('agentForm').reset();
        document.getElementById('imagePreview').innerHTML = '<i class="fas fa-user text-muted" style="font-size: 2rem;"></i>';
    }
}

// Phone number formatting
document.getElementById('phone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 10) {
        value = value.substring(0, 10);
    }
    e.target.value = value;
});

// Email validation
document.getElementById('email').addEventListener('blur', function() {
    const email = this.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (email && !emailRegex.test(email)) {
        this.setCustomValidity('Please enter a valid email address');
        this.classList.add('is-invalid');
    } else {
        this.setCustomValidity('');
        this.classList.remove('is-invalid');
    }
});
</script>
<?= $this->endSection() ?>
