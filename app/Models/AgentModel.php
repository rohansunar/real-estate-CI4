<?php

namespace App\Models;

use CodeIgniter\Model;

class AgentModel extends Model
{
    protected $table            = 'agents';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['profile_image', 'name', 'email', 'phone', 'address', 'qualification', 'is_active'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'name'          => 'required|max_length[255]',
        'email'         => 'required|valid_email|is_unique[agents.email,id,{id}]',
        'phone'         => 'required|max_length[20]',
        'address'       => 'permit_empty|max_length[1000]',
        'qualification' => 'permit_empty|max_length[255]',
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
        'phone' => [
            'required' => 'Phone number is required.',
            'max_length' => 'Phone number cannot exceed 20 characters.'
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
}
