<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * PropertyModel
 *
 * Handles property data management with simple array-based media support.
 * This model manages property records including multiple images and YouTube videos.
 *
 * Key Features:
 * - Simple array format for image paths (reverted from complex JSON objects)
 * - Automatic JSON encoding/decoding for database storage
 * - Multiple image storage and retrieval with optimization support
 * - Multiple YouTube video URL management
 * - Automatic timestamp management
 * - Data validation and sanitization
 *
 * Database Schema:
 * - images: JSON array of simple image file paths (e.g., ["uploads/properties/image1.jpg"])
 * - youtube_video: JSON array of YouTube video URLs
 * - Maintains image optimization features while using simple path storage
 *
 * @author Gold Properties Team
 * @version 3.0 - Reverted to simple array format while keeping optimization
 * @since 2025-08-20
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
    protected $beforeDelete   = ['deleteAssociatedImages'];
    protected $afterDelete    = [];

    /**
     * Process images and youtube_video arrays before saving to database
     *
     * Automatically converts PHP arrays to JSON strings for database storage.
     * This method is called before insert/update operations to ensure
     * proper data format for the database.
     *
     * Handles:
     * - Simple image file paths array → JSON string (e.g., ["uploads/properties/image1.jpg"])
     * - Multiple YouTube video URLs array → JSON string
     * - Maintains data integrity during storage
     * - Ensures simple array format for images (no complex optimization objects)
     *
     * @param array $data The data array containing property information
     * @return array Modified data array with JSON-encoded media fields
     */
    protected function processImages(array $data)
    {
        // Convert images array to JSON for database storage
        // Ensure images are stored as simple array of paths
        if (isset($data['data']['images']) && is_array($data['data']['images'])) {
            // Validate that images are simple strings (paths), not complex objects
            $simpleImages = [];
            foreach ($data['data']['images'] as $image) {
                if (is_string($image)) {
                    $simpleImages[] = $image;
                } else {
                    // Log warning if complex data is passed (shouldn't happen with new implementation)
                    log_message('warning', 'PropertyModel: Complex image data detected during save, expected simple path string');
                }
            }
            $data['data']['images'] = json_encode($simpleImages);
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
     * - JSON string → Simple image file paths array (e.g., ["uploads/properties/image1.jpg"])
     * - JSON string → Multiple YouTube video URLs array
     * - Graceful fallback to empty arrays for invalid JSON
     * - Support for both single record and multiple records
     * - Ensures simple array format for backward compatibility
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
                    // Decode images JSON to simple array of paths
                    if (isset($row['images'])) {
                        $decodedImages = json_decode($row['images'], true) ?? [];
                        // Ensure we have simple array of strings
                        $row['images'] = is_array($decodedImages) ? $decodedImages : [];
                    }
                    // Decode youtube_video JSON to array
                    if (isset($row['youtube_video'])) {
                        $row['youtube_video'] = json_decode($row['youtube_video'], true) ?? [];
                    }
                }
            } else {
                // Handle single record (find, etc.)
                if (isset($data['data']['images'])) {
                    $decodedImages = json_decode($data['data']['images'], true) ?? [];
                    // Ensure we have simple array of strings
                    $data['data']['images'] = is_array($decodedImages) ? $decodedImages : [];
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

    /**
     * Delete associated images before property deletion
     *
     * This callback method is automatically called before a property is deleted.
     * It handles the cascade deletion of all associated images to prevent
     * orphaned files on the server.
     *
     * @param array $data Data array containing property information
     * @return array Unmodified data array (callbacks must return data)
     */
    protected function deleteAssociatedImages(array $data): array
    {
        try {
            // Get the property ID from the data
            $propertyId = null;

            if (isset($data['id'])) {
                // Single record deletion - ID is passed as array
                $propertyId = is_array($data['id']) ? $data['id'][0] : $data['id'];
            } elseif (isset($data['data']) && is_array($data['data'])) {
                // Batch deletion - handle first ID (CodeIgniter limitation)
                $propertyId = is_array($data['data']) ? $data['data'][0] : $data['data'];
            }

            if (!$propertyId) {
                log_message('warning', 'PropertyModel: No property ID found for image deletion. Data structure: ' . json_encode($data));
                return $data;
            }

            // Fetch the property to get image data
            $property = $this->find($propertyId);

            if (!$property || empty($property['images'])) {
                log_message('info', "PropertyModel: No images to delete for property ID {$propertyId}");
                return $data;
            }

            // Initialize image management service
            $imageService = new \App\Services\ImageManagementService();

            // Decode images if they're stored as JSON
            $images = is_string($property['images']) ? json_decode($property['images'], true) : $property['images'];

            if (empty($images) || !is_array($images)) {
                log_message('info', "PropertyModel: No valid images found for property ID {$propertyId}");
                return $data;
            }

            // Delete property images (handles both legacy and new formats)
            $deletionResult = $imageService->deletePropertyImages($images);

            if ($deletionResult['success']) {
                log_message('info', "PropertyModel: Successfully deleted images for property ID {$propertyId}. {$deletionResult['summary']}");
            } else {
                log_message('error', "PropertyModel: Failed to delete some images for property ID {$propertyId}. {$deletionResult['summary']}");
            }

            // Log any specific error messages
            if (!empty($deletionResult['messages'])) {
                foreach ($deletionResult['messages'] as $message) {
                    log_message('warning', "PropertyModel: {$message}");
                }
            }

        } catch (\Exception $e) {
            // Log error but don't prevent property deletion
            log_message('error', 'PropertyModel: Error during image deletion: ' . $e->getMessage());
        }

        // Always return the data to allow the deletion to proceed
        return $data;
    }
}
