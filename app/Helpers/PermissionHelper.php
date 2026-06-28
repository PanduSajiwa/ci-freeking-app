<?php

namespace App\Helpers;

class PermissionHelper
{
    /**
     * Check if user has required role
     * @param string|array $requiredRoles
     * @return bool
     */
    public static function hasRole($requiredRoles)
    {
        $userRole = session()->get('role');

        if (is_array($requiredRoles)) {
            return in_array($userRole, $requiredRoles);
        }

        return $userRole === $requiredRoles;
    }

    /**
     * Check if user is Employee
     * @return bool
     */
    public static function isEmployee()
    {
        return session()->get('role') === 'employee';
    }

    /**
     * Check if user is Operation Manager
     * @return bool
     */
    public static function isOperationManager()
    {
        return session()->get('role') === 'operation_manager';
    }

    /**
     * Check if user is Admin/Parking Department
     * @return bool
     */
    public static function isAdmin()
    {
        return session()->get('role') === 'admin';
    }

    /**
     * Check if user can approve requests
     * @return bool
     */
    public static function canApprove()
    {
        return in_array(session()->get('role'), ['operation_manager', 'admin', 'parking_dept']);
    }

    /**
     * Check if user can manage users
     * @return bool
     */
    public static function canManageUsers()
    {
        return session()->get('role') === 'admin';
    }

    /**
     * Check if user can create submissions
     * @return bool
     */
    public static function canCreateSubmission()
    {
        return in_array(session()->get('role'), ['employee', 'admin']);
    }

    /**
     * Check if user can view all submissions
     * @return bool
     */
    public static function canViewAllSubmissions()
    {
        return in_array(session()->get('role'), ['operation_manager', 'admin']);
    }

    /**
     * Check if user can manage parking quota
     * @return bool
     */
    public static function canManageQuota()
    {
        return session()->get('role') === 'admin';
    }

    /**
     * Check if user can view reports
     * @return bool
     */
    public static function canViewReports()
    {
        return in_array(session()->get('role'), ['admin', 'parking_dept']);
    }

    /**
     * Check if user can manage customers
     * @return bool
     */
    public static function canManageCustomers()
    {
        return in_array(session()->get('role'), ['employee', 'admin', 'parking_dept']);
    }

    /**
     * Check if user can manage vehicles
     * @return bool
     */
    public static function canManageVehicles()
    {
        return in_array(session()->get('role'), ['employee', 'admin']);
    }

    /**
     * Check if user can manage parking usage
     * @return bool
     */
    public static function canManageParkingUsage()
    {
        return session()->get('role') === 'admin';
    }

    /**
     * Check if user owns the submission
     * @param int $submittedById
     * @return bool
     */
    public static function ownsSubmission($submittedById)
    {
        return session()->get('user_id') == $submittedById;
    }

    /**
     * Check if user owns the customer record
     * @param int $createdById
     * @return bool
     */
    public static function ownsCustomer($createdById)
    {
        return session()->get('user_id') == $createdById;
    }

    /**
     * Check if user owns the vehicle record
     * @param int $createdById
     * @return bool
     */
    public static function ownsVehicle($createdById)
    {
        return session()->get('user_id') == $createdById;
    }

    /**
     * Get permission message for denied access
     * @param string $action
     * @return string
     */
    public static function getDeniedMessage($action = 'access this feature')
    {
        return 'Anda tidak memiliki izin untuk ' . $action;
    }
}
