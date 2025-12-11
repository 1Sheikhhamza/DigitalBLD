<?php

namespace App\Http\Controllers\Auth\Subscriber;

use App\Http\Controllers\Frontend\BaseController;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\HomeService;

class EventController extends BaseController
{
    protected $subscriberId;

    public function __construct(HomeService $homeService)
    {
        parent::__construct($homeService);
        $this->middleware(function ($request, $next) {
            $this->subscriberId = auth('subscriber')->id();
            return $next($request);
        });
    }

    public function index()
    {
        $events = Event::where('user_id', $this->subscriberId)->get();
        return view('auth.subscribers.profile.my_events', compact('events'));
    }

    public function loadEventJson()
    {
        $events = Event::where('user_id', $this->subscriberId)->get();
        return response()->json($events);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date'  => 'required|date',
            // 'end_date'    => 'nullable|date|after_or_equal:start_date',
            'start_time'  => 'nullable|date_format:H:i',
            // 'end_time'    => 'nullable|date_format:H:i|after_or_equal:start_time',
            // 'color'       => 'nullable|string|max:7', // e.g. "#ff0000"
        ]);

        $event = Event::create([
            'user_id'     => $this->subscriberId,
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_date'  => $validated['start_date'],
            // 'end_date'    => $validated['end_date']   ?? null,
            'start_time'  => $validated['start_time']  ?? null,
            // 'end_time'    => $validated['end_time']    ?? null,
            // 'color'       => $validated['color']       ?? null,
        ]);

        if (isset($request->from) && $request->from == 'page') {
            return redirect()->back()->with('success', 'Event reminder added successfully!');
        } else {
            return response()->json([
                'success' => true,
                'event'   => $event,
            ], 201);
        }
    }


    public function show(Event $event)
    {
        $this->authorizeEvent($event);
        return view('events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        $this->authorizeEvent($event);

        return view('subscriber.events.edit', compact('event'));
    }


    public function update(Request $request, Event $event)
    {
        $this->authorizeEvent($event);

        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $event->update($request->only([
            'title',
            'description',
            'start_date',
            'end_date',
            'start_time',
            'end_time',
            'color'
        ]));

        return back()->with('success', 'Event updated!');
    }

    /* public function destroy(Event $event)
    {
        $this->authorizeEvent($event);
        $event->delete();

        return response()->json(['success' => true]);
    } */

    public function destroy(Request $request, Event $event)
    {
        $this->authorizeEvent($event);
        $event->delete();

        // Check if request expects JSON (AJAX)
        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        // If normal form request, redirect back with message
        return redirect()->back()->with('success', 'Event deleted successfully!');
    }


    protected function authorizeEvent(Event $event)
    {
        if ($event->user_id !== $this->subscriberId) {
            abort(403);
        }
    }
}
