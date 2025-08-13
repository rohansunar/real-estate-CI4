<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-4">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Create New Property</h2>
            <p class="text-muted">Add a new property listing to your portfolio</p>
        </div>
        <div class="mt-3 mt-sm-0">
            <a href="<?= base_url('dashboard/properties') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Properties
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

<!-- Property Creation Form -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Property Information</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('properties/create') ?>" method="post" enctype="multipart/form-data" id="propertyForm">
                    <?= csrf_field() ?>
                    
                    <!-- Basic Information -->
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label for="title" class="form-label fw-medium">Property Title <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control <?= isset(session()->getFlashdata('errors')['title']) ? 'is-invalid' : '' ?>" 
                                   id="title" 
                                   name="title" 
                                   value="<?= old('title') ?>"
                                   placeholder="e.g., Beautiful 3BHK Apartment in Siliguri"
                                   required>
                            <div class="form-text">Enter a descriptive title for your property</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="type" class="form-label fw-medium">Property Type <span class="text-danger">*</span></label>
                            <select class="form-select <?= isset(session()->getFlashdata('errors')['type']) ? 'is-invalid' : '' ?>" 
                                    id="type" 
                                    name="type" 
                                    required>
                                <option value="">Select Property Type</option>
                                <option value="house" <?= old('type') === 'house' ? 'selected' : '' ?>>House</option>
                                <option value="apartment" <?= old('type') === 'apartment' ? 'selected' : '' ?>>Apartment</option>
                                <option value="villa" <?= old('type') === 'villa' ? 'selected' : '' ?>>Villa</option>
                                <option value="land" <?= old('type') === 'land' ? 'selected' : '' ?>>Land</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="location" class="form-label fw-medium">Location <span class="text-danger">*</span></label>
                            <select class="form-select <?= isset(session()->getFlashdata('errors')['location']) ? 'is-invalid' : '' ?>" 
                                    id="location" 
                                    name="location" 
                                    required>
                                <option value="">Select Location</option>
                                <?php foreach ($locations as $location): ?>
                                    <option value="<?= esc($location) ?>" <?= old('location') === $location ? 'selected' : '' ?>>
                                        <?= esc($location) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="area" class="form-label fw-medium">Area (sq ft)</label>
                            <input type="number"
                                   class="form-control <?= isset(session()->getFlashdata('errors')['area']) ? 'is-invalid' : '' ?>"
                                   id="area"
                                   name="area"
                                   value="<?= old('area') ?>"
                                   placeholder="e.g., 1200"
                                   min="1">
                            <div class="form-text">Enter the total area in square feet (optional)</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">YouTube Video URLs</label>
                            <div id="youtube-videos-container">
                                <div class="youtube-video-input mb-2">
                                    <div class="input-group">
                                        <input type="url"
                                               class="form-control youtube-video-url"
                                               name="youtube_videos[]"
                                               placeholder="https://www.youtube.com/watch?v=..."
                                               onblur="validateYouTubeUrl(this)">
                                        <button type="button" class="btn btn-outline-danger" onclick="removeVideoInput(this)" style="display: none;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addVideoInput()">
                                <i class="fas fa-plus me-1"></i>Add Another Video
                            </button>
                            <div class="form-text mt-2">Add YouTube video tours (optional). You can add multiple videos.</div>
                        </div>
                    </div>

                    <!-- Featured Property Toggle -->
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       role="switch"
                                       id="is_featured"
                                       name="is_featured"
                                       value="1"
                                       <?= old('is_featured') ? 'checked' : '' ?>>
                                <label class="form-check-label fw-medium" for="is_featured">
                                    <i class="fas fa-star text-warning me-2"></i>
                                    Featured Property
                                </label>
                            </div>
                            <div class="form-text">Mark this property as featured to highlight it on the home page and in search results</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-medium">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control <?= isset(session()->getFlashdata('errors')['description']) ? 'is-invalid' : '' ?>" 
                                  id="description" 
                                  name="description" 
                                  rows="6" 
                                  placeholder="Describe your property in detail. Include features, amenities, nearby facilities, etc."
                                  required><?= old('description') ?></textarea>
                        <div class="form-text">Provide a detailed description of the property (minimum 10 characters)</div>
                    </div>

                    <!-- Image Upload Section -->
                    <div class="mb-4">
                        <label class="form-label fw-medium">Property Images</label>
                        <div class="border rounded p-4 bg-light">
                            <div class="text-center mb-3">
                                <i class="fas fa-cloud-upload-alt text-primary mb-2" style="font-size: 2rem;"></i>
                                <h6 class="text-dark">Upload Property Images</h6>
                                <p class="text-muted small mb-0">Drag and drop images here or click to browse</p>
                            </div>
                            
                            <input type="file" 
                                   class="form-control" 
                                   id="images" 
                                   name="images[]" 
                                   multiple 
                                   accept="image/*"
                                   onchange="previewImages(this)">
                            
                            <div class="form-text mt-2">
                                <i class="fas fa-info-circle me-1"></i>
                                You can upload multiple images. Maximum file size: 2MB per image. Supported formats: JPG, PNG, GIF
                            </div>
                            
                            <!-- Image Preview Container -->
                            <div id="imagePreview" class="row g-3 mt-3" style="display: none;">
                                <div class="col-12">
                                    <h6 class="text-dark mb-3">Image Preview:</h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                            <i class="fas fa-undo me-2"></i>
                            Reset Form
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save me-2"></i>
                            Create Property
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Sidebar with Tips -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-lightbulb text-warning me-2"></i>
                    Tips for Better Listings
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-start mb-3">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                            <i class="fas fa-camera text-primary"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-1">High-Quality Images</h6>
                        <p class="text-muted small mb-0">Upload clear, well-lit photos from multiple angles. First image will be the main display image.</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-start mb-3">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-opacity-10 rounded-circle p-2">
                            <i class="fas fa-edit text-success"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-1">Detailed Description</h6>
                        <p class="text-muted small mb-0">Include key features, nearby amenities, transportation links, and unique selling points.</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-start mb-3">
                    <div class="flex-shrink-0">
                        <div class="bg-info bg-opacity-10 rounded-circle p-2">
                            <i class="fas fa-map-marker-alt text-info"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-1">Accurate Location</h6>
                        <p class="text-muted small mb-0">Select the correct location to help buyers find your property easily.</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-2">
                            <i class="fas fa-video text-warning"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-1">Video Tour</h6>
                        <p class="text-muted small mb-0">Add a YouTube video tour to give buyers a better sense of the property.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar text-primary me-2"></i>
                    Your Property Stats
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3 text-center">
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <div class="h4 fw-bold text-primary mb-1">0</div>
                            <div class="small text-muted">Total Properties</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <div class="h4 fw-bold text-success mb-1">0</div>
                            <div class="small text-muted">Active Listings</div>
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
// Property creation form functionality
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('propertyForm');
    const submitBtn = document.getElementById('submitBtn');

    // Form submission handling
    form.addEventListener('submit', function(e) {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Property...';
        submitBtn.disabled = true;
    });
});

