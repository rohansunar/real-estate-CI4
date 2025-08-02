<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-4">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Blog Management</h2>
            <p class="text-muted">Manage your blog posts and content</p>
        </div>
        <div class="mt-3 mt-sm-0">
            <a href="<?= base_url('dashboard/blog/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Create New Post
            </a>
        </div>
    </div>
</div>

<!-- Alert Messages -->
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

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card card-stats border-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="icon-stat bg-primary-light">
                            <i class="fas fa-blog"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted mb-1 small fw-medium">Total Posts</p>
                        <p class="h3 fw-bold mb-0"><?= number_format($statistics['total'] ?? 0) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-stats border-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="icon-stat bg-success-light">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted mb-1 small fw-medium">Published</p>
                        <p class="h3 fw-bold mb-0"><?= number_format($statistics['published'] ?? 0) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-stats border-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="icon-stat bg-warning-light">
                            <i class="fas fa-edit"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted mb-1 small fw-medium">Drafts</p>
                        <p class="h3 fw-bold mb-0"><?= number_format($statistics['draft'] ?? 0) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-stats border-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="icon-stat bg-info-light">
                            <i class="fas fa-calendar-week"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted mb-1 small fw-medium">This Week</p>
                        <p class="h3 fw-bold mb-0"><?= number_format($statistics['recent'] ?? 0) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Blog Posts Table -->
<div class="card">
    <div class="card-header border-bottom">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="card-title mb-1">Blog Posts</h5>
                <p class="card-text text-muted small mb-0">All blog posts and their status</p>
            </div>
            <div class="text-muted small">
                Showing <?= count($posts) ?> posts
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Post</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($posts)): ?>
                        <?php foreach ($posts as $post): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <?php if ($post['featured_image']): ?>
                                                <img src="<?= base_url($post['featured_image']) ?>" 
                                                     alt="<?= esc($post['title']) ?>" 
                                                     class="rounded" 
                                                     style="width: 3rem; height: 3rem; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                     style="width: 3rem; height: 3rem;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ms-3">
                                            <div class="fw-medium"><?= esc($post['title']) ?></div>
                                            <div class="text-muted small">
                                                <?= $post['excerpt'] ? esc(substr($post['excerpt'], 0, 60)) . '...' : 'No excerpt' ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($post['category']): ?>
                                        <span class="badge bg-secondary"><?= esc($post['category']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">Uncategorized</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = [
                                        'published' => 'success',
                                        'draft' => 'warning',
                                        'archived' => 'secondary'
                                    ];
                                    $statusIcon = [
                                        'published' => 'check-circle',
                                        'draft' => 'edit',
                                        'archived' => 'archive'
                                    ];
                                    ?>
                                    <span class="badge bg-<?= $statusClass[$post['status']] ?>">
                                        <i class="fas fa-<?= $statusIcon[$post['status']] ?> me-1"></i>
                                        <?= ucfirst($post['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-medium"><?= number_format($post['views']) ?></span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= date('M j, Y', strtotime($post['created_at'])) ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-end">
                                        <div class="btn-group btn-group-sm">
                                            <?php if ($post['status'] === 'published'): ?>
                                                <a href="<?= base_url('blog/' . $post['slug']) ?>" 
                                                   class="btn btn-ghost"
                                                   title="View Post"
                                                   target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?= base_url('dashboard/blog/edit/' . $post['id']) ?>" 
                                               class="btn btn-ghost"
                                               title="Edit Post">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="deletePost(<?= $post['id'] ?>)" 
                                                    class="btn btn-ghost text-danger"
                                                    title="Delete Post">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-blog text-muted mb-3" style="font-size: 4rem;"></i>
                                    <h5 class="text-dark mb-2">No blog posts found</h5>
                                    <p class="text-muted">Start by creating your first blog post.</p>
                                    <a href="<?= base_url('dashboard/blog/create') ?>" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>
                                        Create First Post
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Blog management functionality
function deletePost(postId) {
    const modalHtml = `
        <div class="modal fade" id="deletePostModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Blog Post</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center py-3">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 4rem; height: 4rem;">
                                <i class="fas fa-exclamation-triangle text-danger fs-4"></i>
                            </div>
                            <h6 class="mb-2">Are you sure you want to delete this blog post?</h6>
                            <p class="text-muted small mb-0">This action cannot be undone. The post and all its data will be permanently removed.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" onclick="confirmDeletePost(${postId})">
                            <i class="fas fa-trash me-2"></i>Delete Post
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('deletePostModal'));
    modal.show();

    document.getElementById('deletePostModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

function confirmDeletePost(postId) {
    const modal = bootstrap.Modal.getInstance(document.getElementById('deletePostModal'));
    modal.hide();

    fetch(`<?= base_url('dashboard/blog/') ?>${postId}`, {
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
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error deleting blog post', 'danger');
    });
}
</script>
<?= $this->endSection() ?>
