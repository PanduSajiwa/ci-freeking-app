<?php
namespace App\Controllers;

use App\Models\VehicleModel;
use App\Helpers\PermissionHelper;

class Vehicles extends BaseController
{
    protected $vehicleModel;

    public function __construct()
    {
        $this->vehicleModel = new VehicleModel();
        // Only Employee and Admin can manage vehicles
        if (!PermissionHelper::canManageVehicles()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }
    
    public function index()
    {
        $data = [
            'title' => 'Master Data Kendaraan',
            'vehicles' => (session()->get('role') === 'employee')
                ? $this->vehicleModel->getWithSubmissions(session()->get('user_id'))
                : $this->vehicleModel->getWithSubmissions()
        ];
        
        return view('vehicles/index', $data);
    }
    
    public function create()
    {
        // Only Admin can create vehicles (Employees can only read)
        if (!PermissionHelper::isAdmin()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $data = [
                'license_plate' => $this->request->getPost('license_plate'),
                'vehicle_type' => $this->request->getPost('vehicle_type'),
                'brand' => $this->request->getPost('brand'),
                'model' => $this->request->getPost('model'),
                'color' => $this->request->getPost('color')
            ];

            $data['created_by'] = session()->get('user_id');
            
            if ($this->vehicleModel->insert($data)) {
                return redirect()->to('/vehicles')->with('success', 'Data kendaraan berhasil ditambahkan');
            } else {
                return redirect()->back()->with('error', 'Gagal menambahkan data kendaraan')->withInput();
            }
        }
        
        $data['title'] = 'Tambah Kendaraan';
        return view('vehicles/create', $data);
    }
    
    public function edit($id)
    {
        // Only Admin can edit vehicles (Employees can only read)
        if (!PermissionHelper::isAdmin()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $data = [
                'license_plate' => $this->request->getPost('license_plate'),
                'vehicle_type' => $this->request->getPost('vehicle_type'),
                'brand' => $this->request->getPost('brand'),
                'model' => $this->request->getPost('model'),
                'color' => $this->request->getPost('color')
            ];
            
            if ($this->vehicleModel->update($id, $data)) {
                return redirect()->to('/vehicles')->with('success', 'Data kendaraan berhasil diupdate');
            } else {
                return redirect()->back()->with('error', 'Gagal mengupdate data kendaraan')->withInput();
            }
        }
        
        $vehicle = $this->vehicleModel->find($id);
        if (!$vehicle) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (session()->get('role') === 'employee' && $vehicle['created_by'] != session()->get('user_id')) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        
        $data = [
            'title' => 'Edit Kendaraan',
            'vehicle' => $vehicle
        ];
        
        return view('vehicles/edit', $data);
    }
    
    public function delete($id)
    {
        // Only Admin can delete vehicles
        if (!PermissionHelper::isAdmin()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (strtolower($this->request->getMethod()) !== 'post') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $vehicle = $this->vehicleModel->find($id);
        if (!$vehicle) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        
        if ($this->vehicleModel->delete($id)) {
            return redirect()->to('/vehicles')->with('success', 'Data kendaraan berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus data kendaraan');
        }
    }
}