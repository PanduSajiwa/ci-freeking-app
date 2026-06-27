<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ParkingSubmissionModel;
use App\Helpers\PermissionHelper;

class Users extends BaseController
{
    protected $userModel;
    protected $submissionModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->submissionModel = new ParkingSubmissionModel();
        // Only Admin can manage users
        if (!PermissionHelper::canManageUsers()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }
    
    public function index()
    {
        $data = [
            'title' => 'Manajemen Pengguna',
            'users' => $this->userModel->findAll()
        ];
        
        return view('users/index', $data);
    }
    
    public function create()
    {
        if (strtolower($this->request->getMethod()) === 'post') {
            $data = [
                'username' => $this->request->getPost('username'),
                'password' => $this->request->getPost('password'),
                'full_name' => $this->request->getPost('full_name'),
                'email' => $this->request->getPost('email'),
                'role' => $this->request->getPost('role'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];
            
            if ($this->userModel->insert($data)) {
                return redirect()->to('/users')->with('success', 'User berhasil ditambahkan');
            } else {
                $errors = $this->userModel->errors();
                if (!empty($errors)) {
                    log_message('error', 'Users::create insert failed - ' . json_encode($errors));
                }
                return redirect()->back()
                    ->with('error', 'Gagal menambahkan user')
                    ->with('validation', $errors)
                    ->withInput();
            }
        }
        
        $data['title'] = 'Tambah User';
        return view('users/create', $data);
    }
    
    public function edit($id)
    {
        if (strtolower($this->request->getMethod()) === 'post') {
            $data = [
                'username' => $this->request->getPost('username'),
                'full_name' => $this->request->getPost('full_name'),
                'email' => $this->request->getPost('email'),
                'role' => $this->request->getPost('role'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];
            
            // Jika password diisi, update password
            if ($this->request->getPost('password')) {
                $data['password'] = $this->request->getPost('password');
            }
            
            // Prevent unique username conflict on update: ensure no other user has this username
            $existing = $this->userModel->where('username', $data['username'])->first();
            if ($existing && $existing['id'] != $id) {
                return redirect()->back()->with('error', 'Gagal mengupdate user')->with('validation', ['username' => 'Username sudah digunakan oleh pengguna lain'])->withInput();
            }

            if ($this->userModel->update($id, $data)) {
                return redirect()->to('/users')->with('success', 'User berhasil diupdate');
            } else {
                $errors = $this->userModel->errors();
                if (!empty($errors)) {
                    return redirect()->back()->with('error', 'Gagal mengupdate user')->with('validation', $errors)->withInput();
                }
                return redirect()->back()->with('error', 'Gagal mengupdate user')->withInput();
            }
        }
        
        $user = $this->userModel->find($id);
        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        
        $data = [
            'title' => 'Edit User',
            'user' => $user
        ];
        
        return view('users/edit', $data);
    }
    
    public function delete($id = null)
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Jika ID tidak provided, coba ambil dari segment URL
        if ($id === null) {
            $id = $this->request->getUri()->getSegment(3);
        }
        
        if (!$id) {
            return redirect()->to('/users')->with('error', 'ID user tidak valid');
        }
        
        // Cegah user menghapus dirinya sendiri
        if ($id == session()->get('user_id')) {
            return redirect()->to('/users')->with('error', 'Tidak dapat menghapus akun sendiri');
        }
        
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/users')->with('error', 'User tidak ditemukan');
        }
        
        // Cek apakah user dapat dihapus (tidak memiliki relasi)
        if ($this->userModel->canDelete($id)) {
            // Hard delete - user tidak memiliki relasi
            if ($this->userModel->delete($id)) {
                return redirect()->to('/users')->with('success', 'User berhasil dihapus');
            } else {
                return redirect()->to('/users')->with('error', 'Gagal menghapus user');
            }
        } else {
            // Soft delete - user memiliki relasi, nonaktifkan saja
            if ($this->userModel->softDelete($id)) {
                return redirect()->to('/users')->with('success', 'User dinonaktifkan karena memiliki data historis');
            } else {
                return redirect()->to('/users')->with('error', 'Gagal menonaktifkan user');
            }
        }
    }
    
    public function toggleStatus($id = null)
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Jika ID tidak provided, coba ambil dari segment URL
        if ($id === null) {
            $id = $this->request->getUri()->getSegment(3);
        }
        
        if (!$id) {
            return redirect()->to('/users')->with('error', 'ID user tidak valid');
        }
        
        // Cegah user menonaktifkan dirinya sendiri
        if ($id == session()->get('user_id')) {
            return redirect()->to('/users')->with('error', 'Tidak dapat menonaktifkan akun sendiri');
        }
        
        $user = $this->userModel->find($id);
        if ($user) {
            $newStatus = $user['is_active'] ? 0 : 1;
            $this->userModel->update($id, ['is_active' => $newStatus]);
            
            $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()->back()->with('success', "User berhasil $statusText");
        }
        
        return redirect()->back()->with('error', 'User tidak ditemukan');
    }
    
    public function forceDelete($id = null)
    {
        // Hanya untuk admin super dalam keadaan emergency
        if (!in_array(session()->get('role'), ['admin'])) {
            return redirect()->to('/users')->with('error', 'Akses ditolak');
        }
        
        if ($id === null) {
            $id = $this->request->getUri()->getSegment(3);
        }
        
        if (!$id) {
            return redirect()->to('/users')->with('error', 'ID user tidak valid');
        }
        
        if ($id == session()->get('user_id')) {
            return redirect()->to('/users')->with('error', 'Tidak dapat menghapus akun sendiri');
        }
        
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/users')->with('error', 'User tidak ditemukan');
        }
        
        // Hapus semua relasi terlebih dahulu
        $this->submissionModel->where('submitted_by', $id)->set(['submitted_by' => null])->update();
        $this->submissionModel->where('operation_manager_id', $id)->set(['operation_manager_id' => null])->update();
        $this->submissionModel->where('parking_dept_id', $id)->set(['parking_dept_id' => null])->update();
        
        // Sekarang hapus user
        if ($this->userModel->delete($id)) {
            return redirect()->to('/users')->with('success', 'User berhasil dihapus paksa (dengan relasi)');
        } else {
            return redirect()->to('/users')->with('error', 'Gagal menghapus user');
        }
    }
}