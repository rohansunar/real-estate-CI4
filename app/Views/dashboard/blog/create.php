<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-4">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Create Blog Post</h2>
            <p class="text-muted">Write and publish a new blog post</p>
        </div>
        <div class="mt-3 mt-sm-0">
            <a href="<?= base_url('dashboard/blog') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Blog
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

<!-- Blog Post Creation Form -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Blog Post Details</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('dashboard/blog/create') ?>" method="post" enctype="multipart/form-data" id="blogForm">
                    <?= csrf_field() ?>
                    
                    <!-- Title -->
                    <div class="mb-4">
                        <label for="title" class="form-label fw-medium">Post Title <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control <?= isset(session()->getFlashdata('errors')['title']) ? 'is-invalid' : '' ?>" 
                               id="title" 
                               name="title" 
                               value="<?= old('title') ?>"
                               placeholder="Enter an engaging blog post title"
                               required>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            A compelling title helps attract readers and improves SEO.
                        </div>
                    </div>

                    <!-- Slug -->
                    <div class="mb-4">
                        <label for="slug" class="form-label fw-medium">URL Slug</label>
                        <input type="text" 
                               class="form-control <?= isset(session()->getFlashdata('errors')['slug']) ? 'is-invalid' : '' ?>" 
                               id="slug" 
                               name="slug" 
                               value="<?= old('slug') ?>"
                               placeholder="auto-generated-from-title">
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Leave empty to auto-generate from title. Use lowercase letters, numbers, and hyphens only.
                        </div>
                    </div>

                    <!-- Excerpt -->
                    <div class="mb-4">
                        <label for="excerpt" class="form-label fw-medium">Excerpt</label>
                        <textarea class="form-control <?= isset(session()->getFlashdata('errors')['excerpt']) ? 'is-invalid' : '' ?>" 
                                  id="excerpt" 
                                  name="excerpt" 
                                  rows="3"
                                  placeholder="Brief summary of the blog post (optional)"><?= old('excerpt') ?></textarea>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            A short description that appears in blog listings and search results.
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="mb-4">
                        <label for="content" class="form-label fw-medium">Content <span class="text-danger">*</span></label>
                        <div id="quill-editor" style="height: 400px;"></div>
                        <!--
                            Note: Removed 'required' attribute from hidden textarea to prevent
                            "An invalid form control with name='content' is not focusable" error.
                            Content validation is now handled by JavaScript before form submission.
                        -->
                        <textarea id="content" name="content" style="display: none;"><?= old('content') ?></textarea>
                        <!--
                            CRITICAL FIX: Hidden status field to ensure proper form submission

                            This field prevents the "The status field is required" validation error
                            that occurs when JavaScript form handling interferes with natural form submission.

                            How it works:
                            1. Default value is 'draft' for fallback
                            2. JavaScript updates this field when buttons are clicked
                            3. Server receives proper status value during validation

                            This approach maintains clean separation between client-side UX enhancements
                            and server-side validation requirements.
                        -->
                        <input type="hidden" id="status" name="status" value="<?= old('status', 'draft') ?>">
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Use the rich text editor to format your content with headings, lists, links, and more.
                        </div>
                    </div>

                    <!-- Featured Image -->
                    <div class="mb-4">
                        <label for="featured_image" class="form-label fw-medium">Featured Image</label>
                        <input type="file" 
                               class="form-control <?= isset(session()->getFlashdata('errors')['featured_image']) ? 'is-invalid' : '' ?>" 
                               id="featured_image" 
                               name="featured_image"
                               accept="image/*"
                               onchange="previewFeaturedImage(this)">
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Upload an image that represents your blog post. Maximum file size: 2MB.
                        </div>
                        <!-- Image Preview -->
                        <div id="imagePreview" class="mt-3" style="display: none;">
                            <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('dashboard/blog') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>
                            Cancel
                        </a>
                        <div class="d-flex gap-2">
                            <button type="submit" name="status" value="draft" class="btn btn-outline-primary" id="saveDraftBtn">
                                <i class="fas fa-save me-2"></i>
                                Save as Draft
                            </button>
                            <button type="submit" name="status" value="published" class="btn btn-primary" id="publishBtn">
                                <i class="fas fa-paper-plane me-2"></i>
                                Publish Post
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Publishing Options -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cog text-secondary me-2"></i>
                    Publishing Options
                </h5>
            </div>
            <div class="card-body">
                <!-- Category -->
                <div class="mb-3">
                    <label for="category" class="form-label fw-medium">Category</label>
                    <select class="form-select <?= isset(session()->getFlashdata('errors')['category']) ? 'is-invalid' : '' ?>" 
                            id="category" 
                            name="category">
                        <option value="">Select Category</option>
                        <option value="Real Estate Tips" <?= old('category') === 'Real Estate Tips' ? 'selected' : '' ?>>Real Estate Tips</option>
                        <option value="Market Trends" <?= old('category') === 'Market Trends' ? 'selected' : '' ?>>Market Trends</option>
                        <option value="Investment Guide" <?= old('category') === 'Investment Guide' ? 'selected' : '' ?>>Investment Guide</option>
                        <option value="Property News" <?= old('category') === 'Property News' ? 'selected' : '' ?>>Property News</option>
                        <option value="Home Buying" <?= old('category') === 'Home Buying' ? 'selected' : '' ?>>Home Buying</option>
                        <option value="Home Selling" <?= old('category') === 'Home Selling' ? 'selected' : '' ?>>Home Selling</option>
                    </select>
                </div>

                <!-- Tags -->
                <div class="mb-3">
                    <label for="tags" class="form-label fw-medium">Tags</label>
                    <input type="text" 
                           class="form-control <?= isset(session()->getFlashdata('errors')['tags']) ? 'is-invalid' : '' ?>" 
                           id="tags" 
                           name="tags" 
                           value="<?= old('tags') ?>"
                           placeholder="real estate, property, investment">
                    <div class="form-text">
                        <i class="fas fa-info-circle me-1"></i>
                        Separate tags with commas. Helps with search and categorization.
                    </div>
                </div>
            </div>
        </div>

        <!-- SEO Settings -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-search text-secondary me-2"></i>
                    SEO Settings
                </h5>
            </div>
            <div class="card-body">
                <!-- Meta Title -->
                <div class="mb-3">
                    <label for="meta_title" class="form-label fw-medium">Meta Title</label>
                    <input type="text" 
                           class="form-control <?= isset(session()->getFlashdata('errors')['meta_title']) ? 'is-invalid' : '' ?>" 
                           id="meta_title" 
                           name="meta_title" 
                           value="<?= old('meta_title') ?>"
                           placeholder="SEO-optimized title">
                    <div class="form-text">
                        <i class="fas fa-info-circle me-1"></i>
                        Leave empty to use post title. Recommended: 50-60 characters.
                    </div>
                </div>

                <!-- Meta Description -->
                <div class="mb-3">
                    <label for="meta_description" class="form-label fw-medium">Meta Description</label>
                    <textarea class="form-control <?= isset(session()->getFlashdata('errors')['meta_description']) ? 'is-invalid' : '' ?>" 
                              id="meta_description" 
                              name="meta_description" 
                              rows="3"
                              placeholder="Brief description for search engines"><?= old('meta_description') ?></textarea>
                    <div class="form-text">
                        <i class="fas fa-info-circle me-1"></i>
                        Recommended: 150-160 characters. Appears in search results.
                    </div>
                </div>
            </div>
        </div>

        <!-- Writing Tips -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-lightbulb text-warning me-2"></i>
                    Writing Tips
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Use clear, engaging headlines
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Include relevant keywords naturally
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Add images to break up text
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Write for your target audience
                    </li>
                    <li class="mb-0">
                        <i class="fas fa-check text-success me-2"></i>
                        Proofread before publishing
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<!-- Quill.js CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<!-- Enhanced form submission styles for better user feedback -->
<style>
.form-submitting {
    opacity: 0.7;
    pointer-events: none;
}

