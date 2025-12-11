<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $announcements = Announcement::latest()->get();
        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        $subscribers = Subscriber::all();
        return view('admin.announcements.create', compact('subscribers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required',
            'target_type' => 'required|in:all,individual',
            'user_id' => 'required_if:target_type,individual'
        ]);

        Announcement::create($request->all());

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement created successfully.');
    }

    public function edit(Announcement $announcement)
    {
        $subscribers = Subscriber::all();
        return view('admin.announcements.edit', compact('announcement', 'subscribers'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'message' => 'required',
            'target_type' => 'required|in:all,individual',
            'user_id' => 'required_if:target_type,individual'
        ]);

        $announcement->update($request->all());

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return redirect()->route('admin.announcements.index')->with('success', 'Announcement deleted successfully.');
    }
}
