<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run()
    {
        $blogPosts = [
            [
                'title' => 'Top 10 Real Estate Investment Tips for Siliguri',
                'slug' => 'top-10-real-estate-investment-tips-siliguri',
                'excerpt' => 'Discover the best strategies for investing in Siliguri\'s growing real estate market. From location analysis to timing your purchase.',
                'content' => '<h2>Why Siliguri is a Prime Investment Destination</h2>
                <p>Siliguri, known as the gateway to North East India, has emerged as one of the most promising real estate investment destinations in West Bengal. With its strategic location and growing infrastructure, the city offers excellent opportunities for both residential and commercial investments.</p>
                
                <h3>1. Location is Everything</h3>
                <p>When investing in Siliguri real estate, focus on areas like Champasari, Bagdogra, and Pradhan Nagar. These locations offer excellent connectivity and are experiencing rapid development.</p>
                
                <h3>2. Understand Market Trends</h3>
                <p>The Siliguri real estate market has shown consistent growth over the past decade. Property values in prime locations have appreciated by 8-12% annually.</p>
                
                <h3>3. Infrastructure Development</h3>
                <p>Keep an eye on upcoming infrastructure projects like new roads, metro connectivity, and commercial developments that can boost property values.</p>
                
                <h3>4. Legal Due Diligence</h3>
                <p>Always verify property documents, clear titles, and ensure all legal formalities are completed before making any investment.</p>
                
                <h3>5. Budget Planning</h3>
                <p>Plan your budget considering not just the property cost but also registration fees, stamp duty, and maintenance costs.</p>',
                'featured_image' => 'uploads/properties/1754052144_86f47640f3d7e380e9cd.jpg',
                'category' => 'Investment Tips',
                'tags' => 'investment, siliguri, real estate, tips, property',
                'status' => 'published',
                'author_id' => 1,
                'views' => rand(150, 500),
                'meta_title' => 'Best Real Estate Investment Tips for Siliguri - Expert Guide',
                'meta_description' => 'Learn the top 10 real estate investment strategies for Siliguri. Expert tips on location, market trends, and maximizing returns.',
                'published_at' => date('Y-m-d H:i:s', strtotime('-15 days')),
                'created_at' => date('Y-m-d H:i:s', strtotime('-15 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-15 days'))
            ],
            [
                'title' => 'Siliguri Property Market Analysis 2025',
                'slug' => 'siliguri-property-market-analysis-2025',
                'excerpt' => 'Comprehensive analysis of Siliguri\'s property market trends, price movements, and future projections for 2025.',
                'content' => '<h2>Market Overview</h2>
                <p>The Siliguri property market has shown remarkable resilience and growth in 2024, setting the stage for an even more promising 2025. This comprehensive analysis covers key trends, price movements, and investment opportunities.</p>
                
                <h3>Price Trends by Area</h3>
                <p><strong>Champasari:</strong> Average price increase of 10% year-over-year, with 2BHK apartments ranging from ₹25-35 lakhs.</p>
                <p><strong>Bagdogra:</strong> Proximity to the airport has driven prices up by 12%, making it a hotspot for investment.</p>
                <p><strong>Jalpaiguri Road:</strong> Commercial properties have seen the highest appreciation at 15%.</p>
                
                <h3>Emerging Localities</h3>
                <p>Areas like Milan More and Khaprail are emerging as affordable alternatives with good growth potential. These areas offer better value for money for first-time buyers.</p>
                
                <h3>Future Projections</h3>
                <p>Based on current trends and planned infrastructure developments, we expect:</p>
                <ul>
                <li>8-10% price appreciation in established areas</li>
                <li>12-15% growth in emerging localities</li>
                <li>Increased demand for 2-3 BHK apartments</li>
                <li>Growing interest in commercial properties</li>
                </ul>',
                'featured_image' => 'uploads/properties/1754120762_80df2b5248aeac84f334.jpg',
                'category' => 'Market Analysis',
                'tags' => 'market analysis, siliguri, property prices, trends, 2025',
                'status' => 'published',
                'author_id' => 1,
                'views' => rand(200, 600),
                'meta_title' => 'Siliguri Property Market Analysis 2025 - Trends & Predictions',
                'meta_description' => 'Complete analysis of Siliguri property market for 2025. Price trends, emerging areas, and investment opportunities.',
                'published_at' => date('Y-m-d H:i:s', strtotime('-10 days')),
                'created_at' => date('Y-m-d H:i:s', strtotime('-10 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-10 days'))
            ],
            [
                'title' => 'First-Time Home Buyer\'s Guide to Siliguri',
                'slug' => 'first-time-home-buyers-guide-siliguri',
                'excerpt' => 'Everything first-time home buyers need to know about purchasing property in Siliguri. From budgeting to legal procedures.',
                'content' => '<h2>Your Journey to Homeownership Starts Here</h2>
                <p>Buying your first home in Siliguri can be both exciting and overwhelming. This comprehensive guide will walk you through every step of the process, ensuring you make informed decisions.</p>
                
                <h3>Step 1: Determine Your Budget</h3>
                <p>Before you start looking at properties, establish a realistic budget. Consider:</p>
                <ul>
                <li>Your monthly income and expenses</li>
                <li>Down payment (typically 20% of property value)</li>
                <li>Home loan eligibility</li>
                <li>Additional costs (registration, stamp duty, legal fees)</li>
                </ul>
                
                <h3>Step 2: Choose the Right Location</h3>
                <p>Siliguri offers various neighborhoods, each with its unique advantages:</p>
                <p><strong>For Families:</strong> Champasari and Pradhan Nagar offer good schools and healthcare facilities.</p>
                <p><strong>For Professionals:</strong> Areas near commercial hubs like Sevoke Road provide easy commute options.</p>
                <p><strong>For Budget-Conscious:</strong> Milan More and Khaprail offer affordable options with growth potential.</p>
                
                <h3>Step 3: Home Loan Process</h3>
                <p>Most first-time buyers require home loans. Here\'s what you need to know:</p>
                <ul>
                <li>Compare interest rates from different banks</li>
                <li>Understand EMI calculations</li>
                <li>Prepare necessary documents</li>
                <li>Get pre-approval to strengthen your negotiating position</li>
                </ul>',
                'featured_image' => 'uploads/properties/1754131111_69ca85cca08ae523e56a.jpg',
                'category' => 'Home Buying',
                'tags' => 'first time buyer, home buying, siliguri, guide, property',
                'status' => 'published',
                'author_id' => 1,
                'views' => rand(300, 700),
                'meta_title' => 'First-Time Home Buyer\'s Complete Guide to Siliguri Properties',
                'meta_description' => 'Complete guide for first-time home buyers in Siliguri. Budget planning, location selection, and home loan tips.',
                'published_at' => date('Y-m-d H:i:s', strtotime('-7 days')),
                'created_at' => date('Y-m-d H:i:s', strtotime('-7 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-7 days'))
            ],
            [
                'title' => 'Luxury Apartments vs Villas: What to Choose in Siliguri',
                'slug' => 'luxury-apartments-vs-villas-siliguri',
                'excerpt' => 'Comparing luxury apartments and villas in Siliguri. Pros, cons, and factors to consider when choosing your dream home.',
                'content' => '<h2>Making the Right Choice for Your Lifestyle</h2>
                <p>When it comes to luxury living in Siliguri, buyers often face the dilemma of choosing between a high-end apartment or a villa. Both options have their unique advantages and considerations.</p>
                
                <h3>Luxury Apartments: Modern Convenience</h3>
                <p><strong>Advantages:</strong></p>
                <ul>
                <li>Lower maintenance responsibilities</li>
                <li>Better security with gated communities</li>
                <li>Shared amenities like gym, pool, clubhouse</li>
                <li>Prime locations with better connectivity</li>
                <li>More affordable than villas</li>
                </ul>
                
                <p><strong>Considerations:</strong></p>
                <ul>
                <li>Limited privacy compared to villas</li>
                <li>Shared walls and common areas</li>
                <li>Monthly maintenance charges</li>
                <li>Parking limitations</li>
                </ul>
                
                <h3>Villas: Ultimate Privacy and Space</h3>
                <p><strong>Advantages:</strong></p>
                <ul>
                <li>Complete privacy and independence</li>
                <li>Larger living spaces and outdoor areas</li>
                <li>Customization possibilities</li>
                <li>No monthly maintenance fees to societies</li>
                <li>Better investment appreciation potential</li>
                </ul>
                
                <p><strong>Considerations:</strong></p>
                <ul>
                <li>Higher purchase and maintenance costs</li>
                <li>Security arrangements needed</li>
                <li>Usually located in outskirts</li>
                <li>Complete responsibility for upkeep</li>
                </ul>',
                'featured_image' => 'uploads/properties/1754131111_7599702e6ab7e70d376d.jpg',
                'category' => 'Property Types',
                'tags' => 'luxury apartments, villas, comparison, siliguri, luxury homes',
                'status' => 'published',
                'author_id' => 1,
                'views' => rand(180, 450),
                'meta_title' => 'Luxury Apartments vs Villas in Siliguri - Complete Comparison',
                'meta_description' => 'Detailed comparison of luxury apartments and villas in Siliguri. Pros, cons, and factors to help you choose.',
                'published_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
            ],
            [
                'title' => 'Understanding Property Documentation in West Bengal',
                'slug' => 'property-documentation-west-bengal',
                'excerpt' => 'Complete guide to property documentation in West Bengal. Essential documents, legal procedures, and common pitfalls to avoid.',
                'content' => '<h2>Navigate Property Documentation with Confidence</h2>
                <p>Understanding property documentation is crucial for any real estate transaction in West Bengal. This guide covers all essential documents and legal procedures you need to know.</p>
                
                <h3>Essential Documents for Property Purchase</h3>
                <p><strong>Title Documents:</strong></p>
                <ul>
                <li>Sale Deed</li>
                <li>Mother Deed</li>
                <li>Chain of Title Documents</li>
                <li>Partition Deed (if applicable)</li>
                </ul>
                
                <p><strong>Clearance Certificates:</strong></p>
                <ul>
                <li>Encumbrance Certificate</li>
                <li>Non-Encumbrance Certificate</li>
                <li>Tax Clearance Certificate</li>
                <li>Building Plan Approval</li>
                </ul>
                
                <h3>Registration Process</h3>
                <p>Property registration in West Bengal involves several steps:</p>
                <ol>
                <li>Document verification</li>
                <li>Stamp duty payment</li>
                <li>Registration fee payment</li>
                <li>Biometric verification</li>
                <li>Document registration</li>
                </ol>
                
                <h3>Common Pitfalls to Avoid</h3>
                <ul>
                <li>Not verifying the seller\'s ownership</li>
                <li>Ignoring pending legal disputes</li>
                <li>Inadequate due diligence on property history</li>
                <li>Not checking for building violations</li>
                </ul>',
                'featured_image' => 'uploads/properties/1754131111_a7672119220345ab33a9.jpg',
                'category' => 'Legal Guide',
                'tags' => 'documentation, legal, property, west bengal, registration',
                'status' => 'published',
                'author_id' => 1,
                'views' => rand(120, 350),
                'meta_title' => 'Property Documentation Guide for West Bengal - Legal Requirements',
                'meta_description' => 'Complete guide to property documentation in West Bengal. Essential documents, registration process, and legal tips.',
                'published_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
            ],
            [
                'title' => 'Smart Home Technology Trends in Modern Properties',
                'slug' => 'smart-home-technology-trends-modern-properties',
                'excerpt' => 'Explore the latest smart home technology trends that are transforming modern properties. From automation to security systems.',
                'content' => '<h2>The Future of Home Living is Here</h2>
                <p>Smart home technology is revolutionizing the way we live, offering unprecedented convenience, security, and energy efficiency. Modern properties in Siliguri are increasingly incorporating these technologies.</p>
                
                <h3>Popular Smart Home Features</h3>
                <p><strong>Home Automation Systems:</strong></p>
                <ul>
                <li>Smart lighting with voice control</li>
                <li>Automated climate control</li>
                <li>Smart door locks and access control</li>
                <li>Motorized curtains and blinds</li>
                </ul>
                
                <p><strong>Security Systems:</strong></p>
                <ul>
                <li>IP cameras with mobile monitoring</li>
                <li>Smart doorbells with video calling</li>
                <li>Motion sensors and alarms</li>
                <li>Integrated security panels</li>
                </ul>
                
                <h3>Energy Management</h3>
                <p>Smart homes offer significant energy savings through:</p>
                <ul>
                <li>Smart thermostats that learn your preferences</li>
                <li>Automated lighting systems</li>
                <li>Solar panel integration</li>
                <li>Smart water heaters</li>
                </ul>
                
                <h3>Investment Value</h3>
                <p>Properties with smart home features typically see:</p>
                <ul>
                <li>5-10% higher resale value</li>
                <li>Faster sale times</li>
                <li>Lower utility costs</li>
                <li>Enhanced security and peace of mind</li>
                </ul>',
                'featured_image' => 'uploads/properties/1754155395_33b81b641812f6b2d5f1.jpg',
                'category' => 'Technology',
                'tags' => 'smart home, technology, automation, modern properties, innovation',
                'status' => 'published',
                'author_id' => 1,
                'views' => rand(90, 280),
                'meta_title' => 'Smart Home Technology Trends in Modern Properties - 2025 Guide',
                'meta_description' => 'Discover the latest smart home technology trends transforming modern properties. Automation, security, and energy efficiency.',
                'published_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ]
        ];

        // Insert blog posts
        foreach ($blogPosts as $post) {
            $this->db->table('blog_posts')->insert($post);
        }

        echo "Blog posts seeded successfully!\n";
    }
}
