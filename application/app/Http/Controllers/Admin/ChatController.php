<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // List all chats
    public function index()
    {
        $chats = Chat::with([
            'subscriber',
            'messages' => function ($q) {
                $q->latest()->limit(1);
            }
        ])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('admin.chats.index', compact('chats'));
    }

    // Show a single chat
    public function show($id)
    {
        $chat = Chat::with(['subscriber', 'messages'])->findOrFail($id);

        // Mark admin as assigned if not already
        if (!$chat->admin_id) {
            $chat->admin_id = Auth::id();
            $chat->save();
        }

        // Mark messages as read
        ChatMessage::where('chat_id', $chat->id)
            ->where('sender_type', 'subscriber')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('admin.chats.show', compact('chat'));
    }

    // Reply to a chat
    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $chat = Chat::findOrFail($id);

        ChatMessage::create([
            'chat_id' => $chat->id,
            'sender_type' => 'admin',
            'sender_id' => Auth::id(),
            'message' => $request->message,
            'is_read' => false,
        ]);

        $chat->touch(); // Update updated_at of chat

        return response()->json(['success' => true]);
    }

    // AJAX fetch new messages
    public function fetchMessages($id)
    {
        $chat = Chat::findOrFail($id);

        // Mark messages as read
        ChatMessage::where('chat_id', $chat->id)
            ->where('sender_type', 'subscriber')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = $chat->messages()->with('chat.subscriber')->get();

        return response()->json($messages);
    }
}
