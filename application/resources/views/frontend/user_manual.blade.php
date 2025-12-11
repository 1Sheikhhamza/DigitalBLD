@extends('layouts.app')

@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection

@section('title', "Welcome to Digital Bangladesh Legal Decisions")
@section('content')

<!-- ===== Banner Section ===== -->
<section class="banner-inner-sec" style="background-image:url('/frontend/assets/img/service-page-bg.jpg')">
    <div class="banner-table">
        <div class="banner-table-cell">
            <div class="container">
                <div class="banner-inner-content text-center text-white">
                    <h2 class="banner-inner-title">User Manual</h2>
                    <div class="search-bar-container">
    <form method="GET" action="">
        <div class="input-group search-input-group position-relative" style="align-items:center;">
            <div class="inputArea">
                <input type="text" class="form-control" name="keywords" id="UserManualKeywords" placeholder="Search by any keywords...">
                <button type="submit" class="bi-search">&nbsp;</button>
            </div>
            <div class="clearBtn">
                <a href="javascript:void()" class="text-danger" onclick="clearContentSearch()"> Clear </a>
            </div>
        </div>
    </form>
</div>

                </div>
            </div>
        </div>
    </div>
</section>


<section class="service-inner-sec single-service-sec section-padding py-5 bg-light">
    <div class="container">

        <div class="accordion shadow-sm" id="faqAccordion">
            @foreach($user_manual as $index => $manual)
            <div class="accordion-item border-0 mb-3 rounded-3 overflow-hidden">
                <h2 class="accordion-header" id="heading{{ $index }}">
                    <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }} fw-semibold" type="button"
                        data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}"
                        aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                        aria-controls="collapse{{ $index }}">
                        {{ $manual->question }}
                    </button>
                </h2>
                <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                    aria-labelledby="heading{{ $index }}" data-bs-parent="#manualAccordion">
                    <div class="accordion-body">
                        {!! $manual->answer !!}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection

@section('footer')
@parent
@endsection

<style>
    .search-bar-container {
        width: 100%;
        border-radius: 0.75rem;
        box-shadow: 0 0 2px 2px #555;
        padding: 0.5rem;
        margin-bottom: 0;
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
        background-color: #FFF;
    }

    .search-bar-container form{
        width: 100%;
        padding: 0;
        margin: 0;
    }

    .inputArea {
        width: 90% !important;
    }

    .clearBtn {
        width: 10% !important;
        text-align: center;
        display: flex;
        justify-content: center;
    }

    .search-input-group .form-control {
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        background-color: #fff;
        margin-right: 3rem;
        border: none;

    }

    .search-input-group .bi-search {
        position: absolute;
        right: 5rem;
        top: 20%;
        color: #6c757d;
        font-size: 1.1rem;
        z-index: 20;
        background: transparent;
        border: none;
    }

    .title-line {
        width: 60px;
        height: 3px;
        background: #0d6efd;
        margin-top: 10px;
        border-radius: 3px;
    }

    .accordion-button {
        background-color: #fff;
        color: #222;
        transition: all 0.3s ease;
        box-shadow: none;
    }

    .accordion-button:not(.collapsed) {
        background-color: #0d6efd;
        color: #fff;
    }

    .accordion-item {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
    }

    .accordion-body {
        background-color: #fafafa;
    }
</style>

@push('scripts')
<script>
    const clearContentSearch = () => {
        const UserManualKeywords = document.getElementById('UserManualKeywords').value = '';
        const baseUrl = "{{ route('user_manual') }}";
        window.location.href = baseUrl;
    }
</script>
@endpush