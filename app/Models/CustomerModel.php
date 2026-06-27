<?php
namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nik', 'full_name', 'phone', 'email', 'address', 'company', 'created_by'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    
    // Validation rules
    protected $validationRules = [
        'nik' => 'required|min_length[16]|max_length[16]',
        'full_name' => 'required|min_length[3]|max_length[100]',
        'email' => 'permit_empty|valid_email'
    ];
    
    public function getUpdateValidationRules($id)
    {
        return [
            'nik' => "required|min_length[16]|max_length[16]|is_unique[customers.nik,id,{$id}]",
            'full_name' => 'required|min_length[3]|max_length[100]',
            'email' => 'permit_empty|valid_email'
        ];
    }
    
    public function getWithSubmissions($userId = null)
    {
        $builder = $this->select('customers.*, COUNT(parking_submissions.id) as total_submissions')
                        ->join('parking_submissions', 'parking_submissions.customer_id = customers.id', 'left')
                        ->groupBy('customers.id');

        if (!is_null($userId)) {
            $builder->where('customers.created_by', $userId);
        }

        return $builder->findAll();
    }
    
    public function getTopCustomers($limit = 10)
    {
        return $this->select('customers.*, COUNT(parking_submissions.id) as submission_count')
                   ->join('parking_submissions', 'parking_submissions.customer_id = customers.id')
                   ->groupBy('customers.id')
                   ->orderBy('submission_count', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }
}