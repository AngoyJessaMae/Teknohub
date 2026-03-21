<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\ServiceRequest;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'Admin') {
            abort(403);
        }

        $month = $request->get('month', 'current');
        $startDate = $endDate = now();

        if ($month === 'current') {
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
        } // Add other months logic

        $totalRevenue = Billing::where('payment_status', 'Paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        $totalRequests = ServiceRequest::whereBetween('date_created', [$startDate, $endDate])
            ->count();

        $statusCounts = ServiceRequest::whereBetween('date_created', [$startDate, $endDate])
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $monthlyRevenue = []; // Calculate for charts

        return view('reports.index', compact('totalRevenue', 'totalRequests', 'statusCounts', 'monthlyRevenue'));
    }
}

