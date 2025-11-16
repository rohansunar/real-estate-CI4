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
 * @author Gold Properties Team
 * @version 2.0 - Enhanced with multiple media support
 * @since 2025-08-02
 */
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\ImageOptimizationService;

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

        // Get filter parameters
        $location = trim($this->request->getGet('location') ?? '');
        $type = trim($this->request->getGet('type') ?? '');
        $minArea = (int) ($this->request->getGet('min_area') ?? 0);
        $featured = $this->request->getGet('featured') ? 1 : 0;

        // Build query with filters
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

        // Apply featured filter
        if ($featured) {
            $builder->where('is_featured', 1);
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
        if ($featured) $searchParams['featured'] = 1;
        $searchQuery = !empty($searchParams) ? '&' . http_build_query($searchParams) : '';

        $data = [
            'title' => 'Properties | Gold Properties',
            'properties' => $properties,
            'currentPage' => $page,
            'totalPages' => ceil($totalProperties / $perPage),
            'perPage' => $perPage,
            'totalProperties' => $totalProperties,
            'hasNextPage' => $page < ceil($totalProperties / $perPage),
            'hasPrevPage' => $page > 1,
            'searchQuery' => $searchQuery,
            'searchParams' => $searchParams,
            'isSearchResults' => !empty($searchParams) // Show as search results if any filters are applied
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
            'title' => 'Search Results | Properties | Gold Properties',
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
            'title' => $property['title'] . ' | Gold Properties',
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
            'title' => 'Create Property | Gold Properties',
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

            // Prepare data for insertion
            $data = [
                'title' => trim($this->request->getPost('title')),
                'description' => trim($this->request->getPost('description')),
                'type' => $this->request->getPost('type'),
                'location' => trim($this->request->getPost('location')),
                'area' => $this->request->getPost('area') ?: null,
                'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
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
            // Use centralized error message service
            $errorService = new \App\Services\ErrorMessageService();
            $errorService->logTechnicalError($e, 'property_creation', [
                'user_id' => session()->get('user_id'),
                'property_title' => $this->request->getPost('title')
            ]);

            $userMessage = $errorService->getUserFriendlyMessage($e, 'property_creation');

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
     * Handle multiple image upload for properties with optimization
     *
     * Processes multiple image files uploaded via the property creation form with
     * comprehensive image optimization including compression, resizing, and format conversion.
     * Returns simple array of image paths for database storage while maintaining optimization features.
     *
     * Enhanced Features:
     * - Automatic image optimization (60-80% file size reduction)
     * - Multiple responsive sizes generation (thumbnail, medium, large)
     * - WebP format conversion with JPEG fallback
     * - Maintains aspect ratios and image quality
     * - Simple array format for easy frontend consumption
     * - Comprehensive error handling and user-friendly messages
     *
     * Security features:
     * - Validates file integrity with isValid()
     * - Prevents file overwrites with hasMoved() check
     * - Uses random filenames to prevent path traversal attacks
     * - Validates image formats and dimensions
     *
     * @return array Simple array of image paths for database storage
     * @throws \Exception If image processing fails
     */
    private function handleImageUpload()
    {
        $images = $this->request->getFiles();
        $processedImages = [];

        // Initialize image optimization service
        $imageOptimizer = new ImageOptimizationService();

        // Use unified storage location (public/uploads/properties)
        $uploadPath = FCPATH . 'uploads/properties';
        if (!is_dir($uploadPath)) {
            if (!mkdir($uploadPath, 0755, true)) {
                throw new \Exception('Failed to create upload directory. Please check server permissions.');
            }
        }

        // Process multiple images if uploaded
        if (isset($images['images'])) {
            foreach ($images['images'] as $index => $image) {
                // Validate each image before processing
                if ($image->isValid() && !$image->hasMoved()) {
                    try {
                        // Generate secure random filename (without extension)
                        $baseFilename = pathinfo($image->getRandomName(), PATHINFO_FILENAME);

                        // Process and optimize the image (creates multiple sizes and formats)
                        $optimizedImageData = $imageOptimizer->processImage(
                            $image,
                            $uploadPath,
                            $baseFilename
                        );

                        // Extract the best image path for simple storage
                        // Priority: medium JPEG > medium WebP > large JPEG > large WebP > thumbnail JPEG
                        $imagePath = $this->getBestImagePath($optimizedImageData, $baseFilename);

                        if ($imagePath) {
                            // Store simple image path for database
                            $processedImages[] = $imagePath;

                            // Log successful optimization
                            log_message('info', "Image optimized and stored: {$image->getClientName()} -> {$imagePath}");
                        } else {
                            throw new \Exception("No optimized image path found for {$image->getClientName()}");
                        }

                    } catch (\Exception $e) {
                        // Log detailed error for debugging
                        log_message('error', "Image optimization failed for {$image->getClientName()}: " . $e->getMessage());

                        // Provide user-friendly error message
                        throw new \Exception("Failed to process image '{$image->getClientName()}'. Please try with a different image or contact support if the issue persists.");
                    }
                }
            }
        }

        return $processedImages;
    }

    /**
     * Extract the best available image path from optimization data
     *
     * This method selects the best image format and size for storage while
     * maintaining the optimization benefits. Priority is given to medium size
     * JPEG images as they provide the best balance of quality and file size.
     *
     * @param array $optimizedImageData Image data from ImageOptimizationService
     * @param string $baseFilename Base filename for fallback
     * @return string|null Best available image path
     */
    private function getBestImagePath(array $optimizedImageData, string $baseFilename): ?string
    {
        // Priority order: medium > large > thumbnail
        $sizePreference = ['medium', 'large', 'thumbnail'];

        foreach ($sizePreference as $size) {
            if (isset($optimizedImageData[$size])) {
                $sizeData = $optimizedImageData[$size];

                // Prefer JPEG over WebP for broader compatibility
                if (!empty($sizeData['jpeg'])) {
                    return $sizeData['jpeg'];
                }
                if (!empty($sizeData['webp'])) {
                    return $sizeData['webp'];
                }
            }
        }

        // Fallback: look for any available image file
        $possibleExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        foreach ($possibleExtensions as $ext) {
            $fallbackPath = "uploads/properties/{$baseFilename}.{$ext}";
            if (file_exists(FCPATH . $fallbackPath)) {
                return $fallbackPath;
            }
        }

        return null;
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
     * Process property sale and commission distribution
     */
    public function processSale($propertyId)
    {
        // Check if user is authenticated (admin or agent)
        if (!session()->get('isLoggedIn') && !session()->get('agent_id')) {
            return redirect()->to('/login')->with('error', 'Please login to process property sales.');
        }

        $propertyId = (int) $propertyId;
        $property = $this->propertyModel->find($propertyId);

        if (!$property) {
            return redirect()->back()->with('error', 'Property not found.');
        }

        if ($property['status'] === 'sold') {
            return redirect()->back()->with('error', 'Property is already sold.');
        }

        // Handle POST request (form submission)
        if ($this->request->getMethod() === 'POST') {
            return $this->handleSaleSubmission($propertyId);
        }

        // Show sale form
        $data = [
            'title' => 'Process Property Sale | Gold Properties',
            'property' => $property,
            'agents' => $this->getActiveAgents()
        ];

        return view('admin/properties/process_sale', $data);
    }

    /**
     * Handle property sale form submission
     */
    private function handleSaleSubmission($propertyId)
    {
        try {
            // Validate form data
            $validationRules = [
                'selling_agent_id' => 'required|integer',
                'sale_amount' => 'required|decimal|greater_than[0]',
                'buyer_name' => 'required|max_length[255]',
                'buyer_contact' => 'required|max_length[20]',
                'sale_date' => 'required|valid_date'
            ];

            if (!$this->validate($validationRules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            // Get form data
            $sellingAgentId = (int) $this->request->getPost('selling_agent_id');
            $saleAmount = (float) $this->request->getPost('sale_amount');
            $buyerName = trim($this->request->getPost('buyer_name'));
            $buyerContact = trim($this->request->getPost('buyer_contact'));
            $saleDate = $this->request->getPost('sale_date');

            // Additional sale data
            $additionalData = [
                'buyer_name' => $buyerName,
                'buyer_contact' => $buyerContact,
                'sale_date' => $saleDate,
                'processed_by' => session()->get('user_id') ?? session()->get('agent_id'),
                'processed_by_type' => session()->get('isLoggedIn') ? 'admin' : 'agent'
            ];

            // Process the sale using PropertySaleService
            $saleService = new \App\Services\PropertySaleService();
            $result = $saleService->processSale($propertyId, $sellingAgentId, $saleAmount, $additionalData);

            if ($result['success']) {
                $message = 'Property sale processed successfully! Commission has been distributed to the agent hierarchy.';
                return redirect()->to('/admin/properties')->with('success', $message);
            } else {
                return redirect()->back()->withInput()->with('error', $result['message']);
            }

        } catch (\Exception $e) {
            log_message('error', 'Property sale submission failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to process property sale. Please try again.');
        }
    }

    /**
     * Get active agents for sale assignment
     */
    private function getActiveAgents()
    {
        $agentModel = new \App\Models\AgentModel();
        return $agentModel->where('is_active', true)
                         ->orderBy('name', 'ASC')
                         ->findAll();
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
