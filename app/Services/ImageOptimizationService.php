<?php

namespace App\Services;

/**
 * ImageOptimizationService
 * 
 * Comprehensive image optimization service for the Gold Properties website.
 * This service handles automatic image compression, resizing, and format conversion
 * to improve website performance and user experience.
 * 
 * Key Features:
 * - Automatic image compression (60-80% file size reduction while maintaining quality)
 * - Multiple responsive image sizes generation (thumbnail, medium, large)
 * - WebP format conversion with JPEG fallback for better compression
 * - Maximum dimension limits for performance optimization
 * - Maintains aspect ratios and image quality
 * - Comprehensive error handling and logging
 * 
 * Performance Goals:
 * - Reduce image file sizes by 60-80%
 * - Generate optimized images for different screen sizes
 * - Improve page load times and bandwidth usage
 * - Maintain visual quality while optimizing file size
 * 
 * @author Gold Properties Team
 * @version 1.0 - Initial implementation with GD library
 * @since 2025-08-20
 */
class ImageOptimizationService
{
    /**
     * Image size configurations for responsive display
     * Each size includes maximum dimensions and quality settings
     */
    private const IMAGE_SIZES = [
        'thumbnail' => ['width' => 300, 'height' => 200, 'quality' => 85],
        'medium'    => ['width' => 800, 'height' => 600, 'quality' => 90],
        'large'     => ['width' => 1920, 'height' => 1280, 'quality' => 95]
    ];

    /**
     * Supported image formats for processing
     */
    private const SUPPORTED_FORMATS = ['jpg', 'jpeg', 'png', 'gif'];

    /**
     * WebP quality setting for optimal compression
     */
    private const WEBP_QUALITY = 85;

    /**
     * Process and optimize uploaded image
     * 
     * This method handles the complete image optimization workflow:
     * 1. Validates the uploaded image file
     * 2. Creates multiple responsive sizes
     * 3. Generates WebP versions with JPEG fallbacks
     * 4. Saves optimized images to appropriate directories
     * 
     * @param \CodeIgniter\HTTP\Files\UploadedFile $uploadedFile The uploaded image file
     * @param string $uploadPath Base upload directory path
     * @param string $filename Desired filename (without extension)
     * @return array Array containing paths to all generated image sizes and formats
     * @throws \Exception If image processing fails
     */
    public function processImage($uploadedFile, string $uploadPath, string $filename): array
    {
        // Validate uploaded file
        if (!$uploadedFile->isValid() || $uploadedFile->hasMoved()) {
            throw new \Exception('Invalid or already moved image file');
        }

        // Validate file format
        $extension = strtolower($uploadedFile->getClientExtension());
        if (!in_array($extension, self::SUPPORTED_FORMATS)) {
            throw new \Exception('Unsupported image format. Please use JPG, PNG, or GIF.');
        }

        // Create source image resource
        $sourceImage = $this->createImageResource($uploadedFile->getTempName(), $extension);
        if (!$sourceImage) {
            throw new \Exception('Failed to create image resource from uploaded file');
        }

        $originalWidth = imagesx($sourceImage);
        $originalHeight = imagesy($sourceImage);
        
        $processedImages = [];

        try {
            // Generate each responsive size
            foreach (self::IMAGE_SIZES as $sizeName => $config) {
                // Calculate new dimensions while maintaining aspect ratio
                $dimensions = $this->calculateDimensions(
                    $originalWidth, 
                    $originalHeight, 
                    $config['width'], 
                    $config['height']
                );

                // Create resized image
                $resizedImage = $this->resizeImage(
                    $sourceImage, 
                    $dimensions['width'], 
                    $dimensions['height']
                );

                if ($resizedImage) {
                    // Generate JPEG version
                    $jpegPath = $this->saveJpegImage(
                        $resizedImage, 
                        $uploadPath, 
                        $filename . '_' . $sizeName . '.jpg', 
                        $config['quality']
                    );

                    // Generate WebP version if supported
                    $webpPath = $this->saveWebpImage(
                        $resizedImage, 
                        $uploadPath, 
                        $filename . '_' . $sizeName . '.webp'
                    );

                    $processedImages[$sizeName] = [
                        'jpeg' => $jpegPath,
                        'webp' => $webpPath,
                        'width' => $dimensions['width'],
                        'height' => $dimensions['height']
                    ];

                    // Clean up resized image resource
                    imagedestroy($resizedImage);
                }
            }

            return $processedImages;

        } finally {
            // Always clean up source image resource
            imagedestroy($sourceImage);
        }
    }