// Image preview functionality
function previewImages(input) {
    const previewContainer = document.getElementById('imagePreview');

    if (input.files && input.files.length > 0) {
        previewContainer.style.display = 'block';

        // Clear existing previews (keep the header)
        const existingPreviews = previewContainer.querySelectorAll('.image-preview-item');
        existingPreviews.forEach(item => item.remove());

        Array.from(input.files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'col-md-6 image-preview-item';
                    previewItem.innerHTML = `
                        <div class="position-relative">
                            <img src="${e.target.result}" class="img-fluid rounded shadow-sm" style="height: 150px; width: 100%; object-fit: cover;">
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-primary">${index + 1}</span>
                            </div>
                            <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 text-white p-2 rounded-bottom">
                                <small class="text-truncate d-block">${file.name}</small>
                                <small class="text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
                            </div>
                        </div>
                    `;
                    previewContainer.appendChild(previewItem);
                };

                reader.readAsDataURL(file);
            }
        });
    } else {
        previewContainer.style.display = 'none';
    }
}

// Reset form functionality
function resetForm() {
    if (confirm('Are you sure you want to reset the form? All entered data will be lost.')) {
        document.getElementById('propertyForm').reset();
        document.getElementById('imagePreview').style.display = 'none';
    }
}

// Form validation enhancement
(function() {
    'use strict';

    const forms = document.querySelectorAll('.needs-validation');

    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();

// YouTube video management
function addVideoInput() {
    const container = document.getElementById('youtube-videos-container');
    const videoInputs = container.querySelectorAll('.youtube-video-input');

    const newInput = document.createElement('div');
    newInput.className = 'youtube-video-input mb-2';
    newInput.innerHTML = `
        <div class="input-group">
            <input type="url"
                   class="form-control youtube-video-url"
                   name="youtube_videos[]"
                   placeholder="https://www.youtube.com/watch?v=..."
                   onblur="validateYouTubeUrl(this)">
            <button type="button" class="btn btn-outline-danger" onclick="removeVideoInput(this)">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

    container.appendChild(newInput);

    // Show remove buttons if there's more than one input
    updateRemoveButtons();
}

function removeVideoInput(button) {
    const container = document.getElementById('youtube-videos-container');
    const videoInput = button.closest('.youtube-video-input');

    if (container.children.length > 1) {
        videoInput.remove();
        updateRemoveButtons();
    }
}

function updateRemoveButtons() {
    const container = document.getElementById('youtube-videos-container');
    const removeButtons = container.querySelectorAll('.btn-outline-danger');

    removeButtons.forEach(button => {
        button.style.display = container.children.length > 1 ? 'block' : 'none';
    });
}

function validateYouTubeUrl(input) {
    const url = input.value.trim();
    if (url && !isValidYouTubeUrl(url)) {
        input.setCustomValidity('Please enter a valid YouTube URL');
        input.classList.add('is-invalid');
    } else {
        input.setCustomValidity('');
        input.classList.remove('is-invalid');
    }
}

function isValidYouTubeUrl(url) {
    const youtubeRegex = /^(https?:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|embed\/)|youtu\.be\/)[\w-]+/;
    return youtubeRegex.test(url);
}
</script>
<?= $this->endSection() ?>
