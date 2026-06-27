<?php
namespace App\Models;

use CodeIgniter\Model;

class ParkingSubmissionModel extends Model
{
    protected $table = 'parking_submissions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'submission_code', 'customer_id', 'vehicle_id', 'submitted_by', 
        'submission_date', 'duration_days', 'purpose', 'id_card_image', 
        'vehicle_image', 'supporting_doc_image', 'operation_manager_approval',
        'operation_manager_id', 'operation_manager_notes', 'operation_manager_approval_date',
        'parking_dept_approval', 'parking_dept_id', 'parking_dept_notes',
        'parking_dept_approval_date', 'quota_given', 'status'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    // Auto-generate submission code before insert
    protected $beforeInsert = ['generateSubmissionCode'];
    
    protected function generateSubmissionCode(array $data)
    {
        $data['data']['submission_code'] = 'FP' . date('Ymd') . sprintf('%04d', rand(1, 9999));
        return $data;
    }
    
    public function getSubmissionStats($startDate = null, $endDate = null)
    {
        $builder = $this->builder();
        $builder->select('status, COUNT(*) as count');
        
        if ($startDate && $endDate) {
            $builder->where("submission_date BETWEEN '$startDate' AND '$endDate'");
        }
        
        $builder->groupBy('status');
        $result = $builder->get()->getResultArray();
        
        $stats = [];
        foreach ($result as $row) {
            $stats[$row['status']] = $row['count'];
        }
        
        return $stats;
    }
    
    public function getPendingApprovalCount($role)
    {
        $builder = $this->builder();
        
        if ($role === 'operation_manager') {
            $builder->where('operation_manager_approval', 'pending')
                   ->where('status', 'submitted');
        } elseif ($role === 'parking_dept') {
            $builder->where('operation_manager_approval', 'approved')
                   ->where('parking_dept_approval', 'pending');
        }
        
        return $builder->countAllResults();
    }
}