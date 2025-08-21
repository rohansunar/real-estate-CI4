<?php

namespace App\Controllers;

use App\Models\BlogPostModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * BlogController
 *
 * Handles blog management functionality in the admin dashboard.
 * This controller provides comprehensive CRUD operations for blog posts including:
 * - Blog post listing with statistics
 * - Blog post creation with Quill.js rich text editor
 * - Blog post editing with content preservation
 * - Featured image upload and management
 * - SEO metadata management
 * - Draft and published status management
 *
 * Key Features:
 * - Replaced TinyMCE with Quill.js (no API key required)
 * - Automatic slug generation from titles
 * - Featured image upload with validation
 * - Rich text content editing
 * - SEO-friendly metadata fields
 *
 * @author White Rock Realtor Team
 * @version 2.0 - Replaced TinyMCE with Quill.js editor
 * @since 2025-08-02
 */

class BlogController extends BaseController
{
    protected $blogModel;

    public function __construct()
    {
        $this->blogModel = new BlogPostModel();
    }

    /**
     * Display blog posts listing for admin
     */
    public function index()
    {
        $posts = $this->blogModel->orderBy('created_at', 'DESC')->findAll();
        $statistics = $this->blogModel->getStatistics();
        
        $data = [
            'title' => 'Blog Management | Dashboard',
            'posts' => $posts,
            'statistics' => $statistics
        ];

        return view('dashboard/blog/index', $data);
    }

    /**
     * Show create blog post form
     */
    public function create()
    {
        $categories = $this->blogModel->getCategories();
        
        $data = [
            'title' => 'Create Blog Post | Dashboard',
            'categories' => $categories
        ];

        return view('dashboard/blog/create', $data);
    }

