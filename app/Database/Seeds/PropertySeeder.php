<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * PropertySeeder
 * 
 * Seeds the database with sample property data for testing and development.
 * Creates 20 diverse properties across different types and locations in Siliguri area.
 * 
 * Features:
 * - Multiple property types (house, apartment, villa, land)
 * - Various locations around Siliguri
 * - Realistic descriptions and areas
 * - Sample images and YouTube videos
 * - Different price ranges and features
 * 
 * @author Real Estate Team
 * @version 1.0
 * @since 2025-08-04
 */
class PropertySeeder extends Seeder
{
    public function run()
    {
        $properties = [
            // Luxury Properties
            [
                'title' => 'Luxury 4BHK Villa with Swimming Pool',
                'description' => 'Stunning luxury villa in the heart of Siliguri featuring 4 spacious bedrooms, modern kitchen, swimming pool, landscaped garden, and premium finishes. Perfect for families seeking luxury and comfort with easy access to city amenities.',
                'type' => 'villa',
                'location' => 'Siliguri',
                'area' => 3500,
                'images' => json_encode(['assets/images/house1.jpg', 'assets/images/house2.jpg', 'assets/images/house3.jpg']),
                'youtube_video' => json_encode(['https://www.youtube.com/watch?v=dQw4w9WgXcQ']),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Premium 3BHK Apartment in Champasari',
                'description' => 'Modern 3BHK apartment in prestigious Champasari area with elevator, parking, security, and beautiful city views. Features include modular kitchen, spacious living room, and premium bathroom fittings.',
                'type' => 'apartment',
                'location' => 'Champasari',
                'area' => 1800,
                'images' => json_encode(['assets/images/house2.jpg', 'assets/images/house1.jpg']),
                'youtube_video' => json_encode([]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Elegant Villa with Garden in Pradhan Nagar',
                'description' => 'Beautiful villa with spacious garden, 3 bedrooms, modern amenities, and peaceful environment. Located in the prime Pradhan Nagar area with excellent connectivity to schools and hospitals.',
                'type' => 'villa',
                'location' => 'Pradhan Nagar',
                'area' => 2800,
                'images' => json_encode(['assets/images/house3.jpg', 'assets/images/house1.jpg']),
                'youtube_video' => json_encode(['https://www.youtube.com/watch?v=dQw4w9WgXcQ']),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],

            // Mid-Range Properties
            [
                'title' => 'Comfortable 2BHK House in Bagdogra',
                'description' => 'Cozy 2BHK house near Bagdogra Airport, perfect for small families or investment. Features include covered parking, small garden, and modern kitchen with easy access to airport and city.',
                'type' => 'house',
                'location' => 'Bagdogra',
                'area' => 1200,
                'images' => json_encode(['assets/images/house1.jpg']),
                'youtube_video' => json_encode([]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Modern 2BHK Apartment in Jalpaiguri',
                'description' => 'Well-designed 2BHK apartment in Jalpaiguri with modern amenities, good ventilation, and proximity to markets and schools. Ideal for young professionals and small families.',
                'type' => 'apartment',
                'location' => 'Jalpaiguri',
                'area' => 1000,
                'images' => json_encode(['assets/images/house2.jpg', 'assets/images/house3.jpg']),
                'youtube_video' => json_encode([]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Spacious 3BHK House in Milan More',
                'description' => 'Large 3BHK house in Milan More area with ample space, natural light, and good connectivity. Features include separate dining area, study room, and covered parking.',
                'type' => 'house',
                'location' => 'Milan More',
                'area' => 1600,
                'images' => json_encode(['assets/images/house3.jpg', 'assets/images/house2.jpg']),
                'youtube_video' => json_encode([]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Family Home in Khaprail',
                'description' => 'Perfect family home in peaceful Khaprail area with 3 bedrooms, large living space, and traditional architecture. Close to schools and local markets with good transportation links.',
                'type' => 'house',
                'location' => 'Khaprail',
                'area' => 1400,
                'images' => json_encode(['assets/images/house1.jpg', 'assets/images/house3.jpg']),
                'youtube_video' => json_encode([]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],

            // Affordable Properties
            [
                'title' => 'Affordable 1BHK Apartment in Siliguri',
                'description' => 'Budget-friendly 1BHK apartment perfect for bachelors or young couples. Located in central Siliguri with easy access to public transport, markets, and offices.',
                'type' => 'apartment',
                'location' => 'Siliguri',
                'area' => 600,
                'images' => json_encode(['assets/images/house2.jpg']),
                'youtube_video' => json_encode([]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Starter Home in Champasari',
                'description' => 'Perfect starter home for first-time buyers. 2BHK house with basic amenities, small garden, and peaceful neighborhood. Great investment opportunity in growing area.',
                'type' => 'house',
                'location' => 'Champasari',
                'area' => 900,
                'images' => json_encode(['assets/images/house1.jpg', 'assets/images/house2.jpg']),
                'youtube_video' => json_encode([]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Compact 2BHK in Pradhan Nagar',
                'description' => 'Well-planned compact 2BHK apartment with efficient space utilization. Modern fittings, good ventilation, and located in the heart of Pradhan Nagar with excellent connectivity.',
                'type' => 'apartment',
                'location' => 'Pradhan Nagar',
                'area' => 800,
                'images' => json_encode(['assets/images/house3.jpg']),
                'youtube_video' => json_encode([]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Additional properties to complete 20 total
        $moreProperties = [
            // Land and Investment Properties
            [
                'title' => 'Prime Commercial Land in Siliguri',
                'description' => 'Excellent commercial land opportunity in prime Siliguri location. Perfect for business development, shopping complex, or office building. Great investment potential with high appreciation value.',
                'type' => 'land',
                'location' => 'Siliguri',
                'area' => 5000,
                'images' => json_encode(['assets/images/house1.jpg']),
                'youtube_video' => json_encode([]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Residential Plot in Bagdogra',
                'description' => 'Ready-to-build residential plot in developing Bagdogra area. Clear title, good road access, and all utilities available. Perfect for building your dream home.',
                'type' => 'land',
                'location' => 'Bagdogra',
                'area' => 2400,
                'images' => json_encode(['assets/images/house2.jpg']),
                'youtube_video' => json_encode([]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Investment Land in Jalpaiguri',
                'description' => 'Strategic land parcel in growing Jalpaiguri area. Suitable for residential or commercial development. Excellent connectivity and future growth potential.',
                'type' => 'land',
                'location' => 'Jalpaiguri',
                'area' => 3200,
                'images' => json_encode(['assets/images/house3.jpg']),
                'youtube_video' => json_encode([]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],

            // More Residential Properties
            [
                'title' => 'Penthouse Apartment in Siliguri',
                'description' => 'Luxurious penthouse with panoramic city views, terrace garden, and premium amenities. Features 3 bedrooms, modern kitchen, and exclusive elevator access.',
                'type' => 'apartment',
                'location' => 'Siliguri',
                'area' => 2200,
                'images' => json_encode(['assets/images/house1.jpg', 'assets/images/house2.jpg', 'assets/images/house3.jpg']),
                'youtube_video' => json_encode(['https://www.youtube.com/watch?v=dQw4w9WgXcQ']),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Traditional House in Milan More',
                'description' => 'Charming traditional house with modern upgrades. Features original architecture, spacious rooms, and beautiful courtyard. Perfect blend of heritage and comfort.',
                'type' => 'house',
                'location' => 'Milan More',
                'area' => 1800,
                'images' => json_encode(['assets/images/house2.jpg', 'assets/images/house1.jpg']),
                'youtube_video' => json_encode([]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Modern Villa in Khaprail',
                'description' => 'Contemporary villa with smart home features, solar panels, and eco-friendly design. 4 bedrooms, home office, and beautiful landscaping.',
                'type' => 'villa',
                'location' => 'Khaprail',
                'area' => 3000,
                'images' => json_encode(['assets/images/house3.jpg', 'assets/images/house2.jpg']),
                'youtube_video' => json_encode(['https://www.youtube.com/watch?v=dQw4w9WgXcQ']),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Studio Apartment in Champasari',
                'description' => 'Modern studio apartment perfect for professionals. Compact yet functional design with all amenities. Great location with easy access to business district.',
                'type' => 'apartment',
                'location' => 'Champasari',
                'area' => 450,
                'images' => json_encode(['assets/images/house1.jpg']),
                'youtube_video' => json_encode([]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Family Villa in Pradhan Nagar',
                'description' => 'Spacious family villa with 5 bedrooms, multiple living areas, and large garden. Perfect for joint families or those who love entertaining guests.',
                'type' => 'villa',
                'location' => 'Pradhan Nagar',
                'area' => 4200,
                'images' => json_encode(['assets/images/house2.jpg', 'assets/images/house3.jpg', 'assets/images/house1.jpg']),
                'youtube_video' => json_encode([]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Duplex House in Bagdogra',
                'description' => 'Modern duplex house with separate entrances, perfect for rental income or extended family. Features 4 bedrooms total, parking for 2 cars, and small garden.',
                'type' => 'house',
                'location' => 'Bagdogra',
                'area' => 2000,
                'images' => json_encode(['assets/images/house3.jpg', 'assets/images/house1.jpg']),
                'youtube_video' => json_encode([]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Luxury Apartment in Jalpaiguri',
                'description' => 'High-end apartment with premium finishes, gym, swimming pool access, and 24/7 security. 3 bedrooms with attached bathrooms and modern kitchen.',
                'type' => 'apartment',
                'location' => 'Jalpaiguri',
                'area' => 1500,
                'images' => json_encode(['assets/images/house1.jpg', 'assets/images/house2.jpg']),
                'youtube_video' => json_encode(['https://www.youtube.com/watch?v=dQw4w9WgXcQ']),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Combine all properties
        $allProperties = array_merge($properties, $moreProperties);

        // Insert all properties
        foreach ($allProperties as $property) {
            $this->db->table('properties')->insert($property);
        }

        echo "Successfully inserted " . count($allProperties) . " properties into the database.\n";
        echo "Properties include:\n";
        echo "- " . count(array_filter($allProperties, fn($p) => $p['type'] === 'villa')) . " Villas\n";
        echo "- " . count(array_filter($allProperties, fn($p) => $p['type'] === 'house')) . " Houses\n";
        echo "- " . count(array_filter($allProperties, fn($p) => $p['type'] === 'apartment')) . " Apartments\n";
        echo "- " . count(array_filter($allProperties, fn($p) => $p['type'] === 'land')) . " Land parcels\n";
    }
}
