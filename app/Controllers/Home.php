<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $propertyModel = new \App\Models\PropertyModel();

        // Get featured properties for carousel (latest 5)
        $featuredProperties = $propertyModel->orderBy('created_at', 'DESC')->limit(5)->findAll();

        // Get recent properties for the grid
        $recentProperties = $propertyModel->orderBy('created_at', 'DESC')->limit(6)->findAll();

        $data = [
            'title' => 'Find Your Dream Home | Real Estate',
            'featuredProperties' => $featuredProperties,
            'recentProperties' => $recentProperties
        ];

        return view('home/index', $data);
    }

    public function about(): string
    {
        $data = [
            'title' => 'About Us | Real Estate'
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