    /**
     * Create image resource from file based on format
     * 
     * @param string $filePath Path to the image file
     * @param string $extension File extension
     * @return resource|false Image resource or false on failure
     */
    private function createImageResource(string $filePath, string $extension)
    {
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                return imagecreatefromjpeg($filePath);
            case 'png':
                return imagecreatefrompng($filePath);
            case 'gif':
                return imagecreatefromgif($filePath);
            default:
                return false;
        }
    }

    /**
     * Calculate new dimensions while maintaining aspect ratio
     * 
     * @param int $originalWidth Original image width
     * @param int $originalHeight Original image height
     * @param int $maxWidth Maximum allowed width
     * @param int $maxHeight Maximum allowed height
     * @return array New dimensions with width and height keys
     */
    private function calculateDimensions(int $originalWidth, int $originalHeight, int $maxWidth, int $maxHeight): array
    {
        // Don't upscale images - use original dimensions if smaller than max
        if ($originalWidth <= $maxWidth && $originalHeight <= $maxHeight) {
            return ['width' => $originalWidth, 'height' => $originalHeight];
        }

        // Calculate scaling ratios
        $widthRatio = $maxWidth / $originalWidth;
        $heightRatio = $maxHeight / $originalHeight;
        
        // Use the smaller ratio to maintain aspect ratio
        $ratio = min($widthRatio, $heightRatio);
        
        return [
            'width' => (int) round($originalWidth * $ratio),
            'height' => (int) round($originalHeight * $ratio)
        ];
    }

    /**
     * Resize image to specified dimensions
     * 
     * @param resource $sourceImage Source image resource
     * @param int $newWidth New width
     * @param int $newHeight New height
     * @return resource|false Resized image resource or false on failure
     */
    private function resizeImage($sourceImage, int $newWidth, int $newHeight)
    {
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        
        if (!$resizedImage) {
            return false;
        }

        // Preserve transparency for PNG images
        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);
        $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
        imagefill($resizedImage, 0, 0, $transparent);

        // Perform high-quality resize
        $success = imagecopyresampled(
            $resizedImage, $sourceImage,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            imagesx($sourceImage), imagesy($sourceImage)
        );

        return $success ? $resizedImage : false;
    }

    /**
     * Save image as JPEG with specified quality
     * 
     * @param resource $image Image resource
     * @param string $uploadPath Upload directory path
     * @param string $filename Filename with extension
     * @param int $quality JPEG quality (0-100)
     * @return string|null Relative path to saved image or null on failure
     */
    private function saveJpegImage($image, string $uploadPath, string $filename, int $quality): ?string
    {
        $fullPath = $uploadPath . '/' . $filename;
        
        if (imagejpeg($image, $fullPath, $quality)) {
            return 'uploads/properties/' . $filename;
        }
        
        return null;
    }

    /**
     * Save image as WebP format
     * 
     * @param resource $image Image resource
     * @param string $uploadPath Upload directory path
     * @param string $filename Filename with extension
     * @return string|null Relative path to saved image or null on failure
     */
    private function saveWebpImage($image, string $uploadPath, string $filename): ?string
    {
        // Check if WebP support is available
        if (!function_exists('imagewebp')) {
            log_message('warning', 'WebP support not available in GD library');
            return null;
        }

        $fullPath = $uploadPath . '/' . $filename;
        
        if (imagewebp($image, $fullPath, self::WEBP_QUALITY)) {
            return 'uploads/properties/' . $filename;
        }
        
        return null;
    }

    /**
     * Get optimized image path for display
     * 
     * This method returns the best available image format and size for display,
     * preferring WebP over JPEG when supported by the browser.
     * 
     * @param array $imageData Image data from processImage method
     * @param string $size Size key (thumbnail, medium, large)
     * @param bool $preferWebp Whether to prefer WebP format
     * @return string|null Path to optimized image or null if not found
     */
    public function getOptimizedImagePath(array $imageData, string $size = 'medium', bool $preferWebp = false): ?string
    {
        if (!isset($imageData[$size])) {
            return null;
        }

        $sizeData = $imageData[$size];

        // Return WebP if preferred and available
        if ($preferWebp && !empty($sizeData['webp'])) {
            return $sizeData['webp'];
        }

        // Fallback to JPEG
        return $sizeData['jpeg'] ?? null;
    }
}
