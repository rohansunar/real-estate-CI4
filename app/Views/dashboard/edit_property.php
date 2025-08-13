<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-4">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Edit Property</h2>
            <p class="text-muted">Update property information</p>
        </div>
        <div class="mt-3 mt-sm-0">
            <a href="<?= base_url('dashboard/properties') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Properties
            </a>
        </div>
    </div>
</div>

<!-- Display Success/Error Messages -->
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

<!-- Property Edit Form -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Property Information</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('dashboard/properties/edit/' . $property['id']) ?>" method="post" enctype="multipart/form-data" id="propertyForm" onsubmit="return validateForm(event)" class="needs-validation" novalidate>
                    <?= csrf_field() ?>
                    
                    <!-- Basic Information -->
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label for="title" class="form-label fw-medium">Property Title <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control <?= isset(session()->getFlashdata('errors')['title']) ? 'is-invalid' : '' ?>" 
                                   id="title" 
                                   name="title" 
                                   value="<?= old('title', $property['title']) ?>"
                                   placeholder="e.g., Beautiful 3BHK Apartment in Siliguri"
                                   required>
                            <div class="form-text">Enter a descriptive title for your property</div>
                        </div>
                    </div>

                    <!-- Property Type and Location -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="type" class="form-label fw-medium">Property Type <span class="text-danger">*</span></label>
                            <select class="form-select <?= isset(session()->getFlashdata('errors')['type']) ? 'is-invalid' : '' ?>" 
                                    id="type" 
                                    name="type" 
                                    required>
                                <option value="">Select Type</option>
                                <option value="house" <?= old('type', $property['type']) === 'house' ? 'selected' : '' ?>>House</option>
                                <option value="apartment" <?= old('type', $property['type']) === 'apartment' ? 'selected' : '' ?>>Apartment</option>
                                <option value="villa" <?= old('type', $property['type']) === 'villa' ? 'selected' : '' ?>>Villa</option>
                                <option value="land" <?= old('type', $property['type']) === 'land' ? 'selected' : '' ?>>Land</option>
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
                                    <option value="<?= esc($location) ?>" <?= old('location', $property['location']) === $location ? 'selected' : '' ?>>
                                        <?= esc($location) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Area -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="area" class="form-label fw-medium">Area (sq ft)</label>
                            <input type="number"
                                   class="form-control <?= isset(session()->getFlashdata('errors')['area']) ? 'is-invalid' : '' ?>"
                                   id="area"
                                   name="area"
                                   value="<?= old('area', $property['area']) ?>"
                                   placeholder="e.g., 1200"
                                   min="1">
                            <div class="form-text">Enter the total area in square feet (optional)</div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input"
                                       type="checkbox"
                                       role="switch"
                                       id="is_featured"
                                       name="is_featured"
                                       value="1"
                                       <?= old('is_featured', $property['is_featured']) ? 'checked' : '' ?>>
                                <label class="form-check-label fw-medium" for="is_featured">
                                    <i class="fas fa-star text-warning me-2"></i>
                                    Featured Property
                                </label>
                            </div>
                            <div class="form-text">Mark this property as featured to highlight it on the home page and in search results</div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="form-label fw-medium">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control <?= isset(session()->getFlashdata('errors')['description']) ? 'is-invalid' : '' ?>" 
                                  id="description" 
                                  name="description" 
                                  rows="5" 
                                  placeholder="Describe the property features, amenities, and other details..."
                                  required><?= old('description', $property['description']) ?></textarea>
                        <div class="form-text">Provide a detailed description of the property</div>
                    </div>

                    <!-- Current Images Display -->
                    <?php
                    $currentImages = [];
                    if (!empty($property['images'])) {
                        $currentImages = is_string($property['images']) ? json_decode($property['images'], true) : $property['images'];
                        $currentImages = $currentImages ?: [];
                    }
                    ?>

                    <!-- Hidden field to track images to remove -->
                    <input type="hidden" name="images_to_remove" id="imagesToRemove" value="">

                    <?php if (!empty($currentImages)): ?>
                    <div class="mb-4">
                        <label class="form-label fw-medium">Current Images</label>
                        <div class="row g-2" id="currentImagesContainer">
                            <?php foreach ($currentImages as $index => $image): ?>
                                <div class="col-md-3" id="imageItem-<?= $index ?>" data-image-index="<?= $index ?>">
                                    <div class="position-relative">
                                        <img src="<?= base_url('/' . $image) ?>"
                                             class="img-fluid rounded"
                                             alt="Property Image"
                                             style="height: 120px; width: 100%; object-fit: cover;">
                                        <button type="button"
                                                class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1"
                                                onclick="removeImage(<?= $index ?>)"
                                                title="Remove Image">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <!-- Overlay for removed state -->
                                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-75 d-none align-items-center justify-content-center rounded removed-overlay" id="imageOverlay-<?= $index ?>">
                                            <div class="text-center text-white">
                                                <i class="fas fa-trash-alt fs-3 mb-2"></i>
                                                <div class="small">Will be removed</div>
                                                <button type="button" class="btn btn-outline-light btn-sm mt-2" onclick="restoreImage(<?= $index ?>)">
                                                    <i class="fas fa-undo me-1"></i>Undo
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- New Images Upload -->
                    <div class="mb-4">
                        <label for="images" class="form-label fw-medium">Add New Images</label>
                        <input type="file" 
                               class="form-control <?= isset(session()->getFlashdata('errors')['images']) ? 'is-invalid' : '' ?>" 
                               id="images" 
                               name="images[]" 
                               accept="image/*" 
                               multiple>
                        <div class="form-text">
                            Upload additional property images (JPG, PNG, GIF). Maximum 2MB per image.
                            You can select multiple images at once.
                        </div>
                    </div>

                    <!-- Current YouTube Videos Display -->
                    <?php
                    $currentVideos = [];
                    if (!empty($property['youtube_video'])) {
                        $currentVideos = is_string($property['youtube_video']) ? json_decode($property['youtube_video'], true) : $property['youtube_video'];
                        $currentVideos = $currentVideos ?: [];
                    }
                    ?>

                    <!-- Hidden field to track videos to remove -->
                    <input type="hidden" name="videos_to_remove" id="videosToRemove" value="">

                    <?php if (!empty($currentVideos)): ?>
                    <div class="mb-4">
                        <label class="form-label fw-medium">Current YouTube Videos</label>
                        <div class="list-group" id="currentVideosContainer">
                            <?php foreach ($currentVideos as $index => $video): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center position-relative" id="videoItem-<?= $index ?>" data-video-index="<?= $index ?>">
                                    <div>
                                        <i class="fab fa-youtube text-danger me-2"></i>
                                        <a href="<?= esc($video) ?>" target="_blank" class="text-decoration-none">
                                            <?= esc($video) ?>
                                        </a>
                                    </div>
                                    <button type="button"
                                            class="btn btn-outline-danger btn-sm"
                                            onclick="removeVideo(<?= $index ?>)"
                                            title="Remove Video">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <!-- Overlay for removed state -->
                                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-75 d-none align-items-center justify-content-center removed-overlay" id="videoOverlay-<?= $index ?>">
                                        <div class="text-center text-white">
                                            <i class="fas fa-trash-alt me-2"></i>
                                            <span class="small">Will be removed</span>
                                            <button type="button" class="btn btn-outline-light btn-sm ms-2" onclick="restoreVideo(<?= $index ?>)">
                                                <i class="fas fa-undo me-1"></i>Undo
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- New YouTube Videos -->
                    <div class="mb-4">
                        <label for="youtube_video" class="form-label fw-medium">Add YouTube Video URLs</label>
                        <div id="youtube-video-container">
                            <div class="input-group mb-2">
                                <input type="url" 
                                       class="form-control" 
                                       name="youtube_video[]" 
                                       placeholder="https://www.youtube.com/watch?v=..."
                                       pattern="https://www\.youtube\.com/watch\?v=.+">
                                <button type="button" class="btn btn-outline-primary" onclick="addYouTubeField()">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-text">
                            Add YouTube video URLs to showcase your property. 
                            Format: https://www.youtube.com/watch?v=VIDEO_ID
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('dashboard/properties') ?>" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save me-2"></i>
                            Update Property
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Property Preview -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Property Preview</h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <?php if (!empty($currentImages)): ?>
                        <img src="<?= base_url('/' . $currentImages[0]) ?>" 
                             class="img-fluid rounded mb-3" 
                             alt="Property Preview"
                             style="height: 200px; width: 100%; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" style="height: 200px;">
                            <span class="text-muted">No Image Available</span>
                        </div>
                    <?php endif; ?>
                    
                    <h6 class="fw-semibold"><?= esc($property['title']) ?></h6>
                    <p class="text-muted small mb-2">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        <?= esc($property['location']) ?>
                    </p>
                    <span class="badge bg-primary"><?= ucfirst($property['type']) ?></span>
                    <?php if ($property['area']): ?>
                        <p class="text-muted small mt-2">
                            <i class="fas fa-expand-arrows-alt me-1"></i>
                            <?= number_format($property['area']) ?> sq ft
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Property edit form functionality
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('propertyForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Form submission handling
    form.addEventListener('submit', function(e) {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating Property...';
        submitBtn.disabled = true;
    });
});

