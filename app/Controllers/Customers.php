<?php
namespace App\Controllers;

use App\Models\CustomerModel;
use App\Helpers\PermissionHelper;

class Customers extends BaseController
{
    protected $customerModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        // Only Employee and Admin can manage customers
        if (!PermissionHelper::canManageCustomers()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }
    
    public function index()
    {
        $data = [
            'title' => 'Master Data Karyawan',
            'customers' => (session()->get('role') === 'employee')
                ? $this->customerModel->getWithSubmissions(session()->get('user_id'))
                : $this->customerModel->getWithSubmissions()
        ];
        
        return view('customers/index', $data);
    }
    
    public function create()
    {
        // Only Admin can create customers (Employees can only read)
        if (!PermissionHelper::isAdmin()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $data = [
                'nik' => $this->request->getPost('nik'),
                'full_name' => $this->request->getPost('full_name'),
                'phone' => $this->request->getPost('phone'),
                'email' => $this->request->getPost('email'),
                'address' => $this->request->getPost('address'),
                'company' => $this->request->getPost('company')
            ];
            // assign owner when created by an employee or any user
            $data['created_by'] = session()->get('user_id');
            
            // Attempt insert and return validation errors if any
            if ($this->customerModel->insert($data)) {
                return redirect()->to('/customers')->with('success', 'Data customer berhasil ditambahkan');
            } else {
                $errors = $this->customerModel->errors();
                if (!empty($errors)) {
                    return redirect()->back()->with('error', 'Gagal menambahkan data customer')->with('validation', $errors)->withInput();
                }
                return redirect()->back()->with('error', 'Gagal menambahkan data customer')->withInput();
            }
        }
        
        $data['title'] = 'Tambah Karyawan';
        return view('customers/create', $data);
    }
    
    public function edit($id)
    {
        // Only Admin can edit customers (Employees can only read)
        if (!PermissionHelper::isAdmin()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $data = [
                'nik' => $this->request->getPost('nik'),
                'full_name' => $this->request->getPost('full_name'),
                'phone' => $this->request->getPost('phone'),
                'email' => $this->request->getPost('email'),
                'address' => $this->request->getPost('address'),
                'company' => $this->request->getPost('company')
            ];

            // Set update-specific validation rules (allow same NIK for current record)
            $this->customerModel->setValidationRules($this->customerModel->getUpdateValidationRules($id));

            if ($this->customerModel->update($id, $data)) {
                return redirect()->to('/customers')->with('success', 'Data customer berhasil diupdate');
            } else {
                $errors = $this->customerModel->errors();
                if (!empty($errors)) {
                    return redirect()->back()->with('error', 'Gagal mengupdate data customer')->with('validation', $errors)->withInput();
                }
                return redirect()->back()->with('error', 'Gagal mengupdate data customer')->withInput();
            }
        }
        
        $customer = $this->customerModel->find($id);
        if (!$customer) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // employees can only access their own customer records
        if (session()->get('role') === 'employee' && $customer['created_by'] != session()->get('user_id')) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        
        $data = [
            'title' => 'Edit Karyawan',
            'customer' => $customer
        ];
        
        return view('customers/edit', $data);
    }
    
    public function delete($id)
    {
        // Only Admin can delete customers
        if (!PermissionHelper::isAdmin()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (strtolower($this->request->getMethod()) !== 'post') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $customer = $this->customerModel->find($id);
        if (!$customer) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        
        if ($this->customerModel->delete($id)) {
            return redirect()->to('/customers')->with('success', 'Data customer berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus data customer');
        }
    }
}