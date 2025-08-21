<?php

namespace App\Services;

/**
 * ImageDisplayService
 *
 * Service for handling optimized image display logic throughout the White Rock Realtor website.
 * This service works with simple array format while maintaining optimization features through
 * intelligent file detection and format selection.
 *
 * Key Features:
 * - Simple array format support (reverted from complex JSON objects)
 * - Intelligent image format selection (WebP with JPEG fallback)
 * - Automatic optimization file detection (finds optimized versions of images)
 * - Responsive image size selection based on context
 * - Backward compatibility with legacy image storage
 * - Browser capability detection for format support
 * - Fallback handling for missing or corrupted images
 *
 * Display Contexts:
 * - Property listing cards (thumbnail size)
 * - Property detail galleries (medium/large sizes)
 * - Hero sections and featured displays (large size)
 * - Mobile-optimized displays (appropriate sizes)
 *
 * @author White Rock Realtor Team
 * @version 2.0 - Updated for simple array format with optimization detection
 * @since 2025-08-20
 */
class ImageDisplayService
{
    /**
     * Default fallback image for properties
     */
    private const DEFAULT_PROPERTY_IMAGE = 'assets/images/default-property.svg';

    /**
     * Image size mappings for different display contexts
     */
    private const CONTEXT_SIZE_MAP = [
        'card' => 'thumbnail',      // Property listing cards
        'gallery' => 'medium',      // Property detail galleries
        'hero' => 'large',          // Hero sections and featured displays
        'thumbnail' => 'thumbnail', // Explicit thumbnail requests
        'medium' => 'medium',       // Explicit medium requests
        'large' => 'large'          // Explicit large requests
    ];

    /**
     * Get optimized image URL for display
     *
     * This method intelligently selects the best available image format and size
     * for the given context, with automatic optimization detection and fallback handling.
     *
     * @param mixed $imageData Image data from database (simple string path)
     * @param string $context Display context (card, gallery, hero, etc.)
     * @param bool $preferWebp Whether to prefer WebP format (auto-detected if null)
     * @return string URL to the optimized image or fallback image
     */
    public function getOptimizedImageUrl($imageData, string $context = 'card', ?bool $preferWebp = null): string
    {
        // Handle empty or null image data
        if (empty($imageData)) {
            return base_url(self::DEFAULT_PROPERTY_IMAGE);
        }

        // Handle simple string path (new simple format)
        if (is_string($imageData)) {
            return $this->handleSimpleImagePath($imageData, $context, $preferWebp);
        }

        // Handle legacy complex array format (for backward compatibility)
        if (is_array($imageData)) {
            return $this->handleLegacyOptimizedImage($imageData, $context, $preferWebp);
        }

        // Fallback for unexpected data types
        return base_url(self::DEFAULT_PROPERTY_IMAGE);
    }

    /**
     * Get multiple image URLs for gallery display
     * 
     * Returns an array of optimized image URLs suitable for gallery display,
     * with proper fallback handling for mixed legacy and optimized images.
     * 
     * @param mixed $imagesData Images data from database
     * @param string $size Size preference (thumbnail, medium, large)
     * @param bool $preferWebp Whether to prefer WebP format
     * @return array Array of image URLs
     */
    public function getGalleryImages($imagesData, string $size = 'medium', ?bool $preferWebp = null): array
    {
        $imageUrls = [];

        // Handle empty data
        if (empty($imagesData)) {
            return [base_url(self::DEFAULT_PROPERTY_IMAGE)];
        }

        // Decode JSON if needed
        if (is_string($imagesData)) {
            $imagesData = json_decode($imagesData, true);
        }

        // Handle array of images
        if (is_array($imagesData)) {
            foreach ($imagesData as $imageData) {
                $imageUrl = $this->getOptimizedImageUrl($imageData, $size, $preferWebp);
                if ($imageUrl) {
                    $imageUrls[] = $imageUrl;
                }
            }
        }

        // Return default image if no valid images found
        return empty($imageUrls) ? [base_url(self::DEFAULT_PROPERTY_IMAGE)] : $imageUrls;
    }

    /**
     * Generate responsive image srcset for modern browsers
     * 
     * Creates a srcset attribute value for responsive images that automatically
     * selects the appropriate image size based on viewport and device capabilities.
     * 
     * @param mixed $imageData Image data from database
     * @param bool $preferWebp Whether to prefer WebP format
     * @return string Srcset attribute value
     */
    public function generateSrcset($imageData, ?bool $preferWebp = null): string
    {
        if (empty($imageData) || !is_array($imageData)) {
            return '';
        }

        $srcsetParts = [];

        // Handle new optimized image format
        if (isset($imageData['sizes'])) {
            foreach ($imageData['sizes'] as $sizeName => $sizeData) {
                $imageUrl = $this->selectImageFormat($sizeData, $preferWebp);
                if ($imageUrl && isset($sizeData['width'])) {
                    $srcsetParts[] = base_url($imageUrl) . ' ' . $sizeData['width'] . 'w';
                }
            }
        }

        return implode(', ', $srcsetParts);
    }

