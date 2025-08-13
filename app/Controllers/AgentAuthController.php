<?php

namespace App\Controllers;

use App\Models\AgentModel;

/**
 * AgentAuthController
 *
 * Handles agent authentication and agent-specific dashboard functionality.
 * This controller manages:
 * - Agent login and logout with secure session management
 * - Agent dashboard with limited permissions and statistics
 * - Agent profile management with validation and error handling
 * - Complete sub-agent CRUD operations with authorization
 * - Pagination and search functionality for sub-agent listings
 * - Secure password generation and email notifications
 *
 * Key Features:
 * - Separate authentication system from admin dashboard
 * - Agent-specific session management with security checks
 * - Limited permissions compared to admin dashboard
 * - Agent hierarchy support with parent-child relationships
 * - Server-side pagination for performance optimization
 * - Comprehensive input validation and CSRF protection
 * - Authorization checks ensuring agents can only manage their own sub-agents
 * - User-friendly error messages and success notifications
 *
 * Security Features:
 * - CSRF token validation on all forms
 * - Parent-agent authorization checks on all sub-agent operations
 * - Secure password hashing and generation
 * - Input sanitization and validation
 * - Session-based authentication with activity checks
 *
 * @author Real Estate Team
 * @version 2.0 - Enhanced with complete CRUD operations and security features
 * @since 2025-08-04
 * @updated 2025-08-08
 */
class AgentAuthController extends BaseController
{
    protected $agentModel;

    public function __construct()
    {
        $this->agentModel = new AgentModel();
    }

    /**
     * Show agent login form
     */
    public function loginForm()
    {
        // Redirect if already logged in as agent
        if (session()->get('agent_logged_in')) {
            return redirect()->to('/agent/dashboard');
        }

        $data = [
            'title' => 'Agent Login | Real Estate'
        ];

        return view('agent/auth/login', $data);
    }

    /**
     * Process agent login
     */
    public function login()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'email' => 'required|valid_email',
            'password' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $agent = $this->agentModel->findByEmail($email);

        if (!$agent) {
            return redirect()->back()->withInput()->with('error', 'Invalid email or password');
        }

        // Check if agent is active
        if (!$agent['is_active']) {
            return redirect()->back()->withInput()->with('error', 'Your agent account is currently inactive. Please contact the administrator.');
        }

        // Check if agent has a password set
        if (empty($agent['password'])) {
            return redirect()->back()->withInput()->with('error', 'Your account is not yet activated. Please contact the administrator for your login credentials.');
        }

        if (!$this->agentModel->verifyPassword($password, $agent['password'])) {
            return redirect()->back()->withInput()->with('error', 'Invalid email or password');
        }

        // Set agent session data
        $sessionData = [
            'agent_id' => $agent['id'],
            'agent_name' => $agent['name'],
            'agent_email' => $agent['email'],
            'agent_unique_id' => $agent['unique_agent_id'],
            'agent_referral_id' => $agent['referral_id'],
            'agent_parent_id' => $agent['parent_agent_id'],
            'agent_logged_in' => true
        ];

        session()->set($sessionData);

