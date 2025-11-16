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
 * @author Gold Properties Team
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

    /**
     * Cache for hierarchy position data to reduce database queries
     */
    protected array $hierarchyPositionCache = [];

    /**
     * Cache for parent agent data
     */
    protected array $parentAgentCache = [];

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
            'required' => 'Please provide the agent\'s full name to create their profile.',
            'max_length' => 'Agent name must be 255 characters or less. Please use a shorter name.'
        ],
        'email' => [
            'required' => 'Email address is required for agent login and system notifications.',
            'valid_email' => 'Please enter a valid email address (e.g., john.doe@example.com).',
            'is_unique' => 'This email address is already registered to another agent. Please use a different email.'
        ],
        'password' => [
            'min_length' => 'Password must be at least 6 characters long for security purposes.'
        ],
        'phone' => [
            'required' => 'Phone number is required for agent contact information and support.',
            'max_length' => 'Phone number must be 20 characters or less. Please use a shorter format.'
        ],
        'unique_agent_id' => [
            'required' => 'A unique agent ID is required for system identification.',
            'is_unique' => 'This agent ID is already in use. Please contact support if you need assistance.',
            'max_length' => 'Agent ID cannot exceed 50 characters. Please use a shorter identifier.'
        ],
        'parent_agent_id' => [
            'integer' => 'Parent agent selection must be a valid agent from the dropdown list.'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword', 'generateUniqueAgentId'];
    protected $afterInsert    = ['clearCacheAfterInsert'];
    protected $beforeUpdate   = ['hashPassword'];
    protected $afterUpdate    = ['clearCacheAfterUpdate'];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = ['clearCacheAfterDelete'];

    /**
     * Get validation rules for agent update (excludes current agent from email uniqueness check)
     * Note: unique_agent_id is excluded from update validation as it's auto-generated and immutable
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
     * Clear cache after insert
     */
    protected function clearCacheAfterInsert(array $data)
    {
        $this->clearCaches();
        return $data;
    }

    /**
     * Clear cache after update
     */
    protected function clearCacheAfterUpdate(array $data)
    {
        if (isset($data['id'])) {
            $this->clearAgentCache($data['id'][0]);
        } else {
            $this->clearCaches();
        }
        return $data;
    }

    /**
     * Clear cache after delete
     */
    protected function clearCacheAfterDelete(array $data)
    {
        if (isset($data['id'])) {
            $this->clearAgentCache($data['id'][0]);
        } else {
            $this->clearCaches();
        }
        return $data;
    }

    /**
     * Generate a unique agent ID using sequential numbering with proper race condition handling
     *
     * This method uses table locking to prevent race conditions when multiple requests
     * try to generate unique IDs simultaneously. In CodeIgniter 4, table locking must
     * be done using raw SQL queries rather than builder methods.
     *
     * Database Compatibility:
     * - Works with both MySQLi (production) and SQLite3 (testing)
     * - Uses database-agnostic queries to avoid compatibility issues
     * - Avoids MySQL-specific functions like REGEXP and CAST with UNSIGNED
     * - Uses string manipulation instead of complex SQL functions
     *
     * Locking Strategy:
     * - Uses WRITE lock on agents table during ID generation
     * - Ensures atomicity of the read-modify-write operation
     * - Properly unlocks table in all code paths (success, error, exception)
     * - Includes comprehensive logging for debugging
     *
     * ID Generation Algorithm:
     * - Finds all existing WRR-formatted IDs (WRR00001, WRR00002, etc.)
     * - Extracts numeric parts and finds the highest number
     * - Increments by 1 to generate the next sequential ID
     * - Validates uniqueness before returning
     *
     * Error Handling:
     * - Rolls back transaction on any error
     * - Ensures table is unlocked even if exceptions occur
     * - Provides user-friendly error messages
     * - Logs detailed error information for debugging
     */
    public function generateUniqueId(): string
    {
        $db = \Config\Database::connect();

        // Use transaction with table locking for race condition safety
        $db->transStart();

        try {
            // Lock the agents table to prevent concurrent modifications
            // In CodeIgniter 4, table locking requires raw SQL queries
            $db->query('LOCK TABLES agents WRITE');
            log_message('info', 'AgentModel::generateUniqueId - Table locked successfully');

            // Find the highest existing numeric ID from WRR format
            // Use database-agnostic approach that works with both MySQLi and SQLite3
            try {
                $allWrrIds = $db->table('agents')
                              ->select('unique_agent_id')
                              ->where('unique_agent_id LIKE', 'WRR%')
                              ->where('LENGTH(unique_agent_id) = 8') // WRR + 5 digits
                              ->orderBy('unique_agent_id', 'DESC')
                              ->limit(100) // Get more records to find the highest
                              ->get()
                              ->getResultArray();

                log_message('info', 'AgentModel::generateUniqueId - Found ' . count($allWrrIds) . ' existing WRR IDs');
            } catch (\Exception $e) {
                log_message('error', 'AgentModel::generateUniqueId - Error querying existing IDs: ' . $e->getMessage());
                // Unlock table before throwing exception
                $db->query('UNLOCK TABLES');
                throw new \Exception('Database query failed while finding existing agent IDs');
            }

            // Find the highest numeric value from WRR format
            $highestNumber = 0;
            $foundValidIds = 0;

            foreach ($allWrrIds as $row) {
                if (!empty($row['unique_agent_id']) && strlen($row['unique_agent_id']) === 8) {
                    $numericPart = substr($row['unique_agent_id'], 3); // Remove 'WRR' prefix
                    if (is_numeric($numericPart)) {
                        $currentNumber = (int)$numericPart;
                        if ($currentNumber > $highestNumber) {
                            $highestNumber = $currentNumber;
                        }
                        $foundValidIds++;
                    }
                }
            }

            log_message('info', 'AgentModel::generateUniqueId - Found ' . $foundValidIds . ' valid WRR IDs, highest number: ' . $highestNumber);

            $result = null;
            if ($highestNumber > 0) {
                $result = ['unique_agent_id' => 'WRR' . str_pad($highestNumber, 5, '0', STR_PAD_LEFT)];
            }

            // Extract numeric part and increment, or start from 1
            $nextNumber = 1;
            if ($result && !empty($result['unique_agent_id'])) {
                $numericPart = substr($result['unique_agent_id'], 3); // Remove 'WRR' prefix
                $nextNumber = (int)$numericPart + 1;
            }

            // Generate new sequential ID with 5-digit format (WRR00001)
            $uniqueId = 'WRR' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            // Verify uniqueness before releasing lock
            $existing = $db->table('agents')
                         ->where('unique_agent_id', $uniqueId)
                         ->countAllResults();

            if ($existing > 0) {
                // Unlock table before throwing exception
                $db->query('UNLOCK TABLES');
                log_message('warning', 'AgentModel::generateUniqueId - Generated ID already exists, unlocking table');
                throw new \Exception('Generated unique ID already exists - this should not happen');
            }

            // Unlock the table before completing transaction
            $db->query('UNLOCK TABLES');
            log_message('info', 'AgentModel::generateUniqueId - Table unlocked successfully');

            $db->transComplete();

            log_message('info', 'AgentModel::generateUniqueId - Generated unique ID: ' . $uniqueId);
            return $uniqueId;

        } catch (\Exception $e) {
            // Ensure table is unlocked even if an error occurs
            try {
                $db->query('UNLOCK TABLES');
                log_message('info', 'AgentModel::generateUniqueId - Table unlocked after error');
            } catch (\Exception $unlockException) {
                log_message('error', 'AgentModel::generateUniqueId - Failed to unlock table after error: ' . $unlockException->getMessage());
            }

            $db->transRollback();
            log_message('error', 'Failed to generate unique agent ID: ' . $e->getMessage());

            // Provide user-friendly error message
            throw new \Exception('Unable to generate a unique agent ID. Please try again or contact support if the problem persists.');
        }
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
     *
     * This method performs a depth-first traversal of the agent hierarchy tree,
     * collecting all sub-agents at all levels beneath a given parent agent.
     *
     * Business Logic:
     * - Only includes active agents (is_active = true)
     * - Maintains hierarchy depth information for UI display
     * - Uses minimal column selection to optimize memory usage
     * - Implements recursion depth limit to prevent infinite loops
     * - Orders by creation date (newest first) for consistent display
     *
     * Memory Optimization:
     * - Uses $this->hierarchySelect to limit columns fetched
     * - Passes result array by reference to avoid copying
     * - Limits recursion depth to prevent stack overflow
     *
     * @param int $parentId The parent agent ID to start from
     * @param array &$result Reference to result array (modified in place)
     * @param int $currentDepth Current recursion depth (0-based)
     * @param int $maxDepth Maximum allowed recursion depth
     */
    private function buildHierarchyRecursive(int $parentId, array &$result, int $currentDepth, int $maxDepth)
    {
        // Safety check: prevent infinite recursion and stack overflow
        if ($currentDepth >= $maxDepth) {
            return;
        }

        // Fetch direct sub-agents of the current parent
        // Only select minimal columns needed for hierarchy display
        $directSubAgents = $this->select($this->hierarchySelect)
                               ->where('parent_agent_id', $parentId)
                               ->where('is_active', true)  // Only active agents
                               ->orderBy('created_at', 'DESC')  // Newest first
                               ->findAll();

        foreach ($directSubAgents as $agent) {
            // Add hierarchy metadata for UI rendering
            $agent['hierarchy_depth'] = $currentDepth + 1;
            $result[] = $agent;

            // Recursively process this agent's sub-agents
            // This creates a depth-first traversal of the entire tree
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
     *
     * This method builds a complete hierarchical tree structure for admin dashboard display.
     * It fetches all root-level agents and recursively builds their complete downline trees.
     *
     * Business Logic:
     * - Root agents are those with parent_agent_id = null or 0
     * - Only includes active agents in the hierarchy
     * - Builds complete nested tree structure with children arrays
     * - Calculates total downline count for each root agent
     * - Orders root agents alphabetically by name for consistent display
     *
     * Performance Considerations:
     * - Can be memory intensive for large hierarchies
     * - Consider using getFullHierarchyTreePaginated() for better performance
     * - Uses recursive tree building which may hit recursion limits
     *
     * Data Structure Returned:
     * [
     *   {
     *     id: 1, name: "Agent A", hierarchy_depth: 1,
     *     has_children: true, total_downline: 15,
     *     children: [
     *       { id: 2, name: "Sub Agent B", hierarchy_depth: 2, children: [...] }
     *     ]
     *   }
     * ]
     *
     * @param int $maxDepth Maximum recursion depth to prevent infinite loops
     * @return array Complete hierarchy tree with nested children
     */
    public function getFullHierarchyTree(int $maxDepth = 10): array
    {
        // Find all root-level agents (those without parents)
        // Uses groupStart/groupEnd for proper OR condition with WHERE clause
        $roots = $this->groupStart()
                      ->where('parent_agent_id', null)  // NULL parent
                      ->orWhere('parent_agent_id', 0)   // Or zero parent (legacy data)
                      ->groupEnd()
                      ->where('is_active', true)        // Only active agents
                      ->orderBy('name', 'ASC')          // Alphabetical order
                      ->findAll();

        // Build complete tree structure for each root agent
        foreach ($roots as &$root) {
            // Set hierarchy metadata
            $root['hierarchy_depth'] = 1;  // Root level is depth 1

            // Recursively build children tree
            $root['children'] = $this->buildHierarchyTree($root['id'], 1, $maxDepth);

            // Add convenience flags for UI rendering
            $root['has_children'] = !empty($root['children']);

            // Calculate total agents in this root's downline (for statistics)
            $root['total_downline'] = $this->countTotalDownline($root['id']);
        }
        unset($root);  // Clean up reference to prevent memory leaks

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

            // Add parent agent information for enhanced display
            $child['parent_agent'] = $this->getParentAgent($child['id']);
            $child['is_root_level'] = $this->isRootLevelAgent($child['id']);

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

            // Add parent agent information for enhanced display
            $item['parent_agent'] = $this->getParentAgent($item['id']);
            $item['is_root_level'] = $this->isRootLevelAgent($item['id']);
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
     * Count total agents in downline using optimized closure table query
     */
    public function countTotalDownline(int $agentId): int
    {
        // Use per-request cache to avoid recomputing for the same agent
        if (isset($this->downlineCountCache[$agentId])) {
            return $this->downlineCountCache[$agentId];
        }

        // Use closure table for O(1) count instead of recursive queries
        $repository = new \App\Models\AgentRepository();
        $count = $repository->getSubtreeCount($agentId);

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
     * Get parent agent information for a given agent
     *
     * This method retrieves the direct parent agent information for display in the
     * enhanced hierarchy UI. It supports the parent agent display functionality
     * that shows parent information prominently when available.
     *
     * FUNCTIONALITY:
     * - Fetches direct parent agent data based on parent_agent_id relationship
     * - Returns null for root level agents (no parent exists)
     * - Adds hierarchy level metadata for UI display purposes
     * - Includes reference to child agent for relationship tracking
     *
     * USAGE IN UI:
     * - Used to display "Under: [Parent Name]" information in agent cards
     * - Enables visual hierarchy distinction between parent and child agents
     * - Supports fallback to "Primary Agent" label when no parent exists
     *
     * PERFORMANCE CONSIDERATIONS:
     * - Uses single database query per agent (cached by CodeIgniter's model layer)
     * - Minimal data selection to reduce memory footprint
     * - Efficient for paginated hierarchy displays
     *
     * @param int $agentId Agent ID to get parent information for
     * @return array|null Parent agent data with hierarchy metadata, or null if no parent exists
     *                    Returns array with keys: id, name, email, unique_agent_id, hierarchy_level, is_parent_of
     */
    public function getParentAgent(int $agentId): ?array
    {
        // Check cache first
        if (isset($this->parentAgentCache[$agentId])) {
            return $this->parentAgentCache[$agentId];
        }

        $agent = $this->find($agentId);

        // Return null if agent doesn't exist or has no parent
        if (!$agent || !$agent['parent_agent_id']) {
            $this->parentAgentCache[$agentId] = null;
            return null;
        }

        $parentAgent = $this->find($agent['parent_agent_id']);

        // Return null if parent agent doesn't exist (data integrity issue)
        if (!$parentAgent) {
            log_message('warning', "Parent agent not found for agent ID {$agentId}, parent_agent_id: {$agent['parent_agent_id']}");
            $this->parentAgentCache[$agentId] = null;
            return null;
        }

        // Add hierarchy level information for UI display
        $parentAgent['hierarchy_level'] = 1; // Direct parent is always level 1 relative to child
        $parentAgent['is_parent_of'] = $agentId; // Reference to child for relationship tracking

        // Cache the result
        $this->parentAgentCache[$agentId] = $parentAgent;

        return $parentAgent;
    }

    /**
     * Check if an agent is a root level agent (has no parent)
     *
     * This method determines whether an agent is at the root level of the hierarchy,
     * which is used to decide between showing parent agent information or the
     * "Primary Agent" label in the UI.
     *
     * FUNCTIONALITY:
     * - Checks if agent has no parent_agent_id or parent_agent_id is 0/null
     * - Used for conditional display logic in hierarchy UI
     * - Supports the enhanced parent agent display feature
     *
     * USAGE IN UI:
     * - Determines whether to show "Primary Agent" badge or parent agent information
     * - Controls CSS class application for visual styling (is-primary vs has-parent)
     * - Used in conditional rendering logic in hierarchy_row.php partial
     *
     * @param int $agentId Agent ID to check for root level status
     * @return bool True if agent is root level (no parent), false if agent has a parent
     */
    public function isRootLevelAgent(int $agentId): bool
    {
        $agent = $this->find($agentId);

        // Agent is root level if it exists and has no parent_agent_id (null, 0, or empty)
        return $agent && (empty($agent['parent_agent_id']) || $agent['parent_agent_id'] == 0);
    }

    /**
     * Get agent's position in hierarchy with caching
     */
    public function getAgentHierarchyPosition(int $agentId): array
    {
        // Check cache first
        if (isset($this->hierarchyPositionCache[$agentId])) {
            return $this->hierarchyPositionCache[$agentId];
        }

        $agent = $this->find($agentId);
        if (!$agent) {
            return [];
        }

        $upline = $this->getUplineHierarchy($agentId);
        $directSubAgents = $this->getSubAgents($agentId);
        $totalDownline = $this->countTotalDownline($agentId);

        $result = [
            'agent' => $agent,
            'hierarchy_level' => count($upline),
            'upline' => $upline,
            'direct_sub_agents' => count($directSubAgents),
            'total_downline' => $totalDownline,
            'is_top_level' => empty($upline)
        ];

        // Cache the result
        $this->hierarchyPositionCache[$agentId] = $result;

        return $result;
    }

    /**
     * Clear all caches to maintain data consistency
     * Call this method when agents are created, updated, or deleted
     */
    public function clearCaches(): void
    {
        $this->downlineCountCache = [];
        $this->hierarchyPositionCache = [];
        $this->parentAgentCache = [];
    }

    /**
     * Clear cache for specific agent and related agents
     */
    public function clearAgentCache(int $agentId): void
    {
        // Clear caches for the agent
        unset($this->downlineCountCache[$agentId]);
        unset($this->hierarchyPositionCache[$agentId]);
        unset($this->parentAgentCache[$agentId]);

        // Clear parent cache for all children of this agent
        $children = $this->getSubAgents($agentId);
        foreach ($children as $child) {
            unset($this->parentAgentCache[$child['id']]);
        }

        // Clear downline cache for all parents up the hierarchy
        $agent = $this->find($agentId);
        if ($agent && $agent['parent_agent_id']) {
            $this->clearParentDownlineCaches($agent['parent_agent_id']);
        }
    }

    /**
     * Recursively clear downline caches for parent hierarchy
     */
    private function clearParentDownlineCaches(int $parentId): void
    {
        unset($this->downlineCountCache[$parentId]);
        unset($this->hierarchyPositionCache[$parentId]);

        $parent = $this->find($parentId);
        if ($parent && $parent['parent_agent_id']) {
            $this->clearParentDownlineCaches($parent['parent_agent_id']);
        }
    }

    // ========================================================================
    // CLOSURE TABLE INTEGRATION METHODS
    // ========================================================================

    /**
     * Create agent using closure table pattern for enhanced hierarchy management
     * Integrates with AgentRepository for optimal performance
     *
     * @param int $parentId Parent agent ID (0 or null for top-level)
     * @param array $data Agent data
     * @return int New agent ID
     * @throws Exception If creation fails
     */
    public function createAgentWithClosureTable(int $parentId = null, array $data = []): int
    {
        // Load the AgentRepository for closure table operations
        $repository = new \App\Models\AgentRepository();

        if ($parentId && $parentId > 0) {
            // Create under parent using closure table
            return $repository->createUnder($parentId, $data);
        } else {
            // Create top-level agent
            $db = \Config\Database::connect();
            $db->transStart();

            try {
                // Insert the agent
                $agentId = $this->insert($data);

                if (!$agentId) {
                    throw new \Exception('Failed to create agent');
                }

                // Insert self-referencing closure table record
                $db->table('agent_tree')->insert([
                    'ancestor_id' => $agentId,
                    'descendant_id' => $agentId,
                    'depth' => 0
                ]);

                $db->transComplete();

                if (!$db->transStatus()) {
                    throw new \Exception('Transaction failed');
                }

                return $agentId;

            } catch (\Exception $e) {
                $db->transRollback();
                log_message('error', 'AgentModel::createAgentWithClosureTable failed: ' . $e->getMessage());
                throw $e;
            }
        }
    }



    /**
     * Get immediate children using closure table
     *
     * @param int $agentId Parent agent ID
     * @return array Direct children
     */
    public function getImmediateChildrenClosureTable(int $agentId): array
    {
        $repository = new \App\Models\AgentRepository();
        return $repository->getImmediateChildren($agentId);
    }

    /**
     * Get upline using closure table
     *
     * @param int $agentId Descendant agent ID
     * @return array Upline agents with depth information
     */
    public function getUplineClosureTable(int $agentId): array
    {
        $repository = new \App\Models\AgentRepository();
        return $repository->getUpline($agentId);
    }

    /**
     * Get subtree count using closure table for optimal performance
     *
     * @param int $agentId Root agent ID
     * @return int Count of agents in subtree
     */
    public function getSubtreeCountClosureTable(int $agentId): int
    {
        $repository = new \App\Models\AgentRepository();
        return $repository->getSubtreeCount($agentId);
    }

    /**
     * Get hierarchy tree with closure table optimization
     * Enhanced version that uses closure table for better performance
     *
     * @param int $agentId Root agent ID
     * @param int $maxDepth Maximum depth to retrieve
     * @return array Nested hierarchy tree
     */
    public function getHierarchyTreeClosureTable(int $agentId, int $maxDepth = 10): array
    {
        // Get all descendants within max depth using closure table
        $descendants = $this->db->table('agent_tree t')
            ->select('a.*, t.depth')
            ->join('agents a', 'a.id = t.descendant_id')
            ->where('t.ancestor_id', $agentId)
            ->where('t.depth <=', $maxDepth)
            ->where('a.is_active', true)
            ->orderBy('t.depth, a.name')
            ->get()
            ->getResultArray();

        if (empty($descendants)) {
            return [];
        }

        // Build nested tree structure
        return $this->buildNestedTreeFromFlat($descendants, $agentId);
    }

    /**
     * Build nested tree structure from flat closure table results
     *
     * @param array $flatData Flat array of agents with depth
     * @param int $rootId Root agent ID
     * @return array Nested tree structure
     */
    private function buildNestedTreeFromFlat(array $flatData, int $rootId): array
    {
        $byId = [];
        $byParent = [];

        // Organize data by ID and parent
        foreach ($flatData as $item) {
            $id = (int) $item['id'];
            $parentId = $item['parent_agent_id'] ? (int) $item['parent_agent_id'] : null;

            $byId[$id] = $item;
            $byId[$id]['children'] = [];
            $byId[$id]['has_children'] = false;

            if ($parentId) {
                $byParent[$parentId][] = $id;
            }
        }

        // Build nested structure
        $buildTree = function($nodeId) use (&$buildTree, &$byId, &$byParent) {
            $node = $byId[$nodeId] ?? null;
            if (!$node) return null;

            $children = $byParent[$nodeId] ?? [];
            foreach ($children as $childId) {
                $childNode = $buildTree($childId);
                if ($childNode) {
                    $node['children'][] = $childNode;
                }
            }

            $node['has_children'] = !empty($node['children']);
            return $node;
        };

        return $buildTree($rootId) ?: [];
    }
}