.form-submitting .card {
    position: relative;
}

.form-submitting .card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    z-index: 10;
}

.submission-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.submission-message {
    background: white;
    padding: 2rem;
    border-radius: 0.5rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    text-align: center;
    max-width: 300px;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Quill.js Rich Text Editor -->
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
/**
 * Initialize Quill Rich Text Editor with Enhanced Error Handling
 *
 * This initialization includes:
 * - Comprehensive toolbar configuration
 * - Real-time content synchronization with hidden textarea
 * - Error handling for editor initialization
 * - Memory leak prevention
 */
let quill;
try {
    quill = new Quill('#quill-editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'align': [] }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
                ['link', 'blockquote', 'code-block'],
                ['clean']
            ]
        },
        placeholder: 'Write your blog post content here...'
    });

    // Set up real-time content synchronization to prevent validation issues
    // This ensures the hidden textarea always has the latest content
    quill.on('text-change', function() {
        try {
            const contentTextarea = document.getElementById('content');
            if (contentTextarea) {
                contentTextarea.value = quill.root.innerHTML;
            }
        } catch (error) {
            // Silent error handling to prevent console spam
            // Error will be caught during form submission if needed
        }
    });

} catch (error) {
    // Log error for debugging but don't spam console in production
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        console.error('Failed to initialize Quill editor:', error);
    }

    // Fallback: Show user-friendly error message
    const editorContainer = document.getElementById('quill-editor');
    if (editorContainer) {
        editorContainer.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Failed to load rich text editor. Please refresh the page and try again.</div>';
    }
}

