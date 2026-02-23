<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Queue;
use App\Models\Billing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class ServiceRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'customer') {
            $requests = ServiceRequest::where('customer_id', $user->customer->customer_id)
                ->with(['employee.user', 'billing'])
                ->latest()
                ->get();
        } elseif ($user->role === 'employee') {
            $requests = ServiceRequest::where('employee_id', $user->employee->employee_id)
                ->with(['customer.user', 'billing'])
                ->latest()
                ->get();
        } else {
            $requests = ServiceRequest::with(['customer.user', 'employee.user', 'billing'])
                ->latest()
                ->get();
        }

        return view('service_requests.index', compact('requests'));
    }

    public function create()
    {
        return view('service_requests.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'device_type' => 'required|string|max:100',
            'device_description' => 'required|string',
        ]);

        $serviceRequest = ServiceRequest::create([
            'customer_id' => Auth::user()->customer->customer_id,
            'device_type' => $validated['device_type'],
            'device_description' => $validated['device_description'],
            'date_created' => Carbon::now(),
            'status' => 'pending',
        ]);

        $nextPosition = Queue::where('status', 'waiting')->count() + 1;

        Queue::create([
            'service_id' => $serviceRequest->service_id,
            'queue_position' => $nextPosition,
            'status' => 'waiting',
        ]);

        Billing::create([
            'service_id' => $serviceRequest->service_id,
            'labor_fee' => 0,
            'parts_fee' => 0,
            'total_amount' => 0,
            'payment_status' => 'pending',
        ]);

        return redirect()->route('service-requests.index')
            ->with('success', 'Service request created successfully!');
    }

    public function show(ServiceRequest $serviceRequest)
    {
        $serviceRequest->load(['customer.user', 'employee.user', 'queue', 'purchases.item', 'billing']);

        return view('service_requests.show', compact('serviceRequest'));
    }

    public function edit(ServiceRequest $serviceRequest)
    {
        $employees = Employee::with('user')->get();

        return view('service_requests.edit', compact('serviceRequest', 'employees'));
    }

    public function update(Request $request, ServiceRequest $serviceRequest)
    {
        $validated = $request->validate([
            'employee_id' => 'nullable|exists:employees,employee_id',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'date_completed' => 'nullable|date',
        ]);

        $serviceRequest->update($validated);

        if ($validated['status'] === 'in_progress' && $serviceRequest->queue) {
            $serviceRequest->queue->update(['status' => 'in_progress']);
        } elseif ($validated['status'] === 'completed') {
            if ($serviceRequest->queue) {
                $serviceRequest->queue->update(['status' => 'completed']);
            }

            if ($request->filled('date_completed')) {
                $serviceRequest->update(['date_completed' => $request->date_completed]);
            }

            $this->updateBillingTotal($serviceRequest);
        }

        return redirect()->route('service-requests.index')
            ->with('success', 'Service request updated successfully!');
    }

    private function updateBillingTotal(ServiceRequest $serviceRequest)
    {
        $billing = $serviceRequest->billing;
        $partsTotal = $serviceRequest->purchases->sum('total_price');
        $laborFee = $billing->labor_fee ?: 50.00;

        $billing->update([
            'labor_fee' => $laborFee,
            'parts_fee' => $partsTotal,
            'total_amount' => $laborFee + $partsTotal,
        ]);
    }

    public function destroy(ServiceRequest $serviceRequest)
    {
        $serviceRequest->delete();

        return redirect()->route('service-requests.index')
            ->with('success', 'Service request deleted successfully!');
    }
}
