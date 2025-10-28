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
 * @author White Rock Realtor Team
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
                    'required' => 'Please enter the agent\'s full name to continue.',
                    'max_length' => 'Agent name must be 255 characters or less. Please use a shorter name.'
                ],
                'email' => [
                    'required' => 'Email address is required for agent login and notifications.',
                    'valid_email' => 'Please enter a valid email address (e.g., john.doe@example.com).',
                    'is_unique' => 'This email address is already registered to another agent. Please use a different email.'
                ],
                'phone' => [
                    'required' => 'Phone number is required for agent contact information.',
                    'max_length' => 'Phone number must be 20 characters or less. Please use a shorter format.'
                ],
                'profile_image' => [
                    'is_image' => 'Please upload a valid image file (JPG, PNG, GIF). Other file types are not supported.',
                    'max_size' => 'Profile image must be smaller than 2MB. Please resize or compress your image.'
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
            // Use centralized error message service
            $errorService = new \App\Services\ErrorMessageService();
            $errorService->logTechnicalError($e, 'agent_management', [
                'user_id' => session()->get('user_id'),
                'agent_name' => $this->request->getPost('name')
            ]);

            $userMessage = $errorService->getUserFriendlyMessage($e, 'agent_management');
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
            'title' => 'Edit Associate | Dashboard',
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
                    'required' => 'Please enter the agent\'s full name to continue.',
                    'max_length' => 'Agent name must be 255 characters or less. Please use a shorter name.'
                ],
                'email' => [
                    'required' => 'Email address is required for agent login and notifications.',
                    'valid_email' => 'Please enter a valid email address (e.g., john.doe@example.com).',
                    'is_unique' => 'This email address is already registered to another agent. Please use a different email.'
                ],
                'phone' => [
                    'required' => 'Phone number is required for agent contact information.',
                    'max_length' => 'Phone number must be 20 characters or less. Please use a shorter format.'
                ],
                'profile_image' => [
                    'is_image' => 'Please upload a valid image file (JPG, PNG, GIF). Other file types are not supported.',
                    'max_size' => 'Profile image must be smaller than 2MB. Please resize or compress your image.'
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
                'profile_image' => $profileImagePath,
                'parent_agent_id' => $this->request->getPost('parent_agent_id') ?: null
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
            // Use centralized error message service
            $errorService = new \App\Services\ErrorMessageService();
            $errorService->logTechnicalError($e, 'agent_management', [
                'user_id' => session()->get('user_id'),
                'agent_id' => $id,
                'agent_name' => $this->request->getPost('name')
            ]);

            $userMessage = $errorService->getUserFriendlyMessage($e, 'agent_management');
            return redirect()->back()->withInput()->with('error', $userMessage);
        }
    }

    /**
      * Delete agent
      *
      * Handles agent deletion with proper validation and cascade delete for associated images.
      * Implements downline protection to prevent deletion of agents with sub-agents.
      * Uses the centralized ImageManagementService for consistent file handling.
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

        // Check if agent has downline agents (sub-agents)
        $downlineCount = $this->agentModel->countTotalDownline($id);

        if ($downlineCount > 0) {
            // Prevent deletion if agent has downline
            $agentName = esc($agent['name']);
            $message = "Cannot delete agent '{$agentName}' because they have {$downlineCount} agent(s) in their downline. ";
            $message .= "Please reassign or remove all downline agents first before deleting this agent.";

            return $this->response->setJSON([
                'success' => false,
                'message' => $message,
                'downline_count' => $downlineCount
            ]);
        }

        // Proceed with deletion if no downline exists
        if ($this->agentModel->delete($id)) {
            // Delete profile image using centralized service
            if (!empty($agent['profile_image'])) {
                $imageService = new \App\Services\ImageManagementService();
                $deletionResult = $imageService->deleteImage($agent['profile_image']);

                if (!$deletionResult['success']) {
                    // Log the error but don't fail the agent deletion
                    log_message('warning', "Failed to delete agent profile image: {$agent['profile_image']}. Error: {$deletionResult['message']}");
                }
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
            // Use centralized image management service for consistent handling
            $imageService = new \App\Services\ImageManagementService();
            $uploadResult = $imageService->uploadImage($image, 'agent');

            if (!$uploadResult['success']) {
                throw new \Exception($uploadResult['message']);
            }

            // Return relative path for database storage
            return $uploadResult['file_path'];

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
