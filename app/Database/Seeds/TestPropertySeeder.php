<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TestPropertySeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title' => 'Modern Family House',
                'description' => 'Beautiful 3-bedroom family house with modern amenities, spacious living areas, and a lovely garden. Perfect for families looking for comfort and style.',
                'type' => 'house',
                'location' => 'Siliguri',
                'area' => 1500,
                'is_featured' => true,
                'images' => json_encode(['assets/images/house1.jpg']),
                'youtube_video' => json_encode([]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Luxury Villa with Pool',
                'description' => 'Stunning luxury villa featuring 4 bedrooms, private pool, and premium finishes throughout. Located in a prestigious neighborhood.',
                'type' => 'villa',
                'location' => 'Bagdogra',
                'area' => 2500,
                'is_featured' => true,
                'images' => json_encode(['assets/images/house2.jpg']),
                'youtube_video' => json_encode([]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Prime Land Plot',
                'description' => 'Excellent land plot in a developing area, perfect for construction of residential or commercial property. Great investment opportunity.',
                'type' => 'land',
                'location' => 'Jalpaiguri',
                'area' => 5000,
                'is_featured' => false,
                'images' => json_encode(['assets/images/house3.jpg']),
                'youtube_video' => json_encode([]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'City Center Apartment',
                'description' => 'Modern 2-bedroom apartment in the heart of the city with all amenities nearby. Perfect for young professionals and couples.',
                'type' => 'apartment',
                'location' => 'Siliguri',
                'area' => 900,
                'is_featured' => true,
                'images' => json_encode(['assets/images/house4.jpg']),
                'youtube_video' => json_encode([]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Cozy Suburban House',
                'description' => 'Charming 2-bedroom house in a quiet suburban area. Features include a small garden and parking space.',
                'type' => 'house',
                'location' => 'Champasari',
                'area' => 1200,
                'is_featured' => false,
                'images' => json_encode(['assets/images/house1.jpg']),
                'youtube_video' => json_encode([]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Executive Villa',
                'description' => 'Premium executive villa with 5 bedrooms, home office, and entertainment area. Ideal for large families.',
                'type' => 'villa',
                'location' => 'Pradhan Nagar',
                'area' => 3000,
                'is_featured' => false,
                'images' => json_encode(['assets/images/house2.jpg']),
                'youtube_video' => json_encode([]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert test data
        $this->db->table('properties')->insertBatch($data);
    }
}
