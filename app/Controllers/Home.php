<?php

namespace App\Controllers;

/**
 * Home Controller
 *
 * Handles the main homepage and public pages of the Gold Properties website.
 * This controller manages the display of featured properties, property types,
 * and provides the main entry point for visitors.
 *
 * Key Features:
 * - Homepage with featured property carousel
 * - Property type sections (house, villa, land, apartment)
 * - SEO-optimized page titles and meta data
 * - Mobile-first responsive design support
 *
 * @author Gold Properties Team
 * @version 2.0 - Enhanced with comprehensive property display
 * @since 2025-08-20
 */
class Home extends BaseController
{
    /**
     * Display the homepage with featured properties and property type sections
     *
     * This method orchestrates the homepage display by:
     * - Loading featured properties for the hero carousel
     * - Fetching properties by type for category sections
     * - Preparing SEO-friendly page data
     * - Ensuring optimal performance with limited queries
     *
     * @return string The rendered homepage view
     */
    public function index(): string
    {
        // Initialize property model for data retrieval
        $propertyModel = new \App\Models\PropertyModel();

        // Get featured properties for hero carousel (latest 5 for variety)
        $featuredProperties = $propertyModel->getFeaturedProperties(5);

        // Get exactly 3 featured properties for the featured section
        // (separate from carousel to avoid duplication)
        $featuredPropertiesSection = $propertyModel->getFeaturedProperties(3);

        // Define property types to display on homepage
        // Limited to 4 main categories for optimal user experience
        $propertyTypes = ['house', 'villa', 'land', 'apartment'];
        $propertiesByType = [];

        // Fetch 3 properties per type to maintain page performance
        // Featured properties are prioritized in each category
        foreach ($propertyTypes as $type) {
            $propertiesByType[$type] = $propertyModel->getPropertiesByType($type, 3);
        }

        // Prepare data array for view rendering
        $data = [
            'title' => 'Find Your Dream Home | Gold Properties', // SEO-optimized title
            'featuredProperties' => $featuredProperties,
            'featuredPropertiesSection' => $featuredPropertiesSection,
            'propertiesByType' => $propertiesByType
        ];

        return view('home/index', $data);
    }

    public function about(): string
    {
        $data = [
            'title' => 'About Us | Gold Properties'
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
