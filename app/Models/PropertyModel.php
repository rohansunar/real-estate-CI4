<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * PropertyModel
 *
 * Handles property data management with enhanced media support.
 * This model manages property records including multiple images and YouTube videos.
 *
 * Key Features:
 * - Automatic JSON encoding/decoding for images and videos arrays
 * - Support for multiple image storage and retrieval
 * - Multiple YouTube video URL management
 * - Automatic timestamp management
 * - Data validation and sanitization
 *
 * Database Schema:
 * - images: JSON array of image file paths
 * - youtube_video: JSON array of YouTube video URLs
 * - Backward compatibility with single video format
 *
 * @author Real Estate Team
 * @version 2.0 - Enhanced with multiple media support
 * @since 2025-08-02
 */
class PropertyModel extends Model
{
    protected $table            = 'properties';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['title', 'description', 'type', 'location', 'area', 'is_featured', 'images', 'youtube_video'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'title'       => 'required|max_length[255]',
        'description' => 'required',
        'type'        => 'required|in_list[house,apartment,villa,land]',
        'location'    => 'required|max_length[255]',
        'area'        => 'permit_empty|integer',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['processImages'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['processImages'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = ['decodeImages'];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Process images and youtube_video arrays before saving to database
     *
     * Automatically converts PHP arrays to JSON strings for database storage.
     * This method is called before insert/update operations to ensure
     * proper data format for the database.
     *
     * Handles:
     * - Multiple image file paths array → JSON string
     * - Multiple YouTube video URLs array → JSON string
     * - Maintains data integrity during storage
     *
     * @param array $data The data array containing property information
     * @return array Modified data array with JSON-encoded media fields
     */
    protected function processImages(array $data)
    {
        // Convert images array to JSON for database storage
        if (isset($data['data']['images']) && is_array($data['data']['images'])) {
            $data['data']['images'] = json_encode($data['data']['images']);
        }

        // Convert youtube_video array to JSON for database storage
        if (isset($data['data']['youtube_video']) && is_array($data['data']['youtube_video'])) {
            $data['data']['youtube_video'] = json_encode($data['data']['youtube_video']);
        }

        return $data;
    }

    /**
     * Decode images and youtube_video JSON after retrieving from database
     *
     * Automatically converts JSON strings back to PHP arrays for application use.
     * This method is called after find/select operations to ensure
     * proper data format for the application.
     *
     * Handles:
     * - JSON string → Multiple image file paths array
     * - JSON string → Multiple YouTube video URLs array
     * - Graceful fallback to empty arrays for invalid JSON
     * - Support for both single record and multiple records
     *
     * @param array $data The data array from database query
     * @return array Modified data array with decoded media fields
     */
    protected function decodeImages(array $data)
    {
        if (isset($data['data'])) {
            // Handle multiple records (findAll, etc.)
            if (is_array($data['data'])) {
                foreach ($data['data'] as &$row) {
                    // Decode images JSON to array
                    if (isset($row['images'])) {
                        $row['images'] = json_decode($row['images'], true) ?? [];
                    }
                    // Decode youtube_video JSON to array
                    if (isset($row['youtube_video'])) {
                        $row['youtube_video'] = json_decode($row['youtube_video'], true) ?? [];
                    }
                }
            } else {
                // Handle single record (find, etc.)
                if (isset($data['data']['images'])) {
                    $data['data']['images'] = json_decode($data['data']['images'], true) ?? [];
                }
                if (isset($data['data']['youtube_video'])) {
                    $data['data']['youtube_video'] = json_decode($data['data']['youtube_video'], true) ?? [];
                }
            }
        }
        return $data;
    }

    /**
     * Get properties by location
     */
    public function getByLocation(string $location, int $limit = 10, int $excludeId = null)
    {
        $builder = $this->where('location', $location);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->orderBy('created_at', 'DESC')
                      ->limit($limit)
                      ->findAll();
    }

    /**
     * Get properties by type
     */
    public function getByType(string $type, int $limit = 10)
    {
        return $this->where('type', $type)
                   ->orderBy('created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Search properties
     */
    public function searchProperties(string $query, int $limit = 10)
    {
        return $this->groupStart()
                   ->like('title', $query)
                   ->orLike('description', $query)
                   ->orLike('location', $query)
                   ->groupEnd()
                   ->orderBy('created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Get featured properties
     */
    public function getFeaturedProperties(int $limit = 10)
    {
        return $this->where('is_featured', true)
                   ->orderBy('created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Get properties by type with featured properties first
     */
    public function getPropertiesByType(string $type, int $limit = 3)
    {
        return $this->where('type', $type)
                   ->orderBy('is_featured', 'DESC')
                   ->orderBy('created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Toggle featured status of a property
     */
    public function toggleFeatured(int $id)
    {
        $property = $this->find($id);
        if ($property) {
            $newStatus = !$property['is_featured'];
            return $this->update($id, ['is_featured' => $newStatus]);
        }
        return false;
    }
}
