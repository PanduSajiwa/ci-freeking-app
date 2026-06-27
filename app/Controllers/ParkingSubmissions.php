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
        $builder->select('parking_submissions.*, customers.full_name as customer_name, vehicles.license_plate')
                ->join('customers', 'customers.id = parking_submissions.customer_id')
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
            // Handle file uploads
            $idCardImage = $this->uploadImage('id_card_image');
            $vehicleImage = $this->uploadImage('vehicle_image');
            $supportingDoc = $this->uploadImage('supporting_doc_image');
            
            $submissionCode = 'FP' . date('YmdHis');

            // Enforce per-employee monthly limit: max 3 submissions per calendar month
            $userId = session()->get('user_id');
            if (session()->get('role') === 'employee') {
                $firstDay = date('Y-m-01');
                $lastDay = date('Y-m-t');
                $builder = $this->submissionModel->builder();
                $count = $builder->where('submitted_by', $userId)
                                 ->where('submission_date >=', $firstDay)
                                 ->where('submission_date <=', $lastDay)
                                 ->countAllResults();

                if (intval($count) >= 3) {
                    return redirect()->back()->with('error', 'Batas pengajuan bulanan tercapai (maks 3 pengajuan per bulan).');
                }
            }
            
            $data = [
                'submission_code' => $submissionCode,
                'customer_id' => $this->request->getPost('customer_id'),
                'vehicle_id' => $this->request->getPost('vehicle_id'),
                'submitted_by' => session()->get('user_id'),
                'submission_date' => date('Y-m-d'),
                'duration_days' => $this->request->getPost('duration_days'),
                'purpose' => $this->request->getPost('purpose'),
                'id_card_image' => $idCardImage,
                'vehicle_image' => $vehicleImage,
                'supporting_doc_image' => $supportingDoc,
                'status' => 'submitted'
            ];
            
            if ($this->submissionModel->insert($data)) {
                return redirect()->to('/parkingsubmissions')->with('success', 'Pengajuan berhasil dikirim');
            } else {
                return redirect()->back()->with('error', 'Gagal mengajukan free parking');
            }
        }
        
        // For employees, show only their own customers/vehicles
        if (session()->get('role') === 'employee') {
            $userId = session()->get('user_id');
            $customers = $this->customerModel->where('created_by', $userId)->findAll();
            $vehicles = $this->vehicleModel->where('created_by', $userId)->findAll();
            
            // Count current month submissions for the employee
            $firstDay = date('Y-m-01');
            $lastDay = date('Y-m-t');
            $builder = $this->submissionModel->builder();
            $submissionCount = $builder->where('submitted_by', $userId)
                                        ->where('submission_date >=', $firstDay)
                                        ->where('submission_date <=', $lastDay)
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
            'isEmployee' => session()->get('role') === 'employee'
        ];
        
        return view('parking_submissions/create', $data);
    }
    
    public function approve($id)
    {
        // Only Operation Manager and Admin can approve
        if (!PermissionHelper::canApprove()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $userRole = session()->get('role');
        $data = [
            'status' => 'approved'
        ];
        
        if ($userRole === 'operation_manager') {
            $data['operation_manager_approval'] = 'approved';
            $data['operation_manager_id'] = session()->get('user_id');
            $data['operation_manager_approval_date'] = date('Y-m-d H:i:s');
        } elseif ($userRole === 'admin') {
            $data['parking_dept_approval'] = 'approved';
            $data['parking_dept_id'] = session()->get('user_id');
            $data['parking_dept_approval_date'] = date('Y-m-d H:i:s');
            $data['quota_given'] = $this->request->getPost('quota_given');
        }
        
        if ($this->submissionModel->update($id, $data)) {
            return redirect()->back()->with('success', 'Pengajuan berhasil disetujui');
        } else {
            return redirect()->back()->with('error', 'Gagal menyetujui pengajuan');
        }
    }
    
    public function reject($id)
    {
        // Only Operation Manager and Admin can reject
        if (!PermissionHelper::canApprove()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $userRole = session()->get('role');
        $notes = $this->request->getPost('notes');
        
        $data = [
            'status' => 'rejected'
        ];
        
        if ($userRole === 'operation_manager') {
            $data['operation_manager_approval'] = 'rejected';
            $data['operation_manager_id'] = session()->get('user_id');
            $data['operation_manager_notes'] = $notes;
            $data['operation_manager_approval_date'] = date('Y-m-d H:i:s');
        } elseif ($userRole === 'admin') {
            $data['parking_dept_approval'] = 'rejected';
            $data['parking_dept_id'] = session()->get('user_id');
            $data['parking_dept_notes'] = $notes;
            $data['parking_dept_approval_date'] = date('Y-m-d H:i:s');
        }
        
        if ($this->submissionModel->update($id, $data)) {
            return redirect()->back()->with('success', 'Pengajuan berhasil ditolak');
        } else {
            return redirect()->back()->with('error', 'Gagal menolak pengajuan');
        }
    }
    
    private function uploadImage($fieldName)
    {
        $file = $this->request->getFile($fieldName);
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads', $newName);
            return $newName;
        }
        
        return null;
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
        $builder->select('parking_submissions.*, customers.full_name, customers.company, customers.nik, customers.phone, customers.email, vehicles.license_plate, vehicles.vehicle_type, vehicles.brand, vehicles.model, vehicles.color')
                ->join('customers', 'customers.id = parking_submissions.customer_id')
                ->join('vehicles', 'vehicles.id = parking_submissions.vehicle_id');
        
        if ($userRole === 'operation_manager') {
            $builder->where('parking_submissions.operation_manager_approval', 'pending')
                    ->where('parking_submissions.status', 'submitted');
        } elseif ($userRole === 'admin') {
            $builder->where('parking_submissions.operation_manager_approval', 'approved')
                    ->where('parking_submissions.parking_dept_approval', 'pending');
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
        $submission = $builder->select('parking_submissions.*, customers.full_name as customer_name, customers.company, customers.nik, customers.phone, customers.email, vehicles.license_plate, vehicles.vehicle_type, vehicles.brand, vehicles.model, vehicles.color')
                            ->join('customers', 'customers.id = parking_submissions.customer_id')
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