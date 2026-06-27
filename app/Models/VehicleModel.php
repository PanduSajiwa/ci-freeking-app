<?php
namespace App\Models;

use CodeIgniter\Model;

class VehicleModel extends Model
{
    protected $table = 'vehicles';
    protected $primaryKey = 'id';
    protected $allowedFields = ['license_plate', 'vehicle_type', 'brand', 'model', 'color', 'created_by'];
    
    public function getWithSubmissions($userId = null)
    {
        $builder = $this->select('vehicles.*, COUNT(parking_submissions.id) as total_submissions')
                        ->join('parking_submissions', 'parking_submissions.vehicle_id = vehicles.id', 'left')
                        ->groupBy('vehicles.id');

        if (!is_null($userId)) {
            $builder->where('vehicles.created_by', $userId);
        }

        return $builder->findAll();
    }
}