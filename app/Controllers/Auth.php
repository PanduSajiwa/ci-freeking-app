<?php
namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function __construct()
    {
        // Skip authentication check for Auth controller
    }
    
    public function login()
    {
        // Jika sudah login, redirect ke route yang sesuai berdasarkan role
        if (session()->get('logged_in')) {
            $role = session()->get('role');
            if ($role === 'admin') {
                return redirect()->to('/users');
            } elseif ($role === 'employee') {
                return redirect()->to('/parkingsubmissions');
            }
            return redirect()->to('/dashboard');
        }
        
        // Ensure we detect POST regardless of case returned by the server
        if (strtolower($this->request->getMethod()) === 'post') {
            // Debug logging to help trace login flow
            log_message('debug', 'Auth::login POST received');
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');
            log_message('debug', 'Auth::login credentials: username=' . ($username ?? '[empty]'));

            $userModel = new UserModel();
            $user = $userModel->where('username', $username)->first();

            if ($user && password_verify($password, $user['password'])) {
                if ($user['is_active']) {
                    $sessionData = [
                        'user_id' => $user['id'],
                        'username' => $user['username'],
                        'full_name' => $user['full_name'],
                        'role' => $user['role'],
                        'logged_in' => true
                    ];
                    session()->set($sessionData);

                    log_message('debug', 'Auth::login success for user_id=' . $user['id']);

                    // Redirect based on role
                    $role = $user['role'];
                    if ($role === 'admin') {
                        $target = '/users';
                    } elseif ($role === 'employee') {
                        $target = '/parkingsubmissions';
                    } else {
                        $target = '/dashboard';
                    }

                    return redirect()->to($target)->with('success', 'Login berhasil!');
                } else {
                    log_message('debug', 'Auth::login failed: account not active for username=' . $username);
                    return redirect()->back()->with('error', 'Akun tidak aktif');
                }
            } else {
                log_message('debug', 'Auth::login failed: invalid credentials for username=' . $username);
                return redirect()->back()->with('error', 'Username atau password salah');
            }
        }
        
        $data = [
            'title' => 'Login - Free Parking System'
        ];
        
        return view('auth/login', $data);
    }
    
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login')->with('success', 'Logout berhasil!');
    }
}