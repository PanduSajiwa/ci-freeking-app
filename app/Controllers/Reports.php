<?php
namespace App\Controllers;

use App\Models\ParkingSubmissionModel;
use App\Models\ParkingUsageModel;
use App\Models\ParkingQuotaModel;
use App\Helpers\PermissionHelper;

class Reports extends BaseController
{
    protected $submissionModel;
    protected $usageModel;
    protected $quotaModel;

    public function __construct()
    {
        $this->submissionModel = new ParkingSubmissionModel();
        $this->usageModel = new ParkingUsageModel();
        $this->quotaModel = new ParkingQuotaModel();

        // Only Admin can view reports
        if (!PermissionHelper::canViewReports()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }
    
    public function index()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        
        $reportsData = $this->getSubmissionReports($startDate, $endDate);
        $vehicleStats = $this->getVehicleTypeStats($startDate, $endDate);
        $quotaUsage = $this->getQuotaUsageReport();
        
        $data = [
            'title' => 'Laporan & Analytics',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'reports' => $reportsData,
            'quotaUsage' => $quotaUsage,
            'vehicleTypeStats' => $vehicleStats,
            'vehicleTypeLabels' => $this->getVehicleTypeLabels($vehicleStats),
            'vehicleTypeData' => $this->getVehicleTypeData($vehicleStats),
            'quotaLabels' => $this->getQuotaLabels($quotaUsage),
            'quotaTotalData' => $this->getQuotaTotalData($quotaUsage),
            'quotaUsedData' => $this->getQuotaUsedData($quotaUsage)
        ];
        
        return view('reports/index', $data);
    }
    
    public function generate()
    {
        $type = $this->request->getPost('report_type');
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        
        switch ($type) {
            case 'submissions':
                return $this->generateSubmissionsReport($startDate, $endDate);
            case 'quota_usage':
                return $this->generateQuotaUsageReport();
            case 'vehicle_stats':
                return $this->generateVehicleStatsReport($startDate, $endDate);
            default:
                return redirect()->back()->with('error', 'Jenis laporan tidak valid');
        }
    }
    
    private function getSubmissionReports($startDate, $endDate)
    {
        $builder = $this->submissionModel->builder();
        return $builder->select('parking_submissions.*, customers.full_name, customers.company, vehicles.license_plate, vehicles.vehicle_type')
                      ->join('customers', 'customers.id = parking_submissions.customer_id')
                      ->join('vehicles', 'vehicles.id = parking_submissions.vehicle_id')
                      ->where("parking_submissions.submission_date BETWEEN '$startDate' AND '$endDate'")
                      ->orderBy('parking_submissions.submission_date', 'DESC')
                      ->get()
                      ->getResultArray();
    }
    
    private function getQuotaUsageReport()
    {
        return $this->quotaModel->orderBy('month_year', 'DESC')->findAll(12); // 12 bulan terakhir
    }
    
    private function getVehicleTypeStats($startDate, $endDate)
    {
        $builder = $this->submissionModel->builder();
        return $builder->select('vehicles.vehicle_type, COUNT(*) as total')
                      ->join('vehicles', 'vehicles.id = parking_submissions.vehicle_id')
                      ->where("parking_submissions.submission_date BETWEEN '$startDate' AND '$endDate'")
                      ->groupBy('vehicles.vehicle_type')
                      ->get()
                      ->getResultArray();
    }
    
    private function generateSubmissionsReport($startDate, $endDate)
    {
        $data = [
            'title' => 'Laporan Pengajuan Free Parking',
            'submissions' => $this->getSubmissionReports($startDate, $endDate),
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
        
        return $this->generatePDF('reports/pdf_submissions', $data, 'laporan-pengajuan.pdf');
    }
    
    private function generatePDF($view, $data, $filename)
    {
        $dompdf = new \Dompdf\Dompdf();
        $html = view($view, $data);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream($filename);
    }
    
    // Helper methods for chart data
    private function getVehicleTypeLabels($stats)
    {
        $labels = [];
        foreach ($stats as $stat) {
            $labels[] = "'" . ucfirst($stat['vehicle_type']) . "'";
        }
        return implode(',', $labels);
    }
    
    private function getVehicleTypeData($stats)
    {
        $data = [];
        foreach ($stats as $stat) {
            $data[] = $stat['total'];
        }
        return implode(',', $data);
    }
    
    private function getQuotaLabels($quotaUsage)
    {
        $labels = [];
        foreach (array_slice($quotaUsage, 0, 6) as $quota) {
            $labels[] = "'" . date('M Y', strtotime($quota['month_year'] . '-01')) . "'";
        }
        return implode(',', $labels);
    }
    
    private function getQuotaTotalData($quotaUsage)
    {
        $data = [];
        foreach (array_slice($quotaUsage, 0, 6) as $quota) {
            $data[] = $quota['total_quota'];
        }
        return implode(',', $data);
    }
    
    private function getQuotaUsedData($quotaUsage)
    {
        $data = [];
        foreach (array_slice($quotaUsage, 0, 6) as $quota) {
            $data[] = $quota['used_quota'];
        }
        return implode(',', $data);
    }
}