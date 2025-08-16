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
 * @author Real Estate Team
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
            'title' => 'Dashboard | Real Estate',
            'totalProperties' => $this->propertyModel->countAllResults(),
            'totalContacts' => $this->contactModel->countAllResults(),
            'unreadContacts' => $this->contactModel->getCountByStatus(false),
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
     * Admin: View full agents hierarchy tree
     */
    public function agentsHierarchy()
    {
        $logger = new \App\Services\HierarchyLogger();
        $logger->log('access', 'Admin viewed full agents hierarchy', [
            'user_id' => session()->get('user_id'),
            'email' => session()->get('user_email'),
        ]);

        // Server-side pagination params (defaults chosen for performance)
        $perPage = (int) ($this->request->getGet('perPage') ?? 10);
        $perPage = max(5, min(50, $perPage)); // Clamp between 5 and 50
        $page = (int) ($this->request->getGet('page') ?? 1);

        try {
            // Fetch only current page of root agents to keep memory low
            $paged = $this->agentModel->getFullHierarchyTreePaginated($perPage, $page, 10);
            $stats = $this->agentModel->getStatistics();
        } catch (\Throwable $e) {
            log_message('error', 'Failed to build full hierarchy tree: ' . $e->getMessage());
            return redirect()->to('/dashboard')->with('error', 'Unable to load hierarchy data right now. Please try again later.');
        }

        $totalPages = (int) ceil(($paged['total'] ?: 0) / $paged['perPage']);

        $data = [
            'title' => 'Agents Hierarchy | Dashboard',
            // Maintain existing variable name for partial compatibility
            'tree' => $paged['roots'],
            'stats' => $stats,
            'pagination' => [
                'page' => $paged['page'],
                'perPage' => $paged['perPage'],
                'total' => $paged['total'],
                'totalPages' => max(1, $totalPages),
                'baseUrl' => base_url('dashboard/agents/hierarchy'),
            ],
        ];

        return view('dashboard/agents/hierarchy', $data);
    }

    /**
     * Admin: Fetch recent hierarchy logs for an agent (AJAX)
     */
    public function agentLogs(int $agentId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_METHOD_NOT_ALLOWED);
        }

        $logger = new \App\Services\HierarchyLogger();
        $lines = $logger->getRecentByAgent($agentId, 50);

        return view('dashboard/agents/partials/agent_logs', [
            'agentId' => $agentId,
            'lines' => $lines,
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
        $unreadContacts = $this->contactModel->getCountByStatus(false);

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
            'unreadContacts' => $unreadContacts,
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
            'unreadContacts' => $this->contactModel->getCountByStatus(false),
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
     */
    public function deleteProperty($id)
    {
        if ($this->request->getMethod() === 'DELETE') {
            if ($this->propertyModel->delete($id)) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => true]);
                }
                return redirect()->to('/dashboard/properties')->with('success', 'Property deleted successfully');
            } else {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete property']);
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
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
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

            return redirect()->back()->withInput()->with('error', $userMessage);
        }
    }

    /**
     * Handle image upload
     */
    private function handleImageUpload()
    {
        $images = $this->request->getFiles();
        $imagePaths = [];

        if (isset($images['images'])) {
            foreach ($images['images'] as $image) {
                if ($image->isValid() && !$image->hasMoved()) {
                    $newName = $image->getRandomName();
                    $image->move(WRITEPATH . 'uploads/properties', $newName);
                    $imagePaths[] = 'uploads/properties/' . $newName;
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
     * Handle image management for property updates
     *
     * This method processes:
     * - Existing images (keeping those not marked for removal)
     * - New uploaded images
     * - Removal of images marked for deletion
     *
     * @param array $property Current property data
     * @param array &$data Data array to update (passed by reference)
     */
    private function handleImageManagement($property, &$data)
    {
        // Parse existing images
        $existingImages = [];
        if (!empty($property['images'])) {
            $existingImages = is_string($property['images']) ? json_decode($property['images'], true) : $property['images'];
            $existingImages = $existingImages ?: [];
        }

        // Get images marked for removal
        $imagesToRemove = [];
        $imagesToRemoveJson = $this->request->getPost('images_to_remove');
        if (!empty($imagesToRemoveJson)) {
            $imagesToRemove = json_decode($imagesToRemoveJson, true) ?: [];
        }

        // Filter out images marked for removal
        $filteredImages = [];
        foreach ($existingImages as $index => $image) {
            if (!in_array($index, $imagesToRemove)) {
                $filteredImages[] = $image;
            } else {
                // Delete the physical file
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
     * This method safely removes image files from the server's writable directory.
     * It includes error handling to prevent application crashes if file deletion fails.
     * All deletion attempts are logged for audit purposes.
     *
     * @param string $imagePath Relative path to the image file (e.g., 'uploads/properties/image.jpg')
     */
    private function deleteImageFile($imagePath)
    {
        try {
            // Construct full path to the image file
            $fullPath = WRITEPATH . $imagePath;

            // Check if file exists before attempting deletion
            if (file_exists($fullPath)) {
                // Attempt to delete the file
                if (unlink($fullPath)) {
                    log_message('info', "Successfully deleted image file: {$fullPath}");
                } else {
                    log_message('warning', "Failed to delete image file (unlink returned false): {$fullPath}");
                }
            } else {
                log_message('info', "Image file not found (may have been already deleted): {$fullPath}");
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
