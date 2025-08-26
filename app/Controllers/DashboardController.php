<?php

namespace App\Controllers;

use App\Models\PropertyModel;
use App\Models\ContactModel;
use App\Models\NewsletterModel;
use App\Models\AgentModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * DashboardController
 *
 * Handles all dashboard-related functionality for authenticated users.
 * This controller manages the admin interface including:
 * - Dashboard overview with statistics
 * - Property management with pagination
 * - Enquiry management with real-time data
 * - User profile management
 * - Subscriber management
 *
 * All methods in this controller require authentication via the 'auth' filter.
 *
 * @author White Rock Realtor Team
 * @version 2.0 - Enhanced with pagination and real-time enquiry data
 * @since 2025-08-02
 */

class DashboardController extends BaseController
{
    protected $propertyModel;
    protected $contactModel;
    protected $newsletterModel;
    protected $agentModel;

    public function __construct()
    {
        $this->propertyModel = new PropertyModel();
        $this->contactModel = new ContactModel();
        $this->newsletterModel = new NewsletterModel();
        $this->agentModel = new AgentModel();
    }

    /**
     * Dashboard home
     */
    public function index()
    {
        // Get agent statistics
        $agentStats = $this->agentModel->getStatistics();

        $data = [
            'title' => 'Dashboard | White Rock Realtor',
            'totalProperties' => $this->propertyModel->countAllResults(), // Fixed: Re-added after notification badge removal
            'totalContacts' => $this->contactModel->countAllResults(),
            'unreadContacts' => $this->contactModel->getCountByStatus(false), // Fixed: Re-added for dashboard stats card
            'totalSubscribers' => $this->newsletterModel->getSubscriberCount(),
            'totalAgents' => $agentStats['total'],
            'activeAgents' => $agentStats['active'],
            'recentAgents' => $agentStats['recent'],
            'agentStats' => $agentStats,
            'recentProperties' => $this->propertyModel->orderBy('created_at', 'DESC')->limit(5)->findAll(),
            'recentContacts' => $this->contactModel->getRecent(5),
            'recentAgentsList' => $this->agentModel->getRecent(5)
        ];

        return view('dashboard/index', $data);
    }

    /**
     * Enhanced admin hierarchy view using closure table for better performance
     */
    public function agentsHierarchy()
    {
        // Check if this is a JSON API request
        if ($this->request->isAJAX() || $this->request->getGet('format') === 'json') {
            return $this->agentsHierarchyData();
        }

        // Use the new enhanced hierarchy view
        return view('dashboard/agents_hierarchy');
    }

    /**
     * JSON API endpoint for admin hierarchy data
     */
    public function agentsHierarchyData()
    {
        try {
            // Get request parameters
            $page = max(1, (int) ($this->request->getGet('page') ?? 1));
            $perPage = max(5, min(50, (int) ($this->request->getGet('per_page') ?? 10)));
            $viewMode = $this->request->getGet('view_mode') ?? 'paginated';
            $search = trim($this->request->getGet('search') ?? '');

            // Get hierarchy data using closure table for better performance
            $repository = new \App\Models\AgentRepository();

            if ($viewMode === 'tree') {
                // Full tree view (limited depth for performance)
                $agents = $this->getFullHierarchyTreeWithClosureTable(5);
                $pagination = null;
            } else {
                // Paginated view of root agents
                $paged = $this->agentModel->getFullHierarchyTreePaginated($perPage, $page, 3);
                $agents = $paged['roots'];
                $pagination = [
                    'page' => $paged['page'],
                    'per_page' => $paged['perPage'],
                    'total' => $paged['total'],
                    'total_pages' => max(1, (int) ceil(($paged['total'] ?: 0) / $paged['perPage'])),
                    'from' => (($paged['page'] - 1) * $paged['perPage']) + 1,
                    'to' => min($paged['page'] * $paged['perPage'], $paged['total'])
                ];
            }

            // Apply search filter if provided
            if (!empty($search)) {
                $agents = $this->filterAgentsBySearch($agents, $search);
            }



            // Get overall statistics
            $statistics = $this->getHierarchyStatistics();

            return $this->response->setJSON([
                'success' => true,
                'agents' => $agents,
                'pagination' => $pagination,
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in agentsHierarchyData: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load hierarchy data'
            ])->setStatusCode(500);
        }
    }

