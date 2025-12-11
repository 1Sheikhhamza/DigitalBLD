<?php

namespace App\Http\Controllers\Auth\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\BlogLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogInteractionController extends Controller
{
    public function storeComment(Request $request, $blogId)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:blog_comments,id'
        ]);

        $subscriberId = Auth::guard('subscriber')->id();

        if (!$subscriberId) {
            return redirect()->back()->with('error', 'You must be logged in to comment.');
        }

        BlogComment::create([
            'blog_id' => $blogId,
            'parent_id' => $request->parent_id,
            'subscriber_id' => $subscriberId,
            'comment' => $request->comment,
            'is_approved' => true // Auto-approve for now
        ]);

        return redirect()->back()->with('success', 'Comment posted successfully.');
    }

    public function deleteComment($id)
    {
        $comment = BlogComment::findOrFail($id);

        if ($comment->subscriber_id != Auth::guard('subscriber')->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted successfully.');
    }

    public function toggleLike(Request $request, $blogId)
    {
        $request->validate([
            'action' => 'required|in:like,dislike',
        ]);

        $subscriberId = Auth::guard('subscriber')->id();

        if (!$subscriberId) {
            return response()->json(['status' => 'error', 'message' => 'Login required.'], 401);
        }

        $isLike = $request->action === 'like';

        $existingInteraction = BlogLike::where('blog_id', $blogId)
            ->where('subscriber_id', $subscriberId)
            ->first();

        if ($existingInteraction) {
            if ($existingInteraction->is_like == $isLike) {
                // Toggle off (remove)
                $existingInteraction->delete();
                $status = 'removed';
            } else {
                // Switch (like -> dislike or vice versa)
                $existingInteraction->update(['is_like' => $isLike]);
                $status = 'updated';
            }
        } else {
            // Create new
            BlogLike::create([
                'blog_id' => $blogId,
                'subscriber_id' => $subscriberId,
                'is_like' => $isLike
            ]);
            $status = 'created';
        }

        // Return new counts
        $blog = Blog::withCount([
            'likes as liked_count' => function ($query) {
                $query->where('is_like', true);
            },
            'likes as disliked_count' => function ($query) {
                $query->where('is_like', false);
            }
        ])->find($blogId);

        return response()->json([
            'status' => 'success',
            'action' => $status,
            'likes' => $blog->liked_count,
            'dislikes' => $blog->disliked_count
        ]);
    }
}
