<?php
namespace App\Models;

use CodeIgniter\Model;

class ParkingUsageModel extends Model
{
    protected $table = 'parking_usage';
    protected $primaryKey = 'id';
    protected $allowedFields = ['submission_id', 'usage_date', 'notes'];
    protected $useTimestamps = true;
    
    public function getUsageBySubmission($submissionId)
    {
        return $this->where('submission_id', $submissionId)->findAll();
    }
    
    public function getMonthlyUsage($monthYear)
    {
        return $this->select('parking_usage.*, parking_submissions.submission_code, customers.full_name')
                   ->join('parking_submissions', 'parking_submissions.id = parking_usage.submission_id')
                   ->join('customers', 'customers.id = parking_submissions.customer_id')
                   ->where("DATE_FORMAT(parking_usage.usage_date, '%Y-%m') = ", $monthYear)
                   ->findAll();
    }
}