<?php

namespace App\Models;

use CodeIgniter\Model;

class ContactModel extends Model
{
    protected $table            = 'contacts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'email', 'phone', 'properties_in', 'message', 'is_read'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'name'          => 'required|max_length[255]',
        'email'         => 'required|valid_email',
        'phone'         => 'required|max_length[20]',
        'properties_in' => 'required|max_length[255]',
        'message'       => 'required',
    ];
    protected $validationMessages   = [];
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
     * Get unread contacts
     */
    public function getUnread(int $limit = null)
    {
        $builder = $this->where('is_read', false)
                       ->orderBy('created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
    }

    /**
     * Mark contact as read
     */
    public function markAsRead(int $id): bool
    {
        return $this->update($id, ['is_read' => true]);
    }

    /**
     * Get contacts count by read status
     */
    public function getCountByStatus(bool $isRead = false): int
    {
        return $this->where('is_read', $isRead)->countAllResults();
    }

    /**
     * Get recent contacts
     */
    public function getRecent(int $limit = 10)
    {
        return $this->orderBy('created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }
}