// Add YouTube video field
function addYouTubeField() {
    const container = document.getElementById('youtube-video-container');
    const newField = document.createElement('div');
    newField.className = 'input-group mb-2';
    newField.innerHTML = `
        <input type="url" 
               class="form-control" 
               name="youtube_video[]" 
               placeholder="https://www.youtube.com/watch?v=..."
               pattern="https://www\.youtube\.com/watch\?v=.+">
        <button type="button" class="btn btn-outline-danger" onclick="removeYouTubeField(this)">
            <i class="fas fa-minus"></i>
        </button>
    `;
    container.appendChild(newField);
}

// Remove YouTube video field
function removeYouTubeField(button) {
    button.parentElement.remove();
}

// Arrays to track items marked for removal
let imagesToRemove = [];
let videosToRemove = [];

/**
 * Remove existing image temporarily (hide from UI, mark for deletion)
 * Images are only permanently deleted when the form is submitted
 */
function removeImage(index) {
    if (confirm('Are you sure you want to remove this image? It will be permanently deleted when you click "Update Property".')) {
        // Add to removal list if not already there
        if (!imagesToRemove.includes(index)) {
            imagesToRemove.push(index);
            updateImagesToRemoveField();
        }

        // Show overlay and hide the image
        const imageItem = document.getElementById(`imageItem-${index}`);
        const overlay = document.getElementById(`imageOverlay-${index}`);

        if (imageItem && overlay) {
            overlay.classList.remove('d-none');
            overlay.classList.add('d-flex');
            imageItem.style.opacity = '0.6';
        }

        showNotification('Image marked for removal. Click "Update Property" to permanently delete it.', 'warning');
    }
}

