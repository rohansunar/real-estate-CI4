<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * CreateCommissionTransactionsTable Migration
 *
 * Creates the commission_transactions table to track commission distributions
 * throughout the agent hierarchy when property sales occur.
 *
 * Features:
 * - Tracks commission amounts and percentages for each agent in the hierarchy
 * - Links to property sales and the agents involved
 * - Supports multi-level commission distribution (10% total up the hierarchy)
 * - Includes transaction status and timestamps for audit trails
 * - Optimized indexes for efficient hierarchy queries
 *
 * @author Real Estate Team
 * @version 1.0
 * @since 2025-08-08
 */
class CreateCommissionTransactionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'property_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'Reference to the sold property'
            ],
            'selling_agent_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'Agent who made the sale'
            ],
            'receiving_agent_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'Agent receiving the commission'
            ],
            'hierarchy_level' => [
                'type'       => 'INT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 0,
                'comment'    => 'Level in hierarchy (0=selling agent, 1=parent, 2=grandparent, etc.)'
            ],
            'sale_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'comment'    => 'Total property sale amount'
            ],
            'commission_percentage' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
                'comment'    => 'Commission percentage for this level'
            ],
            'commission_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'comment'    => 'Actual commission amount earned'
            ],
            'transaction_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'processed', 'paid', 'cancelled'],
                'default'    => 'pending',
                'comment'    => 'Status of commission payment'
            ],
            'transaction_reference' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'External transaction reference'
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Additional notes about the commission'
            ],
            'processed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'When commission was processed'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['property_id', 'selling_agent_id'], false, 'idx_property_selling_agent');
        $this->forge->addKey('receiving_agent_id', false, 'idx_receiving_agent');
        $this->forge->addKey(['receiving_agent_id', 'transaction_status'], false, 'idx_agent_status');
        $this->forge->addKey('hierarchy_level', false, 'idx_hierarchy_level');
        $this->forge->addKey('created_at', false, 'idx_created_at');

        $this->forge->createTable('commission_transactions');

        // Add foreign key constraints
        $this->db->query('ALTER TABLE commission_transactions ADD CONSTRAINT fk_commission_property FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE commission_transactions ADD CONSTRAINT fk_commission_selling_agent FOREIGN KEY (selling_agent_id) REFERENCES agents(id) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE commission_transactions ADD CONSTRAINT fk_commission_receiving_agent FOREIGN KEY (receiving_agent_id) REFERENCES agents(id) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down()
    {
        // Drop foreign key constraints first
        $this->db->query('ALTER TABLE commission_transactions DROP FOREIGN KEY fk_commission_property');
        $this->db->query('ALTER TABLE commission_transactions DROP FOREIGN KEY fk_commission_selling_agent');
        $this->db->query('ALTER TABLE commission_transactions DROP FOREIGN KEY fk_commission_receiving_agent');

        $this->forge->dropTable('commission_transactions');
    }
}
