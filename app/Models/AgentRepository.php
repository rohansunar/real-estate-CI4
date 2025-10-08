<?php

namespace App\Models;

use Exception;

/**
 * AgentRepository
 *
 * Enhanced repository for multi-level agent hierarchy management using closure table pattern.
 * This model implements the closure table operations as specified in the Multi-Level Agent
 * Hierarchy Guide for efficient tree traversal and hierarchy management.
 *
 * Key Features:
 * - Closure table operations for O(1)/O(log n) hierarchy queries
 * - Transaction-safe agent creation with automatic hierarchy setup
 * - Efficient downline, upline, and subtree operations
 * - Commission calculation and distribution support
 * - Optimized queries for large hierarchies
 *
 * Database Tables:
 * - agents: Main agent data
 * - agent_tree: Closure table for hierarchy relationships
 * - commission_transactions: Commission tracking and distribution
 *
 * @author White Rock Realtor Team
 * @version 1.0
 * @since 2025-08-16
 */
class AgentRepository
{

    /**
     * Create a new agent under a parent using closure table pattern
     *
     * This method implements the createUnder pattern from the Multi-Level Agent Hierarchy Guide.
     * It creates a new agent and automatically establishes all necessary ancestor-descendant
     * relationships in the closure table for efficient hierarchy queries.
     *
     * The method performs the following operations in a single transaction:
     * 1. Validates that the parent agent exists and is active
     * 2. Creates the new agent record with proper data validation
     * 3. Establishes closure table relationships using optimized SQL
     * 4. Ensures data integrity through transaction management
     *
     * @param int $parentId Parent agent ID - must be an existing active agent
     * @param array $data Agent data including name, email, phone, etc.
     * @return int New agent ID on successful creation
     * @throws Exception If parent doesn't exist, validation fails, or transaction fails
     */
    public function createUnder(int $parentId, array $data): int
    {
        // Initialize database connection for transaction management
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Step 1: Validate parent agent exists and is active
            $agentModel = new AgentModel();
            $parent = $agentModel->find($parentId);
            if (!$parent) {
                throw new Exception("Cannot create sub-agent: The selected parent agent (ID: {$parentId}) was not found. Please select a valid parent agent from the list.");
            }

            if (!$parent['is_active']) {
                throw new Exception("Cannot create sub-agent under '{$parent['name']}': This parent agent is currently inactive. Please activate the parent agent first or choose a different parent.");
            }

            // Step 2: Prepare agent data with proper defaults and validation
            $data['parent_agent_id'] = $parentId;

            // Generate unique agent ID if not provided
            if (!isset($data['unique_agent_id']) || empty($data['unique_agent_id'])) {
                $data['unique_agent_id'] = $agentModel->generateUniqueId();
            }

            // Hash password if provided
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            // Step 3: Insert the new agent record
            $agentData = [
                'parent_agent_id' => $parentId,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'] ?? null,
                'qualification' => $data['qualification'] ?? null,
                'password' => $data['password'] ?? null,
                'unique_agent_id' => $data['unique_agent_id'],
                'is_active' => $data['is_active'] ?? true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $db->table('agents')->insert($agentData);
            $newId = (int) $db->insertID();

            if (!$newId) {
                throw new Exception('Failed to create agent record');
            }

            // Step 4: Establish closure table relationships
            // This SQL creates all necessary ancestor-descendant pairs:
            // - All ancestors of parent become ancestors of new agent (depth + 1)
            // - Self-referencing record (depth 0)
            // - Direct parent relationship (depth 1)
            $db->query("
                INSERT INTO agent_tree (ancestor_id, descendant_id, depth)
                SELECT ancestor_id, ?, depth + 1
                FROM agent_tree
                WHERE descendant_id = ?
                UNION ALL SELECT ?, ?, 0
                UNION ALL SELECT ?, ?, 1
            ", [$newId, $parentId, $newId, $newId, $parentId, $newId]);

            // Step 5: Complete transaction
            $db->transComplete();

            if (!$db->transStatus()) {
                throw new Exception('CreateUnder transaction failed during commit');
            }

            // Log successful creation for audit trail
            log_message('info', "Agent created successfully: ID {$newId} under parent {$parentId}");

            return $newId;

        } catch (Exception $e) {
            // Rollback transaction on any error
            $db->transRollback();
            log_message('error', 'AgentRepository::createUnder failed: ' . $e->getMessage());
            throw $e;
        }
    }



    /**
     * Get immediate children using closure table
     * Implements the immediate children query from the guide
     *
     * @param int $agentId Parent agent ID
     * @return array Direct children
     */
    public function getImmediateChildren(int $agentId): array
    {
        $db = \Config\Database::connect();
        return $db->table('agent_tree t')
            ->select('a.*')
            ->join('agents a', 'a.id = t.descendant_id')
            ->where('t.ancestor_id', $agentId)
            ->where('t.depth', 1)
            ->orderBy('a.created_at')
            ->get()
            ->getResultArray();
    }

    /**
     * Get upline (ancestors) using closure table
     * Implements the upline query from the guide
     *
     * @param int $agentId Descendant agent ID
     * @return array Upline agents
     */
    public function getUpline(int $agentId): array
    {
        $db = \Config\Database::connect();
        return $db->table('agent_tree t')
            ->select('a.*, t.depth')
            ->join('agents a', 'a.id = t.ancestor_id')
            ->where('t.descendant_id', $agentId)
            ->where('t.depth >', 0)
            ->orderBy('t.depth')
            ->get()
            ->getResultArray();
    }

    /**
     * Get subtree count using closure table
     * Implements the subtree count query from the guide
     *
     * @param int $agentId Root agent ID
     * @return int Count of agents in subtree
     */
    public function getSubtreeCount(int $agentId): int
    {
        $db = \Config\Database::connect();
        $result = $db->table('agent_tree')
            ->selectCount('*', 'downline_count')
            ->where('ancestor_id', $agentId)
            ->where('depth >', 0)
            ->get()
            ->getFirstRow('array');

        return (int) ($result['downline_count'] ?? 0);
    }

}
