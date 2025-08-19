<?php

/**
 * Database State Checker for White Rock Realtor
 * 
 * This script helps diagnose the current state of the agents table
 * before running migrations on shared hosting.
 * 
 * Usage: php check_database_state.php
 */

require_once 'vendor/autoload.php';

// Load CodeIgniter
$pathsConfig = new Config\Paths();
require_once $pathsConfig->systemDirectory . '/bootstrap.php';

$app = Config\Services::codeigniter();
$app->initialize();

$db = \Config\Database::connect();

echo "=== White Rock Realtor Database State Checker ===\n\n";

try {
    // Check if agents table exists
    if (!$db->tableExists('agents')) {
        echo "❌ ERROR: 'agents' table does not exist!\n";
        echo "Please run the basic migrations first:\n";
        echo "php spark migrate --to=2025-08-01-000002\n\n";
        exit(1);
    }

    echo "✅ 'agents' table exists\n\n";

    // Check table structure
    echo "=== AGENTS TABLE STRUCTURE ===\n";
    $columns = $db->query("SHOW COLUMNS FROM agents")->getResultArray();
    foreach ($columns as $column) {
        echo "- {$column['Field']} ({$column['Type']}) " . 
             ($column['Null'] === 'YES' ? 'NULL' : 'NOT NULL') . 
             ($column['Default'] ? " DEFAULT {$column['Default']}" : '') . "\n";
    }
    echo "\n";

    // Check existing indexes
    echo "=== EXISTING INDEXES ===\n";
    $indexes = $db->query("SHOW INDEX FROM agents")->getResultArray();
    $indexNames = [];
    foreach ($indexes as $index) {
        if (!in_array($index['Key_name'], $indexNames)) {
            $indexNames[] = $index['Key_name'];
            $unique = $index['Non_unique'] == 0 ? ' (UNIQUE)' : '';
            echo "- {$index['Key_name']}{$unique}\n";
        }
    }
    echo "\n";

    // Check for specific indexes that the migration will try to create
    echo "=== MIGRATION CONFLICT CHECK ===\n";
    $requiredIndexes = [
        'idx_parent_active',
        'idx_active_created', 
        'idx_email_active',
        'idx_hierarchy_level',
        'idx_parent_level'
    ];

    $conflictFound = false;
    foreach ($requiredIndexes as $indexName) {
        $exists = $db->query("SHOW INDEX FROM agents WHERE Key_name = ?", [$indexName])->getNumRows() > 0;
        if ($exists) {
            echo "⚠️  Index '{$indexName}' already exists\n";
            $conflictFound = true;
        } else {
            echo "✅ Index '{$indexName}' does not exist (will be created)\n";
        }
    }

    // Check for hierarchy_level column
    $hierarchyLevelExists = $db->query("SHOW COLUMNS FROM agents LIKE 'hierarchy_level'")->getNumRows() > 0;
    if ($hierarchyLevelExists) {
        echo "⚠️  Column 'hierarchy_level' already exists\n";
        $conflictFound = true;
    } else {
        echo "✅ Column 'hierarchy_level' does not exist (will be created)\n";
    }

    echo "\n";

    // Check migration status
    echo "=== MIGRATION STATUS ===\n";
    if ($db->tableExists('migrations')) {
        $migrations = $db->table('migrations')->orderBy('batch', 'ASC')->get()->getResultArray();
        echo "Completed migrations:\n";
        foreach ($migrations as $migration) {
            echo "- {$migration['filename']} (batch {$migration['batch']})\n";
        }
        
        // Check if the problematic migration has been run
        $problemMigration = $db->table('migrations')
            ->where('filename', '2025-08-08-173508_AddHierarchyIndexesToAgents')
            ->get()->getRowArray();
        
        if ($problemMigration) {
            echo "\n⚠️  The problematic migration has already been recorded as completed!\n";
            echo "This suggests a partial migration failure.\n";
        }
    } else {
        echo "❌ Migrations table does not exist. Please run: php spark migrate:create\n";
    }

    echo "\n=== RECOMMENDATIONS ===\n";
    
    if ($conflictFound) {
        echo "✅ GOOD NEWS: The fixed migration will handle existing indexes/columns safely.\n";
        echo "The migration has been updated to check for existing structures before creating them.\n\n";
        echo "You can now safely run: php spark migrate\n";
    } else {
        echo "✅ No conflicts detected. You can run the migration normally.\n";
        echo "Run: php spark migrate\n";
    }

    echo "\n=== AGENT COUNT ===\n";
    $agentCount = $db->table('agents')->countAllResults();
    echo "Total agents in database: {$agentCount}\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Please check your database connection settings in .env file\n";
    exit(1);
}

echo "\n=== SCRIPT COMPLETED ===\n";
