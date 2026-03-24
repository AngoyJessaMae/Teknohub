<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\Customer;
use App\Models\User;
use App\Models\Employee;
use App\Models\Queue;
use App\Models\Billing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class ServiceRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'Customer') {
            $requests = ServiceRequest::where('customer_id', $user->customer->customer_id)
                ->with(['employee.user', 'billing'])
                ->latest()
                ->get();
        } elseif ($user->role === 'Employee') {
            $requests = ServiceRequest::with(['customer.user', 'billing', 'employee.user'])
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
        $customers = Customer::with('user')->get();
        $staff = Employee::with('user')->get();
        $items = \App\Models\Item::where('stock_quantity', '>', 0)->get();
        
        return view('service_requests.create', compact('customers', 'staff', 'items'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $rules = [
            'service_type' => 'required|in:diagnostic,hardware_repair,software_install,cleaning,upgrade,data_recovery',
            'device_type' => 'required|string',
            'device_description' => 'required|string',
'appointment_request' => 'nullable|date',
'priority_level' => 'nullable|string|max:50',
            'parts' => 'nullable|json',
            'staff_id' => 'nullable|exists:employees,employee_id',
        ];

        $customerType = $request->input('customer_type');
        if ($user->role !== 'Customer') {
            if ($customerType === 'existing' || empty($customerType)) {
                $rules['customer_id'] = 'required|exists:customers,customer_id';
            } elseif ($customerType === 'new') {
                $rules['new_customer_name'] = 'required|string|max:255';
                $rules['new_customer_email'] = 'required|email|unique:users,email';
                $rules['new_customer_contact'] = 'required|string|max:20';
            }
        }

        $validated = $request->validate($rules);

        $customerId = null;
        $employeeId = null;

        if ($user->role === 'Customer') {
            $customerId = $user->customer->customer_id;
            $employeeId = $validated['staff_id'] ?? null;
        } else { // Admin or Employee
            $customerType = $request->input('customer_type', 'existing');
            if ($customerType === 'existing') {
                $customerId = $validated['customer_id'] ?? null;
            } else {
                $newUser = User::create([
                    'full_name' => $validated['new_customer_name'],
                    'email' => $validated['new_customer_email'],
                    'contact_number' => $validated['new_customer_contact'],
                    'password' => Hash::make('customer123'),
                    'role' => 'Customer',
                    'account_status' => 'Active',
                ]);
                $newCustomer = Customer::create([
                    'user_id' => $newUser->user_id,
                ]);
                $customerId = $newCustomer->customer_id;
            }
            // If current user is an employee, default to them; if admin, use selected staff_id or null.
            $employeeId = $validated['staff_id'] ?? ($user->employee->employee_id ?? null);
        }

        $serviceRequest = ServiceRequest::create([
            'customer_id' => $customerId,
            'employee_id' => $employeeId,
            'service_type' => $validated['service_type'],
            'device_type' => $validated['device_type'],
            'device_description' => $validated['device_description'],
            'date_created' => Carbon::now(),
            'date_received' => Carbon::now(),
            'appointment_request' => $validated['appointment_request'] ?? null,
            'status' => 'pending',
        ]);

        // Create purchases from parts
        if (isset($validated['parts']) && $validated['parts']) {
            $parts = json_decode($validated['parts'], true);
            foreach ($parts as $partStr) {
                [$itemId, $qty] = explode(':', $partStr);
                $qty = (int) $qty;
                $item = \App\Models\Item::find($itemId);
                if ($item && $item->stock_quantity >= $qty) {
                    \App\Models\Purchase::create([
                        'item_id' => $itemId,
                        'service_id' => $serviceRequest->service_id,
                        'customer_id' => $customerId,
                        'quantity' => $qty,
                        'total_price' => $item->price * $qty,
                        'date_purchased' => Carbon::now(),
                    ]);
                    // Reduce stock
                    $item->decrement('stock_quantity', $qty);
                }
            }
        }

        $nextPosition = Queue::where('status', 'waiting')->count() + 1;
        Queue::create([
            'service_id' => $serviceRequest->service_id,
            'queue_number' => $nextPosition,
            'queue_position' => $nextPosition,
            'priority_level' => $validated['priority_level'] ?? 'Normal',
            'queue_status' => 'waiting',
            'status' => 'waiting',
        ]);

        // Calculate initial billing totals including parts
        $partsTotal = $serviceRequest->purchases()->sum('total_price');
        $laborRate = \App\Models\LaborRate::where('service_type', $validated['service_type'])->first();
        $laborFee = $laborRate ? $laborRate->standard_fee : 50.00;
        $totalAmount = $laborFee + $partsTotal;

        Billing::create([
            'service_id' => $serviceRequest->service_id,
            'employee_id' => $employeeId,
            'labor_fee' => $laborFee,
            'parts_fee' => $partsTotal,
            'total_amount' => $totalAmount,
            'payment_status' => 'Pending',
            'date_billed' => Carbon::now()->toDateString(),
        ]);

        return redirect()->route('service-requests.index')
            ->with('success', 'Service request created successfully!');
    }

    // ... rest of methods remain the same
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

        if ($validated['status'] === 'cancelled' && $serviceRequest->status !== 'cancelled') {
            if ($serviceRequest->billing) {
                $serviceRequest->billing->delete();
            }
            if ($serviceRequest->queue) {
                $serviceRequest->queue->delete();
            }
        }

        $serviceRequest->update($validated);

        if ($validated['status'] === 'in_progress' && $serviceRequest->queue) {
            $serviceRequest->queue->update(['status' => 'in_progress', 'queue_status' => 'in_progress']);
        } elseif ($validated['status'] === 'completed') {
            if (!$serviceRequest->billing) {
                Billing::create([
                    'service_id' => $serviceRequest->service_id,
                    'employee_id' => $serviceRequest->employee_id,
                    'labor_fee' => 0,
                    'parts_fee' => 0,
                    'total_amount' => 0,
                    'payment_status' => 'Pending',
                    'date_billed' => Carbon::now()->toDateString(),
                ]);
            }
            if ($serviceRequest->queue) {
                $serviceRequest->queue->update(['status' => 'completed', 'queue_status' => 'completed']);
            }
            if ($request->filled('date_completed')) {
                $serviceRequest->update(['date_completed' => $request->date_completed]);
            }
            $this->updateBillingTotal($serviceRequest);
        }

        if ($serviceRequest->billing) {
            $serviceRequest->billing->update(['employee_id' => $serviceRequest->employee_id]);
        }

        return redirect()->route('service-requests.index')
            ->with('success', 'Service request updated successfully!');
    }

    private function updateBillingTotal(ServiceRequest $serviceRequest)
    {
        $billing = $serviceRequest->billing;
        if (!$billing) return;
        $serviceRequest->fresh(); // Reload to get latest purchases
        $partsTotal = $serviceRequest->purchases->sum('total_price');
        $laborRate = \App\Models\LaborRate::where('service_type', $serviceRequest->service_type)->first();
        $laborFee = $laborRate ? $laborRate->standard_fee : 50.00;
        $billing->update([
            'labor_fee' => $laborFee,
            'parts_fee' => $partsTotal,
            'total_amount' => $laborFee + $partsTotal,
            'employee_id' => $serviceRequest->employee_id,
            'date_billed' => $billing->date_billed ?: Carbon::now()->toDateString(),
        ]);
    }

    public function destroy(ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->billing) $serviceRequest->billing->delete();
        if ($serviceRequest->queue) $serviceRequest->queue->delete();
        $serviceRequest->delete();
        return redirect()->route('service-requests.index')
            ->with('success', 'Service request deleted successfully!');
    }
}

