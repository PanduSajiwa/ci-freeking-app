<?php
namespace App\Controllers;

use App\Models\ParkingUsageModel;
use App\Models\ParkingSubmissionModel;
use App\Helpers\PermissionHelper;

class ParkingUsage extends BaseController
{
    protected $usageModel;
    protected $submissionModel;

    public function __construct()
    {
        $this->usageModel = new ParkingUsageModel();
        $this->submissionModel = new ParkingSubmissionModel();
    }

    public function index()
    {
        // Only Admin can manage parking usage
        if (!PermissionHelper::canManageParkingUsage()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $monthYear = $this->request->getGet('month_year') ?? date('Y-m');
        
        $data = [
            'title' => 'Tracking Penggunaan Parkir',
            'usageData' => $this->usageModel->getMonthlyUsage($monthYear),
            'currentMonth' => $monthYear,
            'approvedSubmissions' => $this->submissionModel->where('status', 'approved')->findAll()
        ];
        
        return view('parking_usage/index', $data);
    }
    
    public function recordUsage()
    {
        // Only Admin can record parking usage
        if (!PermissionHelper::canManageParkingUsage()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $data = [
                'submission_id' => $this->request->getPost('submission_id'),
                'usage_date' => $this->request->getPost('usage_date'),
                'notes' => $this->request->getPost('notes')
            ];
            
            if ($this->usageModel->insert($data)) {
                return redirect()->to('/parkingusage')->with('success', 'Penggunaan parkir berhasil dicatat');
            } else {
                return redirect()->back()->with('error', 'Gagal mencatat penggunaan parkir');
            }
        }
    }
    
    public function delete($id)
    {
        // Only Admin can delete parking usage records
        if (!PermissionHelper::canManageParkingUsage()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (strtolower($this->request->getMethod()) !== 'post') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($this->usageModel->delete($id)) {
            return redirect()->back()->with('success', 'Data penggunaan berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus data penggunaan');
        }
    }
}