/**
 * Restore a temporarily removed image
 */
function restoreImage(index) {
    // Remove from removal list
    imagesToRemove = imagesToRemove.filter(i => i !== index);
    updateImagesToRemoveField();

    // Hide overlay and restore the image
    const imageItem = document.getElementById(`imageItem-${index}`);
    const overlay = document.getElementById(`imageOverlay-${index}`);

    if (imageItem && overlay) {
        overlay.classList.add('d-none');
        overlay.classList.remove('d-flex');
        imageItem.style.opacity = '1';
    }

    showNotification('Image restored successfully.', 'success');
}

/**
 * Remove existing video temporarily (hide from UI, mark for deletion)
 * Videos are only permanently deleted when the form is submitted
 */
function removeVideo(index) {
    if (confirm('Are you sure you want to remove this video? It will be permanently deleted when you click "Update Property".')) {
        // Add to removal list if not already there
        if (!videosToRemove.includes(index)) {
            videosToRemove.push(index);
            updateVideosToRemoveField();
        }

        // Show overlay and hide the video
        const videoItem = document.getElementById(`videoItem-${index}`);
        const overlay = document.getElementById(`videoOverlay-${index}`);

        if (videoItem && overlay) {
            overlay.classList.remove('d-none');
            overlay.classList.add('d-flex');
            videoItem.style.opacity = '0.6';
        }

        showNotification('Video marked for removal. Click "Update Property" to permanently delete it.', 'warning');
    }
}

/**
 * Restore a temporarily removed video
 */
function restoreVideo(index) {
    // Remove from removal list
    videosToRemove = videosToRemove.filter(i => i !== index);
    updateVideosToRemoveField();

    // Hide overlay and restore the video
    const videoItem = document.getElementById(`videoItem-${index}`);
    const overlay = document.getElementById(`videoOverlay-${index}`);

    if (videoItem && overlay) {
        overlay.classList.add('d-none');
        overlay.classList.remove('d-flex');
        videoItem.style.opacity = '1';
    }

    showNotification('Video restored successfully.', 'success');
}

/**
 * Update the hidden field with images to remove
 */
function updateImagesToRemoveField() {
    const field = document.getElementById('imagesToRemove');
    if (field) {
        field.value = JSON.stringify(imagesToRemove);
    }
}

/**
 * Update the hidden field with videos to remove
 */
function updateVideosToRemoveField() {
    const field = document.getElementById('videosToRemove');
    if (field) {
        field.value = JSON.stringify(videosToRemove);
    }
}

/**
 * Show notification message with enhanced styling and accessibility
 *
 * @param {string} message - The message to display
 * @param {string} type - The type of notification ('success', 'warning', 'error', 'info')
 */
