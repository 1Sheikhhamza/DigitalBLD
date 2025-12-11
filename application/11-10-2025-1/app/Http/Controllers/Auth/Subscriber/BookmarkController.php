<?php

namespace App\Http\Controllers\Auth\Subscriber;

use App\Http\Controllers\Frontend\BaseController;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use App\Services\HomeService;

class BookmarkController extends BaseController
{

    public function __construct(HomeService $homeService)
    {
        parent::__construct($homeService);
    }

    public function index(Request $request)
    {
        $bookmarkedDecisions = auth('subscriber')->user()
            ->bookmarks()
            ->with(['decision' => function ($query) {
                $query->select('id', 'parties', 'decided_on', 'division','case_no', 'judgment')
                    ->whereNull('deleted_at'); // only include non-deleted decisions
            }])
            ->get()
            ->pluck('decision')
            ->filter(); // remove null values

        return view('auth.subscribers.profile.bookmark', compact('bookmarkedDecisions'));
    }

    public function removeBookmark(Request $request, $id)
    {
        $userId = auth('subscriber')->id();
        $bookmark = Bookmark::withTrashed()
            ->where('user_id', $userId)
            ->where('decision_id', $id)
            ->first();

        if ($bookmark) {
            $bookmark->delete();
            return redirect()->back()->with('success', 'Bookmark removed successfully!');
        }

        return redirect()->back()->with('error', 'Bookmark not found.');
    }



    public function toggle(Request $request)
    {
        $userId = auth('subscriber')->id();
        $decisionId = $request->decision_id;

        // Include soft-deleted records
        $bookmark = Bookmark::withTrashed()
            ->where('user_id', $userId)
            ->where('decision_id', $decisionId)
            ->first();


        if ($bookmark) {
            if ($bookmark->trashed()) {
                // Restore if it was soft deleted
                $bookmark->restore();
                return response()->json(['status' => 'added']);
            } else {
                // Soft delete if exists
                $bookmark->delete();
                return response()->json(['status' => 'removed']);
            }
        } else {
            // Create new
            Bookmark::create(['user_id' => $userId, 'decision_id' => $decisionId]);
            return response()->json(['status' => 'added']);
        }
    }
}
