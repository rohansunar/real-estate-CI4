<?php

namespace App\Models;

use CodeIgniter\Model;

class BlogPostModel extends Model
{
    protected $table            = 'blog_posts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title', 'slug', 'excerpt', 'content', 'featured_image', 'category', 'tags', 
        'status', 'author_id', 'views', 'meta_title', 'meta_description', 'published_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'title'       => 'required|max_length[255]',
        'slug'        => 'required|max_length[255]|is_unique[blog_posts.slug,id,{id}]',
        'content'     => 'required',
        'status'      => 'required|in_list[draft,published,archived]',
        'category'    => 'permit_empty|max_length[100]',
        'meta_title'  => 'permit_empty|max_length[255]',
        'meta_description' => 'permit_empty|max_length[500]',
    ];
    
    protected $validationMessages   = [
        'title' => [
            'required' => 'Blog post title is required.',
            'max_length' => 'Title cannot exceed 255 characters.'
        ],
        'slug' => [
            'required' => 'URL slug is required.',
            'is_unique' => 'This URL slug is already in use.',
            'max_length' => 'Slug cannot exceed 255 characters.'
        ],
        'content' => [
            'required' => 'Blog post content is required.'
        ],
        'status' => [
            'required' => 'Post status is required.',
            'in_list' => 'Status must be draft, published, or archived.'
        ]
    ];
    
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateSlug', 'setPublishedAt'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['generateSlug', 'setPublishedAt'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Generate slug from title if not provided
     */
    protected function generateSlug(array $data)
    {
        if (isset($data['data']['title']) && empty($data['data']['slug'])) {
            $data['data']['slug'] = $this->createSlug($data['data']['title']);
        }
        return $data;
    }

    /**
     * Set published_at timestamp when status changes to published
     */
    protected function setPublishedAt(array $data)
    {
        if (isset($data['data']['status']) && $data['data']['status'] === 'published') {
            if (empty($data['data']['published_at'])) {
                $data['data']['published_at'] = date('Y-m-d H:i:s');
            }
        }
        return $data;
    }

    /**
     * Create URL-friendly slug
     */
    public function createSlug(string $title): string
    {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Ensure uniqueness
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->where('slug', $slug)->first()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    /**
     * Get published posts
     */
    public function getPublished(int $limit = null, int $offset = 0)
    {
        $builder = $this->where('status', 'published')
                       ->where('published_at <=', date('Y-m-d H:i:s'))
                       ->orderBy('published_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->findAll();
    }

    /**
     * Get posts by category
     */
    public function getByCategory(string $category, int $limit = null)
    {
        $builder = $this->where('category', $category)
                       ->where('status', 'published')
                       ->where('published_at <=', date('Y-m-d H:i:s'))
                       ->orderBy('published_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
    }

    /**
     * Search posts
     */
    public function searchPosts(string $query, int $limit = 10)
    {
        return $this->groupStart()
                   ->like('title', $query)
                   ->orLike('content', $query)
                   ->orLike('excerpt', $query)
                   ->orLike('tags', $query)
                   ->groupEnd()
                   ->where('status', 'published')
                   ->where('published_at <=', date('Y-m-d H:i:s'))
                   ->orderBy('published_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Get recent posts
     */
    public function getRecent(int $limit = 5)
    {
        return $this->getPublished($limit);
    }

    /**
     * Get popular posts (by views)
     */
    public function getPopular(int $limit = 5)
    {
        return $this->where('status', 'published')
                   ->where('published_at <=', date('Y-m-d H:i:s'))
                   ->orderBy('views', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Get related posts
     */
    public function getRelated(int $postId, string $category = null, int $limit = 3)
    {
        $builder = $this->where('id !=', $postId)
                       ->where('status', 'published')
                       ->where('published_at <=', date('Y-m-d H:i:s'));
        
        if ($category) {
            $builder->where('category', $category);
        }
        
        return $builder->orderBy('published_at', 'DESC')
                      ->limit($limit)
                      ->findAll();
    }

    /**
     * Increment view count
     */
    public function incrementViews(int $id): bool
    {
        return $this->set('views', 'views + 1', false)
                   ->where('id', $id)
                   ->update();
    }

    /**
     * Get post statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->countAllResults(false),
            'published' => $this->where('status', 'published')->countAllResults(false),
            'draft' => $this->where('status', 'draft')->countAllResults(false),
            'archived' => $this->where('status', 'archived')->countAllResults(false),
            'recent' => $this->where('created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')))->countAllResults(false)
        ];
    }

    /**
     * Get all categories with post counts
     */
    public function getCategories(): array
    {
        return $this->select('category, COUNT(*) as count')
                   ->where('category IS NOT NULL')
                   ->where('category !=', '')
                   ->where('status', 'published')
                   ->groupBy('category')
                   ->orderBy('category', 'ASC')
                   ->findAll();
    }
}
