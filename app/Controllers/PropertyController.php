<?php

namespace App\Controllers;

use App\Models\PropertyModel;

/**
 * PropertyController
 *
 * Handles public property listing and single property views with enhanced media gallery support.
 * This controller manages:
 * - Property listing with image display
 * - Single property view with multiple images and YouTube videos
 * - Property creation with multiple image and video upload
 * - Enhanced media gallery with lightbox and carousel functionality
 *
 * Key Features:
 * - Multiple image upload and display
 * - Multiple YouTube video support with validation
 * - Responsive image gallery with thumbnails
 * - Lightbox with video playback support
 * - Mobile-first responsive design
 *
 * @author Real Estate Team
 * @version 2.0 - Enhanced with multiple media support
 * @since 2025-08-02
 */
use CodeIgniter\HTTP\ResponseInterface;

class PropertyController extends BaseController
{
    protected $propertyModel;

    public function __construct()
    {
        $this->propertyModel = new PropertyModel();
    }

    /**
     * Display properties listing page with pagination
     *
     * This method handles the main properties listing page with modern pagination.
     * It efficiently loads properties in batches to improve performance and user experience.
     *
     * Features:
     * - Pagination with configurable items per page (default: 12)
     * - Responsive grid layout optimized for all devices
     * - SEO-friendly URLs with page parameters
     * - Performance optimized with LIMIT/OFFSET queries
     * - Comprehensive pagination metadata for frontend
     *
     * Performance Considerations:
     * - Uses LIMIT/OFFSET for efficient database queries
     * - Calculates total count only once per request
     * - Provides all necessary pagination data to avoid additional queries
     *
     * @return string The rendered properties listing view with pagination data
     */
    public function index()
    {
        $perPage = 12; // Number of properties per page
        $page = (int) ($this->request->getGet('page') ?? 1);
        $offset = ($page - 1) * $perPage;

        // Get total count for pagination
        $totalProperties = $this->propertyModel->countAll();

        // Fetch properties with pagination
        $properties = $this->propertyModel->orderBy('created_at', 'DESC')
                                         ->limit($perPage, $offset)
                                         ->findAll();

        $data = [
            'title' => 'Properties | Real Estate',
            'properties' => $properties,
            'currentPage' => $page,
            'totalPages' => ceil($totalProperties / $perPage),
            'perPage' => $perPage,
            'totalProperties' => $totalProperties,
            'hasNextPage' => $page < ceil($totalProperties / $perPage),
            'hasPrevPage' => $page > 1
        ];

        return view('properties/index', $data);
    }

    /**
     * Search properties with filters and pagination
     *
     * This method handles property search with multiple filter criteria including
     * location, property type, and minimum area. It maintains the same pagination
     * structure as the main index method for consistency.
     *
     * Features:
     * - Multi-criteria search (location, type, min_area)
     * - Maintains pagination for search results
     * - Preserves search parameters in pagination links
     * - User-friendly error handling for invalid search criteria
     * - Performance optimized with proper database queries
     *
     * @return string The rendered properties search results view with pagination
     */
    public function search()
    {
        $perPage = 12; // Number of properties per page
        $page = (int) ($this->request->getGet('page') ?? 1);
        $offset = ($page - 1) * $perPage;

        // Get search parameters
        $location = trim($this->request->getGet('location') ?? '');
        $type = trim($this->request->getGet('type') ?? '');
        $minArea = (int) ($this->request->getGet('min_area') ?? 0);

        // Build search query
        $builder = $this->propertyModel->orderBy('created_at', 'DESC');

        // Apply location filter
        if (!empty($location)) {
            $builder->where('location', $location);
        }

        // Apply property type filter
        if (!empty($type)) {
            $builder->where('type', $type);
        }

        // Apply minimum area filter
        if ($minArea > 0) {
            $builder->where('area >=', $minArea);
        }

        // Get total count for pagination
        $totalProperties = $builder->countAllResults(false); // false to preserve the query

        // Fetch properties with pagination
        $properties = $builder->limit($perPage, $offset)->findAll();

        // Build search query string for pagination links
        $searchParams = [];
        if (!empty($location)) $searchParams['location'] = $location;
        if (!empty($type)) $searchParams['type'] = $type;
        if ($minArea > 0) $searchParams['min_area'] = $minArea;
        $searchQuery = !empty($searchParams) ? '&' . http_build_query($searchParams) : '';

        $data = [
            'title' => 'Search Results | Properties | Real Estate',
            'properties' => $properties,
            'currentPage' => $page,
            'totalPages' => ceil($totalProperties / $perPage),
            'perPage' => $perPage,
            'totalProperties' => $totalProperties,
            'hasNextPage' => $page < ceil($totalProperties / $perPage),
            'hasPrevPage' => $page > 1,
            'searchQuery' => $searchQuery,
            'searchParams' => $searchParams,
            'isSearchResults' => true
        ];

        return view('properties/index', $data);
    }

    /**
     * Display single property
     */
    public function view($place, $id)
    {
        $property = $this->propertyModel->where('location', $place)
                                      ->where('id', $id)
                                      ->first();

        if (!$property) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Get similar properties from the same location
        $similarProperties = $this->propertyModel->getByLocation($place, 6, $id);

        $data = [
            'title' => $property['title'] . ' | Real Estate',
            'property' => $property,
            'similarProperties' => $similarProperties
        ];

        // Add helper methods to the view
        helper('property');

        return view('properties/single', $data);
    }

