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



        if ($role === 'Customer') {
            $customerRequests = collect();
            $totalRequests = 0;
            $pendingRepairs = 0;
            $completedRepairs = 0;

            if ($user->customer) {
                $customerId = $user->customer->customer_id;
                $customerRequests = ServiceRequest::where('customer_id', $customerId)
                    ->with(['employee.user', 'billing'])
                    ->latest()
                    ->take(5)
                    ->get();

                $totalRequests = ServiceRequest::where('customer_id', $customerId)->count();
                $pendingRepairs = ServiceRequest::where('customer_id', $customerId)->where('status', 'pending')->count();
                $completedRepairs = ServiceRequest::where('customer_id', $customerId)->where('status', 'completed')->count();
            }

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
            $stats = ServiceRequest::selectRaw('
                COUNT(*) as totalRequests,
                SUM(CASE WHEN status = \'pending\' THEN 1 ELSE 0 END) as pendingRepairs,
                SUM(CASE WHEN status = \'completed\' THEN 1 ELSE 0 END) as completedRepairs
            ')->first();

            $totalRevenue = Billing::where('payment_status', 'Paid')->sum('total_amount');

            $recentRequests = ServiceRequest::with(['customer.user', 'employee.user'])
                ->latest()
                ->take(5)
                ->get();

            $userStats = User::selectRaw('
                COUNT(*) as totalUsers,
                SUM(CASE WHEN role = \'Employee\' THEN 1 ELSE 0 END) as totalEmployees,
                SUM(CASE WHEN role = \'Employee\' AND account_status = \'Pending\' THEN 1 ELSE 0 END) as pendingEmployeeApprovals,
                SUM(CASE WHEN role = \'Customer\' THEN 1 ELSE 0 END) as totalCustomers
            ')->first();

            return view('dashboard.admin', compact(
                'stats',
                'totalRevenue',
                'recentRequests',
                'userStats'
            ));
        }

        return view('dashboard.default');
    }
}