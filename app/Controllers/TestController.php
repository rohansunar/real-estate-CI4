<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PropertyModel;
use App\Models\ContactModel;
use App\Models\NewsletterModel;

class TestController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Migration Test | Real Estate'
        ];

        return view('test/index', $data);
    }

    public function database()
    {
        $userModel = new UserModel();
        $propertyModel = new PropertyModel();
        $contactModel = new ContactModel();
        $newsletterModel = new NewsletterModel();

        $tests = [];

        // Test database connections
        try {
            $db = \Config\Services::database();
            $tests['database_connection'] = [
                'status' => 'success',
                'message' => 'Database connection successful'
            ];
        } catch (\Exception $e) {
            $tests['database_connection'] = [
                'status' => 'error',
                'message' => 'Database connection failed: ' . $e->getMessage()
            ];
        }

        // Test table existence
        $tables = ['users', 'properties', 'contacts', 'newsletter'];
        foreach ($tables as $table) {
            try {
                $db = \Config\Services::database();
                if ($db->tableExists($table)) {
                    $tests["table_$table"] = [
                        'status' => 'success',
                        'message' => "Table '$table' exists"
                    ];
                } else {
                    $tests["table_$table"] = [
                        'status' => 'error',
                        'message' => "Table '$table' does not exist"
                    ];
                }
            } catch (\Exception $e) {
                $tests["table_$table"] = [
                    'status' => 'error',
                    'message' => "Error checking table '$table': " . $e->getMessage()
                ];
            }
        }

        // Test model functionality
        try {
            $userCount = $userModel->countAllResults();
            $tests['user_model'] = [
                'status' => 'success',
                'message' => "User model working. Count: $userCount"
            ];
        } catch (\Exception $e) {
            $tests['user_model'] = [
                'status' => 'error',
                'message' => 'User model error: ' . $e->getMessage()
            ];
        }

        try {
            $propertyCount = $propertyModel->countAllResults();
            $tests['property_model'] = [
                'status' => 'success',
                'message' => "Property model working. Count: $propertyCount"
            ];
        } catch (\Exception $e) {
            $tests['property_model'] = [
                'status' => 'error',
                'message' => 'Property model error: ' . $e->getMessage()
            ];
        }

        $data = [
            'title' => 'Database Test Results',
            'tests' => $tests
        ];

        return view('test/database', $data);
    }

    public function createSampleData()
    {
        $userModel = new UserModel();
        $propertyModel = new PropertyModel();

        try {
            // Create a test user
            $userData = [
                'name' => 'Test Admin',
                'email' => 'admin@realestate.com',
                'password' => 'password123'
            ];

            if (!$userModel->findByEmail($userData['email'])) {
                $userModel->insert($userData);
            }

            // Create sample properties with Siliguri locations
            $properties = [
                [
                    'title' => 'Beautiful Family Home in Siliguri',
                    'description' => 'A stunning 4-bedroom family home with modern amenities and a large garden in the heart of Siliguri.',
                    'type' => 'house',
                    'location' => 'Siliguri',
                    'area' => 2500,
                    'images' => json_encode([])
                ],
                [
                    'title' => 'Modern Apartment in Champasari',
                    'description' => 'A contemporary 2-bedroom apartment in the popular Champasari area with excellent connectivity.',
                    'type' => 'apartment',
                    'location' => 'Champasari',
                    'area' => 1200,
                    'images' => json_encode([])
                ],
                [
                    'title' => 'Luxury Villa near Bagdogra',
                    'description' => 'An exclusive villa with panoramic views of the hills, located near Bagdogra Airport.',
                    'type' => 'villa',
                    'location' => 'Bagdogra',
                    'area' => 3500,
                    'images' => json_encode([])
                ],
                [
                    'title' => 'Commercial Space in Jalpaiguri',
                    'description' => 'Prime commercial property in the business district of Jalpaiguri, perfect for offices.',
                    'type' => 'commercial',
                    'location' => 'Jalpaiguri',
                    'area' => 1800,
                    'images' => json_encode([])
                ],
                [
                    'title' => 'Residential Plot in Pradhan Nagar',
                    'description' => 'Well-located residential land in Pradhan Nagar, ideal for building your dream home.',
                    'type' => 'land',
                    'location' => 'Pradhan Nagar',
                    'area' => 5000,
                    'images' => json_encode([])
                ]
            ];

            foreach ($properties as $property) {
                $propertyModel->insert($property);
            }

            return redirect()->to('/test/database')->with('success', 'Sample data created successfully!');

        } catch (\Exception $e) {
            return redirect()->to('/test/database')->with('error', 'Error creating sample data: ' . $e->getMessage());
        }
    }
}