    /**
     * Store new blog post
     */
    public function store()
    {
        try {
            // Define validation rules
            $validationRules = [
                'title' => 'required|max_length[255]',
                'content' => 'required',
                'status' => 'required|in_list[draft,published,archived]',
                'category' => 'permit_empty|max_length[100]',
                'tags' => 'permit_empty|max_length[500]',
                'excerpt' => 'permit_empty|max_length[1000]',
                'meta_title' => 'permit_empty|max_length[255]',
                'meta_description' => 'permit_empty|max_length[500]',
                'featured_image' => 'permit_empty|is_image[featured_image]|max_size[featured_image,2048]'
            ];

            // Validate the request data
            if (!$this->validate($validationRules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            // Handle featured image upload
            $featuredImagePath = $this->handleImageUpload();

            // Prepare data for insertion
            $data = [
                'title' => trim($this->request->getPost('title')),
                'content' => $this->request->getPost('content'),
                'excerpt' => trim($this->request->getPost('excerpt')) ?: null,
                'category' => trim($this->request->getPost('category')) ?: null,
                'tags' => trim($this->request->getPost('tags')) ?: null,
                'status' => $this->request->getPost('status'),
                'featured_image' => $featuredImagePath,
                'meta_title' => trim($this->request->getPost('meta_title')) ?: null,
                'meta_description' => trim($this->request->getPost('meta_description')) ?: null,
                'author_id' => session()->get('user_id') ?? 1
            ];

            // Generate slug if not provided
            if (empty($this->request->getPost('slug'))) {
                $data['slug'] = $this->blogModel->createSlug($data['title']);
            } else {
                $data['slug'] = $this->blogModel->createSlug($this->request->getPost('slug'));
            }

            // Insert blog post into database
            if ($this->blogModel->insert($data)) {
                return redirect()->to('/dashboard/blog')->with('success', 'Blog post created successfully!');
            } else {
                throw new \Exception('Database insertion failed');
            }

        } catch (\Exception $e) {
            log_message('error', 'Blog post creation failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create blog post. Please try again.');
        }
    }

    /**
     * Show edit blog post form
     */
    public function edit($id)
    {
        $post = $this->blogModel->find($id);

        if (!$post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $categories = $this->blogModel->getCategories();

        $data = [
            'title' => 'Edit Blog Post | Dashboard',
            'post' => $post,
            'categories' => $categories
        ];

        return view('dashboard/blog/edit', $data);
    }

    /**
     * Update blog post
     */
    public function update($id)
    {
        $post = $this->blogModel->find($id);

        if (!$post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        try {
            // Define validation rules
            $validationRules = [
                'title' => 'required|max_length[255]',
                'content' => 'required',
                'status' => 'required|in_list[draft,published,archived]',
                'category' => 'permit_empty|max_length[100]',
                'tags' => 'permit_empty|max_length[500]',
                'excerpt' => 'permit_empty|max_length[1000]',
                'meta_title' => 'permit_empty|max_length[255]',
                'meta_description' => 'permit_empty|max_length[500]',
                'featured_image' => 'permit_empty|is_image[featured_image]|max_size[featured_image,2048]'
            ];

            // Validate the request data
            if (!$this->validate($validationRules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            // Handle new featured image upload if any
            $featuredImagePath = $this->handleImageUpload();
            if (!$featuredImagePath) {
                $featuredImagePath = $post['featured_image']; // Keep existing image
            }

            // Prepare data for update
            $data = [
                'title' => trim($this->request->getPost('title')),
                'content' => $this->request->getPost('content'),
                'excerpt' => trim($this->request->getPost('excerpt')) ?: null,
                'category' => trim($this->request->getPost('category')) ?: null,
                'tags' => trim($this->request->getPost('tags')) ?: null,
                'status' => $this->request->getPost('status'),
                'featured_image' => $featuredImagePath,
                'meta_title' => trim($this->request->getPost('meta_title')) ?: null,
                'meta_description' => trim($this->request->getPost('meta_description')) ?: null
            ];

            // Update slug if title changed
            if ($data['title'] !== $post['title']) {
                $data['slug'] = $this->blogModel->createSlug($data['title']);
            }

            if ($this->blogModel->update($id, $data)) {
                return redirect()->to('/dashboard/blog')->with('success', 'Blog post updated successfully');
            } else {
                throw new \Exception('Database update failed');
            }

        } catch (\Exception $e) {
            log_message('error', 'Blog post update failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update blog post. Please try again.');
        }
    }

    /**
     * Delete blog post
     *
     * Handles blog post deletion with proper cascade delete for featured images.
     * Uses the centralized ImageManagementService for consistent file handling.
     */
    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/dashboard/blog');
        }

        $post = $this->blogModel->find($id);

        if (!$post) {
            return $this->response->setJSON(['success' => false, 'message' => 'Blog post not found']);
        }

        // Delete blog post from database first
        if ($this->blogModel->delete($id)) {
            // Delete featured image using centralized service
            if (!empty($post['featured_image'])) {
                $imageService = new \App\Services\ImageManagementService();
                $deletionResult = $imageService->deleteImage($post['featured_image']);

                if (!$deletionResult['success']) {
                    // Log the error but don't fail the blog post deletion
                    log_message('warning', "Failed to delete blog featured image: {$post['featured_image']}. Error: {$deletionResult['message']}");
                }
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Blog post deleted successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete blog post']);
        }
    }

    /**
     * Handle featured image upload
     *
     * Uses centralized ImageManagementService for consistent file handling
     * and unified storage location.
     */
    private function handleImageUpload()
    {
        $image = $this->request->getFile('featured_image');

        if ($image && $image->isValid() && !$image->hasMoved()) {
            try {
                // Use centralized image management service
                $imageService = new \App\Services\ImageManagementService();
                $uploadResult = $imageService->uploadImage($image, 'blog');

                if ($uploadResult['success']) {
                    return $uploadResult['file_path'];
                } else {
                    log_message('error', 'Blog image upload failed: ' . $uploadResult['message']);
                    return null;
                }
            } catch (\Exception $e) {
                log_message('error', 'Blog image upload exception: ' . $e->getMessage());
                return null;
            }
        }

        return null;
    }
}