    /**
     * Handle simple image path with optimization detection
     *
     * This method takes a simple image path and attempts to find the best
     * optimized version based on the display context and browser capabilities.
     *
     * @param string $imagePath Simple image path from database
     * @param string $context Display context
     * @param bool|null $preferWebp WebP preference
     * @return string Full image URL
     */
    private function handleSimpleImagePath(string $imagePath, string $context, ?bool $preferWebp): string
    {
        // Auto-detect WebP preference if not specified
        if ($preferWebp === null) {
            $preferWebp = $this->supportsWebp();
        }

        // Determine preferred size based on context
        $preferredSize = self::CONTEXT_SIZE_MAP[$context] ?? 'medium';

        // Try to find optimized version of the image
        $optimizedPath = $this->findOptimizedImage($imagePath, $preferredSize, $preferWebp);
        if ($optimizedPath) {
            return base_url($optimizedPath);
        }

        // Fallback to original image if it exists
        $fullPath = FCPATH . $imagePath;
        if (file_exists($fullPath)) {
            return base_url($imagePath);
        }

        // Return default image if file doesn't exist
        return base_url(self::DEFAULT_PROPERTY_IMAGE);
    }

    /**
     * Handle legacy optimized image format (array with size data)
     *
     * This method provides backward compatibility for the old complex array format.
     *
     * @param array $imageData Legacy optimized image data
     * @param string $context Display context
     * @param bool|null $preferWebp WebP preference
     * @return string Full image URL
     */
    private function handleLegacyOptimizedImage(array $imageData, string $context, ?bool $preferWebp): string
    {
        // Determine preferred size based on context
        $preferredSize = self::CONTEXT_SIZE_MAP[$context] ?? 'medium';

        // Handle new format with sizes array
        if (isset($imageData['sizes'][$preferredSize])) {
            $sizeData = $imageData['sizes'][$preferredSize];
            $imagePath = $this->selectImageFormat($sizeData, $preferWebp);
            
            if ($imagePath) {
                return base_url($imagePath);
            }
        }

        // Fallback to any available size
        if (isset($imageData['sizes']) && is_array($imageData['sizes'])) {
            foreach (['medium', 'large', 'thumbnail'] as $fallbackSize) {
                if (isset($imageData['sizes'][$fallbackSize])) {
                    $sizeData = $imageData['sizes'][$fallbackSize];
                    $imagePath = $this->selectImageFormat($sizeData, $preferWebp);
                    
                    if ($imagePath) {
                        return base_url($imagePath);
                    }
                }
            }
        }

        // Final fallback to default image
        return base_url(self::DEFAULT_PROPERTY_IMAGE);
    }

    /**
     * Select best image format (WebP vs JPEG) based on preference and availability
     * 
     * @param array $sizeData Size data containing format paths
     * @param bool|null $preferWebp WebP preference
     * @return string|null Selected image path or null if none available
     */
    private function selectImageFormat(array $sizeData, ?bool $preferWebp): ?string
    {
        // Auto-detect WebP preference if not specified
        if ($preferWebp === null) {
            $preferWebp = $this->supportsWebp();
        }

        // Prefer WebP if supported and available
        if ($preferWebp && !empty($sizeData['webp'])) {
            $webpPath = FCPATH . $sizeData['webp'];
            if (file_exists($webpPath)) {
                return $sizeData['webp'];
            }
        }

        // Fallback to JPEG
        if (!empty($sizeData['jpeg'])) {
            $jpegPath = FCPATH . $sizeData['jpeg'];
            if (file_exists($jpegPath)) {
                return $sizeData['jpeg'];
            }
        }

        return null;
    }

    /**
     * Find optimized version of an image based on simple path
     *
     * This method attempts to locate optimized versions of an image by
     * analyzing the file path and looking for size-specific variants.
     *
     * @param string $imagePath Original image path
     * @param string $size Desired size (thumbnail, medium, large)
     * @param bool $preferWebp Whether to prefer WebP format
     * @return string|null Path to optimized image or null if not found
     */
    private function findOptimizedImage(string $imagePath, string $size, bool $preferWebp): ?string
    {
        // Extract path components
        $pathInfo = pathinfo($imagePath);
        $directory = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];
        $extension = $pathInfo['extension'] ?? 'jpg';

        // Try to find size-specific optimized versions
        $sizeVariants = [
            $filename . '_' . $size,  // e.g., image_medium.jpg
            $filename                 // fallback to original
        ];

        $formatPreferences = $preferWebp ? ['webp', 'jpg', 'jpeg', 'png'] : ['jpg', 'jpeg', 'png', 'webp'];

        foreach ($sizeVariants as $variant) {
            foreach ($formatPreferences as $format) {
                $testPath = $directory . '/' . $variant . '.' . $format;
                $fullPath = FCPATH . $testPath;

                if (file_exists($fullPath)) {
                    return $testPath;
                }
            }
        }

        return null;
    }

    /**
     * Detect WebP support in current browser
     * 
     * @return bool True if WebP is supported
     */
    private function supportsWebp(): bool
    {
        // Check Accept header for WebP support
        $acceptHeader = $_SERVER['HTTP_ACCEPT'] ?? '';
        return strpos($acceptHeader, 'image/webp') !== false;
    }

    /**
     * Get image dimensions for a specific size
     * 
     * @param mixed $imageData Image data from database
     * @param string $size Size name (thumbnail, medium, large)
     * @return array|null Array with width and height keys, or null if not available
     */
    public function getImageDimensions($imageData, string $size = 'medium'): ?array
    {
        if (!is_array($imageData) || !isset($imageData['sizes'][$size])) {
            return null;
        }

        $sizeData = $imageData['sizes'][$size];
        
        if (isset($sizeData['width']) && isset($sizeData['height'])) {
            return [
                'width' => $sizeData['width'],
                'height' => $sizeData['height']
            ];
        }

        return null;
    }

    /**
     * Check if image data represents an optimized image
     * 
     * @param mixed $imageData Image data to check
     * @return bool True if data represents an optimized image
     */
    public function isOptimizedImage($imageData): bool
    {
        return is_array($imageData) && isset($imageData['sizes']) && is_array($imageData['sizes']);
    }
}
