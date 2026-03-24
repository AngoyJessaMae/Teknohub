<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QueueController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $queueQuery = Queue::with(['serviceRequest.customer.user'])
            ->where('status', 'waiting')
            ->orderBy('queue_position');

        if ($user && $user->role === 'Employee') {
            if (!$user->employee) {
                $queues = collect();
                return view('queue.index', compact('queues'));
            }

            $queueQuery->whereHas('serviceRequest', function ($query) use ($user) {
                $query->where('employee_id', $user->employee->employee_id);
            });
        }

        $queues = $queueQuery->get();

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
            'queue_number' => $maxQueuePosition + 1,
            'queue_position' => $maxQueuePosition + 1,
            'priority_level' => 'Normal',
            'queue_status' => 'waiting',
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
        $queue->update([
            'queue_number' => $validated['queue_position'],
        ]);

        return redirect()->route('queue.index')
            ->with('success', 'Queue position updated!');
    }

    public function processNext()
    {
        $user = Auth::user();

        $nextInQueueQuery = Queue::where('status', 'waiting')
            ->orderBy('queue_position');

        if ($user && $user->role === 'Employee') {
            if (!$user->employee) {
                return redirect()->route('queue.index')
                    ->with('info', 'No employee profile found for this account.');
            }

            $nextInQueueQuery->whereHas('serviceRequest', function ($query) use ($user) {
                $query->where('employee_id', $user->employee->employee_id);
            });
        }

        $nextInQueue = $nextInQueueQuery->first();

        if (!$nextInQueue) {
            return redirect()->route('queue.index')
                ->with('info', 'No items in queue!');
        }

        $serviceRequest = $nextInQueue->serviceRequest;
        $serviceRequest->update(['status' => 'in_progress']);
        $nextInQueue->update([
            'status' => 'in_progress',
            'queue_status' => 'in_progress',
        ]);

        Queue::where('status', 'waiting')
            ->where('queue_position', '>', $nextInQueue->queue_position)
            ->decrement('queue_position');

        Queue::where('status', 'waiting')
            ->where('queue_position', '>', $nextInQueue->queue_position)
            ->decrement('queue_number');

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
