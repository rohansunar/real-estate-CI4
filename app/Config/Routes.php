<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Root route - homepage
$routes->get('/', 'Home::index');

// Error route
$routes->get('error', 'Home::error');

// About Us and Blog routes
$routes->get('about', 'Home::about');
$routes->get('blog', 'Home::blog');

// Property routes
// Note: Route order is important! More specific routes must come before general ones
// to prevent route conflicts where 'details' could be matched as a location segment
$routes->group('properties', function($routes) {
    $routes->get('/', 'PropertyController::index');
    $routes->get('details/(:num)', 'PropertyController::details/$1'); // API endpoint for property details (must be first)
    $routes->get('create', 'PropertyController::create', ['filter' => 'auth']);
    $routes->post('create', 'PropertyController::store', ['filter' => 'auth']);
    $routes->get('(:segment)/(:num)', 'PropertyController::view/$1/$2'); // Single property view by location/id (must be last)
});

// Authentication routes
$routes->group('auth', function($routes) {
    $routes->get('login', 'AuthController::loginForm');
    $routes->post('login', 'AuthController::login');
    $routes->get('logout', 'AuthController::logout');
});

// Dashboard routes (all protected by auth filter)
$routes->group('dashboard', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'DashboardController::index');
    $routes->get('profile', 'DashboardController::profile');

    // Enquiries management
    $routes->get('enquiries', 'DashboardController::enquiries');
    $routes->post('enquiries/mark-read/(:num)', 'DashboardController::markEnquiryRead/$1');
    $routes->post('enquiries/mark-all-read', 'DashboardController::markAllEnquiriesRead');
    $routes->get('enquiries/(:num)', 'DashboardController::deleteEnquiry/$1');
    $routes->delete('enquiries/(:num)', 'DashboardController::deleteEnquiry/$1');
    $routes->patch('enquiries/(:num)', 'DashboardController::updateEnquiry/$1');

    // Properties management
    $routes->get('properties', 'DashboardController::properties');
    $routes->get('properties/(:num)', 'DashboardController::viewProperty/$1');
    $routes->delete('properties/(:num)', 'DashboardController::deleteProperty/$1');
    $routes->get('properties/edit/(:num)', 'DashboardController::editProperty/$1');
    $routes->post('properties/edit/(:num)', 'DashboardController::updateProperty/$1');

    // Agent management
    $routes->get('agents', 'AgentController::index');
    $routes->get('agents/create', 'AgentController::create');
    $routes->post('agents/create', 'AgentController::store');
    $routes->get('agents/edit/(:num)', 'AgentController::edit/$1');
    $routes->post('agents/edit/(:num)', 'AgentController::update/$1');
    $routes->delete('agents/(:num)', 'AgentController::delete/$1');


    // Subscribers management
    $routes->get('subscribers', 'DashboardController::subscribers');

    // Blog management
    $routes->get('blog', 'BlogController::index');
    $routes->get('blog/create', 'BlogController::create');
    $routes->post('blog/create', 'BlogController::store');
    $routes->get('blog/edit/(:num)', 'BlogController::edit/$1');
    $routes->post('blog/edit/(:num)', 'BlogController::update/$1');
    $routes->delete('blog/(:num)', 'BlogController::delete/$1');
});

// Contact routes
$routes->get('contact', 'ContactController::index');
$routes->post('contact/submit', 'ContactController::submit');

// Public blog routes
$routes->get('blog', 'PublicBlogController::index');
$routes->get('blog/search', 'PublicBlogController::search');
$routes->get('blog/category/(:segment)', 'PublicBlogController::category/$1');
$routes->get('blog/(:segment)', 'PublicBlogController::single/$1');

// Newsletter routes
$routes->post('newsletter/subscribe', 'NewsletterController::subscribe');
$routes->post('newsletter/unsubscribe', 'NewsletterController::unsubscribe');

// Test routes (for migration validation)
$routes->group('test', function($routes) {
    $routes->get('/', 'TestController::index');
    $routes->get('database', 'TestController::database');
    $routes->get('createSampleData', 'TestController::createSampleData');
    $routes->get('testPropertyCreation', 'TestController::testPropertyCreation');
    $routes->get('testAgentCreation', 'TestController::testAgentCreation');
    $routes->get('cleanup', 'TestController::cleanup');
});
