<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPropertyIdToContacts extends Migration
{
    public function up()
    {
        // Add property_id column to contacts table for property-specific inquiries
        $this->forge->addColumn('contacts', [
            'property_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'properties_in'
            ]
        ]);

        // Add foreign key constraint (optional, but good practice)
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        // Drop foreign key first
        $this->forge->dropForeignKey('contacts', 'contacts_property_id_foreign');

        // Drop the column
        $this->forge->dropColumn('contacts', 'property_id');
    }
}
