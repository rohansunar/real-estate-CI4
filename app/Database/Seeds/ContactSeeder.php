<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * ContactSeeder
 * 
 * Seeds the database with sample contact inquiries and newsletter subscriptions.
 * Creates realistic customer inquiries for testing dashboard functionality.
 * 
 * Features:
 * - Diverse inquiry types (property interest, general questions, etc.)
 * - Various contact methods and preferences
 * - Different inquiry statuses (read/unread)
 * - Newsletter subscriptions
 * 
 * @author Gold Properties Team
 * @version 1.0
 * @since 2025-08-04
 */
class ContactSeeder extends Seeder
{
    public function run()
    {
        // Contact inquiries (updated to match actual table structure)
        $contacts = [
            [
                'name' => 'Rahul Sharma',
                'email' => 'rahul.sharma@gmail.com',
                'phone' => '+91 98765 11111',
                'properties_in' => 'Champasari',
                'message' => 'Hi, I am interested in the 3BHK apartment in Champasari. Could you please provide more details about the pricing, amenities, and availability? I would like to schedule a visit this weekend.',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
            ],
            [
                'name' => 'Sunita Devi',
                'email' => 'sunita.devi@yahoo.com',
                'phone' => '+91 98765 22222',
                'properties_in' => 'Pradhan Nagar',
                'message' => 'I saw your villa listing in Pradhan Nagar and I am very interested. My family is looking for a spacious home with a garden. Can we arrange a viewing? Also, please let me know about the loan assistance options.',
                'is_read' => 1,
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-4 days'))
            ],
            [
                'name' => 'Arjun Patel',
                'email' => 'arjun.patel@hotmail.com',
                'phone' => '+91 98765 33333',
                'properties_in' => 'Siliguri',
                'message' => 'I am looking for investment properties in Siliguri area. Could you suggest some good options with high rental yield potential? My budget is around 15-20 lakhs.',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ],
            [
                'name' => 'Kavita Singh',
                'email' => 'kavita.singh@gmail.com',
                'phone' => '+91 98765 44444',
                'properties_in' => 'Bagdogra',
                'message' => 'Hello, I am interested in the house near Bagdogra Airport. As I work in the aviation industry, the location is perfect for me. Please share more details about the property and arrange a site visit.',
                'is_read' => 1,
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
            ],
            [
                'name' => 'Deepak Kumar',
                'email' => 'deepak.kumar@outlook.com',
                'phone' => '+91 98765 55555',
                'properties_in' => 'Siliguri',
                'message' => 'I am a first-time home buyer looking for a 2BHK apartment in Siliguri. My budget is limited, so I need something affordable but in a good location. Can you help me find suitable options?',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime('-6 hours')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-6 hours'))
            ],
            [
                'name' => 'Ritu Agarwal',
                'email' => 'ritu.agarwal@gmail.com',
                'phone' => '+91 98765 66666',
                'properties_in' => 'Milan More',
                'message' => 'I need to get my property valued for sale. It is a 3BHK house in Milan More area. Do you provide property valuation services? What are the charges?',
                'is_read' => 1,
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 week')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-6 days'))
            ],
            [
                'name' => 'Manoj Thakur',
                'email' => 'manoj.thakur@rediffmail.com',
                'phone' => '+91 98765 77777',
                'properties_in' => 'Siliguri',
                'message' => 'I am interested in the commercial land in Siliguri. I want to set up a small business there. Please provide details about the land area, price, and legal documentation.',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime('-12 hours')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-12 hours'))
            ],
            [
                'name' => 'Pooja Banerjee',
                'email' => 'pooja.banerjee@gmail.com',
                'phone' => '+91 98765 88888',
                'properties_in' => 'Siliguri',
                'message' => 'I am relocating to Siliguri for work and looking for rental properties. Do you handle rental properties as well? I need a furnished 2BHK apartment for at least 2 years.',
                'is_read' => 1,
                'created_at' => date('Y-m-d H:i:s', strtotime('-4 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
            ]
        ];

        // Newsletter subscriptions
        $newsletters = [
            ['email' => 'subscriber1@gmail.com', 'created_at' => date('Y-m-d H:i:s', strtotime('-1 week')), 'updated_at' => date('Y-m-d H:i:s', strtotime('-1 week'))],
            ['email' => 'subscriber2@yahoo.com', 'created_at' => date('Y-m-d H:i:s', strtotime('-5 days')), 'updated_at' => date('Y-m-d H:i:s', strtotime('-5 days'))],
            ['email' => 'subscriber3@hotmail.com', 'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')), 'updated_at' => date('Y-m-d H:i:s', strtotime('-3 days'))],
            ['email' => 'subscriber4@outlook.com', 'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')), 'updated_at' => date('Y-m-d H:i:s', strtotime('-2 days'))],
            ['email' => 'subscriber5@gmail.com', 'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')), 'updated_at' => date('Y-m-d H:i:s', strtotime('-1 day'))],
            ['email' => 'newsletter@example.com', 'created_at' => date('Y-m-d H:i:s', strtotime('-6 hours')), 'updated_at' => date('Y-m-d H:i:s', strtotime('-6 hours'))],
            ['email' => 'updates@test.com', 'created_at' => date('Y-m-d H:i:s', strtotime('-3 hours')), 'updated_at' => date('Y-m-d H:i:s', strtotime('-3 hours'))],
        ];

        // Check if tables exist
        if (!$this->db->tableExists('contacts')) {
            echo "Contacts table does not exist. Please run migrations first.\n";
            return;
        }

        if (!$this->db->tableExists('newsletter')) {
            echo "Newsletter table does not exist. Please run migrations first.\n";
            return;
        }

        // Insert contact inquiries
        foreach ($contacts as $contact) {
            $this->db->table('contacts')->insert($contact);
        }

        // Insert newsletter subscriptions
        foreach ($newsletters as $newsletter) {
            $this->db->table('newsletter')->insert($newsletter);
        }

        echo "Successfully inserted " . count($contacts) . " contact inquiries into the database.\n";
        echo "Successfully inserted " . count($newsletters) . " newsletter subscriptions into the database.\n";
        echo "\nInquiry Statistics:\n";
        echo "- " . count(array_filter($contacts, fn($c) => $c['is_read'] == 0)) . " Unread inquiries\n";
        echo "- " . count(array_filter($contacts, fn($c) => $c['is_read'] == 1)) . " Read inquiries\n";
        echo "- All inquiries are location-specific for better customer service\n";
        echo "- Inquiries cover: Siliguri, Champasari, Pradhan Nagar, Bagdogra, Milan More\n";
    }
}
