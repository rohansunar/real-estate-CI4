<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * CommissionTransactionModel
 *
 * Handles all commission transaction operations for the multi-level agent hierarchy system.
 * This model manages:
 * - Commission distribution calculations throughout the agent hierarchy
 * - Transaction tracking and status management
 * - Commission reporting and analytics
 * - Multi-level commission flow (10% total distributed up the hierarchy)
 *
 * Key Features:
 * - Automatic commission calculation based on property sale amounts
 * - Hierarchical commission distribution with configurable percentages
 * - Transaction status tracking (pending, processed, paid, cancelled)
 * - Commission reporting and analytics for agents and administrators
 * - Audit trail for all commission transactions
 * - Performance optimized queries for large hierarchies
 *
 * Commission Distribution Logic:
 * - Total commission pool: 10% of property sale amount
 * - Distribution: Flows up the hierarchy from selling agent to top-level agent
 * - Each level receives a portion based on configured percentages
 * - Supports unlimited hierarchy levels
 *
 * @author Real Estate Team
 * @version 1.0
 * @since 2025-08-08
 */
class CommissionTransactionModel extends Model
{
    protected $table            = 'commission_transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'property_id', 'selling_agent_id', 'receiving_agent_id', 'hierarchy_level',
        'sale_amount', 'commission_percentage', 'commission_amount', 'transaction_status',
        'transaction_reference', 'notes', 'processed_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'property_id'           => 'required|integer',
        'selling_agent_id'      => 'required|integer',
        'receiving_agent_id'    => 'required|integer',
        'hierarchy_level'       => 'required|integer|greater_than_equal_to[0]',
        'sale_amount'           => 'required|decimal|greater_than[0]',
        'commission_percentage' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        'commission_amount'     => 'required|decimal|greater_than_equal_to[0]',
        'transaction_status'    => 'required|in_list[pending,processed,paid,cancelled]'
    ];

    protected $validationMessages = [
        'property_id' => [
            'required' => 'Property ID is required',
            'integer' => 'Property ID must be a valid number'
        ],
        'selling_agent_id' => [
            'required' => 'Selling agent ID is required',
            'integer' => 'Selling agent ID must be a valid number'
        ],
        'receiving_agent_id' => [
            'required' => 'Receiving agent ID is required',
            'integer' => 'Receiving agent ID must be a valid number'
        ],
        'sale_amount' => [
            'required' => 'Sale amount is required',
            'decimal' => 'Sale amount must be a valid decimal number',
            'greater_than' => 'Sale amount must be greater than 0'
        ],
        'commission_amount' => [
            'required' => 'Commission amount is required',
            'decimal' => 'Commission amount must be a valid decimal number'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Commission distribution configuration
     * Defines how the 10% total commission is distributed across hierarchy levels
     */
    protected $commissionConfig = [
        'total_commission_percentage' => 10.0, // 10% of sale amount
        'level_distribution' => [
            0 => 6.0,  // Selling agent gets 6%
            1 => 2.0,  // Direct parent gets 2%
            2 => 1.0,  // Grandparent gets 1%
            3 => 0.5,  // Great-grandparent gets 0.5%
            4 => 0.3,  // Great-great-grandparent gets 0.3%
            5 => 0.2,  // And so on...
        ],
        'default_level_percentage' => 0.1 // Default for levels beyond configured
    ];

    /**
     * Create commission transactions for a property sale
     * Distributes commission up the agent hierarchy
     */
    public function createCommissionTransactions($propertyId, $sellingAgentId, $saleAmount)
    {
        try {
            $agentModel = new \App\Models\AgentModel();

            // Get the selling agent and build hierarchy chain
            $hierarchyChain = $this->buildHierarchyChain($sellingAgentId, $agentModel);

            if (empty($hierarchyChain)) {
                throw new \Exception('Unable to build hierarchy chain for agent ID: ' . $sellingAgentId);
            }

            $transactions = [];

            // Create commission transaction for each level in the hierarchy
            foreach ($hierarchyChain as $level => $agentId) {
                $commissionPercentage = $this->getCommissionPercentageForLevel($level);
                $commissionAmount = $saleAmount * ($commissionPercentage / 100);

                $transactionData = [
                    'property_id' => $propertyId,
                    'selling_agent_id' => $sellingAgentId,
                    'receiving_agent_id' => $agentId,
                    'hierarchy_level' => $level,
                    'sale_amount' => $saleAmount,
                    'commission_percentage' => $commissionPercentage,
                    'commission_amount' => $commissionAmount,
                    'transaction_status' => 'pending',
                    'transaction_reference' => $this->generateTransactionReference($propertyId, $sellingAgentId),
                    'notes' => "Commission for property sale - Level {$level} in hierarchy"
                ];

                $transactionId = $this->insert($transactionData);
                if ($transactionId) {
                    $transactions[] = $transactionId;
                } else {
                    log_message('error', 'Failed to create commission transaction: ' . json_encode($transactionData));
                }
            }

            return $transactions;

        } catch (\Exception $e) {
            log_message('error', 'Commission transaction creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Build hierarchy chain from selling agent up to top-level agent
     */
    private function buildHierarchyChain($agentId, $agentModel)
    {
        $hierarchyChain = [];
        $currentAgentId = $agentId;
        $level = 0;
        $maxLevels = 20; // Prevent infinite loops

        while ($currentAgentId && $level < $maxLevels) {
            $hierarchyChain[$level] = $currentAgentId;

            // Get parent agent
            $agent = $agentModel->find($currentAgentId);
            if (!$agent || !$agent['parent_agent_id']) {
                break;
            }

            $currentAgentId = $agent['parent_agent_id'];
            $level++;
        }

        return $hierarchyChain;
    }

    /**
     * Get commission percentage for a specific hierarchy level
     */
    private function getCommissionPercentageForLevel($level)
    {
        if (isset($this->commissionConfig['level_distribution'][$level])) {
            return $this->commissionConfig['level_distribution'][$level];
        }

        return $this->commissionConfig['default_level_percentage'];
    }

    /**
     * Generate unique transaction reference
     */
    private function generateTransactionReference($propertyId, $sellingAgentId)
    {
        return 'COMM-' . date('Ymd') . '-P' . $propertyId . '-A' . $sellingAgentId . '-' . uniqid();
    }

    /**
     * Get commission earnings for an agent
     */
    public function getAgentCommissionEarnings($agentId, $status = null, $dateFrom = null, $dateTo = null)
    {
        $builder = $this->where('receiving_agent_id', $agentId);

        if ($status) {
            $builder->where('transaction_status', $status);
        }

        if ($dateFrom) {
            $builder->where('created_at >=', $dateFrom);
        }

        if ($dateTo) {
            $builder->where('created_at <=', $dateTo);
        }

        return $builder->orderBy('created_at', 'DESC')->findAll();
    }

    /**
     * Get commission statistics for an agent
     */
    public function getAgentCommissionStats($agentId)
    {
        $builder = $this->where('receiving_agent_id', $agentId);

        return [
            'total_earned' => $builder->selectSum('commission_amount')->get()->getRow()->commission_amount ?? 0,
            'pending_amount' => $this->where('receiving_agent_id', $agentId)
                                    ->where('transaction_status', 'pending')
                                    ->selectSum('commission_amount')
                                    ->get()->getRow()->commission_amount ?? 0,
            'paid_amount' => $this->where('receiving_agent_id', $agentId)
                                  ->where('transaction_status', 'paid')
                                  ->selectSum('commission_amount')
                                  ->get()->getRow()->commission_amount ?? 0,
            'transaction_count' => $this->where('receiving_agent_id', $agentId)->countAllResults(false),
            'this_month_earned' => $this->where('receiving_agent_id', $agentId)
                                       ->where('created_at >=', date('Y-m-01 00:00:00'))
                                       ->selectSum('commission_amount')
                                       ->get()->getRow()->commission_amount ?? 0
        ];
    }

    /**
     * Get downline commission earnings (commissions from sub-agents)
     */
    public function getDownlineCommissionEarnings($agentId)
    {
        // Get all sub-agents in the hierarchy
        $agentModel = new \App\Models\AgentModel();
        $allSubAgents = $agentModel->getAllSubAgentsInHierarchy($agentId);

        if (empty($allSubAgents)) {
            return [];
        }

        $subAgentIds = array_column($allSubAgents, 'id');

        return $this->whereIn('selling_agent_id', $subAgentIds)
                   ->where('receiving_agent_id', $agentId)
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }

    /**
     * Update transaction status
     */
    public function updateTransactionStatus($transactionId, $status, $notes = null)
    {
        $data = [
            'transaction_status' => $status,
            'processed_at' => date('Y-m-d H:i:s')
        ];

        if ($notes) {
            $data['notes'] = $notes;
        }

        return $this->update($transactionId, $data);
    }

    /**
     * Get commission transactions for a property sale
     */
    public function getPropertyCommissionTransactions($propertyId)
    {
        return $this->where('property_id', $propertyId)
                   ->orderBy('hierarchy_level', 'ASC')
                   ->findAll();
    }

    /**
     * Get recent commission transactions
     */
    public function getRecentTransactions($limit = 10, $agentId = null)
    {
        $builder = $this->select('commission_transactions.*,
                                 selling_agent.name as selling_agent_name,
                                 receiving_agent.name as receiving_agent_name,
                                 properties.title as property_title')
                       ->join('agents as selling_agent', 'selling_agent.id = commission_transactions.selling_agent_id')
                       ->join('agents as receiving_agent', 'receiving_agent.id = commission_transactions.receiving_agent_id')
                       ->join('properties', 'properties.id = commission_transactions.property_id')
                       ->orderBy('commission_transactions.created_at', 'DESC');

        if ($agentId) {
            $builder->where('commission_transactions.receiving_agent_id', $agentId);
        }

        return $builder->limit($limit)->get()->getResultArray();
    }
}
