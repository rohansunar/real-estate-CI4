<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<section class="bg-light py-3" style="margin-top: 76px;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="<?= base_url() ?>" class="text-decoration-none">
                        <i class="fas fa-home me-1"></i>Home
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= base_url('blog') ?>" class="text-decoration-none">Blog</a>
                </li>
                <?php if ($post['category']): ?>
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('blog/category/' . urlencode($post['category'])) ?>" class="text-decoration-none">
                            <?= esc($post['category']) ?>
                        </a>
                    </li>
                <?php endif; ?>
                <li class="breadcrumb-item active" aria-current="page">
                    <?= esc($post['title']) ?>
                </li>
            </ol>
        </nav>
    </div>
</section>

<!-- Blog Post Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <article class="blog-post">
                    <!-- Post Header -->
                    <header class="mb-4">
                        <h1 class="display-5 fw-bold text-dark mb-3"><?= esc($post['title']) ?></h1>
                        
                        <!-- Post Meta -->
                        <div class="d-flex flex-wrap align-items-center text-muted mb-4">
                            <div class="me-4 mb-2">
                                <i class="fas fa-calendar-alt me-2"></i>
                                <?= date('F j, Y', strtotime($post['published_at'])) ?>
                            </div>
                            <?php if ($post['category']): ?>
                                <div class="me-4 mb-2">
                                    <i class="fas fa-folder me-2"></i>
                                    <a href="<?= base_url('blog/category/' . urlencode($post['category'])) ?>" 
                                       class="text-decoration-none">
                                        <?= esc($post['category']) ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="me-4 mb-2">
                                <i class="fas fa-eye me-2"></i>
                                <?= number_format($post['views']) ?> views
                            </div>
                        </div>
                        
                        <!-- Featured Image -->
                        <?php if ($post['featured_image']): ?>
                            <div class="mb-4">
                                <img src="<?= base_url($post['featured_image']) ?>" 
                                     alt="<?= esc($post['title']) ?>"
                                     class="img-fluid rounded shadow-sm">
                            </div>
                        <?php endif; ?>
                    </header>
                    
                    <!-- Post Content -->
                    <div class="post-content">
                        <?= $post['content'] ?>
                    </div>
                    
                    <!-- Tags -->
                    <?php if ($post['tags']): ?>
                        <div class="mt-4 pt-4 border-top">
                            <h6 class="text-muted mb-3">Tags:</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <?php 
                                $tags = explode(',', $post['tags']);
                                foreach ($tags as $tag): 
                                    $tag = trim($tag);
                                    if ($tag):
                                ?>
                                    <span class="badge bg-light text-dark border">
                                        <i class="fas fa-tag me-1"></i>
                                        <?= esc($tag) ?>
                                    </span>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Share Buttons -->
                    <div class="mt-4 pt-4 border-top">
                        <h6 class="text-muted mb-3">Share this post:</h6>
                        <div class="d-flex gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(current_url()) ?>" 
                               target="_blank" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="fab fa-facebook-f me-1"></i>
                                Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?= urlencode(current_url()) ?>&text=<?= urlencode($post['title']) ?>" 
                               target="_blank" 
                               class="btn btn-outline-info btn-sm">
                                <i class="fab fa-twitter me-1"></i>
                                Twitter
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode(current_url()) ?>" 
                               target="_blank" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="fab fa-linkedin-in me-1"></i>
                                LinkedIn
                            </a>
                            <a href="https://wa.me/?text=<?= urlencode($post['title'] . ' - ' . current_url()) ?>" 
                               target="_blank" 
                               class="btn btn-outline-success btn-sm">
                                <i class="fab fa-whatsapp me-1"></i>
                                WhatsApp
                            </a>
                        </div>
                    </div>
                </article>
                
                <!-- Related Posts -->
                <?php if (!empty($relatedPosts)): ?>
                    <section class="mt-5 pt-5 border-top">
                        <h3 class="mb-4">Related Posts</h3>
                        <div class="row g-4">
                            <?php foreach ($relatedPosts as $relatedPost): ?>
                                <div class="col-md-4">
                                    <div class="card h-100 shadow-sm border-0">
                                        <?php if ($relatedPost['featured_image']): ?>
                                            <div class="card-img-top-wrapper" style="height: 150px; overflow: hidden;">
                                                <img src="<?= base_url($relatedPost['featured_image']) ?>" 
                                                     class="card-img-top" 
                                                     alt="<?= esc($relatedPost['title']) ?>"
                                                     style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                        <?php endif; ?>
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <a href="<?= base_url('blog/' . $relatedPost['slug']) ?>" 
                                                   class="text-decoration-none text-dark">
                                                    <?= esc($relatedPost['title']) ?>
                                                </a>
                                            </h6>
                                            <small class="text-muted">
                                                <?= date('M j, Y', strtotime($relatedPost['published_at'])) ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
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
                                <?php if ($recentPost['id'] != $post['id']): // Don't show current post ?>
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
                                <?php endif; ?>
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
.post-content {
    font-size: 1.1rem;
    line-height: 1.8;
}

.post-content h2,
.post-content h3,
.post-content h4 {
    margin-top: 2rem;
    margin-bottom: 1rem;
    color: #2c3e50;
}

.post-content p {
    margin-bottom: 1.5rem;
}

.post-content ul,
.post-content ol {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}

.post-content li {
    margin-bottom: 0.5rem;
}

.post-content blockquote {
    border-left: 4px solid var(--bs-primary);
    padding-left: 1.5rem;
    margin: 2rem 0;
    font-style: italic;
    color: #6c757d;
}

.post-content img {
    max-width: 100%;
    height: auto;
    border-radius: 0.375rem;
    margin: 1.5rem 0;
}

.post-content code {
    background-color: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.9em;
}

.post-content pre {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.375rem;
    overflow-x: auto;
    margin: 1.5rem 0;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll for anchor links within the post
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
