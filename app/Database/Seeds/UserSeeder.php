<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * UserSeeder
 * 
 * Seeds the database with sample user data including admin and regular users.
 * Creates default admin account and several test users for development.
 * 
 * Default Admin Credentials:
 * - Email: admin@goldproperties.in
 * - Password: admin123
 * 
 * Test User Credentials:
 * - Email: user@example.com
 * - Password: user123
 * 
 * @author Gold Properties Team
 * @version 1.0
 * @since 2025-08-04
 */
class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            // Default Admin User
            [
                'name' => 'Admin User',
                'email' => 'admin@goldproperties.in',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Test Users
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => password_hash('user123', PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => password_hash('user123', PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike@example.com',
                'password' => password_hash('user123', PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Sarah Wilson',
                'email' => 'sarah@example.com',
                'password' => password_hash('user123', PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'David Brown',
                'email' => 'david@example.com',
                'password' => password_hash('user123', PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Lisa Davis',
                'email' => 'lisa@example.com',
                'password' => password_hash('user123', PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Robert Miller',
                'email' => 'robert@example.com',
                'password' => password_hash('user123', PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Emily Taylor',
                'email' => 'emily@example.com',
                'password' => password_hash('user123', PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Test User',
                'email' => 'user@example.com',
                'password' => password_hash('user123', PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Check if users table exists
        if (!$this->db->tableExists('users')) {
            echo "Users table does not exist. Please run migrations first.\n";
            return;
        }

        // Clear existing users (optional - remove in production)
        $this->db->table('users')->truncate();

        // Insert users
        foreach ($users as $user) {
            $this->db->table('users')->insert($user);
        }

        echo "Successfully inserted " . count($users) . " users into the database.\n";
        echo "\n=== DEFAULT LOGIN CREDENTIALS ===\n";
        echo "Admin Account:\n";
        echo "  Email: admin@goldproperties.in\n";
        echo "  Password: admin123\n\n";
        echo "Test User Account:\n";
        echo "  Email: user@example.com\n";
        echo "  Password: user123\n\n";
        echo "Additional test users created with emails: john@example.com, jane@example.com, etc.\n";
        echo "All test users have password: user123\n";
    }
}
