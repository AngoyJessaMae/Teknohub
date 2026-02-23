<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function index()
    {
        $queues = Queue::with(['serviceRequest.customer.user'])
            ->where('status', 'waiting')
            ->orderBy('queue_position')
            ->get();

        return view('queue.index', compact('queues'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:service_requests,service_id',
        ]);

        $maxQueuePosition = Queue::max('queue_position');

        $queue = Queue::create([
            'service_id' => $validated['service_id'],
            'queue_position' => $maxQueuePosition + 1,
            'status' => 'waiting',
        ]);

        return redirect()->route('queue.index')
            ->with('success', 'Service request added to the queue!');
    }


    public function updatePosition(Request $request, Queue $queue)
    {
        $validated = $request->validate([
            'queue_position' => 'required|integer|min:1',
        ]);

        $queue->update($validated);

        return redirect()->route('queue.index')
            ->with('success', 'Queue position updated!');
    }

    public function processNext()
    {
        $nextInQueue = Queue::where('status', 'waiting')
            ->orderBy('queue_position')
            ->first();

        if (!$nextInQueue) {
            return redirect()->route('queue.index')
                ->with('info', 'No items in queue!');
        }

        $serviceRequest = $nextInQueue->serviceRequest;
        $serviceRequest->update(['status' => 'in_progress']);
        $nextInQueue->update(['status' => 'in_progress']);

        Queue::where('status', 'waiting')
            ->where('queue_position', '>', $nextInQueue->queue_position)
            ->decrement('queue_position');

        return redirect()->route('service-requests.edit', $serviceRequest)
            ->with('success', 'Processing next service request!');
    }

    public function destroy(Queue $queue)
    {
        $queue->delete();

        return redirect()->route('queue.index')
            ->with('success', 'Service request removed from the queue!');
    }
}
