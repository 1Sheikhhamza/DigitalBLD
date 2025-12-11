@extends('layouts.app')

@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection

@section('title', "Welcome to Digital Bangladesh Legal Decisions")

@section('content')
<style>
    .force-bullet {
        list-style: disc !important;
        margin-left: 20px !important;
        padding-left: 0 !important;
        display: block !important;
    }
    .force-bullet li {
        margin-bottom: 5px;
    }
</style>

<main class="main">
    {{-- 🔹 Automatic Dynamic Section Rendering --}}
    @foreach($homepageSections as $key => $section)
        @if(($section['display'] ?? 1) == 1)
            {{-- Try to include matching Blade if exists --}}
            @includeIf("frontend.homepage.$key", ['section' => $section])
        @endif
    @endforeach
</main>
@endsection

@section('footer')
    @parent
@endsection


