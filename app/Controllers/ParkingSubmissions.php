<?php
namespace App\Controllers;

use App\Models\ParkingSubmissionModel;
use App\Models\CustomerModel;
use App\Models\VehicleModel;
use App\Models\ParkingQuotaModel;
use App\Helpers\PermissionHelper;

class ParkingSubmissions extends BaseController
{
    protected $submissionModel;
    protected $customerModel;
    protected $vehicleModel;

    public function __construct()
    {
        $this->submissionModel = new ParkingSubmissionModel();
        $this->customerModel = new CustomerModel();
        $this->vehicleModel = new VehicleModel();
    }

    public function index()
    {
        $userRole = session()->get('role');
        $userId = session()->get('user_id');

        $builder = $this->submissionModel->builder();
        $builder->select('parking_submissions.*, vehicles.license_plate')
                ->join('vehicles', 'vehicles.id = parking_submissions.vehicle_id');

        if ($userRole === 'employee') {
            $builder->where('parking_submissions.submitted_by', $userId);
        }

        $data = [
            'title' => 'Pengajuan Free Parking',
            'submissions' => $builder->get()->getResultArray()
        ];

        return view('parking_submissions/index', $data);
    }
    
    public function create()
    {
        // Only Employee and Admin can create submissions
        if (!PermissionHelper::canCreateSubmission()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $userId = session()->get('user_id');
            $userRole = session()->get('role');
            $submissionCode = 'FP' . date('YmdHis');

            // Check monthly limit for employees (3 per month)
            if ($userRole === 'employee') {
                $firstDay = date('Y-m-01');
                $lastDay = date('Y-m-t');
                $builder = $this->submissionModel->builder();
                $count = $builder->where('submitted_by', $userId)
                                 ->where('submission_date >=', $firstDay)
                                 ->where('submission_date <=', $lastDay)
                                 ->whereNotIn('status', ['rejected'])
                                 ->countAllResults();

                if (intval($count) >= 3) {
                    session()->setFlashdata('post_data', $this->request->getPost());
                    return redirect()->back()->with('error', 'Batas pengajuan bulanan tercapai (maks 3 pengajuan per bulan). Silakan coba bulan depan.');
                }
            }

            if ($userRole === 'employee') {
                // Employee: Manual entry mode
                $employeeName = session()->get('full_name');
                $licensePlate = trim(strtoupper($this->request->getPost('license_plate') ?? ''));
                $vehicleBrand = trim($this->request->getPost('vehicle_brand') ?? '');
                $vehicleModel = trim($this->request->getPost('vehicle_model') ?? '');
                $vehicleType = $this->request->getPost('vehicle_type') ?? 'car';
                $vehicleColor = trim($this->request->getPost('vehicle_color') ?? '');
                $durationDays = intval($this->request->getPost('duration_days') ?? 1);
                $purpose = trim($this->request->getPost('purpose') ?? 'Ajukan Parkir');

                log_message('info', "Employee submission attempt - user: $userId, plate: $licensePlate, brand: $vehicleBrand");

                // Get or create customer
                try {
                    $db = \Config\Database::connect();
                    $existingCustomer = $db->query(
                        'SELECT id FROM customers WHERE LOWER(full_name) = LOWER(?) AND created_by = ?',
                        [$employeeName, $userId]
                    )->getRow();

                    if ($existingCustomer) {
                        $customerId = $existingCustomer->id;
                    } else {
                        $customerId = $this->customerModel->insert([
                            'full_name' => $employeeName,
                            'company' => session()->get('company') ?? 'Unknown',
                            'created_by' => $userId
                        ], true);

                        if (!$customerId) {
                            log_message('error', "Failed to create customer for user $userId: " . $this->customerModel->errors());
                            throw new \Exception('Gagal membuat data karyawan');
                        }
                    }
                    log_message('info', "Customer ID for submission: $customerId");

                    // Get or create vehicle
                    $existingVehicle = $db->query(
                        'SELECT id FROM vehicles WHERE license_plate = ? AND created_by = ?',
                        [$licensePlate, $userId]
                    )->getRow();

                    if ($existingVehicle) {
                        $vehicleId = $existingVehicle->id;
                    } else {
                        $vehicleId = $this->vehicleModel->insert([
                            'license_plate' => $licensePlate,
                            'vehicle_type' => $vehicleType,
                            'brand' => $vehicleBrand,
                            'model' => $vehicleModel,
                            'color' => $vehicleColor,
                            'created_by' => $userId
                        ], true);

                        if (!$vehicleId) {
                            log_message('error', "Failed to create vehicle for user $userId: " . $this->vehicleModel->errors());
                            throw new \Exception('Gagal membuat data kendaraan');
                        }
                    }
                    log_message('info', "Vehicle ID for submission: $vehicleId");

                    // Optional file upload
                    $parkingTicket = $this->uploadImage('parking_ticket');

                    $data = [
                        'submission_code' => $submissionCode,
                        'customer_id' => $customerId,
                        'vehicle_id' => $vehicleId,
                        'submitted_by' => $userId,
                        'submission_date' => date('Y-m-d'),
                        'duration_days' => $durationDays,
                        'purpose' => $purpose,
                        'supporting_doc_image' => $parkingTicket,
                        'status' => 'submitted'
                    ];

                } catch (\Exception $e) {
                    log_message('error', "Employee submission exception: " . $e->getMessage());
                    session()->setFlashdata('post_data', $this->request->getPost());
                    return redirect()->back()->with('error', $e->getMessage());
                }
            } else {
                // Admin/Non-Employee: Dropdown selection mode
                $idCardImage = $this->uploadImage('id_card_image');
                $vehicleImage = $this->uploadImage('vehicle_image');
                $supportingDoc = $this->uploadImage('supporting_doc_image');

                $data = [
                    'submission_code' => $submissionCode,
                    'customer_id' => $this->request->getPost('customer_id'),
                    'vehicle_id' => $this->request->getPost('vehicle_id'),
                    'submitted_by' => $userId,
                    'submission_date' => date('Y-m-d'),
                    'duration_days' => $this->request->getPost('duration_days'),
                    'purpose' => $this->request->getPost('purpose'),
                    'id_card_image' => $idCardImage,
                    'vehicle_image' => $vehicleImage,
                    'supporting_doc_image' => $supportingDoc,
                    'status' => 'submitted'
                ];
            }

            try {
                log_message('info', "Inserting submission with data: " . json_encode($data));
                $submissionId = $this->submissionModel->insert($data, true);

                if ($submissionId) {
                    log_message('info', "Submission created successfully - ID: $submissionId");
                    return redirect()->to('/parkingsubmissions')->with('success', 'Pengajuan berhasil dikirim. Data Anda telah tersimpan dengan baik.');
                } else {
                    log_message('error', "Submission insert returned false. Errors: " . json_encode($this->submissionModel->errors()));
                    session()->setFlashdata('post_data', $this->request->getPost());
                    return redirect()->back()->with('error', 'Gagal menyimpan pengajuan: ' . json_encode($this->submissionModel->errors()));
                }
            } catch (\Exception $e) {
                log_message('error', "Submission exception: " . $e->getMessage());
                session()->setFlashdata('post_data', $this->request->getPost());
                return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
            }
        }
        
        // For employees, show only their own customers/vehicles
        $userRole = session()->get('role');
        $userId = session()->get('user_id');
        $userName = session()->get('full_name');

        if ($userRole === 'employee') {
            $customers = $this->customerModel->where('created_by', $userId)->findAll();
            $vehicles = $this->vehicleModel->where('created_by', $userId)->findAll();

            // Count current month submissions for the employee (excluding rejected)
            $firstDay = date('Y-m-01');
            $lastDay = date('Y-m-t');
            $builder = $this->submissionModel->builder();
            $submissionCount = $builder->where('submitted_by', $userId)
                                        ->where('submission_date >=', $firstDay)
                                        ->where('submission_date <=', $lastDay)
                                        ->whereNotIn('status', ['rejected'])
                                        ->countAllResults();
        } else {
            $customers = $this->customerModel->findAll();
            $vehicles = $this->vehicleModel->findAll();
            $submissionCount = 0;
        }

        $data = [
            'title' => 'Ajukan Free Parking',
            'customers' => $customers,
            'vehicles' => $vehicles,
            'submissionCount' => $submissionCount,
            'submissionLimit' => 3,
            'isEmployee' => $userRole === 'employee',
            'userName' => $userName
        ];
        
        return view('parking_submissions/create', $data);
    }
    
