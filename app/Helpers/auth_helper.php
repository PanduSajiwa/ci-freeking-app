<?php
// app/Helpers/auth_helper.php

if (!function_exists('getUserRole')) {
    function getUserRole()
    {
        return session()->get('role');
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin()
    {
        return session()->get('role') === 'admin';
    }
}

if (!function_exists('isOperationManager')) {
    function isOperationManager()
    {
        return session()->get('role') === 'operation_manager';
    }
}

if (!function_exists('isParkingDept')) {
    function isParkingDept()
    {
        return session()->get('role') === 'parking_dept';
    }
}

if (!function_exists('isEmployee')) {
    function isEmployee()
    {
        return session()->get('role') === 'employee';
    }
}

if (!function_exists('hasPermission')) {
    function hasPermission($allowedRoles)
    {
        $userRole = session()->get('role');
        return in_array($userRole, $allowedRoles);
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date, $format = 'd/m/Y')
    {
        if (!$date) return '-';
        return date($format, strtotime($date));
    }
}

if (!function_exists('getStatusBadge')) {
    function getStatusBadge($status)
    {
        $badges = [
            'draft' => 'secondary',
            'submitted' => 'warning',
            'under_review' => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
            'completed' => 'primary'
        ];
        return $badges[$status] ?? 'secondary';
    }
}

if (!function_exists('getRoleBadge')) {
    function getRoleBadge($role)
    {
        $badges = [
            'admin' => 'danger',
            'operation_manager' => 'warning',
            'parking_dept' => 'info',
            'employee' => 'success'
        ];
        return $badges[$role] ?? 'secondary';
    }
}