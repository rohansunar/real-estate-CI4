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
 * - Unique ID generation for agents
 * - Agent profile image handling
 * - Agent status management and statistics
 *
 * Key Features:
 * - Comprehensive validation rules with unique constraints
 * - Automatic password hashing via callbacks
 * - Unique agent ID generation
 * - Agent hierarchy support for sub-agents
 * - Profile image upload support
 * - Status-based filtering and statistical reporting
 * - Memory leak prevention with proper cleanup
 *
 * Database Schema:
 * - id: Primary key (auto-increment)
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
    protected $allowedFields    = ['profile_image', 'name', 'email', 'password', 'phone', 'address', 'qualification', 'is_active', 'unique_agent_id', 'parent_agent_id'];

    /**
     * Minimal column set used for hierarchy queries to reduce memory footprint.
     * Avoids selecting large/unneeded columns when building trees.
     */
    protected array $hierarchySelect = [
        // Include qualification for /agent/downline table (avoids undefined index)
        'id', 'name', 'email', 'phone', 'unique_agent_id', 'qualification', 'is_active', 'parent_agent_id', 'created_at'
    ];

    /**
     * Simple per-request cache for downline counts to avoid repeated recursion.
     * Keyed by agent ID.
     */
    protected array $downlineCountCache = [];

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

        // Select only minimal columns to reduce memory usage
        $directSubAgents = $this->select($this->hierarchySelect)
                               ->where('parent_agent_id', $parentId)
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
     * Get full hierarchy tree for all top-level agents (admin view)
     * Returns an array of root agents with nested children
     */
    public function getFullHierarchyTree(int $maxDepth = 10): array
    {
        // Fetch top-level agents (no parent) that are active
        $roots = $this->groupStart()
                      ->where('parent_agent_id', null)
                      ->orWhere('parent_agent_id', 0)
                      ->groupEnd()
                      ->where('is_active', true)
                      ->orderBy('name', 'ASC')
                      ->findAll();

        foreach ($roots as &$root) {
            $root['hierarchy_depth'] = 1;
            $root['children'] = $this->buildHierarchyTree($root['id'], 1, $maxDepth);
            $root['has_children'] = !empty($root['children']);
            $root['total_downline'] = $this->countTotalDownline($root['id']);
        }
        unset($root);

        return $roots;
    }

    /**
     * Get full hierarchy tree (admin) with server-side pagination of root agents.
     *
     * This keeps memory usage low by fetching only the requested page of root
     * agents, while still building children recursively for each root.
     *
     * @param int $perPage  Number of root agents per page
     * @param int $page     Current page (1-based)
     * @param int $maxDepth Maximum depth of children to include
     * @return array{roots: array<int, array>, total: int, perPage: int, page: int}
     */
    public function getFullHierarchyTreePaginated(int $perPage = 10, int $page = 1, int $maxDepth = 10): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        // Count total number of root agents (no parent)
        $totalRoots = $this->groupStart()
            ->where('parent_agent_id', null)
            ->orWhere('parent_agent_id', 0)
            ->groupEnd()
            ->where('is_active', true)
            ->countAllResults(false);

        // Fetch only the current page of root agents
        $roots = $this->groupStart()
            ->where('parent_agent_id', null)
            ->orWhere('parent_agent_id', 0)
            ->groupEnd()
            ->where('is_active', true)
            ->orderBy('name', 'ASC')
            ->limit($perPage, $offset)
            ->findAll();

        foreach ($roots as &$root) {
            $root['hierarchy_depth'] = 1;
            $root['children'] = $this->buildHierarchyTree((int) $root['id'], 1, $maxDepth);
            $root['has_children'] = !empty($root['children']);
            $root['total_downline'] = $this->countTotalDownline((int) $root['id']);
        }
        unset($root); // Avoid leaking reference

        return [
            'roots'   => $roots,
            'total'   => (int) $totalRoots,
            'perPage' => $perPage,
            'page'    => $page,
        ];
    }

    /**
     * Get an agent's hierarchy tree with server-side pagination for direct children (level 1).
     *
     * The immediate children of the given agent are paginated; deeper levels remain fully
     * expanded for those children to keep the UI useful while controlling total node count.
     *
     * @param int $agentId The parent/owner agent ID
     * @param int $perPage Number of direct children per page
     * @param int $page    Current page (1-based)
     * @param int $maxDepth Maximum depth of children to include
     * @return array{nodes: array<int, array>, total: int, perPage: int, page: int}
     */
    public function getHierarchyTreePaginated(int $agentId, int $perPage = 10, int $page = 1, int $maxDepth = 10): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        // Total count of direct sub-agents to compute pages
        $total = $this->where('parent_agent_id', $agentId)
            ->where('is_active', true)
            ->countAllResults(false);

        // Fetch current page of direct children
        $directChildren = $this->select($this->hierarchySelect)
            ->where('parent_agent_id', $agentId)
            ->where('is_active', true)
            ->orderBy('name', 'ASC')
            ->limit($perPage, $offset)
            ->findAll();

        $nodes = [];
        foreach ($directChildren as $child) {
            $child['hierarchy_depth'] = 1; // relative to current agent
            $child['children'] = $this->buildHierarchyTree((int) $child['id'], 1, $maxDepth);
            $child['has_children'] = !empty($child['children']);
            $child['total_downline'] = $this->countTotalDownline((int) $child['id']);
            $nodes[] = $child;
        }

        return [
            'nodes'   => $nodes,
            'total'   => (int) $total,
            'perPage' => $perPage,
            'page'    => $page,
        ];
    }

    /**
     * Check if $descendantId belongs to the downline of $ancestorId.
     * Walks up the parent chain to the root; O(depth) and memory-safe.
     */
    public function isDescendant(int $ancestorId, int $descendantId, int $maxDepth = 50): bool
    {
        $currentId = $descendantId;
        $depth = 0;
        while ($currentId && $depth < $maxDepth) {
            if ($currentId === $ancestorId) {
                return true; // Same agent considered descendant (useful when ancestor clicks self)
            }
            $agent = $this->select(['id', 'parent_agent_id'])->find($currentId);
            if (!$agent || empty($agent['parent_agent_id'])) {
                return false;
            }
            if ((int)$agent['parent_agent_id'] === $ancestorId) {
                return true; // Direct child
            }
            $currentId = (int)$agent['parent_agent_id'];
            $depth++;
        }
        return false;
    }

    /**
     * Return direct children for a given parent with server-side pagination.
     * @return array{items: array<int, array>, total: int, perPage: int, page: int}
     */
    public function getDirectChildrenPaginated(int $parentId, int $perPage = 10, int $page = 1): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $total = $this->where('parent_agent_id', $parentId)
            ->where('is_active', true)
            ->countAllResults(false);

        $items = $this->select($this->hierarchySelect)
            ->where('parent_agent_id', $parentId)
            ->where('is_active', true)
            ->orderBy('name', 'ASC')
            ->limit($perPage, $offset)
            ->findAll();

        // Augment minimal metadata used by UI
        foreach ($items as &$item) {
            $item['hierarchy_depth'] = 1; // relative to parent
            // Quick check for children presence
            $item['has_children'] = $this->where('parent_agent_id', $item['id'])->countAllResults(false) > 0;
        }
        unset($item);

        return [
            'items' => $items,
            'total' => (int)$total,
            'perPage' => $perPage,
            'page' => $page,
        ];
    }

    /**
     * Get counts of downline grouped by level distance from a given agent.
     * Level 1 = direct children, Level 2 = grandchildren, etc.
     * Returns ['counts' => [1=>x,2=>y,...], 'total_levels' => n, 'total_agents' => m]
     */
    public function getDownlineLevelCountsRelative(int $agentId, int $maxDepth = 10): array
    {
        $counts = [];
        $currentLevelIds = [$agentId];
        $totalAgents = 0;

        for ($level = 1; $level <= $maxDepth; $level++) {
            // Find all agents whose parent_agent_id is in currentLevelIds
            if (empty($currentLevelIds)) {
                break;
            }
            $children = $this->select(['id'])
                ->whereIn('parent_agent_id', $currentLevelIds)
                ->where('is_active', true)
                ->findAll();
            $ids = array_map(fn($row) => (int)$row['id'], $children);
            $count = count($ids);
            if ($count === 0) {
                break;
            }
            $counts[$level] = $count;
            $totalAgents += $count;
            $currentLevelIds = $ids; // advance
        }

        return [
            'counts' => $counts,
            'total_levels' => count($counts),
            'total_agents' => $totalAgents,
        ];
    }



    /**
     * Build hierarchy tree with nested structure
     */
    private function buildHierarchyTree(int $parentId, int $currentDepth, int $maxDepth): array
    {
        if ($currentDepth >= $maxDepth) {
            return [];
        }

        // Select only minimal columns to reduce memory usage
        $directSubAgents = $this->select($this->hierarchySelect)
                               ->where('parent_agent_id', $parentId)
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
        // Use simple per-request cache to avoid recomputing for the same agent
        if (isset($this->downlineCountCache[$agentId])) {
            return $this->downlineCountCache[$agentId];
        }

        $allSubAgents = $this->getAllSubAgentsInHierarchy($agentId);
        $count = count($allSubAgents);
        $this->downlineCountCache[$agentId] = $count;
        return $count;
    }

    /**
     * Get upline hierarchy (direct parent only - Level 1)
     * Modified to show only direct parent instead of full parent chain
     */
    public function getUplineHierarchy(int $agentId): array
    {
        $upline = [];
        $agent = $this->find($agentId);

        // Only get direct parent (Level 1)
        if ($agent && $agent['parent_agent_id']) {
            $parentAgent = $this->find($agent['parent_agent_id']);
            if ($parentAgent) {
                $parentAgent['hierarchy_level'] = 1;
                $upline[] = $parentAgent;
            }
        }

        return $upline;
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
