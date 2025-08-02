<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="position-relative py-5 bg-primary text-white" style="margin-top: 76px;">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-4">
                    Search Results
                </h1>
                <p class="fs-5 mb-0">
                    <?php if (!empty($searchQuery)): ?>
                        Found <?= $totalPosts ?> result<?= $totalPosts != 1 ? 's' : '' ?> for "<?= esc($searchQuery) ?>"
                    <?php else: ?>
                        Search our blog posts
                    <?php endif; ?>
                </p>
            </div>
            <div class="col-lg-4">
                <div class="text-center">
                    <i class="fas fa-search display-1 text-white opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Search Results Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Search Form -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form action="<?= base_url('blog/search') ?>" method="get">
                            <div class="input-group input-group-lg">
                                <input type="text" 
                                       class="form-control" 
                                       name="q" 
                                       placeholder="Search blog posts..."
                                       value="<?= esc($searchQuery ?? '') ?>"
                                       required>
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search me-2"></i>
                                    Search
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
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
                                            <?php if ($post['category']): ?>
                                                <span class="badge bg-primary"><?= esc($post['category']) ?></span>
                                            <?php endif; ?>
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
                    
                <?php elseif (!empty($searchQuery)): ?>
                    <!-- No Results -->
                    <div class="text-center py-5">
                        <i class="fas fa-search display-1 text-muted mb-4"></i>
                        <h3 class="text-muted">No results found</h3>
                        <p class="text-muted">
                            We couldn't find any posts matching "<?= esc($searchQuery) ?>". 
                            Try different keywords or browse our categories.
                        </p>
                        <div class="mt-4">
                            <a href="<?= base_url('blog') ?>" class="btn btn-primary me-2">
                                <i class="fas fa-arrow-left me-2"></i>
                                Back to All Posts
                            </a>
                            <button type="button" class="btn btn-outline-primary" onclick="document.querySelector('input[name=q]').focus()">
                                <i class="fas fa-search me-2"></i>
                                Try Another Search
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Search Tips -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-lightbulb me-2 text-warning"></i>
                                Search Tips
                            </h5>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Use specific keywords related to real estate
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Try searching for location names like "Siliguri" or "Champasari"
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Search for property types like "apartment", "villa", or "investment"
                                </li>
                                <li class="mb-0">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Use simple terms for better results
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Popular Searches -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-fire me-2"></i>
                            Popular Searches
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="<?= base_url('blog/search?q=investment') ?>" class="badge bg-light text-dark text-decoration-none">investment</a>
                            <a href="<?= base_url('blog/search?q=siliguri') ?>" class="badge bg-light text-dark text-decoration-none">siliguri</a>
                            <a href="<?= base_url('blog/search?q=property') ?>" class="badge bg-light text-dark text-decoration-none">property</a>
                            <a href="<?= base_url('blog/search?q=apartment') ?>" class="badge bg-light text-dark text-decoration-none">apartment</a>
                            <a href="<?= base_url('blog/search?q=villa') ?>" class="badge bg-light text-dark text-decoration-none">villa</a>
                            <a href="<?= base_url('blog/search?q=market') ?>" class="badge bg-light text-dark text-decoration-none">market</a>
                            <a href="<?= base_url('blog/search?q=tips') ?>" class="badge bg-light text-dark text-decoration-none">tips</a>
                            <a href="<?= base_url('blog/search?q=legal') ?>" class="badge bg-light text-dark text-decoration-none">legal</a>
                        </div>
                    </div>
                </div>
                
                <!-- Categories -->
                <?php if (!empty($categories)): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-folder me-2"></i>
                                Browse Categories
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php foreach ($categories as $category): ?>
                                <a href="<?= base_url('blog/category/' . urlencode($category['category'])) ?>" 
                                   class="d-block text-decoration-none py-1">
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

.badge:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease;
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
