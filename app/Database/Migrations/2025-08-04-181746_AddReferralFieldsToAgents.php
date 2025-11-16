<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * AddReferralFieldsToAgents Migration
 *
 * Adds referral_id and unique_agent_id fields to the agents table
 * to support agent hierarchy and referral system functionality.
 *
 * Features:
 * - referral_id: Unique identifier for agent referrals (VARCHAR, unique, indexed)
 * - unique_agent_id: Auto-generated unique agent identifier (VARCHAR, unique, indexed)
 * - password: Password field for agent authentication
 * - parent_agent_id: Foreign key to support agent hierarchy
 *
 * @author Gold Properties Team
 * @version 1.0
 * @since 2025-08-04
 */
class AddReferralFieldsToAgents extends Migration
{
    public function up()
    {
        // First, add columns without unique constraints
        $this->forge->addColumn('agents', [
            'referral_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'id'
            ],
            'unique_agent_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'referral_id'
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'email'
            ],
            'parent_agent_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'password'
            ]
        ]);

        // Generate unique IDs for existing agents
        $agents = $this->db->table('agents')->get()->getResultArray();
        foreach ($agents as $agent) {
            $uniqueId = 'WRR' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // Ensure uniqueness
            while ($this->db->table('agents')->where('unique_agent_id', $uniqueId)->get()->getNumRows() > 0) {
                $uniqueId = 'WRR' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            }

            $this->db->table('agents')
                    ->where('id', $agent['id'])
                    ->update(['unique_agent_id' => $uniqueId]);
        }

        // Now add unique constraints and indexes
        $this->db->query('ALTER TABLE agents ADD UNIQUE KEY unique_referral_id (referral_id)');
        $this->db->query('ALTER TABLE agents ADD UNIQUE KEY unique_agent_id_key (unique_agent_id)');
        $this->db->query('ALTER TABLE agents ADD INDEX idx_parent_agent_id (parent_agent_id)');

        // Add foreign key constraint for parent_agent_id
        $this->db->query('ALTER TABLE agents ADD CONSTRAINT fk_parent_agent FOREIGN KEY (parent_agent_id) REFERENCES agents(id) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        // Drop foreign key constraint first
        $this->db->query('ALTER TABLE agents DROP FOREIGN KEY fk_parent_agent');

        // Drop indexes
        $this->db->query('ALTER TABLE agents DROP INDEX unique_referral_id');
        $this->db->query('ALTER TABLE agents DROP INDEX unique_agent_id_key');
        $this->db->query('ALTER TABLE agents DROP INDEX idx_parent_agent_id');

        // Drop the added columns (keeping unique_agent_id, password, parent_agent_id but removing referral_id)
        $this->forge->dropColumn('agents', ['referral_id', 'unique_agent_id', 'password', 'parent_agent_id']);
    }
}
