<?php

/**
 * Migration Recovery Script for White Rock Realtor
 * 
 * This script helps recover from failed migrations by:
 * 1. Removing the failed migration from the migrations table
 * 2. Cleaning up any partially created structures
 * 3. Allowing you to re-run the fixed migration
 * 
 * Usage: php migration_recovery.php
 */

require_once 'vendor/autoload.php';

// Load CodeIgniter
$pathsConfig = new Config\Paths();
require_once $pathsConfig->systemDirectory . '/bootstrap.php';

$app = Config\Services::codeigniter();
$app->initialize();

$db = \Config\Database::connect();

echo "=== White Rock Realtor Migration Recovery Tool ===\n\n";

$migrationFile = '2025-08-08-173508_AddHierarchyIndexesToAgents';

try {
    // Check if the migration is recorded as completed
    $migration = $db->table('migrations')
        ->where('filename', $migrationFile)
        ->get()->getRowArray();

    if ($migration) {
        echo "Found migration record: {$migrationFile}\n";
        echo "Batch: {$migration['batch']}\n";
        echo "Recorded at: {$migration['time']}\n\n";

        echo "This migration is marked as completed but may have failed partially.\n";
        echo "Do you want to remove it from the migrations table so you can re-run it? (y/n): ";
        
        $handle = fopen("php://stdin", "r");
        $response = trim(fgets($handle));
        fclose($handle);

        if (strtolower($response) === 'y' || strtolower($response) === 'yes') {
            // Remove the migration record
            $db->table('migrations')->where('filename', $migrationFile)->delete();
            echo "✅ Removed migration record from database.\n";
            echo "You can now run: php spark migrate\n\n";
        } else {
            echo "❌ Migration record not removed. Exiting.\n";
            exit(0);
        }
    } else {
        echo "✅ Migration is not recorded as completed. You can run it normally.\n";
        echo "Run: php spark migrate\n\n";
    }

    // Optional: Clean up any problematic structures
    echo "=== OPTIONAL CLEANUP ===\n";
    echo "Do you want to clean up any existing indexes that might conflict? (y/n): ";
    
    $handle = fopen("php://stdin", "r");
    $response = trim(fgets($handle));
    fclose($handle);

    if (strtolower($response) === 'y' || strtolower($response) === 'yes') {
        $indexesToCheck = [
            'idx_parent_active',
            'idx_active_created', 
            'idx_email_active',
            'idx_hierarchy_level',
            'idx_parent_level'
        ];

        foreach ($indexesToCheck as $indexName) {
            $exists = $db->query("SHOW INDEX FROM agents WHERE Key_name = ?", [$indexName])->getNumRows() > 0;
            if ($exists) {
                try {
                    $db->query("ALTER TABLE agents DROP INDEX {$indexName}");
                    echo "✅ Dropped existing index: {$indexName}\n";
                } catch (Exception $e) {
                    echo "⚠️  Could not drop index {$indexName}: " . $e->getMessage() . "\n";
                }
            }
        }

        // Check and remove hierarchy_level column if it exists
        $columnExists = $db->query("SHOW COLUMNS FROM agents LIKE 'hierarchy_level'")->getNumRows() > 0;
        if ($columnExists) {
            try {
                $db->query("ALTER TABLE agents DROP COLUMN hierarchy_level");
                echo "✅ Dropped existing column: hierarchy_level\n";
            } catch (Exception $e) {
                echo "⚠️  Could not drop column hierarchy_level: " . $e->getMessage() . "\n";
            }
        }

        echo "\n✅ Cleanup completed. You can now run: php spark migrate\n";
    }

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== RECOVERY SCRIPT COMPLETED ===\n";
