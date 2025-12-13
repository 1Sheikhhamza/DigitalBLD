<?php

namespace App\Http\Controllers;

use App\Models\DemoBooking;
use Illuminate\Http\Request;

class DemoBookingController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'booking_date' => 'required|date',
            'booking_time' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'details' => 'nullable|string',
        ]);

        DemoBooking::create($validated);

        return response()->json(['success' => true, 'message' => 'Demo booked successfully! We will contact you soon.']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = DemoBooking::latest()->paginate(10);
        return view('admin.demo_bookings.index', compact('bookings'));
    }
}
