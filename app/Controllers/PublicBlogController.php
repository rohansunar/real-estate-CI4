<?php

namespace App\Controllers;

use App\Models\BlogPostModel;

/**
 * PublicBlogController
 *
 * Handles public-facing blog functionality for website visitors.
 * This controller manages:
 * - Blog post listing with pagination
 * - Individual blog post display
 * - Category-based post filtering
 * - Blog post search functionality
 * - SEO-optimized URLs and metadata
 *
 * Key Features:
 * - Responsive card-based layout
 * - Bootstrap 5 pagination
 * - Category and tag filtering
 * - Search functionality with highlighting
 * - Social media sharing integration
 * - View count tracking
 * - Related posts suggestions
 *
 * @author White Rock Realtor Team
 * @version 2.0 - Enhanced with full public blog functionality
 * @since 2025-08-02
 */
class PublicBlogController extends BaseController
{
    protected $blogModel;

    public function __construct()
    {
        $this->blogModel = new BlogPostModel();
    }

    /**
     * Display blog listing page
     */
    public function index()
    {
        $perPage = 9; // Number of posts per page
        $page = (int) ($this->request->getGet('page') ?? 1);
        $offset = ($page - 1) * $perPage;

        // Get published posts with pagination
        $posts = $this->blogModel->getPublished($perPage, $offset);
        $totalPosts = $this->blogModel->where('status', 'published')
                                    ->where('published_at <=', date('Y-m-d H:i:s'))
                                    ->countAllResults(false);

        // Get categories for sidebar
        $categories = $this->blogModel->getCategories();
        
        // Get recent posts for sidebar
        $recentPosts = $this->blogModel->getRecent(5);

        $data = [
            'title' => 'Blog | White Rock Realtor Insights',
            'posts' => $posts,
            'categories' => $categories,
            'recentPosts' => $recentPosts,
            'currentPage' => $page,
            'totalPages' => ceil($totalPosts / $perPage),
            'totalPosts' => $totalPosts
        ];

        return view('blog/index', $data);
    }

    /**
     * Display single blog post
     */
    public function single($slug)
    {
        $post = $this->blogModel->where('slug', $slug)
                              ->where('status', 'published')
                              ->where('published_at <=', date('Y-m-d H:i:s'))
                              ->first();

        if (!$post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Increment view count
        $this->blogModel->incrementViews($post['id']);

        // Get related posts
        $relatedPosts = $this->blogModel->getRelated($post['id'], $post['category'], 3);

        // Get categories for sidebar
        $categories = $this->blogModel->getCategories();
        
        // Get recent posts for sidebar
        $recentPosts = $this->blogModel->getRecent(5);

        $data = [
            'title' => $post['meta_title'] ?: $post['title'] . ' | Blog',
            'metaDescription' => $post['meta_description'] ?: $post['excerpt'],
            'post' => $post,
            'relatedPosts' => $relatedPosts,
            'categories' => $categories,
            'recentPosts' => $recentPosts
        ];

        return view('blog/single', $data);
    }

    /**
     * Display posts by category
     */
    public function category($category)
    {
        $perPage = 9;
        $page = (int) ($this->request->getGet('page') ?? 1);
        $offset = ($page - 1) * $perPage;

        // Get posts by category with pagination
        $posts = $this->blogModel->where('category', $category)
                               ->where('status', 'published')
                               ->where('published_at <=', date('Y-m-d H:i:s'))
                               ->orderBy('published_at', 'DESC')
                               ->limit($perPage, $offset)
                               ->findAll();

        $totalPosts = $this->blogModel->where('category', $category)
                                    ->where('status', 'published')
                                    ->where('published_at <=', date('Y-m-d H:i:s'))
                                    ->countAllResults(false);

        // Get categories for sidebar
        $categories = $this->blogModel->getCategories();
        
        // Get recent posts for sidebar
        $recentPosts = $this->blogModel->getRecent(5);

        $data = [
            'title' => ucfirst($category) . ' | Blog',
            'posts' => $posts,
            'categories' => $categories,
            'recentPosts' => $recentPosts,
            'currentCategory' => $category,
            'currentPage' => $page,
            'totalPages' => ceil($totalPosts / $perPage),
            'totalPosts' => $totalPosts
        ];

        return view('blog/category', $data);
    }

    /**
     * Search blog posts
     */
    public function search()
    {
        $query = trim($this->request->getGet('q') ?? '');
        
        if (empty($query)) {
            return redirect()->to('/blog');
        }

        $posts = $this->blogModel->searchPosts($query, 20);

        // Get categories for sidebar
        $categories = $this->blogModel->getCategories();
        
        // Get recent posts for sidebar
        $recentPosts = $this->blogModel->getRecent(5);

        $data = [
            'title' => 'Search Results for "' . esc($query) . '" | Blog',
            'posts' => $posts,
            'categories' => $categories,
            'recentPosts' => $recentPosts,
            'searchQuery' => $query,
            'totalPosts' => count($posts)
        ];

        return view('blog/search', $data);
    }
}
