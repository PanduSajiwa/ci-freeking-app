<?php
namespace App\Controllers;

use App\Models\ParkingSubmissionModel;
use App\Models\ParkingQuotaModel;
use App\Models\CustomerModel;
use App\Models\VehicleModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $submissionModel = new ParkingSubmissionModel();
        $quotaModel = new ParkingQuotaModel();
        $customerModel = new CustomerModel();
        $vehicleModel = new VehicleModel();
        
        $userRole = session()->get('role');
        $userId = session()->get('user_id');
        
        // Base stats
        $data = [
            'title' => 'Dashboard',
            'totalSubmissions' => $submissionModel->countAll(),
            'pendingSubmissions' => $submissionModel->where('status', 'submitted')->countAllResults(),
            'approvedSubmissions' => $submissionModel->where('status', 'approved')->countAllResults(),
            'currentMonthQuota' => $quotaModel->getCurrentMonthQuota(),
            'totalCustomers' => $customerModel->countAll(),
            'totalVehicles' => $vehicleModel->countAll()
        ];
        
        // Role-specific stats
        if ($userRole === 'operation_manager') {
            $data['pendingApprovals'] = $submissionModel->getPendingApprovalCount('operation_manager');
        } elseif ($userRole === 'admin') {
            $data['pendingApprovals'] = $submissionModel->getPendingApprovalCount('parking_dept');
        } elseif ($userRole === 'employee') {
            $data['mySubmissions'] = $submissionModel->where('submitted_by', $userId)->countAllResults();
            $data['myPendingSubmissions'] = $submissionModel->where('submitted_by', $userId)
                                                           ->where('status', 'submitted')
                                                           ->countAllResults();
        }
        
        // Recent submissions
        $data['recentSubmissions'] = $submissionModel->select('parking_submissions.*, customers.full_name, vehicles.license_plate')
                                                    ->join('customers', 'customers.id = parking_submissions.customer_id')
                                                    ->join('vehicles', 'vehicles.id = parking_submissions.vehicle_id')
                                                    ->orderBy('parking_submissions.created_at', 'DESC')
                                                    ->limit(5)
                                                    ->get()
                                                    ->getResultArray();
        
        return view('dashboard/index', $data);
    }
}