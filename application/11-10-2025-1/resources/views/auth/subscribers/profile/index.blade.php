@extends('auth.subscribers.layouts.app')
@section('title', Auth::guard('subscriber')->user()->name.' | BLD Index')

@section('content')
<div class="container py-4">
    @include('auth.subscribers.profile.volume_nav')

    <div class="mt-4">
        @if(!empty($volumeData->index_file) && file_exists(public_path('uploads/volume/pdfs/' . $volumeData->index_file)))
            <iframe src="{{ asset('uploads/volume/pdfs/' . $volumeData->index_file) }}" width="100%" height="600px"></iframe>
        @else
            <p>File not found.</p>
        @endif
    </div>
</div>
@endsection
