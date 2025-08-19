<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $propertyModel = new \App\Models\PropertyModel();

        // Get featured properties for carousel (latest 5)
        $featuredProperties = $propertyModel->getFeaturedProperties(5);

        // Get exactly 3 featured properties for the featured section (different from carousel)
        $featuredPropertiesSection = $propertyModel->getFeaturedProperties(3);

        // Get properties by type with featured properties first (3 per type)
        $propertyTypes = ['house', 'villa', 'land', 'apartment'];
        $propertiesByType = [];

        foreach ($propertyTypes as $type) {
            $propertiesByType[$type] = $propertyModel->getPropertiesByType($type, 3);
        }

        $data = [
            'title' => 'Find Your Dream Home | White Rock Realtor',
            'featuredProperties' => $featuredProperties,
            'featuredPropertiesSection' => $featuredPropertiesSection,
            'propertiesByType' => $propertiesByType
        ];

        return view('home/index', $data);
    }

    public function about(): string
    {
        $data = [
            'title' => 'About Us | White Rock Realtor'
        ];

        return view('home/about', $data);
    }



    public function error()
    {
        $data = [
            'title' => 'Page Not Found',
            'message' => 'The page you are looking for does not exist.'
        ];

        return view('errors/error_404', $data);
    }
}
