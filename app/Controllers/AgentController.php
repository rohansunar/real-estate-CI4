<?php

namespace App\Controllers;

use App\Models\AgentModel;

/**
 * AgentController
 *
 * Manages agent-related operations in the dashboard.
 * This controller handles:
 * - Agent listing with pagination
 * - Agent creation with profile image upload
 * - Agent editing with proper email validation
 * - Agent deletion with confirmation
 * - Profile image management
 *
 * Key Features:
 * - Fixed email validation for updates (excludes current agent)
 * - Secure file upload handling
 * - Comprehensive error handling and logging
 * - User-friendly success/error messages
 *
 * @author Real Estate Team
 * @version 2.0 - Fixed email validation and enhanced error handling
 * @since 2025-08-02
 */
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
            // Get validation rules from model
            $validationRules = $this->agentModel->getCreateValidationRules();
            $validationRules['profile_image'] = 'permit_empty|is_image[profile_image]|max_size[profile_image,2048]';

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

            // Generate automatic login credentials
            $generatedPassword = $this->generateSecurePassword();

            // Prepare data for insertion
            $data = [
                'name' => trim($this->request->getPost('name')),
                'email' => trim($this->request->getPost('email')),
                'password' => $generatedPassword,
                'phone' => trim($this->request->getPost('phone')),
                'address' => trim($this->request->getPost('address')) ?: null,
                'qualification' => trim($this->request->getPost('qualification')) ?: null,
                'profile_image' => $profileImagePath,
                'parent_agent_id' => $this->request->getPost('parent_agent_id') ?: null,
                'is_active' => true
            ];

            // Store the plain password for email
            $data['plain_password'] = $generatedPassword;

            // Insert agent into database (skip model validation since we already validated)
            $this->agentModel->skipValidation(true);
            $agentId = $this->agentModel->insert($data);

            if ($agentId) {
                // Get the created agent with generated unique_agent_id
                $createdAgent = $this->agentModel->find($agentId);
                $data['unique_agent_id'] = $createdAgent['unique_agent_id'];

                // Send welcome email to the agent
                $this->sendWelcomeEmail($data);

                $successMessage = 'Agent created successfully! ';
                $successMessage .= 'Unique ID: ' . $createdAgent['unique_agent_id'] . ' | ';
                $successMessage .= 'Login credentials have been sent via email.';

                return redirect()->to('/dashboard/agents')->with('success', $successMessage);
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
            // Get validation rules from model (excludes current agent from email uniqueness check)
            $validationRules = $this->agentModel->getUpdateValidationRules($id);
            $validationRules['profile_image'] = 'permit_empty|is_image[profile_image]|max_size[profile_image,2048]';

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

            // Attempt to update the agent (skip model validation since we already validated)
            $this->agentModel->skipValidation(true);
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
     * Generate a secure password for new agents
     */
    private function generateSecurePassword(): string
    {
        $length = 12;
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $password;
    }

    /**
     * Send welcome email to new agent with login credentials
     */
    private function sendWelcomeEmail($agentData)
    {
        // Enhanced email service with agent credentials
        try {
            $emailService = new \App\Services\EmailService();
            $emailService->sendAgentWelcomeEmail($agentData);
        } catch (\Exception $e) {
            // Log email sending failure but don't stop agent creation
            log_message('error', 'Failed to send welcome email to agent: ' . $e->getMessage());

            // Log credentials for manual delivery if needed
            log_message('info', 'Agent credentials for manual delivery:');
            log_message('info', 'Email: ' . $agentData['email']);
            log_message('info', 'Password: ' . ($agentData['plain_password'] ?? 'N/A'));
            log_message('info', 'Unique ID: ' . ($agentData['unique_agent_id'] ?? 'Auto-generated'));
        }
    }
}
