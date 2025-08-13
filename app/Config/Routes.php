<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Root route - homepage
$routes->get('/', 'Home::index');

// Error route
$routes->get('error', 'Home::error');

// About Us route
$routes->get('about', 'Home::about');

// Property routes
// Note: Route order is important! More specific routes must come before general ones
// to prevent route conflicts where 'details' could be matched as a location segment
$routes->group('properties', function($routes) {
    $routes->get('/', 'PropertyController::index');
    $routes->get('search', 'PropertyController::search'); // Property search functionality
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
    $routes->get('forgot-password', 'AuthController::forgotPasswordForm');
    $routes->post('forgot-password', 'AuthController::forgotPassword');
    $routes->get('reset-password', 'AuthController::resetPasswordForm');
    $routes->post('reset-password', 'AuthController::resetPassword');
});

// Agent Authentication routes
$routes->group('agent', function($routes) {
    $routes->get('login', 'AgentAuthController::loginForm');
    $routes->post('login', 'AgentAuthController::login');
    $routes->get('logout', 'AgentAuthController::logout');

    // Agent Dashboard routes (protected by agent_auth filter)
    $routes->group('/', ['filter' => 'agent_auth'], function($routes) {
        $routes->get('dashboard', 'AgentAuthController::dashboard');
        $routes->get('profile', 'AgentAuthController::profile');
        $routes->post('profile', 'AgentAuthController::updateProfile');

        // Sub-agent management
        $routes->get('sub-agents', 'AgentAuthController::subAgents');
        $routes->get('sub-agents/create', 'AgentAuthController::createSubAgent');
        $routes->post('sub-agents/create', 'AgentAuthController::storeSubAgent');
        $routes->get('sub-agents/edit/(:num)', 'AgentAuthController::editSubAgent/$1');
        $routes->post('sub-agents/edit/(:num)', 'AgentAuthController::updateSubAgent/$1');
        $routes->get('sub-agents/view/(:num)', 'AgentAuthController::viewSubAgent/$1');
        $routes->post('sub-agents/delete/(:num)', 'AgentAuthController::deleteSubAgent/$1');
        $routes->delete('sub-agents/delete/(:num)', 'AgentAuthController::deleteSubAgent/$1');

        // Multi-level hierarchy management
        $routes->get('hierarchy', 'AgentAuthController::hierarchyTree');
        $routes->get('downline', 'AgentAuthController::downlineManagement');

        // Commission management
        $routes->get('commissions', 'AgentAuthController::commissionDashboard');
    });
});

// Dashboard routes (all protected by auth filter)
$routes->group('dashboard', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'DashboardController::index');
    $routes->get('profile', 'DashboardController::profile');

    // Enquiries management
    $routes->get('enquiries', 'DashboardController::enquiries');
    $routes->get('enquiries/details/(:num)', 'DashboardController::getEnquiryDetails/$1');
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

    // Property sales and commission management
    $routes->get('properties/sale/(:num)', 'PropertyController::processSale/$1');
    $routes->post('properties/sale/(:num)', 'PropertyController::processSale/$1');
    $routes->get('properties/commission/(:num)', 'PropertyController::commissionSummary/$1');
    $routes->post('properties/toggle-featured/(:num)', 'DashboardController::toggleFeatured/$1');

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
