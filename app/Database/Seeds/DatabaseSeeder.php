<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * DatabaseSeeder
 * 
 * Master seeder that runs all individual seeders in the correct order.
 * This seeder populates the entire database with sample data for development and testing.
 * 
 * Seeding Order:
 * 1. Users (including admin account)
 * 2. Properties (20 diverse properties)
 * 3. Agents (real estate professionals)
 * 4. Blog Posts (if table exists)
 * 5. Contact Inquiries and Newsletter Subscriptions
 * 
 * Usage:
 * php spark db:seed DatabaseSeeder
 * 
 * @author Real Estate Team
 * @version 1.0
 * @since 2025-08-04
 */
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "🏠 REAL ESTATE DATABASE SEEDER\n";
        echo str_repeat("=", 60) . "\n\n";

        echo "Starting database seeding process...\n\n";

        // 1. Seed Users (including admin)
        echo "📊 Step 1: Seeding Users...\n";
        echo str_repeat("-", 40) . "\n";
        try {
            $this->call('UserSeeder');
            echo "✅ Users seeded successfully!\n\n";
        } catch (\Exception $e) {
            echo "❌ Error seeding users: " . $e->getMessage() . "\n\n";
        }

        // 2. Seed Properties
        echo "🏘️  Step 2: Seeding Properties...\n";
        echo str_repeat("-", 40) . "\n";
        try {
            $this->call('PropertySeeder');
            echo "✅ Properties seeded successfully!\n\n";
        } catch (\Exception $e) {
            echo "❌ Error seeding properties: " . $e->getMessage() . "\n\n";
        }

        // 3. Seed Agents
        echo "👥 Step 3: Seeding Agents...\n";
        echo str_repeat("-", 40) . "\n";
        try {
            $this->call('AgentSeeder');
            echo "✅ Agents seeded successfully!\n\n";
        } catch (\Exception $e) {
            echo "❌ Error seeding agents: " . $e->getMessage() . "\n\n";
        }

        // 3b. Seed Deep Agent Hierarchy (10 levels)
        echo "🌳 Step 3b: Seeding 10-level Agent Hierarchy...\n";
        echo str_repeat("-", 40) . "\n";
        try {
            $this->call('AgentHierarchySeeder');
            echo "✅ Agent hierarchy seeded successfully!\n\n";
        } catch (\Exception $e) {
            echo "❌ Error seeding agent hierarchy: " . $e->getMessage() . "\n\n";
        }

        // 4. Seed Blog Posts (if table exists)
        echo "📝 Step 4: Seeding Blog Posts...\n";
        echo str_repeat("-", 40) . "\n";
        try {
            if ($this->db->tableExists('blog_posts')) {
                $this->call('BlogPostSeeder');
                echo "✅ Blog posts seeded successfully!\n\n";
            } else {
                echo "⚠️  Blog posts table not found, skipping...\n\n";
            }
        } catch (\Exception $e) {
            echo "❌ Error seeding blog posts: " . $e->getMessage() . "\n\n";
        }

        // 5. Seed Contact Inquiries and Newsletter
        echo "📧 Step 5: Seeding Contact Inquiries & Newsletter...\n";
        echo str_repeat("-", 40) . "\n";
        try {
            $this->call('ContactSeeder');
            echo "✅ Contact data seeded successfully!\n\n";
        } catch (\Exception $e) {
            echo "❌ Error seeding contact data: " . $e->getMessage() . "\n\n";
        }

        // Summary
        echo str_repeat("=", 60) . "\n";
        echo "🎉 DATABASE SEEDING COMPLETED!\n";
        echo str_repeat("=", 60) . "\n\n";

        echo "📋 SUMMARY:\n";
        echo "• Users: Admin + Test users created\n";
        echo "• Properties: 20 diverse properties across Siliguri area\n";
        echo "• Agents: Professional real estate agents\n";
        echo "• Blog Posts: Sample blog content (if table exists)\n";
        echo "• Contacts: Sample inquiries and newsletter subscriptions\n\n";

        echo "🔐 DEFAULT LOGIN CREDENTIALS:\n";
        echo "Admin Dashboard:\n";
        echo "  📧 Email: admin@realestate.com\n";
        echo "  🔑 Password: admin123\n\n";
        echo "Test User Account:\n";
        echo "  📧 Email: user@example.com\n";
        echo "  🔑 Password: user123\n\n";

        echo "🚀 Your real estate website is now ready for testing!\n";
        echo "Visit: http://localhost:8080 (or your configured URL)\n\n";

        echo "📖 For more information, see developer.md documentation.\n";
        echo str_repeat("=", 60) . "\n";
    }
}