    /**
     * Get full hierarchy tree using closure table
     */
    private function getFullHierarchyTreeWithClosureTable($maxDepth = 5)
    {
        // Get all root agents (no parent)
        $roots = $this->agentModel->groupStart()
            ->where('parent_agent_id', null)
            ->orWhere('parent_agent_id', 0)
            ->groupEnd()
            ->where('is_active', true)
            ->orderBy('name', 'ASC')
            ->findAll();

        foreach ($roots as &$root) {
            $root['hierarchy_level'] = 0;
            $root['children'] = $this->agentModel->getHierarchyTreeClosureTable($root['id'], $maxDepth);
            $root['has_children'] = !empty($root['children']);
            $root['total_downline'] = $this->agentModel->getSubtreeCountClosureTable($root['id']);
        }
        unset($root);

        return $roots;
    }

    /**
     * Filter agents by search query
     */
    private function filterAgentsBySearch($agents, $search)
    {
        return array_filter($agents, function($agent) use ($search) {
            return stripos($agent['name'], $search) !== false ||
                   stripos($agent['email'], $search) !== false ||
                   stripos($agent['unique_agent_id'], $search) !== false;
        });
    }



    /**
     * Get overall hierarchy statistics
     */
    private function getHierarchyStatistics()
    {
        $stats = $this->agentModel->getStatistics();

        // Get max depth using closure table
        $db = \Config\Database::connect();
        $maxDepthQuery = $db->table('agent_tree')
            ->selectMax('depth', 'max_depth')
            ->get()
            ->getFirstRow('array');

        $maxDepth = (int) ($maxDepthQuery['max_depth'] ?? 0);



        return [
            'total_agents' => $stats['total'],
            'active_agents' => $stats['active'],
            'inactive_agents' => $stats['inactive'],
            'max_depth' => $maxDepth,

        ];
    }

