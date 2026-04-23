<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\ServiceRequest;
use App\Models\LaborRate;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BillingController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'Customer') {
            $billings = Billing::whereHas('serviceRequest', function ($query) use ($user) {
                $query->where('customer_id', $user->customer->customer_id);
            })->with(['serviceRequest.customer.user', 'employee.user'])->latest()->get();
        } elseif ($user->role === 'Employee') {
            $billings = Billing::where('employee_id', $user->employee->employee_id)
                ->with(['serviceRequest.customer.user', 'employee.user'])
                ->latest()
                ->get();
        } else {
            $billings = Billing::with(['serviceRequest.customer.user', 'employee.user'])
                ->latest()
                ->get();
        }

        return view('billing.index', compact('billings'));
    }


    public function create(ServiceRequest $serviceRequest)
    {
        // no billing for cancelled requests
        if ($serviceRequest->status === 'cancelled') {
            return redirect()->route('service-requests.show', $serviceRequest)
                ->with('error', 'Cannot create billing for cancelled service request.');
        }

        $serviceRequest->load(['customer.user', 'employee.user', 'purchases.item', 'billing']);

        $partsTotal = $serviceRequest->purchases->sum('total_price');
        $laborRate = \App\Models\LaborRate::where('service_type', $serviceRequest->service_type)->first();
        $suggestedLaborFee = $laborRate ? $laborRate->standard_fee : 50.00;
        $billingEmployeeId = Auth::user()->employee ? Auth::user()->employee->employee_id : $serviceRequest->employee_id;
        $laborFee = $serviceRequest->billing ? $serviceRequest->billing->labor_fee : $suggestedLaborFee;
        $totalAmount = $laborFee + $partsTotal;

        return view('billing.create', compact('serviceRequest', 'laborFee', 'partsTotal', 'totalAmount', 'suggestedLaborFee', 'billingEmployeeId'));
    }

    //store billing info
    public function store(Request $request, ServiceRequest $serviceRequest)
    {
        // no billing for cancelled requests
        if ($serviceRequest->status === 'cancelled') {
            return redirect()->route('service-requests.show', $serviceRequest)
                ->with('error', 'Cannot create billing for cancelled service request.');
        }

        $validated = $request->validate([
            'labor_fee' => 'required|numeric|min:0',
            'parts_fee' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'warranty' => 'nullable|string|max:255',
            'payment_mode' => 'required|in:Cash,Credit Card,Debit Card,G-Cash,PayMaya,Bank Transfer',
            'payment_status' => 'required|in:Paid,Unpaid,Pending',
            'payment_date' => 'nullable|date',
        ]);

        // Create billing 
        $billing = $serviceRequest->billing;
        $billingEmployeeId = Auth::user()->employee ? Auth::user()->employee->employee_id : $serviceRequest->employee_id;
        if (!$billing) {
            $billing = Billing::create([
                'service_id' => $serviceRequest->service_id,
                'employee_id' => $billingEmployeeId,
                'labor_fee' => $validated['labor_fee'],
                'parts_fee' => $validated['parts_fee'],
                'total_amount' => $validated['total_amount'],
                'payment_status' => 'Pending',
                'date_billed' => now()->toDateString(),
            ]);
        }

        $laborFee = $validated['labor_fee'];
        $partsFee = $validated['parts_fee'];
        $totalAmount = $validated['total_amount'];

        // update billing with payment details
        $billingEmployeeId = Auth::user()->employee ? Auth::user()->employee->employee_id : $serviceRequest->employee_id;
        $billing->update([
            'labor_fee' => $laborFee,
            'parts_fee' => $partsFee,
            'total_amount' => $totalAmount,
            'employee_id' => $billingEmployeeId,
            'warranty' => $validated['warranty'] ?? $billing->warranty,
            'payment_mode' => $validated['payment_mode'],
            'payment_status' => $validated['payment_status'],
            'date_billed' => $billing->date_billed ?? now()->toDateString(),
            'payment_date' => $validated['payment_status'] === 'Paid' ? ($validated['payment_date'] ?? now()->toDateString()) : null,
        ]);

        return redirect()->route('service-requests.show', $serviceRequest)
            ->with('success', 'Billing generated successfully!');
    }

    public function show(Billing $billing)
    {
        $billing->load(['serviceRequest.customer.user', 'serviceRequest.purchases.item']);

        return view('billing.show', compact('billing'));
    }

    public function updatePaymentStatus(Request $request, Billing $billing)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:paid,unpaid,pending,Paid,Unpaid,Pending',
        ]);

        // Normalize to title case
        $validated['payment_status'] = ucfirst(strtolower($validated['payment_status']));

        // If status is changing to 'Paid', also set the payment date if not already set
        if ($validated['payment_status'] === 'Paid' && is_null($billing->payment_date)) {
            $validated['payment_date'] = now();
        }

        $billing->update($validated);

        $message = "Your payment for service #{$billing->service_id} has been marked as {$billing->payment_status}.";
        if ($billing->payment_status === 'Paid') {
            $message = "Your payment for service #{$billing->service_id} has been confirmed. You can now view your official receipt.";
        }

        // Notify customer
        Notification::create([
            'user_id' => $billing->serviceRequest->customer->user_id,
            'message' => $message,
            'link' => route('billing.show', $billing),
        ]);

        return redirect()->route('billing.show', $billing)
            ->with('success', 'Payment status updated successfully!');
    }

    //delete billing for cancelled sr
    public function deleteForCancelled(Billing $billing)
    {
        $serviceRequest = $billing->serviceRequest;

        $billing->delete();

        return redirect()->route('service-requests.show', $serviceRequest)
            ->with('success', 'Billing deleted successfully!');
    }

    //remove the del billing from storage
    public function destroy(Billing $billing)
    {
        $serviceRequest = $billing->serviceRequest;

        $billing->delete();

        return redirect()->route('service-requests.show', $serviceRequest)
            ->with('success', 'Billing deleted successfully!');
    }

    public function submitPayment(Request $request, Billing $billing)
    {
        $validated = $request->validate([
            'payment_mode' => 'required|in:Cash,Credit Card,Debit Card,G-Cash,PayMaya,Bank Transfer',
            'receipt' => [
                Rule::requiredIf(fn($input) => $input['payment_mode'] !== 'Cash'),
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:2048'
            ],
        ]);

        $updateData = [
            'payment_mode' => $validated['payment_mode'],
            'payment_status' => 'Pending',
            'payment_date' => now(),
        ];

        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
            $updateData['receipt_path'] = $receiptPath;
        }

        $billing->update($updateData);

        // Notify admins
        $admins = User::where('role', 'Admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'message' => "A new payment has been submitted for service #{$billing->service_id}.",
                'link' => route('billing.show', $billing),
            ]);
        }

        return response()->json(['success' => 'Payment submitted successfully! Please wait for verification.']);
    }

    public function showReceipt(Billing $billing)
    {
        // Ensure that only the customer or an admin/employee can view the receipt
        $this->authorize('view', $billing);

        return view('billing.receipt', compact('billing'));
    }
}
