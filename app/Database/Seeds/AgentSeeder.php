<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * AgentSeeder
 * 
 * Seeds the database with sample real estate agent data.
 * Creates diverse agents with different specializations and experience levels.
 * 
 * Features:
 * - Professional agent profiles
 * - Contact information
 * - Qualifications and specializations
 * - Active status for all agents
 * 
 * @author Real Estate Team
 * @version 1.0
 * @since 2025-08-04
 */
class AgentSeeder extends Seeder
{
    public function run()
    {
        $agents = [
            [
                'profile_image' => 'assets/images/agents/agent1.jpg',
                'name' => 'Rajesh Kumar',
                'email' => 'rajesh@realestate.com',
                'phone' => '+91 98765 43210',
                'address' => 'Siliguri, West Bengal, India',
                'qualification' => 'MBA in Real Estate, 8+ years experience',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'profile_image' => 'assets/images/agents/agent2.jpg',
                'name' => 'Priya Sharma',
                'email' => 'priya@realestate.com',
                'phone' => '+91 98765 43211',
                'address' => 'Champasari, Siliguri, West Bengal',
                'qualification' => 'B.Com, Real Estate License, 5+ years experience',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'profile_image' => 'assets/images/agents/agent3.jpg',
                'name' => 'Amit Das',
                'email' => 'amit@realestate.com',
                'phone' => '+91 98765 43212',
                'address' => 'Pradhan Nagar, Siliguri, West Bengal',
                'qualification' => 'B.Tech, Property Consultant Certification, 6+ years experience',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'profile_image' => 'assets/images/agents/agent4.jpg',
                'name' => 'Sneha Roy',
                'email' => 'sneha@realestate.com',
                'phone' => '+91 98765 43213',
                'address' => 'Bagdogra, Siliguri, West Bengal',
                'qualification' => 'MA Economics, Real Estate Specialist, 4+ years experience',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'profile_image' => 'assets/images/agents/agent5.jpg',
                'name' => 'Vikash Gupta',
                'email' => 'vikash@realestate.com',
                'phone' => '+91 98765 43214',
                'address' => 'Milan More, Siliguri, West Bengal',
                'qualification' => 'BBA, Property Investment Advisor, 7+ years experience',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'profile_image' => 'assets/images/agents/agent6.jpg',
                'name' => 'Anita Bhattacharya',
                'email' => 'anita@realestate.com',
                'phone' => '+91 98765 43215',
                'address' => 'Jalpaiguri, West Bengal',
                'qualification' => 'M.Com, Certified Real Estate Broker, 9+ years experience',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'profile_image' => 'assets/images/agents/agent7.jpg',
                'name' => 'Suresh Agarwal',
                'email' => 'suresh@realestate.com',
                'phone' => '+91 98765 43216',
                'address' => 'Khaprail, Siliguri, West Bengal',
                'qualification' => 'B.Sc, Real Estate Sales Expert, 3+ years experience',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'profile_image' => 'assets/images/agents/agent8.jpg',
                'name' => 'Meera Joshi',
                'email' => 'meera@realestate.com',
                'phone' => '+91 98765 43217',
                'address' => 'Siliguri, West Bengal',
                'qualification' => 'MBA Finance, Property Valuation Expert, 6+ years experience',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Check if agents table exists
        if (!$this->db->tableExists('agents')) {
            echo "Agents table does not exist. Please run migrations first.\n";
            return;
        }

        // Insert agents
        foreach ($agents as $agent) {
            $this->db->table('agents')->insert($agent);
        }

        echo "Successfully inserted " . count($agents) . " agents into the database.\n";
        echo "All agents are marked as active and ready to handle property inquiries.\n";
        echo "Agent specializations include:\n";
        echo "- Luxury properties and villas\n";
        echo "- Residential apartments and houses\n";
        echo "- Commercial and investment properties\n";
        echo "- Property valuation and consultation\n";
    }
}
