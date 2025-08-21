<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\AgentModel;
use App\Models\PropertyModel;

/**
 * MigrateImages Command
 * 
 * Migrates existing images from inconsistent storage locations to the unified
 * public/uploads/ directory structure. This command handles:
 * - Moving agent images from writable/uploads/agents/ to public/uploads/agents/
 * - Moving property images from writable/uploads/properties/ to public/uploads/properties/
 * - Updating database references to reflect new paths
 * - Creating necessary directory structure with proper permissions
 * 
 * Usage:
 * php spark migrate:images [--dry-run] [--force]
 * 
 * Options:
 * --dry-run: Show what would be migrated without actually moving files
 * --force: Skip confirmation prompts
 * 
 * @author White Rock Realtor Team
 * @version 1.0
 * @since 2025-08-20
 */
class MigrateImages extends BaseCommand
{
    /**
     * The Command's Group
     */
    protected $group = 'Migration';

    /**
     * The Command's Name
     */
    protected $name = 'migrate:images';

    /**
     * The Command's Description
     */
    protected $description = 'Migrate images from inconsistent storage locations to unified public/uploads/ structure';

    /**
     * The Command's Usage
     */
    protected $usage = 'migrate:images [--dry-run] [--force]';

    /**
     * The Command's Arguments
     */
    protected $arguments = [];

    /**
     * The Command's Options
     */
    protected $options = [
        '--dry-run' => 'Show what would be migrated without actually moving files',
        '--force'   => 'Skip confirmation prompts'
    ];

    /**
     * Migration statistics
     */
    private $stats = [
        'agents_migrated' => 0,
        'agents_failed' => 0,
        'properties_migrated' => 0,
        'properties_failed' => 0,
        'files_moved' => 0,
        'files_failed' => 0
    ];

    /**
     * Execute the command
     */
    public function run(array $params)
    {
        $isDryRun = CLI::getOption('dry-run');
        $isForced = CLI::getOption('force');

        CLI::write('Image Storage Migration Tool', 'yellow');
        CLI::write('================================', 'yellow');
        CLI::newLine();

        if ($isDryRun) {
            CLI::write('DRY RUN MODE - No files will be moved', 'cyan');
            CLI::newLine();
        }

        // Show current storage analysis
        $this->analyzeCurrentStorage();

        if (!$isForced && !$isDryRun) {
            $confirm = CLI::prompt('Do you want to proceed with the migration?', ['y', 'n']);
            if ($confirm !== 'y') {
                CLI::write('Migration cancelled.', 'yellow');
                return;
            }
        }

        CLI::newLine();
        CLI::write('Starting image migration...', 'green');
        CLI::newLine();

        try {
            // Create unified directory structure
            if (!$isDryRun) {
                $this->createUnifiedDirectories();
            }

            // Migrate agent images
            $this->migrateAgentImages($isDryRun);

            // Migrate property images
            $this->migratePropertyImages($isDryRun);

            // Display final statistics
            $this->displayMigrationSummary($isDryRun);

        } catch (\Exception $e) {
            CLI::error('Migration failed: ' . $e->getMessage());
            return;
        }
    }

    /**
     * Analyze current storage locations
     */
    private function analyzeCurrentStorage()
    {
        CLI::write('Analyzing current image storage...', 'white');
        CLI::newLine();

        // Check agent images in writable directory
        $writableAgentsPath = WRITEPATH . 'uploads/agents';
        $agentImageCount = 0;
        if (is_dir($writableAgentsPath)) {
            $agentImages = glob($writableAgentsPath . '/*');
            $agentImageCount = count(array_filter($agentImages, 'is_file'));
        }

        // Check property images in writable directory
        $writablePropertiesPath = WRITEPATH . 'uploads/properties';
        $propertyImageCount = 0;
        if (is_dir($writablePropertiesPath)) {
            $propertyImages = glob($writablePropertiesPath . '/*');
            $propertyImageCount = count(array_filter($propertyImages, 'is_file'));
        }

        // Check existing public directory structure
        $publicAgentsPath = FCPATH . 'uploads/agents';
        $publicPropertiesPath = FCPATH . 'uploads/properties';

        CLI::write("Agent images in writable/uploads/agents/: {$agentImageCount}", 'white');
        CLI::write("Property images in writable/uploads/properties/: {$propertyImageCount}", 'white');
        CLI::write("Public agents directory exists: " . (is_dir($publicAgentsPath) ? 'Yes' : 'No'), 'white');
        CLI::write("Public properties directory exists: " . (is_dir($publicPropertiesPath) ? 'Yes' : 'No'), 'white');
        CLI::newLine();
    }

