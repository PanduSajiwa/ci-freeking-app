<?php
use App\Controllers\Auth;
use App\Controllers\Dashboard;
use App\Controllers\Customers;
use App\Controllers\Vehicles;
use App\Controllers\ParkingSubmissions;
use App\Controllers\Users;
use App\Controllers\Reports;
use App\Controllers\ParkingUsage;

// Default route - redirect to login if not authenticated, otherwise to dashboard
$routes->get('/', function() {
    if (session()->get('logged_in')) {
        return redirect()->to('/dashboard');
    } else {
        return redirect()->to('/auth/login');
    }
});

// Auth routes - should be accessible without login
$routes->get('/auth/login', 'Auth::login');
$routes->post('/auth/login', 'Auth::login');
$routes->get('/auth/logout', 'Auth::logout');

// Protected routes - require login
$routes->get('/dashboard', 'Dashboard::index');

// All DELETE routes (with auth filter, using explicit parameter passing)
$routes->match(['POST'], '/customers/delete/(:num)', 'Customers::delete/$1', ['filter' => 'auth']);
$routes->match(['POST'], '/vehicles/delete/(:num)', 'Vehicles::delete/$1', ['filter' => 'auth']);
$routes->match(['POST'], '/users/delete/(:num)', 'Users::delete/$1', ['filter' => 'auth']);
$routes->match(['POST'], '/users/toggleStatus/(:num)', 'Users::toggleStatus/$1', ['filter' => 'auth']);
$routes->match(['POST'], '/parkingsubmissions/delete/(:num)', 'ParkingSubmissions::delete/$1', ['filter' => 'auth']);
$routes->match(['POST'], '/parkingusage/delete/(:num)', 'ParkingUsage::delete/$1', ['filter' => 'auth']);

// Master Data - require login and specific roles
$routes->group('', ['filter' => 'auth'], function($routes) {
    
    // Customers
    $routes->get('/customers', 'Customers::index');
    $routes->get('/customers/create', 'Customers::create');
    $routes->post('/customers/create', 'Customers::create');
    $routes->get('/customers/edit/(:num)', 'Customers::edit/$1');
    $routes->post('/customers/edit/(:num)', 'Customers::edit/$1');

    // Vehicles
    $routes->get('/vehicles', 'Vehicles::index');
    $routes->get('/vehicles/create', 'Vehicles::create');
    $routes->post('/vehicles/create', 'Vehicles::create');
    $routes->get('/vehicles/edit/(:num)', 'Vehicles::edit/$1');
    $routes->post('/vehicles/edit/(:num)', 'Vehicles::edit/$1');

    // Users (Admin only)
    $routes->get('/users', 'Users::index');
    $routes->get('/users/create', 'Users::create');
    $routes->post('/users/create', 'Users::create');
    $routes->get('/users/edit/(:num)', 'Users::edit/$1');
    $routes->post('/users/edit/(:num)', 'Users::edit/$1');

    // Parking Submissions
    $routes->get('/parkingsubmissions', 'ParkingSubmissions::index');
    $routes->get('/parkingsubmissions/create', 'ParkingSubmissions::create');
    $routes->post('/parkingsubmissions/create', 'ParkingSubmissions::create');
    $routes->get('/parkingsubmissions/approval', 'ParkingSubmissions::approvalList');
    $routes->get('/parkingsubmissions/view/(:num)', 'ParkingSubmissions::view/$1');
    $routes->get('/parkingsubmissions/approve/(:num)', 'ParkingSubmissions::approve/$1');
    $routes->post('/parkingsubmissions/approve/(:num)', 'ParkingSubmissions::approveWithQuota/$1');
    $routes->get('/parkingsubmissions/reject/(:num)', 'ParkingSubmissions::reject/$1');
    $routes->post('/parkingsubmissions/reject/(:num)', 'ParkingSubmissions::reject/$1');
    $routes->post('/parkingsubmissions/terminate/(:num)', 'ParkingSubmissions::terminate/$1');

    // Parking Usage
    $routes->get('/parkingusage', 'ParkingUsage::index');
    $routes->post('/parkingusage/record', 'ParkingUsage::recordUsage');

    // Reports
    $routes->get('/reports', 'Reports::index');
    $routes->post('/reports/generate', 'Reports::generate');
});