    /**
     * Admin: View agent details (AJAX)
     *
     * This method allows admin to view detailed information about any agent
     * in the hierarchy, including their profile, statistics, and sub-agents.
     */
    public function viewAgentDetails(int $agentId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_METHOD_NOT_ALLOWED);
        }

        try {
            // Get agent details
            $agent = $this->agentModel->find($agentId);

            if (!$agent) {
                return $this->response->setStatusCode(404)->setBody(
                    '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Agent not found.</div>'
                );
            }

            // Get additional agent statistics
            $directSubAgents = $this->agentModel->getSubAgents($agentId, 10); // Get up to 10 direct sub-agents
            $totalDownline = $this->agentModel->countTotalDownline($agentId);
            $hierarchyPosition = $this->agentModel->getAgentHierarchyPosition($agentId);

            // Get parent agent information if exists
            $parentAgent = null;
            if ($agent['parent_agent_id']) {
                $parentAgent = $this->agentModel->find($agent['parent_agent_id']);
            }

            $data = [
                'agent' => $agent,
                'directSubAgents' => $directSubAgents,
                'totalDownline' => $totalDownline,
                'hierarchyPosition' => $hierarchyPosition,
                'parentAgent' => $parentAgent,
                'directSubAgentCount' => count($directSubAgents)
            ];

            return view('dashboard/agents/view_agent_details', $data);

        } catch (\Exception $e) {
            log_message('error', 'Admin agent details view failed: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setBody(
                '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Failed to load agent details. Please try again.</div>'
            );
        }
    }

    /**
     * Admin: Fetch recent hierarchy logs for an agent (AJAX)
     */
    public function agentLogs(int $agentId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_METHOD_NOT_ALLOWED);
        }

        // For now, return basic agent information
        // This can be enhanced with actual logging functionality later
        $agent = $this->agentModel->find($agentId);

        if (!$agent) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Agent not found'
            ]);
        }

        $logs = [
            [
                'timestamp' => date('Y-m-d H:i:s'),
                'action' => 'Agent viewed',
                'details' => 'Admin accessed agent details'
            ]
        ];

        return $this->response->setJSON([
            'success' => true,
            'agent' => $agent,
            'logs' => $logs
        ]);
    }







    /**
     * User profile
     */
    public function profile()
    {
        $data = [
            'title' => 'Profile | Dashboard'
        ];

        return view('dashboard/profile', $data);
    }

    /**
     * All enquiries with pagination
     */
    public function enquiries()
    {
        $perPage = 10; // Number of enquiries per page
        $page = (int) ($this->request->getGet('page') ?? 1);
        $offset = ($page - 1) * $perPage;

        $totalContacts = $this->contactModel->countAll();

        // Get weekly contacts (last 7 days)
        $weekStart = date('Y-m-d H:i:s', strtotime('-7 days'));
        $weeklyContacts = $this->contactModel->where('created_at >=', $weekStart)->countAllResults();

        // Get paginated contacts
        $contacts = $this->contactModel->orderBy('created_at', 'DESC')
                                     ->limit($perPage, $offset)
                                     ->findAll();

        $data = [
            'title' => 'Enquiries Management',
            'pageTitle' => 'Enquiries',
            'contacts' => $contacts,
            'totalContacts' => $totalContacts,
            'weeklyContacts' => $weeklyContacts,
            'currentPage' => $page,
            'totalPages' => ceil($totalContacts / $perPage),
            'perPage' => $perPage,
            'user' => session()->get('user')
        ];

        return view('dashboard/enquiries', $data);
    }

    /**
     * Delete enquiry
     */
    public function deleteEnquiry($id)
    {
        if ($this->request->getMethod() === 'DELETE' || $this->request->getMethod() === 'GET') {
            if ($this->contactModel->delete($id)) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => true]);
                }
                return redirect()->to('/dashboard/enquiries')->with('success', 'Enquiry deleted successfully');
            } else {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete enquiry']);
                }
                return redirect()->to('/dashboard/enquiries')->with('error', 'Failed to delete enquiry');
            }
        }

        return redirect()->to('/dashboard/enquiries');
    }

    /**
     * Mark enquiry as read
     */
    public function markEnquiryRead($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/dashboard/enquiries');
        }

        $contact = $this->contactModel->find($id);
        if (!$contact) {
            return $this->response->setJSON(['success' => false, 'message' => 'Enquiry not found']);
        }

        if ($this->contactModel->update($id, ['is_read' => true])) {
            return $this->response->setJSON(['success' => true, 'message' => 'Enquiry marked as read']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to mark enquiry as read']);
        }
    }

    /**
     * Update enquiry status
     */
    public function updateEnquiry($id)
    {
        if ($this->request->getMethod() === 'PATCH') {
            $isRead = $this->request->getJSON(true)['is_read'] ?? true;

            if ($this->contactModel->update($id, ['is_read' => $isRead])) {
                return $this->response->setJSON(['success' => true]);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to update enquiry']);
            }
        }

        return $this->response->setStatusCode(ResponseInterface::HTTP_METHOD_NOT_ALLOWED);
    }

    /**
     * Get enquiry details for modal
     */
    public function getEnquiryDetails($id)
    {
        if ($this->request->isAJAX()) {
            $enquiry = $this->contactModel->find($id);

            if (!$enquiry) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Enquiry not found'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'enquiry' => $enquiry
            ]);
        }

        return $this->response->setStatusCode(ResponseInterface::HTTP_METHOD_NOT_ALLOWED);
    }

    /**
     * All properties admin view with pagination
     */
    public function properties()
    {
        $perPage = 10; // Number of properties per page
        $page = (int) ($this->request->getGet('page') ?? 1);
        $offset = ($page - 1) * $perPage;

        // Get total count for pagination
        $totalProperties = $this->propertyModel->countAll();

        // Get paginated properties
        $properties = $this->propertyModel->orderBy('created_at', 'DESC')
                                         ->limit($perPage, $offset)
                                         ->findAll();

        $data = [
            'title' => 'Properties Management',
            'pageTitle' => 'Properties',
            'properties' => $properties,
            'user' => session()->get('user'),
            'currentPage' => $page,
            'totalPages' => ceil($totalProperties / $perPage),
            'perPage' => $perPage,
            'totalProperties' => $totalProperties,
            'startRecord' => $offset + 1,
            'endRecord' => min($offset + $perPage, $totalProperties)
        ];

        return view('dashboard/properties', $data);
    }

    /**
     * Single property admin view
     */
    public function viewProperty($id)
    {
        $property = $this->propertyModel->find($id);

        if (!$property) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => $property['title'] . ' | Dashboard',
            'property' => $property
        ];

        return view('dashboard/property_single', $data);
    }

    /**
     * Delete property
     *
     * Handles property deletion with automatic cascade delete for associated images.
     * The cascade delete is now handled by the PropertyModel's beforeDelete callback,
     * so this method focuses on the HTTP response handling.
     */
    public function deleteProperty($id)
    {
        if ($this->request->getMethod() === 'DELETE') {
            // The PropertyModel's beforeDelete callback will automatically handle image deletion
            if ($this->propertyModel->delete($id)) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Property and associated images deleted successfully'
                    ]);
                }
                return redirect()->to('/dashboard/properties')->with('success', 'Property deleted successfully');
            } else {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Failed to delete property. Please try again.'
                    ]);
                }
                return redirect()->to('/dashboard/properties')->with('error', 'Failed to delete property');
            }
        }

        return redirect()->to('/dashboard/properties');
    }

    /**
     * Toggle featured status of a property
     */
    public function toggleFeatured($id)
    {
        if ($this->request->getMethod() === 'POST') {
            $property = $this->propertyModel->find($id);

            if (!$property) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Property not found']);
                }
                return redirect()->to('/dashboard/properties')->with('error', 'Property not found');
            }

            $newStatus = !$property['is_featured'];
            $statusText = $newStatus ? 'featured' : 'unfeatured';

            if ($this->propertyModel->update($id, ['is_featured' => $newStatus])) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => "Property marked as {$statusText} successfully",
                        'is_featured' => $newStatus
                    ]);
                }
                return redirect()->to('/dashboard/properties')->with('success', "Property marked as {$statusText} successfully");
            } else {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Failed to update featured status']);
                }
                return redirect()->to('/dashboard/properties')->with('error', 'Failed to update featured status');
            }
        }

        return redirect()->to('/dashboard/properties');
    }

    /**
     * Edit property form
     */
    public function editProperty($id)
    {
        $property = $this->propertyModel->find($id);

        if (!$property) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => 'Edit Property | Dashboard',
            'property' => $property,
            'locations' => $this->getLocations()
        ];

        return view('dashboard/edit_property', $data);
    }

    /**
     * Update property
     */
    public function updateProperty($id)
    {
        try {
            $property = $this->propertyModel->find($id);

            if (!$property) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }

            // Define validation rules with custom messages
            $validationRules = [
                'title' => 'required|max_length[255]',
                'description' => 'required|min_length[10]',
                'type' => 'required|in_list[house,apartment,villa,land]',
                'location' => 'required|max_length[255]',
                'area' => 'permit_empty|integer|greater_than[0]',
                'images.*' => 'permit_empty|is_image[images]|max_size[images,2048]'
            ];

            $validationMessages = [
                'title' => [
                    'required' => 'Property title is required.',
                    'max_length' => 'Property title cannot exceed 255 characters.'
                ],
                'description' => [
                    'required' => 'Property description is required.',
                    'min_length' => 'Property description must be at least 10 characters long.'
                ],
                'type' => [
                    'required' => 'Property type is required.',
                    'in_list' => 'Please select a valid property type.'
                ],
                'location' => [
                    'required' => 'Property location is required.',
                    'max_length' => 'Location cannot exceed 255 characters.'
                ],
                'area' => [
                    'integer' => 'Area must be a valid number.',
                    'greater_than' => 'Area must be greater than 0.'
                ],
                'images.*' => [
                    'is_image' => 'Please upload valid image files only.',
                    'max_size' => 'Each image must be smaller than 2MB.'
                ]
            ];

            // Validate the request data
            if (!$this->validate($validationRules, $validationMessages)) {
                // Fix for Array to string conversion error in SiteURI.php
                // When validation fails, we need to handle array form data properly
                // to prevent arrays from being passed to URL generation functions
                $inputData = $this->request->getPost();

                // Convert array fields to JSON strings to prevent URL generation errors
                if (isset($inputData['youtube_videos']) && is_array($inputData['youtube_videos'])) {
                    $inputData['youtube_videos'] = json_encode($inputData['youtube_videos']);
                }
                if (isset($inputData['images']) && is_array($inputData['images'])) {
                    $inputData['images'] = json_encode($inputData['images']);
                }

                return redirect()->back()->withInput($inputData)->with('errors', $this->validator->getErrors());
            }

            // Prepare data for update
            $data = [
                'title' => trim($this->request->getPost('title')),
                'description' => trim($this->request->getPost('description')),
                'type' => $this->request->getPost('type'),
                'location' => trim($this->request->getPost('location')),
                'area' => $this->request->getPost('area') ?: null,
                'is_featured' => $this->request->getPost('is_featured') ? 1 : 0
            ];

            // Handle image management (existing + new - removed)
            $this->handleImageManagement($property, $data);

            // Handle YouTube video management (existing + new - removed)
            $this->handleVideoManagement($property, $data);

            // Update property in database
            if ($this->propertyModel->update($id, $data)) {
                return redirect()->to('/dashboard/properties')->with('success', 'Property updated successfully! Changes are now visible on the website.');
            } else {
                throw new \Exception('Database update failed');
            }

        } catch (\Exception $e) {
            // Log detailed error for debugging
            log_message('error', 'Property update failed: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine());

            // User-friendly error message
            $userMessage = 'Failed to update property. Please check your information and try again.';

            if (strpos($e->getMessage(), 'upload') !== false) {
                $userMessage = 'There was an issue uploading your images. Please try again with smaller image files.';
            } elseif (strpos($e->getMessage(), 'database') !== false) {
                $userMessage = 'We are experiencing technical difficulties. Please try again in a few minutes.';
            }

            // Fix for Array to string conversion error in SiteURI.php
            // Handle array form data properly in exception scenarios
            $inputData = $this->request->getPost();

            // Convert array fields to JSON strings to prevent URL generation errors
            if (isset($inputData['youtube_videos']) && is_array($inputData['youtube_videos'])) {
                $inputData['youtube_videos'] = json_encode($inputData['youtube_videos']);
            }
            if (isset($inputData['images']) && is_array($inputData['images'])) {
                $inputData['images'] = json_encode($inputData['images']);
            }

            return redirect()->back()->withInput($inputData)->with('error', $userMessage);
        }
    }

    /**
     * Handle image upload
     *
     * Uses unified storage location (public/uploads/properties) for consistency
     * with the centralized image management approach.
     */
    private function handleImageUpload()
    {
        $images = $this->request->getFiles();
        $imagePaths = [];

        if (isset($images['images'])) {
            foreach ($images['images'] as $image) {
                if ($image->isValid() && !$image->hasMoved()) {
                    try {
                        // Use centralized image management service
                        $imageService = new \App\Services\ImageManagementService();
                        $uploadResult = $imageService->uploadImage($image, 'property');

                        if ($uploadResult['success']) {
                            $imagePaths[] = $uploadResult['file_path'];
                        } else {
                            log_message('error', 'Property image upload failed: ' . $uploadResult['message']);
                        }
                    } catch (\Exception $e) {
                        log_message('error', 'Property image upload exception: ' . $e->getMessage());
                    }
                }
            }
        }

        return $imagePaths;
    }



    /**
     * Subscribers management
     */
    public function subscribers()
    {
        $newsletterModel = new \App\Models\NewsletterModel();
        $subscribers = $newsletterModel->orderBy('created_at', 'DESC')->findAll();

        $data = [
            'title' => 'Subscribers | Dashboard',
            'subscribers' => $subscribers,
            'totalSubscribers' => count($subscribers),
            'recentSubscribers' => $newsletterModel->where('created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')))->countAllResults(false)
        ];

        return view('dashboard/subscribers', $data);
    }

    /**
     * Handle YouTube video URLs
     */
    private function handleYouTubeVideoUpload()
    {
        $videoUrls = $this->request->getPost('youtube_video');
        $validUrls = [];

        if (!empty($videoUrls)) {
            // Handle both single string and array of URLs
            if (is_string($videoUrls)) {
                $videoUrls = [$videoUrls];
            }

            foreach ($videoUrls as $url) {
                $url = trim($url);
                if (!empty($url) && $this->isValidYouTubeUrl($url)) {
                    $validUrls[] = $url;
                }
            }
        }

        return array_unique($validUrls); // Remove duplicates
    }

    /**
     * Validate YouTube URL
     */
    private function isValidYouTubeUrl($url)
    {
        $pattern = '/^(https?:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|embed\/)|youtu\.be\/)[\w\-]+/';
        return preg_match($pattern, $url);
    }

    /**
     * Handle complex image management for property updates
     *
     * This method orchestrates the multi-step process of updating property images
     * during property edit operations. It handles three main scenarios:
     * 1. Preserving existing images that should remain
     * 2. Processing and uploading new images from form submission
     * 3. Removing images marked for deletion (both from database and filesystem)
     *
     * Business Logic Flow:
     * 1. Parse existing images from database (JSON → array)
     * 2. Identify images marked for removal via hidden form field
     * 3. Filter existing images, removing marked ones and deleting files
     * 4. Process new image uploads with validation and optimization
     * 5. Merge filtered existing + new images into final array
     * 6. Update data array with final image list for database storage
     *
     * Data Flow:
     * - Input: Current property data + form submission data
     * - Processing: Image filtering, file operations, new uploads
     * - Output: Updated data array with final image list
     *
     * Error Handling:
     * - File deletion errors are logged but don't stop the process
     * - Upload errors bubble up to calling method
     * - Maintains data integrity even if some operations fail
     *
     * @param array $property Current property data from database
     * @param array &$data Data array to update (passed by reference for efficiency)
     * @throws \Exception If critical image operations fail
     */
    private function handleImageManagement($property, &$data)
    {
        // Step 1: Parse existing images from database storage format
        // Images are stored as JSON string in database, convert to PHP array
        $existingImages = [];
        if (!empty($property['images'])) {
            $existingImages = is_string($property['images']) ? json_decode($property['images'], true) : $property['images'];
            $existingImages = $existingImages ?: [];  // Fallback to empty array if decode fails
        }

        // Step 2: Get list of images marked for removal from hidden form field
        // Frontend JavaScript populates this field with indices of images to delete
        $imagesToRemove = [];
        $imagesToRemoveJson = $this->request->getPost('images_to_remove');
        if (!empty($imagesToRemoveJson)) {
            $imagesToRemove = json_decode($imagesToRemoveJson, true) ?: [];
        }

        // Step 3: Filter existing images and delete marked ones
        // This preserves images that should remain and removes unwanted ones
        $filteredImages = [];
        foreach ($existingImages as $index => $image) {
            if (!in_array($index, $imagesToRemove)) {
                // Keep this image - add to filtered list
                $filteredImages[] = $image;
            } else {
                // Remove this image - delete physical file from filesystem
                // Note: Deletion errors are logged but don't stop the process
                $this->deleteImageFile($image);
            }
        }

        // Handle new image uploads
        $newImages = $this->handleImageUpload();

        // Combine filtered existing images with new images
        $data['images'] = array_merge($filteredImages, $newImages);
    }

    /**
     * Handle video management for property updates
     *
     * This method processes:
     * - Existing videos (keeping those not marked for removal)
     * - New video URLs
     * - Removal of videos marked for deletion
     *
     * @param array $property Current property data
     * @param array &$data Data array to update (passed by reference)
     */
    private function handleVideoManagement($property, &$data)
    {
        // Parse existing videos
        $existingVideos = [];
        if (!empty($property['youtube_video'])) {
            $existingVideos = is_string($property['youtube_video']) ? json_decode($property['youtube_video'], true) : $property['youtube_video'];
            $existingVideos = $existingVideos ?: [];
        }

        // Get videos marked for removal
        $videosToRemove = [];
        $videosToRemoveJson = $this->request->getPost('videos_to_remove');
        if (!empty($videosToRemoveJson)) {
            $videosToRemove = json_decode($videosToRemoveJson, true) ?: [];
        }

        // Filter out videos marked for removal
        $filteredVideos = [];
        foreach ($existingVideos as $index => $video) {
            if (!in_array($index, $videosToRemove)) {
                $filteredVideos[] = $video;
            }
        }

        // Handle new video URLs
        $newVideos = $this->handleYouTubeVideoUpload();

        // Combine filtered existing videos with new videos
        $data['youtube_video'] = array_merge($filteredVideos, $newVideos);
    }

    /**
     * Delete image file from filesystem
     *
     * This method safely removes image files using the centralized ImageManagementService.
     * It handles both the old writable directory and new public directory locations.
     * All deletion attempts are logged for audit purposes.
     *
     * @param string $imagePath Relative path to the image file (e.g., 'uploads/properties/image.jpg')
     */
    private function deleteImageFile($imagePath)
    {
        try {
            // Use centralized image management service for consistent handling
            $imageService = new \App\Services\ImageManagementService();
            $result = $imageService->deleteImage($imagePath);

            if (!$result['success']) {
                log_message('warning', "Failed to delete image file via ImageManagementService: {$imagePath}. Error: {$result['message']}");

                // Fallback: try old location for backward compatibility during transition
                $oldPath = WRITEPATH . $imagePath;
                if (file_exists($oldPath)) {
                    if (unlink($oldPath)) {
                        log_message('info', "Successfully deleted image file from old location: {$oldPath}");
                    } else {
                        log_message('warning', "Failed to delete image file from old location: {$oldPath}");
                    }
                }
            }
        } catch (\Exception $e) {
            // Log error but don't throw exception to prevent breaking the update process
            log_message('error', "Exception while deleting image file {$imagePath}: " . $e->getMessage());
        }
    }

    /**
     * Get available locations (Siliguri and nearby areas)
     */
    private function getLocations()
    {
        // Load from config file
        $config = new \Config\Locations();
        return $config->locations;
    }
}
