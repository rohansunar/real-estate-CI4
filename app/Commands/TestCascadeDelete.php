<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\PropertyModel;

/**
 * TestCascadeDelete Command
 * 
 * Tests the cascade delete functionality by using the PropertyModel
 * to delete a property and verify that associated images are automatically removed.
 * 
 * Usage: php spark test:cascade-delete [property_id]
 */
class TestCascadeDelete extends BaseCommand
{
    protected $group = 'Testing';
    protected $name = 'test:cascade-delete';
    protected $description = 'Test cascade delete functionality for property images';
    protected $usage = 'test:cascade-delete [property_id]';
    protected $arguments = [
        'property_id' => 'ID of the property to delete (optional, defaults to 47)'
    ];

    public function run(array $params)
    {
        $propertyId = $params[0] ?? 47;
        
        CLI::write('=== Testing Cascade Delete Functionality ===', 'yellow');
        CLI::write("Testing with Property ID: {$propertyId}", 'white');
        CLI::newLine();

        $propertyModel = new PropertyModel();
        
        // Step 1: Verify property exists and get image data
        CLI::write('Step 1: Checking property and images...', 'cyan');
        
        $property = $propertyModel->find($propertyId);
        
        if (!$property) {
            CLI::error("Property {$propertyId} not found!");
            return;
        }
        
        CLI::write("Property: {$property['title']}", 'green');
        CLI::write("Images data: {$property['images']}", 'white');
        
        // Parse images
        $images = is_string($property['images']) ? json_decode($property['images'], true) : $property['images'];
        
        if (empty($images) || !is_array($images)) {
            CLI::error('No images found for this property!');
            return;
        }
        
        // Step 2: Check which image files exist before deletion
        CLI::newLine();
        CLI::write('Step 2: Checking image files before deletion...', 'cyan');
        
        $existingImages = [];
        foreach ($images as $imagePath) {
            if (is_string($imagePath)) {
                // Legacy format
                $fullPath = FCPATH . $imagePath;
                if (file_exists($fullPath)) {
                    $existingImages[] = $imagePath;
                    CLI::write("✓ Image exists: {$imagePath}", 'green');
                } else {
                    CLI::write("✗ Image missing: {$imagePath}", 'red');
                }
            } elseif (is_array($imagePath) && isset($imagePath['sizes'])) {
                // New format with size variants
                foreach ($imagePath['sizes'] as $size => $sizeData) {
                    if (isset($sizeData['jpeg'])) {
                        $fullPath = FCPATH . $sizeData['jpeg'];
                        if (file_exists($fullPath)) {
                            $existingImages[] = $sizeData['jpeg'];
                            CLI::write("✓ Image exists ({$size} JPEG): {$sizeData['jpeg']}", 'green');
                        }
                    }
                    if (isset($sizeData['webp'])) {
                        $fullPath = FCPATH . $sizeData['webp'];
                        if (file_exists($fullPath)) {
                            $existingImages[] = $sizeData['webp'];
                            CLI::write("✓ Image exists ({$size} WebP): {$sizeData['webp']}", 'green');
                        }
                    }
                }
            }
        }
        
        if (empty($existingImages)) {
            CLI::error('No image files found on disk!');
            return;
        }
        
        CLI::write("Found " . count($existingImages) . " image files to test", 'white');
        
        // Step 3: Confirm deletion
        CLI::newLine();
        CLI::write('Step 3: Confirming deletion...', 'cyan');
        
        $confirm = CLI::prompt("Are you sure you want to delete property {$propertyId}? This will test cascade delete.", ['y', 'n']);
        
        if ($confirm !== 'y') {
            CLI::write('Test cancelled.', 'yellow');
            return;
        }
        
        // Step 4: Delete property using PropertyModel (this should trigger cascade delete)
        CLI::newLine();
        CLI::write('Step 4: Deleting property (should trigger cascade delete)...', 'cyan');
        
        $deleteResult = $propertyModel->delete($propertyId);
        
        if (!$deleteResult) {
            CLI::error('Failed to delete property!');
            return;
        }
        
        CLI::write('✓ Property deleted successfully', 'green');
        
        // Step 5: Check if images were automatically deleted
        CLI::newLine();
        CLI::write('Step 5: Verifying cascade delete worked...', 'cyan');
        
        $deletedCount = 0;
        $remainingCount = 0;
        
        foreach ($existingImages as $imagePath) {
            $fullPath = FCPATH . $imagePath;
            if (file_exists($fullPath)) {
                CLI::write("✗ Image still exists: {$imagePath}", 'red');
                $remainingCount++;
            } else {
                CLI::write("✓ Image deleted: {$imagePath}", 'green');
                $deletedCount++;
            }
        }
        
        // Step 6: Results
        CLI::newLine();
        CLI::write('=== Test Results ===', 'yellow');
        CLI::write("Images deleted: {$deletedCount}", 'white');
        CLI::write("Images remaining: {$remainingCount}", 'white');
        
        if ($remainingCount === 0) {
            CLI::write('✅ CASCADE DELETE TEST PASSED!', 'green');
            CLI::write('All images were automatically deleted when the property was removed.', 'green');
        } else {
            CLI::write('❌ CASCADE DELETE TEST FAILED!', 'red');
            CLI::write('Some images were not automatically deleted.', 'red');
            
            // Clean up remaining images manually
            CLI::newLine();
            $cleanup = CLI::prompt('Clean up remaining images manually?', ['y', 'n']);
            if ($cleanup === 'y') {
                foreach ($existingImages as $imagePath) {
                    $fullPath = FCPATH . $imagePath;
                    if (file_exists($fullPath)) {
                        if (unlink($fullPath)) {
                            CLI::write("✓ Manually deleted: {$imagePath}", 'yellow');
                        } else {
                            CLI::write("✗ Failed to delete: {$imagePath}", 'red');
                        }
                    }
                }
            }
        }
        
        CLI::newLine();
        CLI::write('Test completed.', 'white');
    }
}
