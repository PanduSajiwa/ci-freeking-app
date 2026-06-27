<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class BaseController extends Controller
{
    protected $helpers = ['auth', 'form', 'url'];
    
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        // Cek authentication untuk semua controller yang extend BaseController
        // Kecuali Auth controller
        if (!session()->get('logged_in') && !$this->isAuthController()) {
            return redirect()->to('/auth/login');
        }
    }
    
    protected function isAuthController()
    {
        $controller = $this->request->getUri()->getSegment(1);
        return $controller === 'auth';
    }
    
    protected function checkPermission($allowedRoles)
    {
        $userRole = session()->get('role');
        if (!in_array($userRole, $allowedRoles)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }
}