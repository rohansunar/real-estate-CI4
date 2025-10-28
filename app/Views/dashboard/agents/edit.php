<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-4">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Edit Associate</h2>
            <p class="text-muted">Update associate information</p>
        </div>
        <div class="mt-3 mt-sm-0">
            <a href="<?= base_url('dashboard/agents') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Associates
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

<!-- Agent Edit Form -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Associate Information</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('dashboard/agents/edit/' . $agent['id']) ?>" method="post" enctype="multipart/form-data" id="agentForm">
                    <?= csrf_field() ?>
                    
                    <!-- Profile Image Upload -->
                    <div class="mb-4">
                        <label class="form-label fw-medium">Profile Image</label>
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div id="imagePreview" class="bg-light border rounded d-flex align-items-center justify-content-center" 
                                     style="width: 100px; height: 100px;">
                                    <?php if ($agent['profile_image']): ?>
                                        <img src="<?= base_url($agent['profile_image']) ?>" 
                                             alt="<?= esc($agent['name']) ?>" 
                                             class="img-fluid rounded" 
                                             style="width: 100px; height: 100px; object-fit: cover;">
                                    <?php else: ?>
                                        <i class="fas fa-user text-muted" style="font-size: 2rem;"></i>
                                    <?php endif; ?>
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
                                    Upload a new profile photo or leave empty to keep current image. Maximum file size: 2MB.
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
                                   value="<?= old('name', $agent['name']) ?>"
                                   placeholder="e.g., John Doe"
                                   required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-medium">Email Address <span class="text-danger">*</span></label>
                            <input type="email" 
                                   class="form-control <?= isset(session()->getFlashdata('errors')['email']) ? 'is-invalid' : '' ?>" 
                                   id="email" 
                                   name="email" 
                                   value="<?= old('email', $agent['email']) ?>"
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
                                   value="<?= old('phone', $agent['phone']) ?>"
                                   placeholder="e.g., +91 98765 43210"
                                   required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="qualification" class="form-label fw-medium">Qualification</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="qualification" 
                                   name="qualification" 
                                   value="<?= old('qualification', $agent['qualification']) ?>"
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
                                  placeholder="Enter the agent's address..."><?= old('address', $agent['address']) ?></textarea>
                        <div class="form-text">Complete address including city, state, and postal code (optional)</div>
                    </div>

                    <!-- Agent IDs and Hierarchy Section -->
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-id-card text-primary me-2"></i>
                                Associate IDs & Hierarchy
                            </h6>
                        </div>
                        <div class="card-body pt-3">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="unique_agent_id" class="form-label fw-medium">Unique Associate ID</label>
                                    <input type="text"
                                           class="form-control <?= isset(session()->getFlashdata('errors')['unique_agent_id']) ? 'is-invalid' : '' ?>"
                                           id="unique_agent_id"
                                           name="unique_agent_id"
                                           value="<?= old('unique_agent_id', $agent['unique_agent_id']) ?>"
                                           placeholder="e.g., WRR00001"
                                           readonly>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Auto-generated unique identifier for this associate
                                    </div>
                                </div>
                            </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="parent_agent_id" class="form-label fw-medium">Parent Associate</label>
                                    <select class="form-select" id="parent_agent_id" name="parent_agent_id">
                                        <option value="">Select Parent Associate (Optional)</option>
                                        <?php
                                        $agentModel = new \App\Models\AgentModel();
                                        $allAgents = $agentModel->where('id !=', $agent['id'])->where('is_active', true)->findAll();
                                        ?>
                                        <?php foreach ($allAgents as $parentAgent): ?>
                                            <option value="<?= $parentAgent['id'] ?>"
                                                    <?= old('parent_agent_id', $agent['parent_agent_id']) == $parentAgent['id'] ? 'selected' : '' ?>>
                                                <?= esc($parentAgent['name']) ?> (<?= esc($parentAgent['unique_agent_id']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Choose a parent associate to establish the reporting hierarchy. Leave empty for top-level associates.
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="is_active" class="form-label fw-medium">Associate Status</label>
                                    <select class="form-select" id="is_active" name="is_active">
                                        <option value="1" <?= old('is_active', $agent['is_active']) == '1' ? 'selected' : '' ?>>Active</option>
                                        <option value="0" <?= old('is_active', $agent['is_active']) == '0' ? 'selected' : '' ?>>Inactive</option>
                                    </select>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Active associates can log in and access the system
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Authentication Section -->
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-key text-warning me-2"></i>
                                Authentication Settings
                            </h6>
                        </div>
                        <div class="card-body pt-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-medium">New Password</label>
                                    <input type="password"
                                           class="form-control <?= isset(session()->getFlashdata('errors')['password']) ? 'is-invalid' : '' ?>"
                                           id="password"
                                           name="password"
                                           placeholder="Enter new password">
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Leave empty to keep the current password unchanged. New passwords must be at least 6 characters long.
                                        <?php if ($agent['password']): ?>
                                            <br><span class="text-success"><i class="fas fa-check me-1"></i>This associate can log into the system</span>
                                        <?php else: ?>
                                            <br><span class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>This associate cannot log in yet - set a password to enable access</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="password_confirm" class="form-label fw-medium">Confirm Password</label>
                                    <input type="password"
                                           class="form-control"
                                           id="password_confirm"
                                           name="password_confirm"
                                           placeholder="Confirm new password">
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Re-enter the password to confirm
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('dashboard/agents') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save me-2"></i>
                            Update
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
                    <i class="fas fa-user-tie text-primary me-2"></i>
                    Associate Details
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <?php if ($agent['profile_image']): ?>
                            <img src="<?= base_url($agent['profile_image']) ?>" 
                                 alt="<?= esc($agent['name']) ?>" 
                                 class="rounded-circle" 
                                 style="width: 3rem; height: 3rem; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 3rem; height: 3rem;">
                                <i class="fas fa-user text-white"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-1"><?= esc($agent['name']) ?></h6>
                        <p class="text-muted small mb-0">
                            <?php if ($agent['unique_agent_id']): ?>
                                Associate ID: <?= esc($agent['unique_agent_id']) ?>
                            <?php else: ?>
                                Database ID: #<?= $agent['id'] ?>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                
                <div class="border-top pt-3">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Status:</span>
                                <span class="badge <?= $agent['is_active'] ? 'status-active' : 'status-inactive' ?>">
                                    <?= $agent['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Joined:</span>
                                <span><?= date('M j, Y', strtotime($agent['created_at'])) ?></span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Last Updated:</span>
                                <span><?= date('M j, Y', strtotime($agent['updated_at'])) ?></span>
                            </div>
                        </div>
                        <?php if ($agent['parent_agent_id']): ?>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Parent Associate:</span>
                                <span class="text-info">
                                    <?php
                                    $agentModel = new \App\Models\AgentModel();
                                    $parentAgent = $agentModel->find($agent['parent_agent_id']);
                                    echo $parentAgent ? esc($parentAgent['name']) : 'Not found';
                                    ?>
                                </span>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Login Access:</span>
                                <span class="badge <?= $agent['password'] ? 'bg-success' : 'bg-warning' ?>">
                                    <?= $agent['password'] ? 'Enabled' : 'Disabled' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cogs text-secondary me-2"></i>
                    Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button"
                            class="btn btn-outline-danger"
                            onclick="deleteAgent(<?= $agent['id'] ?>)">
                        <i class="fas fa-trash me-2"></i>
                        Delete Associate
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Agent edit form functionality
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('agentForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Form submission handling
    form.addEventListener('submit', function(e) {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating Agent...';
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
    }
}



// Delete agent
function deleteAgent(agentId) {
    if (confirm('Are you sure you want to delete this associate? This action cannot be undone.')) {
        fetch(`<?= base_url('dashboard/agents/') ?>${agentId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => window.location.href = '<?= base_url('dashboard/agents') ?>', 1000);
            } else {
                showNotification(data.message, 'danger');
            }
        })
        .catch(error => {
            showNotification('Error deleting agent', 'danger');
        });
    }
}

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