    /**
     * Get property details (API endpoint)
     */
    public function details($id)
    {
        $property = $this->propertyModel->find($id);

        if (!$property) {
            return $this->response->setJSON(['error' => 'Property not found'])
                                 ->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        return $this->response->setJSON($property);
    }

    /**
     * Show create property form
     */
    public function create()
    {
        $data = [
            'title' => 'Create Property | Real Estate',
            'locations' => $this->getLocations()
        ];

        return view('properties/create', $data);
    }

    /**
     * Store new property
     *
     * Handles property creation with comprehensive validation and error handling.
     * Includes image upload processing and user-friendly error messages.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function store()
    {
        try {
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
                    'required' => 'Property title is required',
                    'max_length' => 'Property title cannot exceed 255 characters'
                ],
                'description' => [
                    'required' => 'Property description is required',
                    'min_length' => 'Description must be at least 10 characters long'
                ],
                'type' => [
                    'required' => 'Property type is required',
                    'in_list' => 'Please select a valid property type'
                ],
                'location' => [
                    'required' => 'Property location is required'
                ],
                'area' => [
                    'integer' => 'Area must be a valid number',
                    'greater_than' => 'Area must be greater than 0'
                ],
                'images.*' => [
                    'uploaded' => 'Please upload at least one property image',
                    'is_image' => 'Only image files are allowed',
                    'max_size' => 'Image size cannot exceed 2MB'
                ]
            ];

            // Validate the request data
            if (!$this->validate($validationRules, $validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            // Prepare data for insertion
            $data = [
                'title' => trim($this->request->getPost('title')),
                'description' => trim($this->request->getPost('description')),
                'type' => $this->request->getPost('type'),
                'location' => trim($this->request->getPost('location')),
                'area' => $this->request->getPost('area') ?: null,
                'images' => $this->handleImageUpload(),
                'youtube_video' => $this->handleYouTubeVideoUpload()
            ];

            // Insert property into database
            if ($this->propertyModel->insert($data)) {
                return redirect()->to('/dashboard/properties')->with('success', 'Property created successfully! It is now visible on the website.');
            } else {
                throw new \Exception('Database insertion failed');
            }

        } catch (\Exception $e) {
            // Log detailed error for debugging
            log_message('error', 'Property creation failed: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine());

            // User-friendly error message
            $userMessage = 'Failed to create property. Please check your information and try again.';

            if (strpos($e->getMessage(), 'upload') !== false) {
                $userMessage = 'There was an issue uploading your images. Please try again with smaller image files.';
            } elseif (strpos($e->getMessage(), 'database') !== false) {
                $userMessage = 'We are experiencing technical difficulties. Please try again in a few minutes.';
            }

            return redirect()->back()->withInput()->with('error', $userMessage);
        }
    }

    /**
     * Handle multiple image upload for properties
     *
     * Processes multiple image files uploaded via the property creation form.
     * Each image is validated, renamed with a random name for security,
     * and stored in the writable/uploads/properties directory.
     *
     * Security features:
     * - Validates file integrity with isValid()
     * - Prevents file overwrites with hasMoved() check
     * - Uses random filenames to prevent path traversal attacks
     *
     * @return array Array of image paths relative to the public directory
     */
    private function handleImageUpload()
    {
        $images = $this->request->getFiles();
        $imagePaths = [];

        // Process multiple images if uploaded
        if (isset($images['images'])) {
            foreach ($images['images'] as $image) {
                // Validate each image before processing
                if ($image->isValid() && !$image->hasMoved()) {
                    // Generate secure random filename
                    $newName = $image->getRandomName();

                    // Move image to secure upload directory
                    $image->move(WRITEPATH . 'uploads/properties', $newName);

                    // Store relative path for database
                    $imagePaths[] = 'uploads/properties/' . $newName;
                }
            }
        }
        return $imagePaths;
    }

    /**
     * Handle multiple YouTube video URLs for properties
     *
     * Processes and validates multiple YouTube video URLs from the property form.
     * Supports both new multiple video format and legacy single video format
     * for backward compatibility.
     *
     * Features:
     * - Validates YouTube URL format using regex pattern
     * - Supports multiple video input formats (array or single string)
     * - Maintains backward compatibility with single video field
     * - Removes duplicate URLs automatically
     * - Trims whitespace from URLs
     *
     * @return array Array of validated YouTube URLs
     */
    private function handleYouTubeVideoUpload()
    {
        $videoUrls = $this->request->getPost('youtube_videos');
        $validUrls = [];

        // Process multiple video URLs if provided
        if (!empty($videoUrls)) {
            // Handle both single string and array of URLs
            if (is_string($videoUrls)) {
                $videoUrls = [$videoUrls];
            }

            // Validate each URL
            foreach ($videoUrls as $url) {
                $url = trim($url);
                if (!empty($url) && $this->isValidYouTubeUrl($url)) {
                    $validUrls[] = $url;
                }
            }
        }

        // Backward compatibility: check for single youtube_video field
        $singleVideo = trim($this->request->getPost('youtube_video') ?: '');
        if (!empty($singleVideo) && $this->isValidYouTubeUrl($singleVideo)) {
            $validUrls[] = $singleVideo;
        }

        // Remove duplicates and return
        return array_unique($validUrls);
    }

    /**
     * Validate YouTube URL format
     *
     * Validates if the provided URL is a valid YouTube video URL.
     * Supports multiple YouTube URL formats:
     * - https://www.youtube.com/watch?v=VIDEO_ID
     * - https://youtube.com/watch?v=VIDEO_ID
     * - https://youtu.be/VIDEO_ID
     * - https://www.youtube.com/embed/VIDEO_ID
     *
     * @param string $url The URL to validate
     * @return bool True if valid YouTube URL, false otherwise
     */
    private function isValidYouTubeUrl($url)
    {
        // Regex pattern to match various YouTube URL formats
        $pattern = '/^(https?:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|embed\/)|youtu\.be\/)[\w\-]+/';
        return preg_match($pattern, $url);
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
