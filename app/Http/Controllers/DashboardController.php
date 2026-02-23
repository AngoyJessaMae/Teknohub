<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\Billing;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        // Common data for all dashboards (if applicable, or can be moved into role-specific blocks)
        $totalRequests = ServiceRequest::count();
        $pendingRepairs = ServiceRequest::where('status', 'pending')->count();
        $completedRepairs = ServiceRequest::where('status', 'completed')->count();
        $totalRevenue = Billing::where('payment_status', 'Paid')->sum('total_amount');

        if ($role === 'Customer') {
            $customerRequests = ServiceRequest::where('customer_id', $user->customer->customer_id)
                ->with(['employee.user', 'billing'])
                ->latest()
                ->take(5)
                ->get();

            return view('dashboard.customer', compact('customerRequests', 'totalRequests', 'pendingRepairs', 'completedRepairs'));
        }

        if ($role === 'Employee') {
            $employeeId = $user->employee->employee_id;

            $assignedRepairs = ServiceRequest::where('employee_id', $employeeId)
                ->with(['customer.user'])
                ->latest()
                ->get();

            $activeRepairs = ServiceRequest::where('employee_id', $employeeId)
                ->where('status', 'in_progress')
                ->count();

            $completedRepairsByEmployee = ServiceRequest::where('employee_id', $employeeId)
                ->where('status', 'completed')
                ->count();

            $managedCustomersCount = ServiceRequest::where('employee_id', $employeeId)
                ->distinct('customer_id')
                ->count('customer_id');

            return view('dashboard.employee', compact(
                'assignedRepairs',
                'activeRepairs',
                'completedRepairsByEmployee',
                'managedCustomersCount'
            ));
        }

        if ($role === 'Admin') {
            $recentRequests = ServiceRequest::with(['customer.user', 'employee.user'])
                ->latest()
                ->take(5)
                ->get();

            $totalUsers = User::count();
            $totalEmployees = User::where('role', 'Employee')->count();
            $pendingEmployeeApprovals = User::where('role', 'Employee')->where('account_status', 'Pending')->count();
            $totalCustomers = User::where('role', 'Customer')->count();

            return view('dashboard.admin', compact(
                'totalRequests',
                'pendingRepairs',
                'completedRepairs',
                'totalRevenue',
                'recentRequests',
                'totalUsers',
                'totalEmployees',
                'pendingEmployeeApprovals',
                'totalCustomers'
            ));
        }

        return view('dashboard.default');
    }
}
