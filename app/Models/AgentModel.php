<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * AgentModel
 *
 * Handles all agent-related database operations including authentication and hierarchy management.
 * This model manages:
 * - Agent CRUD operations with enhanced validation
 * - Agent authentication with password hashing
 * - Agent hierarchy system (parent-child relationships)
 * - Unique ID generation for agents and referrals
 * - Agent profile image handling
 * - Agent status management and statistics
 *
 * Key Features:
 * - Comprehensive validation rules with unique constraints
 * - Automatic password hashing via callbacks
 * - Unique agent ID and referral ID generation
 * - Agent hierarchy support for sub-agents
 * - Profile image upload support
 * - Status-based filtering and statistical reporting
 * - Memory leak prevention with proper cleanup
 *
 * Database Schema:
 * - id: Primary key (auto-increment)
 * - referral_id: Unique referral identifier (nullable, indexed)
 * - unique_agent_id: Auto-generated unique agent ID (required, indexed)
 * - name: Agent full name (required)
 * - email: Agent email address (required, unique)
 * - password: Hashed password for authentication (nullable)
 * - phone: Contact phone number (required)
 * - address: Physical address (optional)
 * - qualification: Professional qualifications (optional)
 * - profile_image: Profile image path (optional)
 * - parent_agent_id: Foreign key to parent agent for hierarchy (nullable)
 * - is_active: Status flag (boolean, default true)
 * - created_at/updated_at: Timestamps
 *
 * @author Real Estate Team
 * @version 3.0 - Enhanced with authentication and hierarchy system
 * @since 2025-08-04
 */