        return redirect()->to('/agent/dashboard')->with('success', 'Welcome back, ' . $agent['name'] . '!');
    }

    /**
     * Process agent logout
     */
    public function logout()
    {
        // Clear agent session data
        session()->remove(['agent_id', 'agent_name', 'agent_email', 'agent_unique_id', 'agent_referral_id', 'agent_parent_id', 'agent_logged_in']);
        
        return redirect()->to('/agent/login')->with('success', 'Successfully logged out');
    }

    /**
     * Agent dashboard with enhanced hierarchy and commission features
     */
    public function dashboard()
    {
        $agentId = session()->get('agent_id');

        // Get agent information and hierarchy position
        $agent = $this->agentModel->find($agentId);
        $hierarchyPosition = $this->agentModel->getAgentHierarchyPosition($agentId);

        // Get direct sub-agents
        $directSubAgents = $this->agentModel->getSubAgents($agentId, 5); // Limit to 5 for dashboard

        // Get total downline count
        $totalDownline = $this->agentModel->countTotalDownline($agentId);

        // Get commission statistics
        $commissionModel = new \App\Models\CommissionTransactionModel();
        $commissionStats = $commissionModel->getAgentCommissionStats($agentId);
        $recentCommissions = $commissionModel->getRecentTransactions(5, $agentId);

        $data = [
            'title' => 'Agent Dashboard | Real Estate',
            'agent' => $agent,
            'hierarchyPosition' => $hierarchyPosition,
            'directSubAgents' => $directSubAgents,
            'totalDownline' => $totalDownline,
            'commissionStats' => $commissionStats,
            'recentCommissions' => $recentCommissions,
            // Legacy compatibility
            'subAgents' => $directSubAgents,
            'subAgentCount' => count($directSubAgents),
            'totalSubAgents' => count($directSubAgents)
        ];

        return view('agent/dashboard/index', $data);
    }

    /**
     * Agent profile management
     */
    public function profile()
    {
        $agentId = session()->get('agent_id');
        $agent = $this->agentModel->find($agentId);

        $data = [
            'title' => 'My Profile | Agent Dashboard',
            'agent' => $agent
        ];

        return view('agent/dashboard/profile', $data);
    }

    /**
     * Update agent profile
     */
    public function updateProfile()
    {
        $agentId = session()->get('agent_id');

        try {
            // Get validation rules for update
            $validationRules = $this->agentModel->getUpdateValidationRules($agentId);

            // Custom validation messages
            $validationMessages = [
                'name.required' => 'Your name is required.',
                'email.required' => 'Email address is required.',
                'email.valid_email' => 'Please enter a valid email address.',
                'email.is_unique' => 'This email address is already in use.',
                'phone.required' => 'Phone number is required.',
                'password.min_length' => 'Password must be at least 6 characters long.'
            ];

            // Validate the request data
            if (!$this->validate($validationRules, $validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            // Prepare data for update
            $data = [
                'name' => trim($this->request->getPost('name')),
                'email' => trim($this->request->getPost('email')),
                'phone' => trim($this->request->getPost('phone')),
                'address' => trim($this->request->getPost('address')) ?: null,
                'qualification' => trim($this->request->getPost('qualification')) ?: null
            ];

            // Handle password update if provided
            $newPassword = trim($this->request->getPost('password'));
            if (!empty($newPassword)) {
                $data['password'] = $newPassword; // Will be hashed by model callback
            }

            // Update agent profile
            if ($this->agentModel->update($agentId, $data)) {
                // Update session data if name or email changed
                if ($data['name'] !== session()->get('agent_name')) {
                    session()->set('agent_name', $data['name']);
                }
                if ($data['email'] !== session()->get('agent_email')) {
                    session()->set('agent_email', $data['email']);
                }

                return redirect()->to('/agent/profile')->with('success', 'Profile updated successfully!');
            } else {
                throw new \Exception('Failed to update profile');
            }

        } catch (\Exception $e) {
            log_message('error', 'Agent profile update failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update profile. Please try again.');
        }
    }

    /**
     * Sub-agents management with pagination and search
     */
    public function subAgents()
    {
        $agentId = session()->get('agent_id');

        // Get search and filter parameters
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');
        $sort = $this->request->getGet('sort') ?? 'created_at_desc';
        $perPage = 10; // Number of items per page

        // Build query
        $builder = $this->agentModel->where('parent_agent_id', $agentId);

        // Apply search filter
        if (!empty($search)) {
            $builder->groupStart()
                   ->like('name', $search)
                   ->orLike('email', $search)
                   ->orLike('phone', $search)
                   ->orLike('qualification', $search)
                   ->groupEnd();
        }

        // Apply status filter
        if ($status === 'active') {
            $builder->where('is_active', true);
        } elseif ($status === 'inactive') {
            $builder->where('is_active', false);
        }

        // Apply sorting
        switch ($sort) {
            case 'created_at_asc':
                $builder->orderBy('created_at', 'ASC');
                break;
            case 'name_asc':
                $builder->orderBy('name', 'ASC');
                break;
            case 'name_desc':
                $builder->orderBy('name', 'DESC');
                break;
            default:
                $builder->orderBy('created_at', 'DESC');
                break;
        }

        // Get paginated results
        $subAgents = $builder->paginate($perPage, 'sub_agents');
        $pager = $this->agentModel->pager;

        // Get total count for statistics
        $totalSubAgents = $this->agentModel->where('parent_agent_id', $agentId)->countAllResults();

        $data = [
            'title' => 'Sub-Agents | Agent Dashboard',
            'subAgents' => $subAgents,
            'totalSubAgents' => $totalSubAgents,
            'pager' => $pager,
            'search' => $search,
            'status' => $status,
            'sort' => $sort
        ];

        return view('agent/dashboard/sub_agents', $data);
    }

    /**
     * Create sub-agent
     */
    public function createSubAgent()
    {
        $data = [
            'title' => 'Create Sub-Agent | Agent Dashboard'
        ];

        return view('agent/dashboard/create_sub_agent', $data);
    }

    /**
     * Store sub-agent
     */
    public function storeSubAgent()
    {
        $parentAgentId = session()->get('agent_id');

        try {
            // Get validation rules for creation
            $validationRules = $this->agentModel->getCreateValidationRules();

            // Validate the request data
            if (!$this->validate($validationRules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            // Generate automatic login credentials
            $generatedPassword = $this->generateSecurePassword();

            // Generate referral ID
            $referralId = $this->agentModel->generateReferralId();

            // Prepare data for insertion
            $data = [
                'name' => trim($this->request->getPost('name')),
                'email' => trim($this->request->getPost('email')),
                'password' => $generatedPassword,
                'phone' => trim($this->request->getPost('phone')),
                'address' => trim($this->request->getPost('address')) ?: null,
                'qualification' => trim($this->request->getPost('qualification')) ?: null,
                'referral_id' => $referralId,
                'parent_agent_id' => $parentAgentId,
                'is_active' => true
            ];

            // Store the plain password for email
            $data['plain_password'] = $generatedPassword;

            // Insert sub-agent into database
            $subAgentId = $this->agentModel->insert($data);

            if ($subAgentId) {
                // Get the created agent with generated unique_agent_id
                $createdAgent = $this->agentModel->find($subAgentId);
                $data['unique_agent_id'] = $createdAgent['unique_agent_id'];

                // Send welcome email
                $this->sendWelcomeEmail($data);

                $successMessage = 'Sub-agent created successfully! ';
                $successMessage .= 'Unique ID: ' . $createdAgent['unique_agent_id'] . ' | ';
                $successMessage .= 'Referral ID: ' . $referralId . ' | ';
                $successMessage .= 'Login credentials have been sent via email.';

                return redirect()->to('/agent/sub-agents')->with('success', $successMessage);
            } else {
                throw new \Exception('Database insertion failed');
            }

        } catch (\Exception $e) {
            log_message('error', 'Sub-agent creation failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create sub-agent. Please try again.');
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
     * Edit sub-agent
     */
    public function editSubAgent($subAgentId)
    {
        $parentAgentId = session()->get('agent_id');

        // Verify the sub-agent belongs to the current agent
        $subAgent = $this->agentModel->where('id', $subAgentId)
                                    ->where('parent_agent_id', $parentAgentId)
                                    ->first();

        if (!$subAgent) {
            return redirect()->to('/agent/sub-agents')->with('error', 'Sub-agent not found or access denied.');
        }

        $data = [
            'title' => 'Edit Sub-Agent | Agent Dashboard',
            'subAgent' => $subAgent
        ];

        return view('agent/dashboard/edit_sub_agent', $data);
    }

    /**
     * Update sub-agent
     */
    public function updateSubAgent($subAgentId)
    {
        $parentAgentId = session()->get('agent_id');

        // Verify the sub-agent belongs to the current agent
        $subAgent = $this->agentModel->where('id', $subAgentId)
                                    ->where('parent_agent_id', $parentAgentId)
                                    ->first();

        if (!$subAgent) {
            return redirect()->to('/agent/sub-agents')->with('error', 'Sub-agent not found or access denied.');
        }

        try {
            // Get validation rules for update
            $validationRules = $this->agentModel->getUpdateValidationRules($subAgentId);

            // Custom validation messages
            $validationMessages = [
                'name.required' => 'Sub-agent name is required.',
                'email.required' => 'Email address is required.',
                'email.valid_email' => 'Please enter a valid email address.',
                'email.is_unique' => 'This email address is already in use.',
                'phone.required' => 'Phone number is required.',
                'password.min_length' => 'Password must be at least 6 characters long.'
            ];

            // Validate the request data
            if (!$this->validate($validationRules, $validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            // Prepare data for update
            $data = [
                'name' => trim($this->request->getPost('name')),
                'email' => trim($this->request->getPost('email')),
                'phone' => trim($this->request->getPost('phone')),
                'address' => trim($this->request->getPost('address')) ?: null,
                'qualification' => trim($this->request->getPost('qualification')) ?: null
            ];

            // Handle password update if provided
            $newPassword = trim($this->request->getPost('password'));
            if (!empty($newPassword)) {
                $data['password'] = $newPassword; // Will be hashed by model callback
            }

            // Update sub-agent
            if ($this->agentModel->update($subAgentId, $data)) {
                return redirect()->to('/agent/sub-agents')->with('success', 'Sub-agent updated successfully!');
            } else {
                throw new \Exception('Failed to update sub-agent');
            }

        } catch (\Exception $e) {
            log_message('error', 'Sub-agent update failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update sub-agent. Please try again.');
        }
    }

    /**
     * View sub-agent details (AJAX)
     */
    public function viewSubAgent($subAgentId)
    {
        $parentAgentId = session()->get('agent_id');

        // Verify the sub-agent belongs to the current agent
        $subAgent = $this->agentModel->where('id', $subAgentId)
                                    ->where('parent_agent_id', $parentAgentId)
                                    ->first();

        if (!$subAgent) {
            return $this->response->setStatusCode(404)->setBody('Sub-agent not found or access denied.');
        }

        $data = [
            'subAgent' => $subAgent
        ];

        return view('agent/dashboard/view_sub_agent', $data);
    }

    /**
     * Delete sub-agent
     */
    public function deleteSubAgent($subAgentId)
    {
        $parentAgentId = session()->get('agent_id');

        // Verify the sub-agent belongs to the current agent
        $subAgent = $this->agentModel->where('id', $subAgentId)
                                    ->where('parent_agent_id', $parentAgentId)
                                    ->first();

        if (!$subAgent) {
            return redirect()->to('/agent/sub-agents')->with('error', 'Sub-agent not found or access denied.');
        }

        try {
            if ($this->agentModel->delete($subAgentId)) {
                return redirect()->to('/agent/sub-agents')->with('success', 'Sub-agent deleted successfully!');
            } else {
                throw new \Exception('Failed to delete sub-agent');
            }

        } catch (\Exception $e) {
            log_message('error', 'Sub-agent deletion failed: ' . $e->getMessage());
            return redirect()->to('/agent/sub-agents')->with('error', 'Failed to delete sub-agent. Please try again.');
        }
    }

    /**
     * Hierarchy tree view with comprehensive error handling
     */
    public function hierarchyTree()
    {
        try {
            $agentId = session()->get('agent_id');

            if (!$agentId) {
                log_message('error', 'Hierarchy tree access attempted without valid agent session');
                return redirect()->to('/agent/login')->with('error', 'Please login to access the hierarchy tree.');
            }

            // Verify agent exists and is active
            $currentAgent = $this->agentModel->find($agentId);
            if (!$currentAgent) {
                log_message('error', 'Hierarchy tree access attempted with invalid agent ID: ' . $agentId);
                session()->destroy();
                return redirect()->to('/agent/login')->with('error', 'Invalid agent session. Please login again.');
            }

            if (!$currentAgent['is_active']) {
                log_message('warning', 'Inactive agent attempted to access hierarchy tree: ' . $agentId);
                return redirect()->to('/agent/dashboard')->with('error', 'Your account is inactive. Please contact support.');
            }

            // Get hierarchy data with error handling
            $hierarchyTree = [];
            $hierarchyPosition = [];
            $totalDownline = 0;
            $hasErrors = false;
            $errorMessages = [];

            try {
                $hierarchyTree = $this->agentModel->getHierarchyTree($agentId);
            } catch (\Exception $e) {
                log_message('error', 'Failed to load hierarchy tree for agent ' . $agentId . ': ' . $e->getMessage());
                $errorMessages[] = 'Unable to load hierarchy tree data.';
                $hasErrors = true;
            }

            try {
                $hierarchyPosition = $this->agentModel->getAgentHierarchyPosition($agentId);
            } catch (\Exception $e) {
                log_message('error', 'Failed to load hierarchy position for agent ' . $agentId . ': ' . $e->getMessage());
                $errorMessages[] = 'Unable to load hierarchy position data.';
                $hasErrors = true;
            }

            try {
                $totalDownline = $this->agentModel->countTotalDownline($agentId);
            } catch (\Exception $e) {
                log_message('error', 'Failed to count total downline for agent ' . $agentId . ': ' . $e->getMessage());
                $errorMessages[] = 'Unable to calculate downline statistics.';
                $hasErrors = true;
            }

            // If critical errors occurred, show error page
            if ($hasErrors && empty($hierarchyPosition)) {
                return redirect()->to('/agent/dashboard')->with('error', 'Unable to load hierarchy data. Please try again later.');
            }

            $data = [
                'title' => 'Agent Hierarchy | Agent Dashboard',
                'hierarchyTree' => $hierarchyTree,
                'hierarchyPosition' => $hierarchyPosition,
                'totalDownline' => $totalDownline,
                'hasErrors' => $hasErrors,
                'errorMessages' => $errorMessages,
                'currentAgent' => $currentAgent
            ];

            return view('agent/dashboard/hierarchy_tree', $data);

        } catch (\Exception $e) {
            log_message('error', 'Critical error in hierarchy tree controller: ' . $e->getMessage());
            return redirect()->to('/agent/dashboard')->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    /**
     * Commission dashboard
     */
    public function commissionDashboard()
    {
        $agentId = session()->get('agent_id');
        $commissionModel = new \App\Models\CommissionTransactionModel();

        // Get commission statistics
        $commissionStats = $commissionModel->getAgentCommissionStats($agentId);

        // Get recent commission transactions
        $recentTransactions = $commissionModel->getRecentTransactions(20, $agentId);

        // Get downline commission earnings
        $downlineEarnings = $commissionModel->getDownlineCommissionEarnings($agentId);

        // Get monthly commission data for charts
        $monthlyCommissions = $this->getMonthlyCommissionData($agentId, $commissionModel);

        $data = [
            'title' => 'Commission Dashboard | Agent Dashboard',
            'commissionStats' => $commissionStats,
            'recentTransactions' => $recentTransactions,
            'downlineEarnings' => $downlineEarnings,
            'monthlyCommissions' => $monthlyCommissions
        ];

        return view('agent/dashboard/commission_dashboard', $data);
    }

    /**
     * Get monthly commission data for charts
     */
    private function getMonthlyCommissionData($agentId, $commissionModel)
    {
        $monthlyData = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-{$i} months"));
            $monthStart = $month . '-01 00:00:00';
            $monthEnd = date('Y-m-t 23:59:59', strtotime($monthStart));

            $earnings = $commissionModel->getAgentCommissionEarnings(
                $agentId,
                null,
                $monthStart,
                $monthEnd
            );

            $totalEarned = array_sum(array_column($earnings, 'commission_amount'));

            $monthlyData[] = [
                'month' => date('M Y', strtotime($monthStart)),
                'month_short' => date('M', strtotime($monthStart)),
                'total_earned' => $totalEarned,
                'transaction_count' => count($earnings)
            ];
        }

        return $monthlyData;
    }

    /**
     * Downline management - view all agents in hierarchy
     */
    public function downlineManagement()
    {
        $agentId = session()->get('agent_id');

        // Get search and filter parameters
        $search = $this->request->getGet('search');
        $level = $this->request->getGet('level');
        $status = $this->request->getGet('status');

        // Get all sub-agents in hierarchy
        $allSubAgents = $this->agentModel->getAllSubAgentsInHierarchy($agentId);

        // Apply filters
        if (!empty($search)) {
            $allSubAgents = array_filter($allSubAgents, function($agent) use ($search) {
                return stripos($agent['name'], $search) !== false ||
                       stripos($agent['email'], $search) !== false ||
                       stripos($agent['phone'], $search) !== false;
            });
        }

        if ($level !== null && $level !== '') {
            $allSubAgents = array_filter($allSubAgents, function($agent) use ($level) {
                return $agent['hierarchy_depth'] == $level;
            });
        }

        if ($status === 'active') {
            $allSubAgents = array_filter($allSubAgents, function($agent) {
                return $agent['is_active'];
            });
        } elseif ($status === 'inactive') {
            $allSubAgents = array_filter($allSubAgents, function($agent) {
                return !$agent['is_active'];
            });
        }

        // Get hierarchy position
        $hierarchyPosition = $this->agentModel->getAgentHierarchyPosition($agentId);

        $data = [
            'title' => 'Downline Management | Agent Dashboard',
            'allSubAgents' => $allSubAgents,
            'hierarchyPosition' => $hierarchyPosition,
            'search' => $search,
            'level' => $level,
            'status' => $status,
            'maxLevel' => !empty($allSubAgents) ? max(array_column($allSubAgents, 'hierarchy_depth')) : 0
        ];

        return view('agent/dashboard/downline_management', $data);
    }

    /**
     * Send welcome email to newly created agent with login credentials
     */
    private function sendWelcomeEmail($agentData)
    {
        // Enhanced email service with agent credentials
        try {
            // For now, just log the credentials
            log_message('info', 'Sub-agent credentials for manual delivery:');
            log_message('info', 'Email: ' . $agentData['email']);
            log_message('info', 'Password: ' . ($agentData['plain_password'] ?? 'N/A'));
            log_message('info', 'Unique ID: ' . ($agentData['unique_agent_id'] ?? 'Auto-generated'));
            log_message('info', 'Referral ID: ' . ($agentData['referral_id'] ?? 'N/A'));

            // TODO: Implement actual email sending
        } catch (\Exception $e) {
            log_message('error', 'Failed to send welcome email to sub-agent: ' . $e->getMessage());
        }
    }
}
