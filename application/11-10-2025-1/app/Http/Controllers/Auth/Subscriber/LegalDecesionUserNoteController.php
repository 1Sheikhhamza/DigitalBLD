<?php

namespace App\Http\Controllers\Auth\Subscriber;

use App\Http\Controllers\Frontend\BaseController;
use App\Models\DecisionComment;
use App\Models\DecisionShare;
use App\Models\LegalDecisionUserNote;
use App\Models\OCRExtraction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Services\HomeService;
use Exception;
use Illuminate\Support\Facades\Log;

class LegalDecesionUserNoteController extends BaseController
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

    public function editDecisionNote($id, $folderId)
    {
        $myNotes = LegalDecisionUserNote::where([['user_id', $this->subscriberId], ['id', $id]])->first();
        return view('auth.subscribers.profile.edit_decision_note', compact('myNotes','folderId'));
    }

    public function updateDecisionNote(Request $request, $id)
    {
        try {
            $request->validate([
                'notes' => 'required|string',
            ]);

            $note = LegalDecisionUserNote::updateOrCreate(
                [
                    'user_id' => $this->subscriberId,
                    'id' => $id,
                ],
                [
                    'notes' => $request->input('notes'),
                ]
            );

            return redirect()->route('subscriber.myDecision', Crypt::encrypt($request->folderId))->with('success', 'Your note has been saved!');
        } catch (Exception $e) {
            Log::write("Edit Note Error: ", $e);
        }
    }


    public function addComment(Request $request)
    {
        $request->validate([
            'request_id' => 'required|integer',
            'decision_id' => 'required|integer',
            'comment' => 'required|string',
        ]);

        $comment = DecisionComment::create([
            'decision_id' => $request->decision_id,
            'request_id' => $request->request_id,
            'user_id' => $this->subscriberId,
            'comment' => $request->comment,
        ]);

        // Return JSON if using AJAX
        return response()->json([
            'status' => 'Comment added successfully!',
            'comment' => $comment->load('user'),
        ]);
    }

    // app/Http/Controllers/DecisionCommentController.php

    public function update(Request $request)
    {
        $request->validate([
            'comment_id' => 'required|integer',
            'comment' => 'required|string',
        ]);

        $comment = DecisionComment::findOrFail($request->comment_id);

        if ($comment->user_id !== $this->subscriberId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->comment = $request->comment;
        $comment->save();

        return response()->json(['status' => 'updated', 'comment' => $comment]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'comment_id' => 'required|integer',
        ]);

        $comment = DecisionComment::findOrFail($request->comment_id);

        if ($comment->user_id !== $this->subscriberId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['status' => 'deleted']);
    }

    //// Share to another user

    public function share(Request $request)
    {
        $request->validate([
            'request_id' => 'required|integer',
            'decision_id' => 'required|integer',
            'receiver_id' => 'required|integer',
        ]);

        // Check if already shared
        $alreadyShared = DecisionShare::where('decision_id', $request->decision_id)
            ->where('sender_id', $this->subscriberId)
            ->where('receiver_id', $request->receiver_id)
            ->where('request_id', $request->request_id)
            ->exists();

        if ($alreadyShared) {
            return response()->json([
                'status' => 'fail',
                'message' => 'This decision has already been shared with the selected user.',
            ]);
        }

        // Create new share
        $share = DecisionShare::create([
            'decision_id' => $request->decision_id,
            'request_id' => $request->request_id,
            'sender_id' => $this->subscriberId,
            'receiver_id' => $request->receiver_id,
        ]);

        return response()->json(['status' => 'success', 'share' => $share]);
    }

    public function revoke($id)
    {
        $shared = DecisionShare::findOrFail($id);

        // Optional: check authorization
        if ($shared->sender_id !== $this->subscriberId) {
            abort(403);
        }

        $shared->delete();

        return back()->with('success', 'Sharing revoked successfully.');
    }



    public function sharedWithMe()
    {
        $sharedDecisions = DecisionShare::where('receiver_id', $this->subscriberId)
            ->with('decision', 'sender')
            ->latest()
            ->get();

        return view('auth.subscribers.profile.shared_decision', compact('sharedDecisions'));
    }
}
