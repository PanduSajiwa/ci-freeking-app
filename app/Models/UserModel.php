<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'password', 'full_name', 'email', 'role', 'is_active'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];
    
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        } else {
            // Jika password kosong pada update, hapus dari data
            unset($data['data']['password']);
        }
        return $data;
    }
    
    // Validation rules
    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
        'password' => 'required|min_length[6]',
        'full_name' => 'required|min_length[3]|max_length[100]',
        'email' => 'permit_empty|valid_email',
        'role' => 'required|in_list[admin,employee,operation_manager,parking_dept]'
    ];
    
    public function verifyPassword($password, $hashedPassword)
    {
        return password_verify($password, $hashedPassword);
    }
    
    // Soft delete method
    public function softDelete($id)
    {
        return $this->update($id, ['is_active' => 0]);
    }
    
    // Get only active users
    public function getActiveUsers()
    {
        return $this->where('is_active', 1)->findAll();
    }
    
    // Check if user can be safely deleted
    public function canDelete($userId)
    {
        $submissionModel = new \App\Models\ParkingSubmissionModel();
        $hasSubmissions = $submissionModel->where('submitted_by', $userId)->countAllResults() > 0;
        
        return !$hasSubmissions;
    }
}