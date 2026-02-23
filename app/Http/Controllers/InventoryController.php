<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $items = Item::latest()->get();

        return view('inventory.index', compact('items'));
    }

    public function create()
    {
        return view('inventory.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        Item::create($validated);

        return redirect()->route('inventory.index')
            ->with('success', 'Item added to inventory successfully!');
    }

    public function edit(Item $item)
    {
        $item->load('purchases');
        return view('inventory.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $item->update($validated);

        return redirect()->route('inventory.index')
            ->with('success', 'Item updated successfully!');
    }

    public function destroy(Item $item)
    {
        $item->delete();

        return redirect()->route('inventory.index')
            ->with('success', 'Item deleted successfully!');
    }

    public function addToService(Request $request, $serviceId)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,item_id',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = Item::find($validated['item_id']);

        if ($item->stock_quantity < $validated['quantity']) {
            return back()->with('error', 'Insufficient stock!');
        }

        $totalPrice = $item->price * $validated['quantity'];

        Purchase::create([
            'item_id' => $validated['item_id'],
            'service_id' => $serviceId,
            'quantity' => $validated['quantity'],
            'total_price' => $totalPrice,
        ]);

        $item->decrement('stock_quantity', $validated['quantity']);

        return back()->with('success', 'Item added to service request!');
    }
}
