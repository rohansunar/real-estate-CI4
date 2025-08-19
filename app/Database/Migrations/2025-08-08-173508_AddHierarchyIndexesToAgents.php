<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * AddHierarchyIndexesToAgents Migration
 *
 * Adds additional indexes to the agents table to optimize hierarchy queries
 * and multi-level agent management operations.
 *
 * Features:
 * - Composite indexes for efficient parent-child queries
 * - Status-based filtering indexes
 * - Email and unique ID lookup optimization
 * - Hierarchy depth calculation support
 *
 * @author White Rock Realtor Team
 * @version 1.0
 * @since 2025-08-08
 */
class AddHierarchyIndexesToAgents extends Migration
{
    public function up()
    {
        // Helper function to check if index exists
        $indexExists = function($indexName) {
            $query = $this->db->query("SHOW INDEX FROM agents WHERE Key_name = ?", [$indexName]);
            return $query->getNumRows() > 0;
        };

        // Helper function to check if column exists
        $columnExists = function($columnName) {
            $query = $this->db->query("SHOW COLUMNS FROM agents LIKE ?", [$columnName]);
            return $query->getNumRows() > 0;
        };

        // Add composite indexes for hierarchy operations (only if they don't exist)
        if (!$indexExists('idx_parent_active')) {
            $this->db->query('ALTER TABLE agents ADD INDEX idx_parent_active (parent_agent_id, is_active)');
            echo "Created index: idx_parent_active\n";
        } else {
            echo "Index idx_parent_active already exists, skipping...\n";
        }

        if (!$indexExists('idx_active_created')) {
            $this->db->query('ALTER TABLE agents ADD INDEX idx_active_created (is_active, created_at)');
            echo "Created index: idx_active_created\n";
        } else {
            echo "Index idx_active_created already exists, skipping...\n";
        }

        if (!$indexExists('idx_email_active')) {
            $this->db->query('ALTER TABLE agents ADD INDEX idx_email_active (email, is_active)');
            echo "Created index: idx_email_active\n";
        } else {
            echo "Index idx_email_active already exists, skipping...\n";
        }

        // Add hierarchy level field for caching hierarchy depth (only if it doesn't exist)
        if (!$columnExists('hierarchy_level')) {
            $this->forge->addColumn('agents', [
                'hierarchy_level' => [
                    'type'       => 'INT',
                    'constraint' => 3,
                    'unsigned'   => true,
                    'default'    => 0,
                    'null'       => false,
                    'after'      => 'parent_agent_id',
                    'comment'    => 'Cached hierarchy level for performance (0=top level, 1=sub-agent, etc.)'
                ]
            ]);
            echo "Created column: hierarchy_level\n";
        } else {
            echo "Column hierarchy_level already exists, skipping...\n";
        }

        // Add indexes for hierarchy level (only if they don't exist)
        if (!$indexExists('idx_hierarchy_level')) {
            $this->db->query('ALTER TABLE agents ADD INDEX idx_hierarchy_level (hierarchy_level)');
            echo "Created index: idx_hierarchy_level\n";
        } else {
            echo "Index idx_hierarchy_level already exists, skipping...\n";
        }

        if (!$indexExists('idx_parent_level')) {
            $this->db->query('ALTER TABLE agents ADD INDEX idx_parent_level (parent_agent_id, hierarchy_level)');
            echo "Created index: idx_parent_level\n";
        } else {
            echo "Index idx_parent_level already exists, skipping...\n";
        }
    }

    public function down()
    {
        // Helper function to check if index exists
        $indexExists = function($indexName) {
            $query = $this->db->query("SHOW INDEX FROM agents WHERE Key_name = ?", [$indexName]);
            return $query->getNumRows() > 0;
        };

        // Helper function to check if column exists
        $columnExists = function($columnName) {
            $query = $this->db->query("SHOW COLUMNS FROM agents LIKE ?", [$columnName]);
            return $query->getNumRows() > 0;
        };

        // Drop the added indexes (only if they exist)
        if ($indexExists('idx_parent_active')) {
            $this->db->query('ALTER TABLE agents DROP INDEX idx_parent_active');
            echo "Dropped index: idx_parent_active\n";
        }

        if ($indexExists('idx_active_created')) {
            $this->db->query('ALTER TABLE agents DROP INDEX idx_active_created');
            echo "Dropped index: idx_active_created\n";
        }

        if ($indexExists('idx_email_active')) {
            $this->db->query('ALTER TABLE agents DROP INDEX idx_email_active');
            echo "Dropped index: idx_email_active\n";
        }

        if ($indexExists('idx_hierarchy_level')) {
            $this->db->query('ALTER TABLE agents DROP INDEX idx_hierarchy_level');
            echo "Dropped index: idx_hierarchy_level\n";
        }

        if ($indexExists('idx_parent_level')) {
            $this->db->query('ALTER TABLE agents DROP INDEX idx_parent_level');
            echo "Dropped index: idx_parent_level\n";
        }

        // Drop the hierarchy_level column (only if it exists)
        if ($columnExists('hierarchy_level')) {
            $this->forge->dropColumn('agents', 'hierarchy_level');
            echo "Dropped column: hierarchy_level\n";
        }
    }
}
