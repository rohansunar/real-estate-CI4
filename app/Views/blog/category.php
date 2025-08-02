<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="position-relative py-5 bg-primary text-white" style="margin-top: 76px;">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-4">
                    <?= esc(ucfirst($currentCategory)) ?> Posts
                </h1>
                <p class="fs-5 mb-0">
                    Explore all posts in the <?= esc($currentCategory) ?> category.
                </p>
            </div>
            <div class="col-lg-4">
                <div class="text-center">
                    <i class="fas fa-folder-open display-1 text-white opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Blog Posts Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <?php if (!empty($posts)): ?>
                    <div class="row g-4">
                        <?php foreach ($posts as $post): ?>
                            <div class="col-md-6">
                                <article class="card h-100 shadow-sm border-0 blog-card">
                                    <?php if ($post['featured_image']): ?>
                                        <div class="card-img-top-wrapper" style="height: 200px; overflow: hidden;">
                                            <img src="<?= base_url($post['featured_image']) ?>" 
                                                 class="card-img-top" 
                                                 alt="<?= esc($post['title']) ?>"
                                                 style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="card-body d-flex flex-column">
                                        <!-- Category and Date -->
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-primary"><?= esc($post['category']) ?></span>
                                            <small class="text-muted">
                                                <?= date('M j, Y', strtotime($post['published_at'])) ?>
                                            </small>
                                        </div>
                                        
                                        <!-- Title -->
                                        <h5 class="card-title">
                                            <a href="<?= base_url('blog/' . $post['slug']) ?>" 
                                               class="text-decoration-none text-dark">
                                                <?= esc($post['title']) ?>
                                            </a>
                                        </h5>
                                        
                                        <!-- Excerpt -->
                                        <p class="card-text text-muted flex-grow-1">
                                            <?= esc($post['excerpt'] ?: substr(strip_tags($post['content']), 0, 120) . '...') ?>
                                        </p>
                                        
                                        <!-- Footer -->
                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                            <a href="<?= base_url('blog/' . $post['slug']) ?>" 
                                               class="btn btn-outline-primary btn-sm">
                                                Read More
                                            </a>
                                            <small class="text-muted">
                                                <i class="fas fa-eye me-1"></i>
                                                <?= number_format($post['views']) ?> views
                                            </small>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Blog pagination" class="mt-5">
                            <ul class="pagination justify-content-center">
                                <!-- Previous Button -->
                                <?php if ($currentPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= base_url('blog/category/' . urlencode($currentCategory) . '?page=' . ($currentPage - 1)) ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php else: ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <i class="fas fa-chevron-left"></i>
                                        </span>
                                    </li>
                                <?php endif; ?>

                                <!-- Page Numbers -->
                                <?php
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($totalPages, $currentPage + 2);
                                
                                // Show first page if not in range
                                if ($startPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= base_url('blog/category/' . urlencode($currentCategory) . '?page=1') ?>">1</a>
                                    </li>
                                    <?php if ($startPage > 2): ?>
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <!-- Current page range -->
                                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                        <?php if ($i == $currentPage): ?>
                                            <span class="page-link"><?= $i ?></span>
                                        <?php else: ?>
                                            <a class="page-link" href="<?= base_url('blog/category/' . urlencode($currentCategory) . '?page=' . $i) ?>"><?= $i ?></a>
                                        <?php endif; ?>
                                    </li>
                                <?php endfor; ?>

                                <!-- Show last page if not in range -->
                                <?php if ($endPage < $totalPages): ?>
                                    <?php if ($endPage < $totalPages - 1): ?>
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= base_url('blog/category/' . urlencode($currentCategory) . '?page=' . $totalPages) ?>"><?= $totalPages ?></a>
                                    </li>
                                <?php endif; ?>

                                <!-- Next Button -->
                                <?php if ($currentPage < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= base_url('blog/category/' . urlencode($currentCategory) . '?page=' . ($currentPage + 1)) ?>">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php else: ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <i class="fas fa-chevron-right"></i>
                                        </span>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                        
                        <!-- Pagination Info -->
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                Showing page <?= $currentPage ?> of <?= $totalPages ?> 
                                (<?= $totalPosts ?> posts in <?= esc($currentCategory) ?>)
                            </small>
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <!-- No Posts -->
                    <div class="text-center py-5">
                        <i class="fas fa-folder-open display-1 text-muted mb-4"></i>
                        <h3 class="text-muted">No posts found in this category</h3>
                        <p class="text-muted">Check back later for new content!</p>
                        <a href="<?= base_url('blog') ?>" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Back to All Posts
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Search -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-search me-2"></i>
                            Search Blog
                        </h6>
                    </div>
                    <div class="card-body">
                        <form action="<?= base_url('blog/search') ?>" method="get">
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control" 
                                       name="q" 
                                       placeholder="Search posts...">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Categories -->
                <?php if (!empty($categories)): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-folder me-2"></i>
                                Categories
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php foreach ($categories as $category): ?>
                                <a href="<?= base_url('blog/category/' . urlencode($category['category'])) ?>" 
                                   class="d-block text-decoration-none py-1 <?= $category['category'] == $currentCategory ? 'fw-bold text-primary' : '' ?>">
                                    <?= esc($category['category']) ?>
                                    <span class="badge bg-light text-dark ms-2"><?= $category['count'] ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Recent Posts -->
                <?php if (!empty($recentPosts)): ?>
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-clock me-2"></i>
                                Recent Posts
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php foreach ($recentPosts as $recentPost): ?>
                                <div class="d-flex mb-3">
                                    <?php if ($recentPost['featured_image']): ?>
                                        <img src="<?= base_url($recentPost['featured_image']) ?>" 
                                             alt="<?= esc($recentPost['title']) ?>"
                                             class="rounded me-3"
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    <?php endif; ?>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <a href="<?= base_url('blog/' . $recentPost['slug']) ?>" 
                                               class="text-decoration-none text-dark">
                                                <?= esc($recentPost['title']) ?>
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            <?= date('M j, Y', strtotime($recentPost['published_at'])) ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.blog-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.blog-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.blog-card .card-title a:hover {
    color: var(--bs-primary) !important;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Add blog-specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Animate blog cards on scroll
    const blogCards = document.querySelectorAll('.blog-card');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.transform = 'translateY(0)';
                    entry.target.style.opacity = '1';
                }, index * 100);
            }
        });
    }, { threshold: 0.1 });
    
    blogCards.forEach(card => {
        card.style.transform = 'translateY(30px)';
        card.style.opacity = '0';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });
});
</script>
<?= $this->endSection() ?>