// Set initial content if available
const initialContent = document.getElementById('content').value;
if (initialContent) {
    quill.root.innerHTML = initialContent;
}

/**
 * Blog Form Functionality with Enhanced Browser Compatibility
 *
 * This script handles the blog creation form with the following key features:
 * - Cross-browser compatible form submission handling
 * - Client-side validation with user-friendly error messages
 * - Visual feedback during form submission
 * - Automatic slug generation from title
 * - Quill.js rich text editor integration
 *
 * Key Fix: Replaced e.submitter (not supported in all browsers) with
 * manual button tracking for better compatibility.
 *
 * @author White Rock Realtor Team
 * @version 3.0 - Enhanced browser compatibility and user experience
 * @since 2025-08-26
 */
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('blogForm');
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    const saveDraftBtn = document.getElementById('saveDraftBtn');
    const publishBtn = document.getElementById('publishBtn');

    // Track which button was clicked for cross-browser compatibility
    // This solves the issue where e.submitter is not supported in older browsers
    let clickedButton = null;
    
    // Auto-generate slug from title
    titleInput.addEventListener('input', function() {
        if (!slugInput.value || slugInput.dataset.autoGenerated) {
            const slug = this.value
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');
            slugInput.value = slug;
            slugInput.dataset.autoGenerated = 'true';
        }
    });
    
    // Mark slug as manually edited
    slugInput.addEventListener('input', function() {
        if (this.value !== '') {
            delete this.dataset.autoGenerated;
        }
    });

    /**
     * Button Click Tracking for Cross-Browser Compatibility
     *
     * These event listeners track which submit button was clicked and
     * update the hidden status field to ensure proper form submission.
     * This prevents "The status field is required" validation error.
     */
    saveDraftBtn.addEventListener('click', function() {
        clickedButton = this;
        // Set status field value for draft submission
        const statusField = document.getElementById('status');
        if (statusField) {
            statusField.value = 'draft';
        }
    });

    publishBtn.addEventListener('click', function() {
        clickedButton = this;
        // Set status field value for published submission
        const statusField = document.getElementById('status');
        if (statusField) {
            statusField.value = 'published';
        }
    });
    
    /**
     * Enhanced Form Submission Handler
     *
     * This handler provides:
     * - Cross-browser compatible button detection
     * - Client-side validation with user-friendly messages
     * - Visual feedback during submission
     * - Proper Quill.js content synchronization
     * - Timeout protection against hanging submissions
     */
    form.addEventListener('submit', function(e) {
        // Use tracked button with fallbacks for maximum browser compatibility
        const submitBtn = clickedButton || e.submitter || saveDraftBtn;

        /**
         * Enhanced Form Validation with Comprehensive Error Handling
         *
         * This validation ensures:
         * - Required fields are properly filled
         * - Quill editor content is valid and synchronized
         * - User-friendly error messages are displayed
         * - Form submission is prevented if validation fails
         */

        // Validate title field
        const title = titleInput ? titleInput.value.trim() : '';
        if (!title) {
            e.preventDefault();
            showUserFriendlyAlert('Missing Title', 'Please enter a blog post title before saving.');
            if (titleInput) titleInput.focus();
            return false;
        }

        // Validate content from Quill editor
        let content = '';
        try {
            if (quill && quill.root) {
                content = quill.root.innerHTML.trim();
            } else {
                throw new Error('Quill editor not properly initialized');
            }
        } catch (error) {
            e.preventDefault();
            showUserFriendlyAlert('Editor Error', 'The rich text editor is not working properly. Please refresh the page and try again.');
            return false;
        }

        // Check if content is empty (accounting for various empty states)
        const isEmpty = !content ||
                       content === '<p><br></p>' ||
                       content === '<p></p>' ||
                       content === '<p>&nbsp;</p>' ||
                       content.replace(/<[^>]*>/g, '').trim() === '';

        if (isEmpty) {
            e.preventDefault();
            showUserFriendlyAlert('Missing Content', 'Please enter some content for your blog post.');
            if (quill) quill.focus();
            return false;
        }

        // Final content synchronization with enhanced error handling
        try {
            const contentTextarea = document.getElementById('content');
            if (contentTextarea) {
                contentTextarea.value = content;
            } else {
                throw new Error('Content textarea not found');
            }
        } catch (error) {
            e.preventDefault();
            showUserFriendlyAlert('Synchronization Error', 'Failed to prepare content for submission. Please try again.');
            return false;
        }

        // Ensure status field is properly set based on clicked button
        // This is a critical fix for "The status field is required" validation error
        try {
            const statusField = document.getElementById('status');
            if (statusField && submitBtn) {
                // Determine status based on button clicked
                const isDraft = (submitBtn === saveDraftBtn) || (submitBtn && submitBtn.value === 'draft');
                statusField.value = isDraft ? 'draft' : 'published';
            }
        } catch (error) {
            e.preventDefault();
            showUserFriendlyAlert('Status Error', 'Failed to set post status. Please try again.');
            return false;
        }

        // Show loading state with enhanced visual feedback
        document.body.classList.add('form-submitting');

        // Determine action based on button clicked
        const isDraft = (submitBtn === saveDraftBtn) || (submitBtn && submitBtn.value === 'draft');

        if (isDraft) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving Draft...';
            showSubmissionOverlay('Saving your draft...');
        } else {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Publishing...';
            showSubmissionOverlay('Publishing your post...');
        }

        submitBtn.disabled = true;

        // Disable both buttons to prevent double submission
        saveDraftBtn.disabled = true;
        publishBtn.disabled = true;


        // Add a timeout to re-enable buttons if submission fails or takes too long
        setTimeout(function() {
            if (saveDraftBtn.disabled || publishBtn.disabled) {
                // Re-enable both buttons
                saveDraftBtn.disabled = false;
                publishBtn.disabled = false;

                // Restore original button text
                saveDraftBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save as Draft';
                publishBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Publish Post';

                // Hide overlay and remove form submitting class
                hideSubmissionOverlay();

                // Show timeout message
                showUserFriendlyAlert('Submission Timeout', 'The form submission is taking longer than expected. Please try again.');
            }
        }, 15000); // 15 second timeout
    });

    /**
     * Show user-friendly alert with better styling than browser default
     */
    function showUserFriendlyAlert(title, message) {
        // Remove any existing alert
        const existingAlert = document.getElementById('customAlert');
        if (existingAlert) {
            existingAlert.remove();
        }

        // Create custom alert
        const alertDiv = document.createElement('div');
        alertDiv.id = 'customAlert';
        alertDiv.className = 'position-fixed top-50 start-50 translate-middle';
        alertDiv.style.zIndex = '9999';
        alertDiv.innerHTML = `
            <div class="alert alert-warning alert-dismissible shadow-lg" role="alert" style="min-width: 300px;">
                <h5 class="alert-heading">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ${title}
                </h5>
                <p class="mb-0">${message}</p>
                <button type="button" class="btn-close" onclick="document.getElementById('customAlert').remove()"></button>
            </div>
        `;

        document.body.appendChild(alertDiv);

        // Auto-remove after 5 seconds
        setTimeout(function() {
            if (document.getElementById('customAlert')) {
                document.getElementById('customAlert').remove();
            }
        }, 5000);
    }

    /**
     * Show submission overlay with message
     */
    function showSubmissionOverlay(message) {
        // Remove existing overlay if any
        const existingOverlay = document.getElementById('submissionOverlay');
        if (existingOverlay) {
            existingOverlay.remove();
        }

        // Create new overlay
        const overlay = document.createElement('div');
        overlay.id = 'submissionOverlay';
        overlay.className = 'submission-overlay';
        overlay.style.display = 'flex';
        overlay.innerHTML = `
            <div class="submission-message">
                <div class="mb-3">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                </div>
                <h5>${message}</h5>
                <p class="text-muted mb-0">Please wait...</p>
            </div>
        `;

        document.body.appendChild(overlay);

        // Auto-hide after 8 seconds as fallback
        setTimeout(function() {
            hideSubmissionOverlay();
        }, 8000);
    }

    /**
     * Hide submission overlay
     */
    function hideSubmissionOverlay() {
        const overlay = document.getElementById('submissionOverlay');
        if (overlay) {
            overlay.remove();
        }
        document.body.classList.remove('form-submitting');
    }

    // Hide overlay when page unloads (form submission successful)
    window.addEventListener('beforeunload', function() {
        hideSubmissionOverlay();
    });
});

// Featured image preview
function previewFeaturedImage(input) {
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}
</script>
<?= $this->endSection() ?>
