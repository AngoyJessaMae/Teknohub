<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\ServiceRequest;
use App\Models\LaborRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    public function index()
    {
        $billings = Billing::with(['serviceRequest.customer.user'])
            ->latest()
            ->get();

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
            'payment_status' => 'required|in:Paid,Unpaid,Pending',
        ]);

        $billing->update($validated);

        return redirect()->route('billing.index')
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
}
