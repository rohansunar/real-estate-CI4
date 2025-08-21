<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\PropertyModel;

/**
 * Test Property Deletion and Image Cleanup Command
 * 
 * This command tests the property deletion functionality to ensure
 * that images are properly deleted from the file system when
 * properties are removed from the database.
 * 
 * Usage: php spark test:property-deletion
 */
class TestPropertyDeletion extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:property-deletion';
    protected $description = 'Test property deletion and image cleanup functionality';

    public function run(array $params)
    {
        CLI::write('=== Property Deletion and Image Cleanup Test ===', 'yellow');
        CLI::newLine();

        $propertyModel = new PropertyModel();

        // Get a property with images for testing
        $properties = $propertyModel->where('images IS NOT NULL')
                                   ->where('images !=', '')
                                   ->where('images !=', '[]')
                                   ->limit(1)
                                   ->findAll();

        if (empty($properties)) {
            CLI::error('No properties with images found for testing.');
            CLI::write('Please create a property with images first.');
            return;
        }

        $property = $properties[0];
        $propertyId = $property['id'];

        CLI::write("Testing property deletion for:");
        CLI::write("ID: {$propertyId}");
        CLI::write("Title: {$property['title']}");
        $imagesDisplay = is_string($property['images']) ? $property['images'] : json_encode($property['images']);
        CLI::write("Images: {$imagesDisplay}");
        CLI::newLine();

        // Parse images to check file existence before deletion
        $images = is_string($property['images']) ? json_decode($property['images'], true) : $property['images'];
        if (!is_array($images)) {
            CLI::error('Invalid image data format.');
            return;
        }

        CLI::write('Checking image files before deletion:', 'cyan');
        $imageFiles = [];
        foreach ($images as $imageInfo) {
            if (is_array($imageInfo) && isset($imageInfo['sizes'])) {
                // New format with size variants
                foreach ($imageInfo['sizes'] as $sizeData) {
                    if (isset($sizeData['jpeg'])) {
                        $imageFiles[] = $sizeData['jpeg'];
                    }
                    if (isset($sizeData['webp'])) {
                        $imageFiles[] = $sizeData['webp'];
                    }
                }
            } elseif (is_string($imageInfo)) {
                // Legacy format - single image path
                $imageFiles[] = $imageInfo;
            }
        }

        $existingFiles = [];
        foreach ($imageFiles as $filePath) {
            // Use the same logic as ImageManagementService::getAbsolutePath
            $relativePath = ltrim($filePath, '/');

            if (str_starts_with($relativePath, 'uploads/') ||
                str_starts_with($relativePath, 'assets/') ||
                str_starts_with($relativePath, 'public/')) {
                $absolutePath = FCPATH . $relativePath;
            } else {
                $absolutePath = FCPATH . 'uploads/' . $relativePath;
            }

            if (file_exists($absolutePath)) {
                $existingFiles[] = $absolutePath;
                CLI::write("✓ EXISTS: {$absolutePath}", 'green');
            } else {
                CLI::write("✗ MISSING: {$absolutePath}", 'red');
            }
        }

        CLI::newLine();
        CLI::write("Found " . count($existingFiles) . " existing image files.");
        CLI::newLine();

        if (empty($existingFiles)) {
            CLI::error('No image files found on disk. Cannot test deletion.');
            return;
        }

        // Ask for confirmation before deletion
        CLI::write('WARNING: This will permanently delete the property and its images!', 'red');
        $confirmation = CLI::prompt('Type "yes" to continue or anything else to cancel');

        if (strtolower($confirmation) !== 'yes') {
            CLI::write('Test cancelled.', 'yellow');
            return;
        }

        CLI::newLine();
        CLI::write('Deleting property...', 'yellow');

        // Delete the property (this should trigger the beforeDelete callback)
        $deleteResult = $propertyModel->delete($propertyId);

        if ($deleteResult) {
            CLI::write('✓ Property deleted from database successfully.', 'green');
            CLI::newLine();
            
            // Check if image files were deleted
            CLI::write('Checking image files after deletion:', 'cyan');
            $remainingFiles = 0;
            foreach ($existingFiles as $filePath) {
                if (file_exists($filePath)) {
                    CLI::write("✗ STILL EXISTS: {$filePath}", 'red');
                    $remainingFiles++;
                } else {
                    CLI::write("✓ DELETED: {$filePath}", 'green');
                }
            }
            
            CLI::newLine();
            CLI::write('Summary:', 'cyan');
            CLI::write('- Total image files before deletion: ' . count($existingFiles));
            CLI::write("- Files still remaining: {$remainingFiles}");
            CLI::write('- Files successfully deleted: ' . (count($existingFiles) - $remainingFiles));
            
            if ($remainingFiles > 0) {
                CLI::newLine();
                CLI::error('❌ IMAGE DELETION BUG CONFIRMED: Some image files were not deleted!');
                CLI::write('The beforeDelete callback may not be working properly.', 'red');
            } else {
                CLI::newLine();
                CLI::write('✅ IMAGE DELETION WORKING: All image files were properly deleted.', 'green');
            }
            
        } else {
            CLI::error('✗ Failed to delete property from database.');
            $errors = $propertyModel->errors();
            if (!empty($errors)) {
                CLI::write('Error: ' . implode(', ', $errors), 'red');
            }
        }

        CLI::newLine();
        CLI::write('=== Test Complete ===', 'yellow');
    }
}
