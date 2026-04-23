<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Mark a specific notification as read and redirect to its link.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\RedirectResponse
     */
    public function read(Notification $notification)
    {
        // Ensure the notification belongs to the authenticated user
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Mark the notification as read if it's unread
        if (is_null($notification->read_at)) {
            $notification->update(['read_at' => now()]);
        }

        // Redirect to the link associated with the notification, if one exists
        if ($notification->link) {
            return redirect($notification->link);
        }

        // If there's no link, redirect back to the previous page
        return back();
    }
}
