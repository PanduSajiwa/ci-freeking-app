<?php
namespace App\Models;

use CodeIgniter\Model;

class ParkingQuotaModel extends Model
{
    protected $table = 'parking_quota_management';
    protected $primaryKey = 'id';
    protected $allowedFields = ['month_year', 'total_quota', 'used_quota', 'created_by'];
    // Timestamps disabled temporarily to avoid missing `created_at`/`updated_at` DB errors.
    protected $useTimestamps = false;
    
    public function getCurrentMonthQuota()
    {
        $currentMonth = date('Y-m');
        return $this->where('month_year', $currentMonth)->first();
    }
    
    public function updateUsedQuota($additionalQuota = 0)
    {
        $currentMonth = date('Y-m');
        $quota = $this->where('month_year', $currentMonth)->first();
        
        if ($quota) {
            $newUsedQuota = $quota['used_quota'] + $additionalQuota;
            return $this->update($quota['id'], ['used_quota' => $newUsedQuota]);
        }
        
        return false;
    }
}