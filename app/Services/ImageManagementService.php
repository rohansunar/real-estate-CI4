<?php

namespace App\Services;

/**
 * ImageManagementService
 * 
 * Centralized service for managing all image operations across the application.
 * This service provides unified image upload, storage, retrieval, and deletion
 * functionality for all entity types (properties, agents, blog posts).
 * 
 * Key Features:
 * - Unified storage location (public/uploads/)
 * - Comprehensive error handling and logging
 * - Automatic directory creation with proper permissions
 * - Secure file validation and sanitization
 * - Cascade delete functionality for entity cleanup
 * - Memory-efficient operations
 * - User-friendly error messages
 * 
 * Storage Structure:
 * - public/uploads/agents/ - Agent profile images
 * - public/uploads/blog/ - Blog featured images  
 * - public/uploads/properties/ - Property images with size variants
 * 
 * @author White Rock Realtor Team
 * @version 1.0
 * @since 2025-08-20
 */
class ImageManagementService
{
    /**
     * Base upload directory (public/uploads/)
     */
    private const BASE_UPLOAD_PATH = FCPATH . 'uploads';
    
    /**
     * Entity-specific subdirectories
     */
    private const ENTITY_DIRECTORIES = [
        'agent' => 'agents',
        'blog' => 'blog', 
        'property' => 'properties'
    ];
    
    /**
     * Allowed image file extensions
     */
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    /**
     * Maximum file size in bytes (2MB)
     */
    private const MAX_FILE_SIZE = 2097152;
    
    /**
     * Upload an image for a specific entity type
     * 
     * @param \CodeIgniter\HTTP\Files\UploadedFile $file The uploaded file
     * @param string $entityType Entity type (agent, blog, property)
     * @param string|null $customFilename Optional custom filename
     * @return array Success response with file path or error details
     */
    public function uploadImage($file, string $entityType, ?string $customFilename = null): array
    {
        try {
            // Validate entity type
            if (!isset(self::ENTITY_DIRECTORIES[$entityType])) {
                return $this->errorResponse('Invalid entity type specified');
            }
            
            // Validate uploaded file
            $validation = $this->validateUploadedFile($file);
            if (!$validation['success']) {
                return $validation;
            }
            
            // Ensure upload directory exists
            $uploadPath = self::BASE_UPLOAD_PATH . '/' . self::ENTITY_DIRECTORIES[$entityType];
            $dirCreation = $this->ensureDirectoryExists($uploadPath);
            if (!$dirCreation['success']) {
                return $dirCreation;
            }
            
            // Generate secure filename
            $filename = $customFilename ?: $file->getRandomName();
            
            // Move uploaded file to destination
            if (!$file->move($uploadPath, $filename)) {
                log_message('error', "Failed to move uploaded file to: {$uploadPath}/{$filename}");
                return $this->errorResponse('Failed to save uploaded image. Please try again.');
            }
            
            // Return success response with relative path
            $relativePath = 'uploads/' . self::ENTITY_DIRECTORIES[$entityType] . '/' . $filename;
            
            log_message('info', "Image uploaded successfully: {$relativePath}");
            
            return [
                'success' => true,
                'message' => 'Image uploaded successfully',
                'file_path' => $relativePath,
                'filename' => $filename
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'Image upload error: ' . $e->getMessage());
            return $this->errorResponse('An unexpected error occurred during image upload');
        }
    }
    
