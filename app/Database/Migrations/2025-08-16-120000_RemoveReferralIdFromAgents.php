<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * RemoveReferralIdFromAgents Migration
 *
 * Removes the referral_id field from the agents table as it's no longer needed.
 * The agent hierarchy system will rely solely on parent_agent_id for relationships.
 *
 * Features:
 * - Removes referral_id column and its unique constraint
 * - Preserves all other agent fields and functionality
 * - Maintains agent hierarchy through parent_agent_id
 *
 * @author White Rock Realtor Team
 * @version 1.0
 * @since 2025-08-16
 */
class RemoveReferralIdFromAgents extends Migration
{
    public function up()
    {
        // Drop the unique constraint for referral_id first
        $this->db->query('ALTER TABLE agents DROP INDEX unique_referral_id');
        
        // Drop the referral_id column
        $this->forge->dropColumn('agents', 'referral_id');
    }

    public function down()
    {
        // Add back the referral_id column
        $this->forge->addColumn('agents', [
            'referral_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'id'
            ]
        ]);
        
        // Add back the unique constraint
        $this->db->query('ALTER TABLE agents ADD UNIQUE KEY unique_referral_id (referral_id)');
    }
}
