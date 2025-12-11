<?php

namespace App\Http\Controllers\Auth\Subscriber;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // Start or get existing chat
    public function store(Request $request)
    {
        try {
            \Log::info('Chat store request received', ['input' => $request->all(), 'user_id' => Auth::guard('subscriber')->id()]);

            $request->validate([
                'message' => 'required|string',
            ]);

            $subscriberId = Auth::guard('subscriber')->id();

            if (!$subscriberId) {
                \Log::error('Subscriber ID not found in session');
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            // Check if open chat exists
            $chat = Chat::firstOrCreate(
                ['subscriber_id' => $subscriberId, 'status' => 'open']
            );

            \Log::info('Chat retrieved/created', ['chat_id' => $chat->id]);

            ChatMessage::create([
                'chat_id' => $chat->id,
                'sender_type' => 'subscriber',
                'sender_id' => $subscriberId,
                'message' => $request->message,
                'is_read' => false,
            ]);

            \Log::info('Message created successfully');

            $chat->touch();

            return response()->json(['success' => true, 'chat_id' => $chat->id]);
        } catch (\Exception $e) {
            \Log::error('Chat store error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // Check for new messages
    public function fetchMessages()
    {
        $subscriberId = Auth::guard('subscriber')->id();
        $chat = Chat::where('subscriber_id', $subscriberId)
            ->where('status', 'open')
            ->first();

        if (!$chat) {
            return response()->json([]);
        }

        // Mark admin messages as read
        ChatMessage::where('chat_id', $chat->id)
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = $chat->messages;
        return response()->json($messages);
    }
}
