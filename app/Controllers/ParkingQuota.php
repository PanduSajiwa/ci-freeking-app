<?php
namespace App\Controllers;

use App\Models\ParkingQuotaModel;
use App\Models\ParkingSubmissionModel;
use App\Helpers\PermissionHelper;

class ParkingQuota extends BaseController
{
    protected $quotaModel;
    protected $submissionModel;

    public function __construct()
    {
        $this->quotaModel = new ParkingQuotaModel();
        $this->submissionModel = new ParkingSubmissionModel();
    }

    public function index()
    {
        // Only Admin can manage parking quota
        if (!PermissionHelper::canManageQuota()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => 'Manajemen Kuota Parkir',
            'currentQuota' => $this->quotaModel->getCurrentMonthQuota(),
            'monthlyQuotas' => $this->quotaModel->orderBy('month_year', 'DESC')->findAll(),
            'pendingSubmissions' => $this->submissionModel->where('parking_dept_approval', 'pending')
                                                         ->where('operation_manager_approval', 'approved')
                                                         ->countAllResults()
        ];
        
        return view('parking_quota/index', $data);
    }
    
    public function manage()
    {
        // Only Admin can manage parking quota
        if (!PermissionHelper::canManageQuota()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $monthYear = $this->request->getPost('month_year');
            $totalQuota = $this->request->getPost('total_quota');

            log_message('info', 'ParkingQuota::manage POST received: month_year=' . $monthYear . ' total_quota=' . $totalQuota . ' user_id=' . session()->get('user_id'));
            // log full POST and session for tracing (info level)
            log_message('info', 'ParkingQuota::manage POST data: ' . var_export($this->request->getPost(), true));
            log_message('info', 'ParkingQuota::manage session data: ' . var_export(session()->get(), true));

            $existingQuota = $this->quotaModel->where('month_year', $monthYear)->first();

            $data = [
                'month_year' => $monthYear,
                'total_quota' => $totalQuota,
                'created_by' => session()->get('user_id')
            ];

            try {
                if ($existingQuota) {
                        $res = $this->quotaModel->update($existingQuota['id'], $data);
                        log_message('info', 'ParkingQuota::manage update result: ' . var_export($res, true));
                    if ($res) {
                        return redirect()->to('/parkingquota')->with('success', 'Kuota berhasil diupdate');
                    }
                } else {
                    // ensure used_quota is initialized
                    $data['used_quota'] = 0;
                    $insertId = $this->quotaModel->insert($data);
                    log_message('info', 'ParkingQuota::manage insertId: ' . var_export($insertId, true));
                    if ($insertId) {
                        return redirect()->to('/parkingquota')->with('success', 'Kuota berhasil ditambahkan');
                    }
                }
            } catch (\Exception $e) {
                $msg = $e->getMessage();
                $trace = $e->getTraceAsString();
                log_message('error', "ParkingQuota::manage error: {$msg}");
                log_message('error', "ParkingQuota::manage trace: {$trace}");
                // also log POST and session at error time
                log_message('error', 'ParkingQuota::manage POST at error: ' . var_export($this->request->getPost(), true));
                log_message('error', 'ParkingQuota::manage session at error: ' . var_export(session()->get(), true));
                // return detailed error for debugging (temporary)
                return redirect()->back()->with('error', 'Gagal mengelola kuota: ' . $msg)->with('error_debug', $trace);
            }

            log_message('warning', 'ParkingQuota::manage reached fallback (no insert/update) for month=' . $monthYear);
            return redirect()->back()->with('error', 'Gagal mengelola kuota');
        }
        
        $data = [
            'title' => 'Kelola Kuota Parkir',
            'currentQuota' => $this->quotaModel->getCurrentMonthQuota()
        ];
        
        return view('parking_quota/manage', $data);
    }
    
    public function updateQuota()
    {
        $submissionId = $this->request->getPost('submission_id');
        $quotaGiven = $this->request->getPost('quota_given');
        
        // Update submission dengan kuota yang diberikan
        $submissionData = [
            'quota_given' => $quotaGiven,
            'parking_dept_approval' => 'approved',
            'parking_dept_id' => session()->get('user_id'),
            'parking_dept_approval_date' => date('Y-m-d H:i:s'),
            'status' => 'approved'
        ];
        
        // aggressive logging + debug flash (info level)
        log_message('info', 'ParkingQuota::updateQuota POST data: ' . var_export($this->request->getPost(), true));
        log_message('info', 'ParkingQuota::updateQuota session data: ' . var_export(session()->get(), true));

        try {
            if ($this->submissionModel->update($submissionId, $submissionData)) {
                // Update used quota
                $this->quotaModel->updateUsedQuota($quotaGiven);

                log_message('info', 'ParkingQuota::updateQuota success for submission_id=' . $submissionId . ' quota_given=' . $quotaGiven);
                return redirect()->to('/parkingquota')->with('success', 'Kuota berhasil diberikan');
            } else {
                log_message('warning', 'ParkingQuota::updateQuota update returned false for submission_id=' . $submissionId);
                return redirect()->back()->with('error', 'Gagal memberikan kuota');
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $trace = $e->getTraceAsString();
            log_message('error', "ParkingQuota::updateQuota error: {$msg}");
            log_message('error', "ParkingQuota::updateQuota trace: {$trace}");
            log_message('error', 'ParkingQuota::updateQuota POST at error: ' . var_export($this->request->getPost(), true));
            log_message('error', 'ParkingQuota::updateQuota session at error: ' . var_export(session()->get(), true));

            return redirect()->back()->with('error', 'Gagal memberikan kuota: ' . $msg)->with('error_debug', $trace);
        }
    }
}