function showNotification(message, type = 'info') {
    // Map type to Bootstrap alert classes
    const alertClass = {
        'success': 'alert-success',
        'warning': 'alert-warning',
        'error': 'alert-danger',
        'danger': 'alert-danger',
        'info': 'alert-info'
    }[type] || 'alert-info';

    // Create accessible toast notification
    const toast = document.createElement('div');
    toast.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'polite');

    // Add appropriate icon based on type
    const icons = {
        'success': 'fas fa-check-circle',
        'warning': 'fas fa-exclamation-triangle',
        'error': 'fas fa-exclamation-circle',
        'danger': 'fas fa-exclamation-circle',
        'info': 'fas fa-info-circle'
    };

    const icon = icons[type] || icons['info'];

    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="${icon} me-2"></i>
            <div class="flex-grow-1">${message}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close notification"></button>
        </div>
    `;

    document.body.appendChild(toast);

    // Auto remove after 6 seconds (slightly longer for better UX)
    setTimeout(() => {
        if (toast.parentNode) {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 150); // Wait for fade animation
        }
    }, 6000);
}

/**
 * Validate form before submission
 * Provides user-friendly validation messages and prevents submission of invalid data
 *
 * @param {Event} event - The form submission event
 * @returns {boolean} - True if form is valid, false otherwise
 */
function validateForm(event) {
    const form = event.target;
    let isValid = true;
    let errorMessages = [];

    // Clear previous validation states
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

    // Validate required fields
    const title = form.querySelector('[name="title"]');
    const description = form.querySelector('[name="description"]');
    const type = form.querySelector('[name="type"]');
    const location = form.querySelector('[name="location"]');
    const area = form.querySelector('[name="area"]');

    // Title validation
    if (!title.value.trim()) {
        addFieldError(title, 'Property title is required');
        errorMessages.push('Property title is required');
        isValid = false;
    } else if (title.value.trim().length < 5) {
        addFieldError(title, 'Property title must be at least 5 characters long');
        errorMessages.push('Property title must be at least 5 characters long');
        isValid = false;
    }

    // Description validation
    if (!description.value.trim()) {
        addFieldError(description, 'Property description is required');
        errorMessages.push('Property description is required');
        isValid = false;
    } else if (description.value.trim().length < 20) {
        addFieldError(description, 'Property description must be at least 20 characters long');
        errorMessages.push('Property description must be at least 20 characters long');
        isValid = false;
    }

    // Type validation
    if (!type.value) {
        addFieldError(type, 'Property type is required');
        errorMessages.push('Property type is required');
        isValid = false;
    }

    // Location validation
    if (!location.value) {
        addFieldError(location, 'Property location is required');
        errorMessages.push('Property location is required');
        isValid = false;
    }

    // Area validation
    if (area.value && (isNaN(area.value) || parseFloat(area.value) <= 0)) {
        addFieldError(area, 'Property area must be a positive number');
        errorMessages.push('Property area must be a positive number');
        isValid = false;
    }

    // YouTube URL validation
    const youtubeInputs = form.querySelectorAll('[name="youtube_videos[]"]');
    youtubeInputs.forEach((input, index) => {
        if (input.value.trim() && !isValidYouTubeUrl(input.value.trim())) {
            addFieldError(input, 'Please enter a valid YouTube URL');
            errorMessages.push(`YouTube video ${index + 1} URL is invalid`);
            isValid = false;
        }
    });

    // Show validation summary if there are errors
    if (!isValid) {
        event.preventDefault();
        showNotification(
            `Please fix the following errors:<br>• ${errorMessages.join('<br>• ')}`,
            'error'
        );

        // Focus on first invalid field
        const firstInvalid = form.querySelector('.is-invalid');
        if (firstInvalid) {
            firstInvalid.focus();
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    return isValid;
}

/**
 * Add error styling and message to a form field
 *
 * @param {HTMLElement} field - The form field element
 * @param {string} message - The error message to display
 */
function addFieldError(field, message) {
    field.classList.add('is-invalid');

    const feedback = document.createElement('div');
    feedback.className = 'invalid-feedback';
    feedback.textContent = message;

    field.parentNode.appendChild(feedback);
}

/**
 * Validate YouTube URL format
 *
 * @param {string} url - The URL to validate
 * @returns {boolean} - True if valid YouTube URL, false otherwise
 */
function isValidYouTubeUrl(url) {
    const youtubeRegex = /^(https?:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|embed\/)|youtu\.be\/)[\w-]+(&[\w=]*)?$/;
    return youtubeRegex.test(url);
}
</script>
<?= $this->endSection() ?>
