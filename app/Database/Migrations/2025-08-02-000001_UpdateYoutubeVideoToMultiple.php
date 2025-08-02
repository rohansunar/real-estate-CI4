<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateYoutubeVideoToMultiple extends Migration
{
    public function up()
    {
        // Change youtube_video field to support JSON array of videos
        $this->forge->modifyColumn('properties', [
            'youtube_video' => [
                'type'       => 'JSON',
                'null'       => true,
                'comment'    => 'Array of YouTube video URLs'
            ]
        ]);
    }

    public function down()
    {
        // Revert back to single VARCHAR field
        $this->forge->modifyColumn('properties', [
            'youtube_video' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true
            ]
        ]);
    }
}
