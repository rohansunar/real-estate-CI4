<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-4">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Edit Blog Post</h2>
            <p class="text-muted">Update your blog post content and settings</p>
        </div>
        <div class="mt-3 mt-sm-0">
            <a href="<?= base_url('dashboard/blog') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Blog
            </a>
        </div>
    </div>
</div>

<!-- Blog Edit Form -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-edit me-2 text-primary"></i>
                    Edit Post Content
                </h5>
            </div>
            <div class="card-body">
                <!-- Display Errors -->
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Please fix the following errors:
                        </h6>
                        <ul class="mb-0">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form id="blogEditForm" action="<?= base_url('dashboard/blog/edit/' . $post['id']) ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <!-- Hidden fields for sidebar data -->
                    <input type="hidden" id="hiddenCategory" name="category" value="<?= old('category', $post['category']) ?>">
                    <input type="hidden" id="hiddenTags" name="tags" value="<?= old('tags', $post['tags']) ?>">
                    <input type="hidden" id="hiddenExcerpt" name="excerpt" value="<?= old('excerpt', $post['excerpt']) ?>">
                    <input type="hidden" id="hiddenMetaTitle" name="meta_title" value="<?= old('meta_title', $post['meta_title']) ?>">
                    <input type="hidden" id="hiddenMetaDescription" name="meta_description" value="<?= old('meta_description', $post['meta_description']) ?>">
                    
                    <!-- Title -->
                    <div class="mb-4">
                        <label for="title" class="form-label fw-medium">Post Title <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control <?= isset(session()->getFlashdata('errors')['title']) ? 'is-invalid' : '' ?>" 
                               id="title" 
                               name="title" 
                               value="<?= old('title', $post['title']) ?>"
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
                               value="<?= old('slug', $post['slug']) ?>"
                               placeholder="auto-generated-from-title">
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Leave empty to auto-generate from title. Use lowercase letters, numbers, and hyphens only.
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
                        <textarea id="content" name="content" style="display: none;"><?= old('content', $post['content']) ?></textarea>
                        <!--
                            CRITICAL FIX: Hidden status field to ensure proper form submission

                            This field prevents the "The status field is required" validation error
                            during blog post editing. The field is updated by JavaScript based on
                            which button is clicked (Save as Draft vs Update & Publish).

                            Default value preserves the current post status for seamless editing.
                        -->
                        <input type="hidden" id="status" name="status" value="<?= old('status', $post['status']) ?>">
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Use the rich text editor to format your content with headings, lists, links, and more.
                        </div>
                    </div>

                    <!-- Featured Image -->
                    <div class="mb-4">
                        <label for="featured_image" class="form-label fw-medium">Featured Image</label>
                        <?php if ($post['featured_image']): ?>
                            <div class="mb-3">
                                <img src="<?= base_url($post['featured_image']) ?>" alt="Current featured image" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                <div class="form-text">Current featured image</div>
                            </div>
                        <?php endif; ?>
                        <input type="file" 
                               class="form-control <?= isset(session()->getFlashdata('errors')['featured_image']) ? 'is-invalid' : '' ?>" 
                               id="featured_image" 
                               name="featured_image"
                               accept="image/*"
                               onchange="previewFeaturedImage(this)">
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Upload a new image to replace the current one. Maximum file size: 2MB.
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
                                Update & Publish
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-cog me-2 text-primary"></i>
                    Post Settings
                </h6>
            </div>
            <div class="card-body">
                    <!-- Category -->
                    <div class="mb-3">
                        <label for="category" class="form-label fw-medium">Category</label>
                        <input type="text"
                               class="form-control"
                               id="category"
                               value="<?= old('category', $post['category']) ?>"
                               placeholder="e.g., Real Estate Tips">
                        <div class="form-text">Optional category for organizing posts</div>
                    </div>

                    <!-- Tags -->
                    <div class="mb-3">
                        <label for="tags" class="form-label fw-medium">Tags</label>
                        <input type="text"
                               class="form-control"
                               id="tags"
                               value="<?= old('tags', $post['tags']) ?>"
                               placeholder="property, investment, tips">
                        <div class="form-text">Comma-separated tags for better discoverability</div>
                    </div>

                    <!-- Excerpt -->
                    <div class="mb-3">
                        <label for="excerpt" class="form-label fw-medium">Excerpt</label>
                        <textarea class="form-control"
                                  id="excerpt"
                                  rows="3"
                                  placeholder="Brief summary of the post..."><?= old('excerpt', $post['excerpt']) ?></textarea>
                        <div class="form-text">Optional short description for post previews</div>
                    </div>

                    <!-- SEO Settings -->
                    <div class="mb-3">
                        <label for="meta_title" class="form-label fw-medium">SEO Title</label>
                        <input type="text"
                               class="form-control"
                               id="meta_title"
                               value="<?= old('meta_title', $post['meta_title']) ?>"
                               placeholder="SEO optimized title">
                        <div class="form-text">Leave empty to use post title</div>
                    </div>

                    <div class="mb-3">
                        <label for="meta_description" class="form-label fw-medium">SEO Description</label>
                        <textarea class="form-control"
                                  id="meta_description"
                                  rows="3"
                                  placeholder="SEO meta description..."><?= old('meta_description', $post['meta_description']) ?></textarea>
                        <div class="form-text">Brief description for search engines</div>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">Current Status</label>
                        <div class="d-flex align-items-center">
                            <?php
                            $statusClass = [
                                'draft' => 'warning',
                                'published' => 'success',
                                'archived' => 'secondary'
                            ];
                            $statusIcon = [
                                'draft' => 'edit',
                                'published' => 'check-circle',
                                'archived' => 'archive'
                            ];
                            ?>
                            <span class="badge bg-<?= $statusClass[$post['status']] ?> me-2">
                                <i class="fas fa-<?= $statusIcon[$post['status']] ?> me-1"></i>
                                <?= ucfirst($post['status']) ?>
                            </span>
                            <?php if ($post['status'] === 'published' && $post['published_at']): ?>
                                <small class="text-muted">
                                    Published on <?= date('M j, Y', strtotime($post['published_at'])) ?>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Views -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">Views</label>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-eye text-muted me-2"></i>
                            <span class="fw-medium"><?= number_format($post['views']) ?></span>
                            <span class="text-muted ms-1">views</span>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<!-- Quill.js CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Quill.js Rich Text Editor -->
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
/**
 * Initialize Quill Rich Text Editor for Blog Editing with Enhanced Error Handling
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

    // Set initial content if available with error handling
    try {
        const contentTextarea = document.getElementById('content');
        const initialContent = contentTextarea ? contentTextarea.value : '';
        if (initialContent) {
            quill.root.innerHTML = initialContent;
        }
    } catch (error) {
        // Only log in development environment
        if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
            console.warn('Failed to set initial content:', error);
        }
    }

    // Set up real-time content synchronization
    quill.on('text-change', function() {
        try {
            const contentTextarea = document.getElementById('content');
            if (contentTextarea) {
                contentTextarea.value = quill.root.innerHTML;
            }
        } catch (error) {
            // Silent error handling to prevent console spam
        }
    });

} catch (error) {
    // Only log in development environment
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        console.error('Failed to initialize Quill editor:', error);
    }

    const editorContainer = document.getElementById('quill-editor');
    if (editorContainer) {
        editorContainer.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Failed to load rich text editor. Please refresh the page and try again.</div>';
    }
}

// Blog form functionality with enhanced browser compatibility
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('blogEditForm');
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    const saveDraftBtn = document.getElementById('saveDraftBtn');
    const publishBtn = document.getElementById('publishBtn');

    // Track which button was clicked for cross-browser compatibility
    let clickedButton = null;

    // Auto-generate slug from title (only if slug is empty or auto-generated)
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

    // Add click event listeners to track which button was clicked and set status
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

    // Sync sidebar fields with hidden fields
    function syncSidebarFields() {
        document.getElementById('hiddenCategory').value = document.getElementById('category').value;
        document.getElementById('hiddenTags').value = document.getElementById('tags').value;
        document.getElementById('hiddenExcerpt').value = document.getElementById('excerpt').value;
        document.getElementById('hiddenMetaTitle').value = document.getElementById('meta_title').value;
        document.getElementById('hiddenMetaDescription').value = document.getElementById('meta_description').value;
    }

    // Add event listeners to sidebar fields
    ['category', 'tags', 'excerpt', 'meta_title', 'meta_description'].forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', syncSidebarFields);
            field.addEventListener('change', syncSidebarFields);
        }
    });

    // Form submission handling with enhanced error handling and cross-browser compatibility
    form.addEventListener('submit', function(e) {
        const submitBtn = clickedButton || e.submitter || saveDraftBtn;

        // Enhanced form validation with comprehensive error handling
        const title = titleInput ? titleInput.value.trim() : '';
        if (!title) {
            e.preventDefault();
            showUserFriendlyAlert('Missing Title', 'Please enter a blog post title before saving.');
            if (titleInput) titleInput.focus();
            return false;
        }

        // Validate content from Quill editor with enhanced error handling
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

        // Show loading state with user feedback
        if (submitBtn === saveDraftBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving Draft...';
        } else if (submitBtn === publishBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
        }

        submitBtn.disabled = true;

        // Update hidden textarea with Quill content
        document.getElementById('content').value = quill.root.innerHTML;

        // Ensure status field is properly set based on clicked button
        // This prevents "The status field is required" validation error
        try {
            const statusField = document.getElementById('status');
            if (statusField && submitBtn) {
                // Determine status based on button clicked
                const isDraft = (submitBtn === saveDraftBtn) || (submitBtn && submitBtn.value === 'draft');
                statusField.value = isDraft ? 'draft' : 'published';
            }
        } catch (error) {
            showUserFriendlyAlert('Status Error', 'Failed to set post status. Please try again.');
            return false;
        }

        // Sync sidebar fields before submission
        syncSidebarFields();


        // Add a timeout to re-enable button if submission fails
        setTimeout(function() {
            if (submitBtn.disabled) {
                submitBtn.disabled = false;
                if (submitBtn === saveDraftBtn) {
                    submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save as Draft';
                } else if (submitBtn === publishBtn) {
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Update & Publish';
                }
            }
        }, 10000); // 10 second timeout
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
});

// Featured image preview function
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
