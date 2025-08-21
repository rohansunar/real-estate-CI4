<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * RevertImagesToArrayFormat Migration
 * 
 * This migration reverts the images field from JSON format back to simple array format
 * while preserving image optimization features. The migration:
 * 
 * 1. Converts existing JSON image data to simple array of image paths
 * 2. Maintains backward compatibility with existing optimized images
 * 3. Preserves all image files and optimization features
 * 4. Uses the first available optimized image (preferring medium size)
 * 
 * @author White Rock Realtor Team
 * @version 1.0
 * @since 2025-08-20
 */
class RevertImagesToArrayFormat extends Migration
{
    public function up()
    {
        // First, let's get all properties with images to convert the data
        $db = \Config\Database::connect();
        $properties = $db->query("SELECT id, images FROM properties WHERE images IS NOT NULL")->getResultArray();
        
        // Process each property to convert JSON format to simple array
        foreach ($properties as $property) {
            $images = json_decode($property['images'], true);
            $simpleImageArray = [];
            
            if (is_array($images)) {
                foreach ($images as $imageData) {
                    if (is_string($imageData)) {
                        // Already in simple format, keep as is
                        $simpleImageArray[] = $imageData;
                    } elseif (is_array($imageData)) {
                        // Complex format with optimization data
                        $imagePath = $this->extractBestImagePath($imageData);
                        if ($imagePath) {
                            $simpleImageArray[] = $imagePath;
                        }
                    }
                }
            }
            
            // Update the property with the simplified array
            if (!empty($simpleImageArray)) {
                $db->query(
                    "UPDATE properties SET images = ? WHERE id = ?",
                    [json_encode($simpleImageArray), $property['id']]
                );
            }
        }
        
        log_message('info', 'RevertImagesToArrayFormat: Successfully converted ' . count($properties) . ' properties to simple array format');
    }

    public function down()
    {
        // Note: This down migration cannot fully restore the complex optimization data
        // as that information would be lost during the up migration.
        // The images will remain in simple array format.
        log_message('warning', 'RevertImagesToArrayFormat: Down migration cannot restore complex optimization data');
    }
    
    /**
     * Extract the best available image path from complex optimization data
     * 
     * @param array $imageData Complex image data with optimization info
     * @return string|null Best available image path
     */
    private function extractBestImagePath(array $imageData): ?string
    {
        // Try to get the best available image path
        // Priority: medium > large > thumbnail > any available
        
        if (isset($imageData['sizes'])) {
            $sizes = $imageData['sizes'];
            
            // Prefer medium size
            if (isset($sizes['medium']['jpeg'])) {
                return 'uploads/properties/' . basename($sizes['medium']['jpeg']);
            }
            if (isset($sizes['medium']['webp'])) {
                return 'uploads/properties/' . basename($sizes['medium']['webp']);
            }
            
            // Fallback to large size
            if (isset($sizes['large']['jpeg'])) {
                return 'uploads/properties/' . basename($sizes['large']['jpeg']);
            }
            if (isset($sizes['large']['webp'])) {
                return 'uploads/properties/' . basename($sizes['large']['webp']);
            }
            
            // Fallback to thumbnail
            if (isset($sizes['thumbnail']['jpeg'])) {
                return 'uploads/properties/' . basename($sizes['thumbnail']['jpeg']);
            }
            if (isset($sizes['thumbnail']['webp'])) {
                return 'uploads/properties/' . basename($sizes['thumbnail']['webp']);
            }
        }
        
        // Try to extract from base_filename if available
        if (isset($imageData['base_filename'])) {
            // Look for existing files with this base name
            $baseName = $imageData['base_filename'];
            $possiblePaths = [
                "uploads/properties/{$baseName}.jpg",
                "uploads/properties/{$baseName}.jpeg",
                "uploads/properties/{$baseName}.png",
                "uploads/properties/{$baseName}.webp"
            ];
            
            foreach ($possiblePaths as $path) {
                if (file_exists(FCPATH . $path)) {
                    return $path;
                }
            }
        }
        
        return null;
    }
}
