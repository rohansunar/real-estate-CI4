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
 * @author Real Estate Team
 * @version 1.0
 * @since 2025-08-08
 */
class AddHierarchyIndexesToAgents extends Migration
{
    public function up()
    {
        // Add composite indexes for hierarchy operations
        $this->db->query('ALTER TABLE agents ADD INDEX idx_parent_active (parent_agent_id, is_active)');
        $this->db->query('ALTER TABLE agents ADD INDEX idx_active_created (is_active, created_at)');
        $this->db->query('ALTER TABLE agents ADD INDEX idx_email_active (email, is_active)');

        // Add hierarchy level field for caching hierarchy depth (optional optimization)
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

        // Add index for hierarchy level
        $this->db->query('ALTER TABLE agents ADD INDEX idx_hierarchy_level (hierarchy_level)');
        $this->db->query('ALTER TABLE agents ADD INDEX idx_parent_level (parent_agent_id, hierarchy_level)');
    }

    public function down()
    {
        // Drop the added indexes
        $this->db->query('ALTER TABLE agents DROP INDEX idx_parent_active');
        $this->db->query('ALTER TABLE agents DROP INDEX idx_active_created');
        $this->db->query('ALTER TABLE agents DROP INDEX idx_email_active');
        $this->db->query('ALTER TABLE agents DROP INDEX idx_hierarchy_level');
        $this->db->query('ALTER TABLE agents DROP INDEX idx_parent_level');

        // Drop the hierarchy_level column
        $this->forge->dropColumn('agents', 'hierarchy_level');
    }
}
