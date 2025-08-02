<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddYoutubeVideoToProperties extends Migration
{
    public function up()
    {
        $this->forge->addColumn('properties', [
            'youtube_video' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'after'      => 'images'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('properties', 'youtube_video');
    }
}
