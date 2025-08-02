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
                        <textarea id="content" name="content" style="display: none;" required><?= old('content') ?></textarea>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Quill.js Rich Text Editor -->
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
// Initialize Quill Rich Text Editor
const quill = new Quill('#quill-editor', {
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

// Set initial content if available
const initialContent = document.getElementById('content').value;
if (initialContent) {
    quill.root.innerHTML = initialContent;
}

// Blog form functionality
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('blogForm');
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    const saveDraftBtn = document.getElementById('saveDraftBtn');
    const publishBtn = document.getElementById('publishBtn');
    
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
    
    // Form submission handling
    form.addEventListener('submit', function(e) {
        const submitBtn = e.submitter;
        
        if (submitBtn === saveDraftBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving Draft...';
        } else if (submitBtn === publishBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Publishing...';
        }

        submitBtn.disabled = true;

        // Update hidden textarea with Quill content
        document.getElementById('content').value = quill.root.innerHTML;
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
