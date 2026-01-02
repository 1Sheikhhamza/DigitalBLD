@extends('auth.subscribers.layouts.app')

@section('content')
    <div class="content-wrapper container-fluid px-0 h-100">
        <div class="row h-100 g-0">
            <div class="col-12 h-100">
                <iframe
                    src="{{ env('AI_SERVICE_URL', 'http://127.0.0.1:8501') }}/?embed=true&user_id={{ auth('subscriber')->id() }}"
                    style="width: 100%; height: 85vh; border: none; border-radius: 10px;" allow="clipboard-write"></iframe>
            </div>
        </div>
    </div>
@endsection