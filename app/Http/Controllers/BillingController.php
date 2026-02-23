<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index()
    {
        $billings = Billing::with(['serviceRequest.customer.user'])
            ->latest()
            ->get();

        return view('billing.index', compact('billings'));
    }

    public function show(Billing $billing)
    {
        $billing->load(['serviceRequest.customer.user', 'serviceRequest.purchases.item']);

        return view('billing.show', compact('billing'));
    }

    public function updatePaymentStatus(Request $request, Billing $billing)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:paid,unpaid,pending',
        ]);

        $billing->update($validated);

        return redirect()->route('billing.index')
            ->with('success', 'Payment status updated successfully!');
    }
}
