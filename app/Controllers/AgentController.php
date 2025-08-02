<?php

namespace App\Controllers;

use App\Models\AgentModel;
use CodeIgniter\HTTP\ResponseInterface;

class AgentController extends BaseController
{
    protected $agentModel;

    public function __construct()
    {
        $this->agentModel = new AgentModel();
    }

    /**
     * Display agents listing with pagination
     */
    public function index()
    {
        $perPage = 15; // Number of agents per page
        $page = (int) ($this->request->getGet('page') ?? 1);
        $offset = ($page - 1) * $perPage;

        $totalAgents = $this->agentModel->countAll();
        $agents = $this->agentModel->orderBy('created_at', 'DESC')
                                 ->limit($perPage, $offset)
                                 ->findAll();
        $statistics = $this->agentModel->getStatistics();

        $data = [
            'title' => 'Agents Management | Dashboard',
            'agents' => $agents,
            'statistics' => $statistics,
            'currentPage' => $page,
            'totalPages' => ceil($totalAgents / $perPage),
            'perPage' => $perPage,
            'totalAgents' => $totalAgents
        ];

        return view('dashboard/agents/index', $data);
    }

    /**
     * Show create agent form
     */
    public function create()
    {
        $data = [
            'title' => 'Create Agent | Dashboard'
        ];

        return view('dashboard/agents/create', $data);
    }

