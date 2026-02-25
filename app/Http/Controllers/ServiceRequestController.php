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
        $user = Auth::user();
        $customers = [];
        $staff = [];
        
        // check for Employee role 
        if ($user->role === 'Employee') {
            // employees can create requests for any customer
            $customers = Customer::with('user')->get();
            // fetch all employees for assignment
            $staff = Employee::with('user')->get();
        }
        
        return view('service_requests.create', compact('customers', 'staff'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        // check required fields
        $validated = $request->validate([
            'device_type' => 'required',
            'device_description' => 'required',
        ]);

        // Handle customer based on user role
        if ($user->role === 'Employee') {
            $customerType = $request->input('customer_type', 'existing');
            
            if ($customerType === 'existing') {
                $customerId = $request->input('customer_id');
                if (!$customerId) {
                    return back()->with('error', 'Please select a customer');
                }
            } else {
                // create new customer
                $newUser = User::create([
                    'full_name' => $request->input('new_customer_name'),
                    'email' => $request->input('new_customer_email'),
                    'contact_number' => $request->input('new_customer_contact'),
                    'password' => Hash::make('customer123'),
                    'role' => 'Customer',
                    'account_status' => 'Active',
                ]);
                
                $newCustomer = Customer::create([
                    'user_id' => $newUser->user_id,
                ]);
                
                $customerId = $newCustomer->customer_id;
            }
            
            // assign staff
            $employeeId = $request->input('staff_id');
            if (!$employeeId) {
                $employeeId = $user->employee->employee_id;
            }
        } else {
            // customer create own request
            $customerId = $user->customer->customer_id;
            $employeeId = null;
        }

        // Create sr
        $serviceRequest = ServiceRequest::create([
            'customer_id' => $customerId,
            'employee_id' => $employeeId,
            'device_type' => $request->device_type,
            'device_description' => $request->device_description,
            'date_created' => Carbon::now(),
            'status' => 'pending',
        ]);

        // create queue entry
        $nextPosition = Queue::where('status', 'waiting')->count() + 1;

        Queue::create([
            'service_id' => $serviceRequest->service_id,
            'queue_position' => $nextPosition,
            'status' => 'waiting',
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

        // If status is changing to cancelled, delete the billing
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
            $serviceRequest->queue->update(['status' => 'in_progress']);
        } elseif ($validated['status'] === 'completed') {
            // Create billing if it doesn't exist
            if (!$serviceRequest->billing) {
                Billing::create([
                    'service_id' => $serviceRequest->service_id,
                    'labor_fee' => 0,
                    'parts_fee' => 0,
                    'total_amount' => 0,
                    'payment_status' => 'pending',
                ]);
            }
            
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
        // Delete billing if exists
        if ($serviceRequest->billing) {
            $serviceRequest->billing->delete();
        }
        
        // delete queue if exists
        if ($serviceRequest->queue) {
            $serviceRequest->queue->delete();
        }
        
        $serviceRequest->delete();

        return redirect()->route('service-requests.index')
            ->with('success', 'Service request deleted successfully!');
    }
}