    /**
     * Delete a single image file
     * 
     * @param string $filePath Relative path to the image file
     * @return array Success/error response
     */
    public function deleteImage(string $filePath): array
    {
        try {
            if (empty($filePath)) {
                return ['success' => true, 'message' => 'No image to delete'];
            }
            
            // Convert relative path to absolute path
            $absolutePath = $this->getAbsolutePath($filePath);
            
            if (!file_exists($absolutePath)) {
                log_message('warning', "Image file not found for deletion: {$absolutePath}");
                return ['success' => true, 'message' => 'Image file not found (may have been already deleted)'];
            }
            
            // Attempt to delete the file
            if (unlink($absolutePath)) {
                log_message('info', "Image deleted successfully: {$absolutePath}");
                return ['success' => true, 'message' => 'Image deleted successfully'];
            } else {
                log_message('error', "Failed to delete image file: {$absolutePath}");
                return $this->errorResponse('Failed to delete image file');
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Image deletion error: ' . $e->getMessage());
            return $this->errorResponse('An unexpected error occurred during image deletion');
        }
    }
    
    /**
     * Delete multiple images (for entities with multiple images)
     * 
     * @param array $filePaths Array of relative file paths
     * @return array Summary of deletion results
     */
    public function deleteMultipleImages(array $filePaths): array
    {
        $results = [
            'success' => true,
            'deleted_count' => 0,
            'failed_count' => 0,
            'messages' => []
        ];
        
        foreach ($filePaths as $filePath) {
            $result = $this->deleteImage($filePath);
            
            if ($result['success']) {
                $results['deleted_count']++;
            } else {
                $results['failed_count']++;
                $results['messages'][] = "Failed to delete: {$filePath}";
            }
        }
        
        // Overall success if at least some deletions succeeded and no critical failures
        if ($results['failed_count'] > 0 && $results['deleted_count'] === 0) {
            $results['success'] = false;
        }
        
        $totalFiles = count($filePaths);
        $results['summary'] = "Deleted {$results['deleted_count']} of {$totalFiles} images";
        
        return $results;
    }
    
    /**
     * Delete property images with size variants (thumbnails, medium, large, webp)
     * 
     * @param array $imageData Property image data from database
     * @return array Summary of deletion results
     */
    public function deletePropertyImages(array $imageData): array
    {
        $allFilePaths = [];
        
        // Extract all file paths from property image data structure
        foreach ($imageData as $imageInfo) {
            if (is_array($imageInfo) && isset($imageInfo['sizes'])) {
                // New format with size variants
                foreach ($imageInfo['sizes'] as $sizeData) {
                    if (isset($sizeData['jpeg'])) {
                        $allFilePaths[] = $sizeData['jpeg'];
                    }
                    if (isset($sizeData['webp'])) {
                        $allFilePaths[] = $sizeData['webp'];
                    }
                }
            } elseif (is_string($imageInfo)) {
                // Legacy format - single image path
                $allFilePaths[] = $imageInfo;
            }
        }
        
        return $this->deleteMultipleImages($allFilePaths);
    }
    
    /**
     * Validate uploaded file
     *
     * Performs comprehensive validation of uploaded image files using proper
     * CodeIgniter 4 methods. This includes file validity, size, extension,
     * and MIME type validation to ensure security and compatibility.
     *
     * @param \CodeIgniter\HTTP\Files\UploadedFile $file The uploaded file to validate
     * @return array Validation result with success status and error message if applicable
     */
    private function validateUploadedFile($file): array
    {
        // Check if file exists and is valid
        if (!$file || !$file->isValid()) {
            return $this->errorResponse('Invalid file upload');
        }

        // Check if file has already been moved
        if ($file->hasMoved()) {
            return $this->errorResponse('File has already been moved');
        }

        // Check file size (2MB limit)
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            return $this->errorResponse('File size exceeds maximum limit of 2MB');
        }

        // Check file extension
        $extension = strtolower($file->getClientExtension());
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            return $this->errorResponse('Invalid file type. Only JPG, PNG, GIF, and WebP images are allowed');
        }

        // Verify it's actually an image using MIME type validation
        // This is the proper CodeIgniter 4 approach instead of the non-existent isImage() method
        $mimeType = $file->getClientMimeType();
        $validMimeTypes = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp'
        ];

        if (!in_array($mimeType, $validMimeTypes)) {
            return $this->errorResponse('File is not a valid image. Please upload a valid image file.');
        }

        // Additional validation: Check if we can get image info from the actual file
        // This provides an extra layer of security by verifying the file content
        $imageInfo = @getimagesize($file->getTempName());
        if ($imageInfo === false) {
            return $this->errorResponse('File appears to be corrupted or is not a valid image');
        }

        return ['success' => true];
    }
    
    /**
     * Ensure directory exists with proper permissions
     * 
     * @param string $path Directory path
     * @return array Success/error response
     */
    private function ensureDirectoryExists(string $path): array
    {
        if (is_dir($path)) {
            return ['success' => true];
        }
        
        if (!mkdir($path, 0755, true)) {
            log_message('error', "Failed to create upload directory: {$path}");
            return $this->errorResponse('Failed to create upload directory. Please check server permissions.');
        }
        
        // Create security index.html file
        $indexFile = $path . '/index.html';
        if (!file_exists($indexFile)) {
            file_put_contents($indexFile, '<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><h1>Directory access is forbidden.</h1></body></html>');
        }
        
        return ['success' => true];
    }
    
    /**
     * Convert relative path to absolute path
     *
     * Handles both legacy paths (assets/images/) and new upload paths (uploads/).
     * This method properly resolves image paths regardless of where they are stored.
     *
     * @param string $relativePath Relative path (e.g., 'uploads/agents/image.jpg' or 'assets/images/house1.jpg')
     * @return string Absolute path
     */
    private function getAbsolutePath(string $relativePath): string
    {
        // Remove leading slash if present
        $relativePath = ltrim($relativePath, '/');

        // Check if path already starts with a known directory structure
        if (str_starts_with($relativePath, 'uploads/') ||
            str_starts_with($relativePath, 'assets/') ||
            str_starts_with($relativePath, 'public/')) {
            // Path already has proper directory structure
            return FCPATH . $relativePath;
        }

        // For paths without directory structure, assume uploads directory
        return FCPATH . 'uploads/' . $relativePath;
    }
    
    /**
     * Generate standardized error response
     * 
     * @param string $message Error message
     * @return array Error response array
     */
    private function errorResponse(string $message): array
    {
        return [
            'success' => false,
            'message' => $message
        ];
    }
}
