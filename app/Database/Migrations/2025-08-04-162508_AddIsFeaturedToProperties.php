<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsFeaturedToProperties extends Migration
{
    public function up()
    {
        // Add is_featured column to properties table
        $this->forge->addColumn('properties', [
            'is_featured' => [
                'type'       => 'BOOLEAN',
                'default'    => false,
                'null'       => false,
                'after'      => 'area'
            ]
        ]);
    }

    public function down()
    {
        // Remove is_featured column from properties table
        $this->forge->dropColumn('properties', 'is_featured');
    }
}
