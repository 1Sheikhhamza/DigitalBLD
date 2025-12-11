@extends('admin.layouts.app')
@section('title', 'Chat with ' . $chat->subscriber->name)
@section('content')
    <div class="app-wrapper">
        @include('admin.layouts.sidebar')
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Chat with {{ $chat->subscriber->name }}</h3>
                        </div>
                        <div class="col-sm-6 text-end">
                            <a href="{{ route('admin.chats.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="app-content">
                <div class="container-fluid">
                    <div class="card" style="height: 70vh;">
                        <div class="card-body p-0 d-flex flex-column h-100">

                            <div id="chat-messages" class="flex-grow-1 p-4 bg-light" style="overflow-y: auto;">
                                @foreach($chat->messages as $msg)
                                    <div
                                        class="d-flex mb-3 {{ $msg->sender_type == 'admin' ? 'justify-content-end' : 'justify-content-start' }}">
                                        <div class="p-3 rounded shadow-sm {{ $msg->sender_type == 'admin' ? 'bg-primary text-white' : 'bg-white' }}"
                                            style="max-width: 70%;">
                                            <div class="small opacity-75 mb-1">
                                                {{ $msg->sender_type == 'admin' ? 'You' : $chat->subscriber->name }}</div>
                                            <div>{{ $msg->message }}</div>
                                            <div class="small opacity-50 text-end mt-1" style="font-size: 0.7rem;">
                                                {{ $msg->created_at->format('h:i A') }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="p-3 border-top bg-white">
                                <form id="admin-chat-form" onsubmit="sendAdminMessage(event)">
                                    <div class="input-group">
                                        <input type="text" id="admin-chat-input" class="form-control"
                                            placeholder="Type your reply..." required>
                                        <button class="btn btn-primary" type="submit"><i class="bi bi-send"></i>
                                            Send</button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </main>
        @include('admin/layouts.footer')
    </div>

    <script>
        const chatId = {{ $chat->id }};
        const checkUrl = "{{ route('admin.chats.messages', $chat->id) }}";
        const sendUrl = "{{ route('admin.chats.reply', $chat->id) }}";

        function scrollToBottom() {
            const container = document.getElementById('chat-messages');
            container.scrollTop = container.scrollHeight;
        }

        document.addEventListener('DOMContentLoaded', scrollToBottom);

        function sendAdminMessage(e) {
            e.preventDefault();
            const input = document.getElementById('admin-chat-input');
            const message = input.value;
            if (!message.trim()) return;

            fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ message: message })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        input.value = '';
                        fetchMessages();
                    }
                });
        }

        function fetchMessages() {
            fetch(checkUrl)
                .then(response => response.json())
                .then(messages => {
                    const container = document.getElementById('chat-messages');
                    container.innerHTML = ''; // Re-render all for simplicity in this version

                    messages.forEach(msg => {
                        const isMe = msg.sender_type === 'admin';
                        const div = document.createElement('div');
                        div.className = `d-flex mb-3 ${isMe ? 'justify-content-end' : 'justify-content-start'}`;

                        div.innerHTML = `
                        <div class="p-3 rounded shadow-sm ${isMe ? 'bg-primary text-white' : 'bg-white'}" style="max-width: 70%;">
                            <div class="small opacity-75 mb-1">${isMe ? 'You' : '{{ $chat->subscriber->name }}'}</div>
                            <div>${msg.message}</div>
                            <div class="small opacity-50 text-end mt-1" style="font-size: 0.7rem;">${new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>
                        </div>
                    `;
                        container.appendChild(div);
                    });
                    scrollToBottom();
                });
        }

        // Poll every 3 seconds
        setInterval(fetchMessages, 3000);

    </script>
@endsection