    /**
     * Create unified directory structure
     */
    private function createUnifiedDirectories()
    {
        $directories = [
            FCPATH . 'uploads',
            FCPATH . 'uploads/agents',
            FCPATH . 'uploads/properties',
            FCPATH . 'uploads/properties/thumbnails',
            FCPATH . 'uploads/properties/medium',
            FCPATH . 'uploads/properties/large',
            FCPATH . 'uploads/properties/webp'
        ];

        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                if (!mkdir($dir, 0755, true)) {
                    throw new \Exception("Failed to create directory: {$dir}");
                }
                CLI::write("Created directory: {$dir}", 'green');
            }

            // Create security index.html file
            $indexFile = $dir . '/index.html';
            if (!file_exists($indexFile)) {
                file_put_contents($indexFile, '<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><h1>Directory access is forbidden.</h1></body></html>');
            }
        }
    }

    /**
     * Migrate agent images
     */
    private function migrateAgentImages(bool $isDryRun)
    {
        CLI::write('Migrating agent images...', 'yellow');

        $agentModel = new AgentModel();
        $agents = $agentModel->where('profile_image IS NOT NULL')
                            ->where('profile_image !=', '')
                            ->findAll();

        foreach ($agents as $agent) {
            $this->migrateAgentImage($agent, $isDryRun);
        }

        CLI::write("Agent migration complete: {$this->stats['agents_migrated']} migrated, {$this->stats['agents_failed']} failed", 'green');
        CLI::newLine();
    }

    /**
     * Migrate single agent image
     */
    private function migrateAgentImage(array $agent, bool $isDryRun)
    {
        $currentPath = $agent['profile_image'];
        
        // Skip if already in correct location
        if (str_starts_with($currentPath, 'uploads/agents/')) {
            return;
        }

        // Construct old and new paths
        $oldAbsolutePath = WRITEPATH . $currentPath;
        $filename = basename($currentPath);
        $newRelativePath = 'uploads/agents/' . $filename;
        $newAbsolutePath = FCPATH . $newRelativePath;

        if (!file_exists($oldAbsolutePath)) {
            CLI::write("Agent {$agent['id']}: Image file not found: {$oldAbsolutePath}", 'red');
            $this->stats['agents_failed']++;
            return;
        }

        if ($isDryRun) {
            CLI::write("Would migrate agent {$agent['id']}: {$currentPath} -> {$newRelativePath}", 'cyan');
            $this->stats['agents_migrated']++;
            return;
        }

        // Move the file
        if (rename($oldAbsolutePath, $newAbsolutePath)) {
            // Update database record
            $agentModel = new AgentModel();
            if ($agentModel->update($agent['id'], ['profile_image' => $newRelativePath])) {
                CLI::write("Migrated agent {$agent['id']}: {$filename}", 'green');
                $this->stats['agents_migrated']++;
                $this->stats['files_moved']++;
            } else {
                CLI::write("Agent {$agent['id']}: File moved but database update failed", 'red');
                $this->stats['agents_failed']++;
            }
        } else {
            CLI::write("Agent {$agent['id']}: Failed to move file: {$filename}", 'red');
            $this->stats['agents_failed']++;
            $this->stats['files_failed']++;
        }
    }

    /**
     * Migrate property images
     */
    private function migratePropertyImages(bool $isDryRun)
    {
        CLI::write('Migrating property images...', 'yellow');

        $propertyModel = new PropertyModel();
        $properties = $propertyModel->where('images IS NOT NULL')
                                   ->where('images !=', '')
                                   ->where('images !=', '[]')
                                   ->findAll();

        foreach ($properties as $property) {
            $this->migratePropertyImage($property, $isDryRun);
        }

        CLI::write("Property migration complete: {$this->stats['properties_migrated']} migrated, {$this->stats['properties_failed']} failed", 'green');
        CLI::newLine();
    }

    /**
     * Migrate single property images
     */
    private function migratePropertyImage(array $property, bool $isDryRun)
    {
        $images = is_string($property['images']) ? json_decode($property['images'], true) : $property['images'];
        
        if (empty($images) || !is_array($images)) {
            return;
        }

        $updated = false;
        $migratedImages = [];

        foreach ($images as $imageInfo) {
            if (is_string($imageInfo)) {
                // Legacy format - single image path
                $migratedPath = $this->migratePropertyImageFile($imageInfo, $isDryRun);
                if ($migratedPath) {
                    $migratedImages[] = $migratedPath;
                    $updated = true;
                } else {
                    $migratedImages[] = $imageInfo; // Keep original if migration failed
                }
            } elseif (is_array($imageInfo) && isset($imageInfo['sizes'])) {
                // New format with size variants
                $migratedImageInfo = $imageInfo;
                foreach ($imageInfo['sizes'] as $size => $sizeData) {
                    if (isset($sizeData['jpeg'])) {
                        $migratedPath = $this->migratePropertyImageFile($sizeData['jpeg'], $isDryRun);
                        if ($migratedPath) {
                            $migratedImageInfo['sizes'][$size]['jpeg'] = $migratedPath;
                            $updated = true;
                        }
                    }
                    if (isset($sizeData['webp'])) {
                        $migratedPath = $this->migratePropertyImageFile($sizeData['webp'], $isDryRun);
                        if ($migratedPath) {
                            $migratedImageInfo['sizes'][$size]['webp'] = $migratedPath;
                            $updated = true;
                        }
                    }
                }
                $migratedImages[] = $migratedImageInfo;
            } else {
                $migratedImages[] = $imageInfo; // Keep as-is if format not recognized
            }
        }

        if ($updated && !$isDryRun) {
            $propertyModel = new PropertyModel();
            if ($propertyModel->update($property['id'], ['images' => json_encode($migratedImages)])) {
                $this->stats['properties_migrated']++;
            } else {
                $this->stats['properties_failed']++;
            }
        } elseif ($updated && $isDryRun) {
            $this->stats['properties_migrated']++;
        }
    }

    /**
     * Migrate single property image file
     */
    private function migratePropertyImageFile(string $currentPath, bool $isDryRun): ?string
    {
        // Skip if already in correct location
        if (str_starts_with($currentPath, 'uploads/properties/')) {
            return null; // No migration needed
        }

        $oldAbsolutePath = WRITEPATH . $currentPath;
        $filename = basename($currentPath);
        $newRelativePath = 'uploads/properties/' . $filename;
        $newAbsolutePath = FCPATH . $newRelativePath;

        if (!file_exists($oldAbsolutePath)) {
            return null; // File doesn't exist
        }

        if ($isDryRun) {
            return $newRelativePath; // Return what the new path would be
        }

        // Move the file
        if (rename($oldAbsolutePath, $newAbsolutePath)) {
            $this->stats['files_moved']++;
            return $newRelativePath;
        } else {
            $this->stats['files_failed']++;
            return null;
        }
    }

    /**
     * Display migration summary
     */
    private function displayMigrationSummary(bool $isDryRun)
    {
        CLI::newLine();
        CLI::write('Migration Summary', 'yellow');
        CLI::write('================', 'yellow');
        
        if ($isDryRun) {
            CLI::write('DRY RUN RESULTS:', 'cyan');
        }
        
        CLI::write("Agents migrated: {$this->stats['agents_migrated']}", 'green');
        CLI::write("Agents failed: {$this->stats['agents_failed']}", $this->stats['agents_failed'] > 0 ? 'red' : 'white');
        CLI::write("Properties migrated: {$this->stats['properties_migrated']}", 'green');
        CLI::write("Properties failed: {$this->stats['properties_failed']}", $this->stats['properties_failed'] > 0 ? 'red' : 'white');
        CLI::write("Files moved: {$this->stats['files_moved']}", 'green');
        CLI::write("Files failed: {$this->stats['files_failed']}", $this->stats['files_failed'] > 0 ? 'red' : 'white');
        
        CLI::newLine();
        
        if (!$isDryRun) {
            CLI::write('Migration completed successfully!', 'green');
            CLI::write('You can now safely remove the old writable/uploads/ directories if they are empty.', 'yellow');
        } else {
            CLI::write('Run without --dry-run to perform the actual migration.', 'yellow');
        }
    }
}