class AgentModel extends Model
{
    protected $table            = 'agents';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['profile_image', 'name', 'email', 'password', 'phone', 'address', 'qualification', 'is_active', 'referral_id', 'unique_agent_id', 'parent_agent_id'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'name'            => 'required|max_length[255]',
        'email'           => 'required|valid_email',
        'password'        => 'permit_empty|min_length[6]',
        'phone'           => 'required|max_length[20]',
        'address'         => 'permit_empty|max_length[1000]',
        'qualification'   => 'permit_empty|max_length[255]',
        'referral_id'     => 'permit_empty|max_length[50]|is_unique[agents.referral_id]',
        'unique_agent_id' => 'permit_empty|max_length[50]|is_unique[agents.unique_agent_id]',
        'parent_agent_id' => 'permit_empty|integer',
    ];
    protected $validationMessages   = [
        'name' => [
            'required' => 'Agent name is required.',
            'max_length' => 'Agent name cannot exceed 255 characters.'
        ],
        'email' => [
            'required' => 'Email address is required.',
            'valid_email' => 'Please enter a valid email address.',
            'is_unique' => 'This email address is already registered.'
        ],
        'password' => [
            'min_length' => 'Password must be at least 6 characters long.'
        ],
        'phone' => [
            'required' => 'Phone number is required.',
            'max_length' => 'Phone number cannot exceed 20 characters.'
        ],
        'referral_id' => [
            'is_unique' => 'This referral ID is already in use.',
            'max_length' => 'Referral ID cannot exceed 50 characters.'
        ],
        'unique_agent_id' => [
            'required' => 'Unique agent ID is required.',
            'is_unique' => 'This agent ID is already in use.',
            'max_length' => 'Agent ID cannot exceed 50 characters.'
        ],
        'parent_agent_id' => [
            'integer' => 'Parent agent ID must be a valid number.'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword', 'generateUniqueAgentId'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['hashPassword'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get validation rules for agent update (excludes current agent from email uniqueness check)
     */
    public function getUpdateValidationRules($agentId)
    {
        return [
            'name'            => 'required|max_length[255]',
            'email'           => "required|valid_email|is_unique[agents.email,id,{$agentId}]",
            'password'        => 'permit_empty|min_length[6]',
            'phone'           => 'required|max_length[20]',
            'address'         => 'permit_empty|max_length[1000]',
            'qualification'   => 'permit_empty|max_length[255]',
            'referral_id'     => "permit_empty|max_length[50]|is_unique[agents.referral_id,id,{$agentId}]",
            'unique_agent_id' => "required|max_length[50]|is_unique[agents.unique_agent_id,id,{$agentId}]",
            'parent_agent_id' => 'permit_empty|integer',
        ];
    }

    /**
     * Get validation rules for agent creation
     */
    public function getCreateValidationRules()
    {
        return [
            'name'            => 'required|max_length[255]',
            'email'           => 'required|valid_email|is_unique[agents.email]',
            'password'        => 'permit_empty|min_length[6]',
            'phone'           => 'required|max_length[20]',
            'address'         => 'permit_empty|max_length[1000]',
            'qualification'   => 'permit_empty|max_length[255]',
            'referral_id'     => 'permit_empty|max_length[50]|is_unique[agents.referral_id]',
            'unique_agent_id' => 'permit_empty|max_length[50]|is_unique[agents.unique_agent_id]',
            'parent_agent_id' => 'permit_empty|integer',
        ];
    }

    /**
     * Get active agents
     */
    public function getActive(int $limit = null)
    {
        $builder = $this->where('is_active', true)
                       ->orderBy('created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
    }

    /**
     * Get agents count by status
     */
    public function getCountByStatus(bool $isActive = true): int
    {
        return $this->where('is_active', $isActive)->countAllResults();
    }

    /**
     * Get recent agents
     */
    public function getRecent(int $limit = 10)
    {
        return $this->orderBy('created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Search agents
     */
    public function searchAgents(string $query, int $limit = 10)
    {
        return $this->groupStart()
                   ->like('name', $query)
                   ->orLike('email', $query)
                   ->orLike('phone', $query)
                   ->orLike('qualification', $query)
                   ->groupEnd()
                   ->orderBy('created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }



    /**
     * Get agent statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->countAllResults(false),
            'active' => $this->getCountByStatus(true),
            'inactive' => $this->getCountByStatus(false),
            'recent' => $this->where('created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')))->countAllResults(false)
        ];
    }

    /**
     * Hash password before saving
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    /**
     * Generate unique agent ID before insert
     */
    protected function generateUniqueAgentId(array $data)
    {
        if (!isset($data['data']['unique_agent_id']) || empty($data['data']['unique_agent_id'])) {
            $data['data']['unique_agent_id'] = $this->generateUniqueId();
        }
        return $data;
    }

    /**
     * Generate a unique agent ID
     */
    public function generateUniqueId(): string
    {
        do {
            $uniqueId = 'AGT' . date('ymd') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while ($this->where('unique_agent_id', $uniqueId)->first());

        return $uniqueId;
    }

    /**
     * Generate a unique referral ID
     */
    public function generateReferralId(): string
    {
        do {
            $referralId = 'REF' . date('ymd') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while ($this->where('referral_id', $referralId)->first());

        return $referralId;
    }

    /**
     * Verify password
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Find agent by email
     */
    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Find agent by unique agent ID
     */
    public function findByUniqueId(string $uniqueId)
    {
        return $this->where('unique_agent_id', $uniqueId)->first();
    }

    /**
     * Find agent by referral ID
     */
    public function findByReferralId(string $referralId)
    {
        return $this->where('referral_id', $referralId)->first();
    }

    /**
     * Get sub-agents for a parent agent
     */
    public function getSubAgents(int $parentAgentId, int $limit = null)
    {
        $builder = $this->where('parent_agent_id', $parentAgentId)
                       ->orderBy('created_at', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Get agent hierarchy count (direct sub-agents only)
     */
    public function getHierarchyCount(int $agentId): int
    {
        return $this->where('parent_agent_id', $agentId)->countAllResults();
    }

    /**
     * Get all sub-agents in hierarchy (multi-level)
     * Returns all agents in the downline hierarchy
     */
    public function getAllSubAgentsInHierarchy(int $agentId, int $maxDepth = 10): array
    {
        $allSubAgents = [];
        $this->buildHierarchyRecursive($agentId, $allSubAgents, 0, $maxDepth);
        return $allSubAgents;
    }

    /**
     * Recursive method to build complete hierarchy
     */
    private function buildHierarchyRecursive(int $parentId, array &$result, int $currentDepth, int $maxDepth)
    {
        if ($currentDepth >= $maxDepth) {
            return; // Prevent infinite recursion
        }

        $directSubAgents = $this->where('parent_agent_id', $parentId)
                               ->where('is_active', true)
                               ->orderBy('created_at', 'DESC')
                               ->findAll();

        foreach ($directSubAgents as $agent) {
            $agent['hierarchy_depth'] = $currentDepth + 1;
            $result[] = $agent;

            // Recursively get sub-agents of this agent
            $this->buildHierarchyRecursive($agent['id'], $result, $currentDepth + 1, $maxDepth);
        }
    }

    /**
     * Get hierarchy tree structure for display
     */
    public function getHierarchyTree(int $agentId, int $maxDepth = 10): array
    {
        return $this->buildHierarchyTree($agentId, 0, $maxDepth);
    }

    /**
     * Build hierarchy tree with nested structure
     */
    private function buildHierarchyTree(int $parentId, int $currentDepth, int $maxDepth): array
    {
        if ($currentDepth >= $maxDepth) {
            return [];
        }

        $directSubAgents = $this->where('parent_agent_id', $parentId)
                               ->where('is_active', true)
                               ->orderBy('name', 'ASC')
                               ->findAll();

        $tree = [];
        foreach ($directSubAgents as $agent) {
            $agent['hierarchy_depth'] = $currentDepth + 1;
            $agent['children'] = $this->buildHierarchyTree($agent['id'], $currentDepth + 1, $maxDepth);
            $agent['has_children'] = !empty($agent['children']);
            $agent['total_downline'] = $this->countTotalDownline($agent['id']);
            $tree[] = $agent;
        }

        return $tree;
    }

    /**
     * Count total agents in downline
     */
    public function countTotalDownline(int $agentId): int
    {
        $allSubAgents = $this->getAllSubAgentsInHierarchy($agentId);
        return count($allSubAgents);
    }

    /**
     * Get upline hierarchy (parent chain)
     */
    public function getUplineHierarchy(int $agentId): array
    {
        $upline = [];
        $currentAgentId = $agentId;
        $maxLevels = 20; // Prevent infinite loops
        $level = 0;

        while ($currentAgentId && $level < $maxLevels) {
            $agent = $this->find($currentAgentId);
            if (!$agent || !$agent['parent_agent_id']) {
                break;
            }

            $parentAgent = $this->find($agent['parent_agent_id']);
            if ($parentAgent) {
                $parentAgent['hierarchy_level'] = $level + 1;
                $upline[] = $parentAgent;
                $currentAgentId = $parentAgent['id'];
                $level++;
            } else {
                break;
            }
        }

        return array_reverse($upline); // Return from top-level down
    }

    /**
     * Get agent's position in hierarchy
     */
    public function getAgentHierarchyPosition(int $agentId): array
    {
        $agent = $this->find($agentId);
        if (!$agent) {
            return [];
        }

        $upline = $this->getUplineHierarchy($agentId);
        $directSubAgents = $this->getSubAgents($agentId);
        $totalDownline = $this->countTotalDownline($agentId);

        return [
            'agent' => $agent,
            'hierarchy_level' => count($upline),
            'upline' => $upline,
            'direct_sub_agents' => count($directSubAgents),
            'total_downline' => $totalDownline,
            'is_top_level' => empty($upline)
        ];
    }
}
