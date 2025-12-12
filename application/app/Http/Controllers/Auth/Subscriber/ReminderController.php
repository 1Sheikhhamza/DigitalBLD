<?php

namespace App\Http\Controllers\Auth\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReminderController extends Controller
{
    public function markAsRead(Request $request)
    {
        $subscriberId = auth('subscriber')->id();

        if ($request->has('id')) {
            $event = Event::where('user_id', $subscriberId)->where('id', $request->id)->first();
            if ($event) {
                $event->update(['is_seen' => true]);
                return response()->json(['success' => true]);
            }
        } else {
            // Mark all as read
            Event::where('user_id', $subscriberId)
                ->where('is_seen', false)
                ->update(['is_seen' => true]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }
}