    /**
     * Store new agent
     */
    public function store()
    {
        try {
            // Define validation rules
            $validationRules = [
                'name' => 'required|max_length[255]',
                'email' => 'required|valid_email|is_unique[agents.email]',
                'phone' => 'required|max_length[20]',
                'address' => 'permit_empty|max_length[1000]',
                'qualification' => 'permit_empty|max_length[255]',
                'profile_image' => 'permit_empty|is_image[profile_image]|max_size[profile_image,2048]'
            ];

            $validationMessages = [
                'name' => [
                    'required' => 'Agent name is required.',
                    'max_length' => 'Agent name cannot exceed 255 characters.'
                ],
                'email' => [
                    'required' => 'Email address is required.',
                    'valid_email' => 'Please enter a valid email address.',
                    'is_unique' => 'This email address is already registered.'
                ],
                'phone' => [
                    'required' => 'Phone number is required.',
                    'max_length' => 'Phone number cannot exceed 20 characters.'
                ],
                'profile_image' => [
                    'is_image' => 'Please upload a valid image file.',
                    'max_size' => 'Profile image must be less than 2MB.'
                ]
            ];

            // Validate the request data
            if (!$this->validate($validationRules, $validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            // Handle profile image upload
            $profileImagePath = $this->handleProfileImageUpload();

            // Prepare data for insertion
            $data = [
                'name' => trim($this->request->getPost('name')),
                'email' => trim($this->request->getPost('email')),
                'phone' => trim($this->request->getPost('phone')),
                'address' => trim($this->request->getPost('address')) ?: null,
                'qualification' => trim($this->request->getPost('qualification')) ?: null,
                'profile_image' => $profileImagePath,
                'is_active' => true
            ];

            // Insert agent into database
            if ($this->agentModel->insert($data)) {
                // Send welcome email to the agent
                $this->sendWelcomeEmail($data);

                return redirect()->to('/dashboard/agents')->with('success', 'Agent created successfully! A welcome email has been sent.');
            } else {
                throw new \Exception('Database insertion failed');
            }

        } catch (\Exception $e) {
            // Log detailed error for debugging
            log_message('error', 'Agent creation failed: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine());

            // User-friendly error message
            $userMessage = 'Failed to create agent. Please check your information and try again.';

            if (strpos($e->getMessage(), 'upload') !== false) {
                $userMessage = 'There was an issue uploading the profile image. Please try again with a smaller image file.';
            } elseif (strpos($e->getMessage(), 'database') !== false) {
                $userMessage = 'We are experiencing technical difficulties. Please try again in a few minutes.';
            }

            return redirect()->back()->withInput()->with('error', $userMessage);
        }
    }

    /**
     * Show edit agent form
     */
    public function edit($id)
    {
        $agent = $this->agentModel->find($id);

        if (!$agent) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => 'Edit Agent | Dashboard',
            'agent' => $agent
        ];

        return view('dashboard/agents/edit', $data);
    }

    /**
     * Update agent
     */
    public function update($id)
    {
        $agent = $this->agentModel->find($id);

        if (!$agent) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        try {
            // Define validation rules with custom messages (email uniqueness excludes current record)
            $validationRules = [
                'name' => 'required|max_length[255]',
                'email' => "required|valid_email|is_unique[agents.email,id,{$id}]",
                'phone' => 'required|max_length[20]',
                'address' => 'permit_empty|max_length[1000]',
                'qualification' => 'permit_empty|max_length[255]',
                'profile_image' => 'permit_empty|is_image[profile_image]|max_size[profile_image,2048]'
            ];

            $validationMessages = [
                'name' => [
                    'required' => 'Agent name is required.',
                    'max_length' => 'Agent name cannot exceed 255 characters.'
                ],
                'email' => [
                    'required' => 'Email address is required.',
                    'valid_email' => 'Please enter a valid email address.',
                    'is_unique' => 'This email address is already registered.'
                ],
                'phone' => [
                    'required' => 'Phone number is required.',
                    'max_length' => 'Phone number cannot exceed 20 characters.'
                ],
                'profile_image' => [
                    'is_image' => 'Please upload a valid image file.',
                    'max_size' => 'Profile image must be less than 2MB.'
                ]
            ];

            // Validate the request data
            if (!$this->validate($validationRules, $validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            // Handle new profile image upload if any
            $profileImagePath = $this->handleProfileImageUpload();
            if (!$profileImagePath) {
                $profileImagePath = $agent['profile_image']; // Keep existing image
            }

            // Prepare data for update
            $data = [
                'name' => trim($this->request->getPost('name')),
                'email' => trim($this->request->getPost('email')),
                'phone' => trim($this->request->getPost('phone')),
                'address' => trim($this->request->getPost('address')) ?: null,
                'qualification' => trim($this->request->getPost('qualification')) ?: null,
                'profile_image' => $profileImagePath
            ];

            // Attempt to update the agent
            if ($this->agentModel->update($id, $data)) {
                return redirect()->to('/dashboard/agents')->with('success', 'Agent updated successfully');
            } else {
                // Get model errors if available
                $modelErrors = $this->agentModel->errors();
                if (!empty($modelErrors)) {
                    log_message('error', 'Agent update validation failed: ' . json_encode($modelErrors));
                    return redirect()->back()->withInput()->with('errors', $modelErrors);
                }
                throw new \Exception('Database update failed - no specific error returned');
            }

        } catch (\Exception $e) {
            // Log detailed error for debugging
            log_message('error', 'Agent update failed: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine());

            // User-friendly error message based on error type
            $userMessage = 'Failed to update agent. Please try again.';

            if (strpos($e->getMessage(), 'upload') !== false) {
                $userMessage = 'There was an issue uploading the profile image. Please try again with a smaller image file.';
            } elseif (strpos($e->getMessage(), 'database') !== false || strpos($e->getMessage(), 'Database') !== false) {
                $userMessage = 'We are experiencing technical difficulties. Please try again in a few minutes.';
            } elseif (strpos($e->getMessage(), 'validation') !== false) {
                $userMessage = 'Please check your information and try again.';
            }

            return redirect()->back()->withInput()->with('error', $userMessage);
        }
    }

    /**
     * Delete agent
     */
    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/dashboard/agents');
        }

        $agent = $this->agentModel->find($id);

        if (!$agent) {
            return $this->response->setJSON(['success' => false, 'message' => 'Agent not found']);
        }

        if ($this->agentModel->delete($id)) {
            // Delete profile image file if exists
            if ($agent['profile_image'] && file_exists(WRITEPATH . $agent['profile_image'])) {
                unlink(WRITEPATH . $agent['profile_image']);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Agent deleted successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete agent']);
        }
    }



    /**
     * Handle profile image upload with comprehensive error handling
     *
     * This method processes agent profile image uploads with robust error handling
     * and security measures. It creates the upload directory if it doesn't exist,
     * validates the uploaded file, and moves it to a secure location.
     *
     * Security Features:
     * - Validates file integrity and type
     * - Uses random filenames to prevent conflicts and path traversal
     * - Creates upload directory with appropriate permissions
     * - Comprehensive error logging for debugging
     *
     * @return string|null Returns the relative path to the uploaded image or null if no image
     * @throws \Exception If upload fails for any reason
     */
    private function handleProfileImageUpload()
    {
        $image = $this->request->getFile('profile_image');

        // Return null if no image was uploaded (this is valid for updates)
        if (!$image || $image->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        // Check if image is valid and hasn't been moved
        if (!$image->isValid()) {
            throw new \Exception('Invalid image file uploaded: ' . $image->getErrorString());
        }

        if ($image->hasMoved()) {
            throw new \Exception('Image file has already been processed');
        }

        try {
            // Ensure upload directory exists with proper permissions
            $uploadPath = WRITEPATH . 'uploads/agents';
            if (!is_dir($uploadPath)) {
                if (!mkdir($uploadPath, 0755, true)) {
                    throw new \Exception('Failed to create upload directory');
                }
            }

            // Generate secure random filename to prevent conflicts and security issues
            $newName = $image->getRandomName();

            // Move the uploaded file to secure location
            if (!$image->move($uploadPath, $newName)) {
                throw new \Exception('Failed to move uploaded image file');
            }

            // Return relative path for database storage
            return 'uploads/agents/' . $newName;

        } catch (\Exception $e) {
            // Log detailed error information for debugging
            log_message('error', 'Profile image upload failed: ' . $e->getMessage());
            throw new \Exception('Image upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Send welcome email to new agent
     */
    private function sendWelcomeEmail($agentData)
    {
        $emailService = new \App\Services\EmailService();
        $emailService->sendAgentWelcomeEmail($agentData);
    }
}
