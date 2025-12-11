@extends('admin.layouts.app')
@section('title', 'Support Chats')
@section('content')
    <div class="app-wrapper">
        @include('admin.layouts.sidebar')
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Support Chats</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="app-content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Active Conversations</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Subscriber</th>
                                            <th>Last Message</th>
                                            <th>Status</th>
                                            <th>Last Updated</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($chats as $chat)
                                            <tr>
                                                <td>
                                                    {{ $chat->subscriber->name }}<br>
                                                    <small class="text-muted">{{ $chat->subscriber->email }}</small>
                                                </td>
                                                <td>
                                                    {{ Str::limit($chat->messages->first()?->message ?? 'No messages', 50) }}
                                                </td>
                                                <td>
                                                    @if($chat->messages->where('is_read', false)->where('sender_type', 'subscriber')->count() > 0)
                                                        <span class="badge bg-danger">New Message</span>
                                                    @else
                                                        <span class="badge bg-success">Open</span>
                                                    @endif
                                                </td>
                                                <td>{{ $chat->updated_at->diffForHumans() }}</td>
                                                <td>
                                                    <a href="{{ route('admin.chats.show', $chat->id) }}"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="bi bi-chat-dots"></i> Reply
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="p-3">
                                    {{ $chats->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        @include('admin/layouts.footer')
    </div>
@endsection