    public function approve($id)
    {
        // Only Operation Manager, Admin, and Parking Dept can approve
        if (!PermissionHelper::canApprove()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $userRole = session()->get('role');
        $submission = $this->submissionModel->find($id);

        $data = [];

        if ($userRole === 'operation_manager') {
            $data['operation_manager_approval'] = 'approved';
            $data['operation_manager_id'] = session()->get('user_id');
            $data['operation_manager_approval_date'] = date('Y-m-d H:i:s');
        } elseif ($userRole === 'admin' || $userRole === 'parking_dept') {
            $data['parking_dept_approval'] = 'approved';
            $data['parking_dept_id'] = session()->get('user_id');
            $data['parking_dept_approval_date'] = date('Y-m-d H:i:s');
            $data['quota_given'] = $this->request->getPost('quota_given');
        }

        // Set final status only when BOTH have approved
        $managerApproval = $data['operation_manager_approval'] ?? $submission['operation_manager_approval'];
        $parkingApproval = $data['parking_dept_approval'] ?? $submission['parking_dept_approval'];

        if ($managerApproval == 'approved' && $parkingApproval == 'approved') {
            $data['status'] = 'approved';
        } elseif ($data) {
            // One person approved, set status to under_review
            $data['status'] = 'under_review';
        }

        if ($this->submissionModel->update($id, $data)) {
            return redirect()->back()->with('success', 'Pengajuan berhasil disetujui');
        } else {
            return redirect()->back()->with('error', 'Gagal menyetujui pengajuan');
        }
    }
    
    public function reject($id)
    {
        // Only Operation Manager, Admin, and Parking Dept can reject
        if (!PermissionHelper::canApprove()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $userRole = session()->get('role');
        $notes = $this->request->getPost('notes');

        $data = [];

        if ($userRole === 'operation_manager') {
            $data['operation_manager_approval'] = 'rejected';
            $data['operation_manager_id'] = session()->get('user_id');
            $data['operation_manager_notes'] = $notes;
            $data['operation_manager_approval_date'] = date('Y-m-d H:i:s');
        } elseif ($userRole === 'admin' || $userRole === 'parking_dept') {
            $data['parking_dept_approval'] = 'rejected';
            $data['parking_dept_id'] = session()->get('user_id');
            $data['parking_dept_notes'] = $notes;
            $data['parking_dept_approval_date'] = date('Y-m-d H:i:s');
        }

        // If anyone rejects, mark as rejected immediately
        $data['status'] = 'rejected';

        if ($this->submissionModel->update($id, $data)) {
            return redirect()->back()->with('success', 'Pengajuan berhasil ditolak');
        } else {
            return redirect()->back()->with('error', 'Gagal menolak pengajuan');
        }
    }
    
    private function uploadImage($fieldName)
    {
        $file = $this->request->getFile($fieldName);

        if (!$file) {
            return null;
        }

        if (!$file->isValid()) {
            log_message('error', 'File upload invalid for field: ' . $fieldName . ' - ' . $file->getErrorString());
            return null;
        }

        if ($file->hasMoved()) {
            log_message('error', 'File already moved for field: ' . $fieldName);
            return null;
        }

        try {
            $newName = $file->getRandomName();
            $uploadPath = ROOTPATH . 'public/uploads';

            // Ensure upload directory exists
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            if (!$file->move($uploadPath, $newName)) {
                log_message('error', 'Failed to move file for field: ' . $fieldName);
                return null;
            }

            return $newName;
        } catch (\Exception $e) {
            log_message('error', 'Upload exception for field ' . $fieldName . ': ' . $e->getMessage());
            return null;
        }
    }

    // Tambahkan method ini ke ParkingSubmissions Controller

    public function approvalList()
    {
        // Only Operation Manager and Admin can view approval list
        if (!PermissionHelper::canApprove()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $userRole = session()->get('role');

        $builder = $this->submissionModel->builder();
        $builder->select('parking_submissions.*, users.full_name as employee_name, customers.company, customers.nik, customers.phone, customers.email, vehicles.license_plate, vehicles.vehicle_type, vehicles.brand, vehicles.model, vehicles.color')
                ->join('users', 'users.id = parking_submissions.submitted_by')
                ->join('customers', 'customers.id = parking_submissions.customer_id', 'left')
                ->join('vehicles', 'vehicles.id = parking_submissions.vehicle_id');
        
        if ($userRole === 'operation_manager') {
            $builder->where('parking_submissions.operation_manager_approval', 'pending')
                    ->where('parking_submissions.status', 'submitted');
        } elseif ($userRole === 'admin') {
            $builder->where('parking_submissions.operation_manager_approval', 'approved')
                    ->where('parking_submissions.parking_dept_approval', 'pending');
        } elseif ($userRole === 'parking_dept') {
            // Parking dept sees all submissions awaiting approval (both manager and parking dept)
            $builder->where('parking_submissions.status', 'submitted')
                    ->where('parking_submissions.parking_dept_approval', 'pending')
                    ->whereIn('parking_submissions.operation_manager_approval', ['pending', 'approved']);
        }
        
        // Also include current month quota info for views
        $quotaModel = new \App\Models\ParkingQuotaModel();
        $currentQuota = $quotaModel->getCurrentMonthQuota();
        $availableQuota = null;
        if ($currentQuota) {
            $availableQuota = intval($currentQuota['total_quota']) - intval($currentQuota['used_quota']);
        }

        $data = [
            'title' => 'Approval Pengajuan',
            'submissions' => $builder->get()->getResultArray(),
            'currentQuota' => $currentQuota,
            'availableQuota' => $availableQuota
        ];
        
        return view('parking_submissions/approval', $data);
    }

    public function view($id)
    {
        $builder = $this->submissionModel->builder();
        $submission = $builder->select('parking_submissions.*, users.full_name as employee_name, customers.company, customers.nik, customers.phone, customers.email, vehicles.license_plate, vehicles.vehicle_type, vehicles.brand, vehicles.model, vehicles.color')
                            ->join('users', 'users.id = parking_submissions.submitted_by')
                            ->join('customers', 'customers.id = parking_submissions.customer_id', 'left')
                            ->join('vehicles', 'vehicles.id = parking_submissions.vehicle_id')
                            ->where('parking_submissions.id', $id)
                            ->get()
                            ->getRowArray();
        
        if (!$submission) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        
        // Auto-expire submissions that have passed their duration
        try {
            if (!in_array($submission['status'], ['expired', 'rejected']) ) {
                $submissionDate = new \DateTime($submission['submission_date']);
                $duration = intval($submission['duration_days']);
                if ($duration > 0) {
                    $expiry = (clone $submissionDate)->modify("+{$duration} days");
                    $now = new \DateTime();
                    if ($now > $expiry) {
                        // mark as expired
                        $this->submissionModel->update($id, ['status' => 'expired']);
                        log_message('info', 'ParkingSubmissions::view marked submission id=' . $id . ' as expired (expiry was ' . $expiry->format('Y-m-d') . ')');
                        // refresh submission data for display
                        $submission['status'] = 'expired';
                    }
                }
            }
        } catch (\Exception $e) {
            // don't block page view on errors; just log
            log_message('error', 'ParkingSubmissions::view expiry check error for id=' . $id . ' - ' . $e->getMessage());
        }
        
        $data = [
            'title' => 'Detail Pengajuan',
            'submission' => $submission
        ];
        
        return view('parking_submissions/view', $data);
    }

    public function approveWithQuota($id)
    {
        if (strtolower($this->request->getMethod()) === 'post') {
            // Variable-slot approval: get requested slots from POST
            $requested = intval($this->request->getPost('quota_given'));
            if ($requested <= 0) {
                return redirect()->back()->with('error', 'Jumlah kuota tidak valid');
            }

            $db = \Config\Database::connect();
            $currentMonth = date('Y-m');

            try {
                $db->transStart();

                // Lock the quota row for update to avoid race conditions
                $row = $db->query('SELECT * FROM `parking_quota_management` WHERE `month_year` = ? FOR UPDATE', [$currentMonth])->getRowArray();
                
                // If no quota exists for this month, auto-create with unlimited quota (999999 slots)
                if (!$row) {
                    $db->table('parking_quota_management')->insert([
                        'month_year' => $currentMonth,
                        'total_quota' => 999999, // Auto-created unlimited quota
                        'used_quota' => 0,
                        'created_by' => session()->get('user_id')
                    ]);
                    // Re-fetch the newly created quota
                    $row = $db->query('SELECT * FROM `parking_quota_management` WHERE `month_year` = ? FOR UPDATE', [$currentMonth])->getRowArray();
                }

                $available = intval($row['total_quota']) - intval($row['used_quota']);
                if ($available < $requested) {
                    $db->transComplete();
                    return redirect()->back()->with('error', 'Kuota tidak cukup. Sisa kuota: ' . $available);
                }

                // Update submission with quota_given = requested slots
                $submissionData = [
                    'parking_dept_approval' => 'approved',
                    'parking_dept_id' => session()->get('user_id'),
                    'parking_dept_approval_date' => date('Y-m-d H:i:s'),
                    'quota_given' => $requested,
                    'status' => 'approved'
                ];

                if (!$this->submissionModel->update($id, $submissionData)) {
                    $db->transComplete();
                    return redirect()->back()->with('error', 'Gagal menyetujui pengajuan');
                }

                // Update used_quota
                $newUsed = intval($row['used_quota']) + $requested;
                $db->table('parking_quota_management')->where('id', $row['id'])->update(['used_quota' => $newUsed]);

                $db->transComplete();

                // Always show success since we auto-create unlimited quota
                return redirect()->to('/parkingsubmissions/approval')->with('success', 'Pengajuan berhasil disetujui dan kuota dipakai');
            } catch (\Exception $e) {
                if ($db->transStatus() === false) {
                    $db->transRollback();
                }
                log_message('error', 'approveWithQuota error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses kuota');
            }
        }
    }

    public function delete($id)
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Only Admin can delete submissions
        if (!PermissionHelper::isAdmin()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin menghapus pengajuan');
        }

        // Get submission first to check if it exists
        $submission = $this->submissionModel->find($id);
        if (!$submission) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($this->submissionModel->delete($id)) {
            return redirect()->to('/parkingsubmissions')->with('success', 'Pengajuan berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus pengajuan');
        }
    }

    public function terminate($id)
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Only Admin can terminate
        if (!PermissionHelper::isAdmin()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menghentikan pengajuan');
        }

        $reason = $this->request->getPost('termination_reason');
        if (empty($reason)) {
            return redirect()->back()->with('error', 'Alasan penghentian wajib diisi');
        }

        $data = [
            'status' => 'terminated',
            'parking_dept_notes' => $reason,
            'parking_dept_id' => session()->get('user_id'),
            'parking_dept_approval_date' => date('Y-m-d H:i:s'),
            'parking_dept_approval' => 'terminated'
        ];

        try {
            if ($this->submissionModel->update($id, $data)) {
                log_message('info', 'ParkingSubmissions::terminate id=' . $id . ' by user=' . session()->get('user_id') . ' role=' . session()->get('role'));
                return redirect()->to('/parkingsubmissions/view/' . $id)->with('success', 'Pengajuan berhasil dihentikan');
            }
        } catch (\Exception $e) {
            log_message('error', 'ParkingSubmissions::terminate error for id=' . $id . ' - ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghentikan pengajuan: ' . $e->getMessage());
        }

        return redirect()->back()->with('error', 'Gagal menghentikan pengajuan');
    }
}