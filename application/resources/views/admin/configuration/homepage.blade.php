@extends('admin.layouts.app')
@section('title', 'Add New Configuration')
@section('content')

<style>
    .input-select-group {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .input-select-group input,
    .input-select-group select {
        flex: 1;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 25px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 25px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 19px;
        width: 19px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.slider {
        background-color: #28a745;
    }

    input:checked+.slider:before {
        transform: translateX(24px);
    }
</style>

<div class="app-wrapper">
    @include('admin.layouts.sidebar')
    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-3">Configuration -> Homepage Section Control</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-content">
            <div class="form-container col-sm-10 offset-1">
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                    <strong>Success:</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <form method="POST" enctype="multipart/form-data" action="{{ route('configuration.homepage.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="card card-success card-outline mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Update Company Profile</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                                    <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                                    <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-lte-toggle="card-remove">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Section Name</th>
                                        <th>Title</th>
                                        <th>Position</th>
                                        <th>Display</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $sections = [
                                    'banner' => 'Banner Section',
                                    'welcome' => 'Welcome Section',
                                    'why-choose' => 'Why Choose',
                                    'digital-bld' => 'A Preview of Digital BLD',
                                    'how-to-works' => 'How BLD Works?',
                                    'testimonials' => 'Testimonials',
                                    'packages' => 'Our Packages',
                                    'photo-gallery' => 'Photo gallery',
                                    'usefull-links' => 'Useful Links',
                                    'faq' => 'FAQ',
                                    'blog' => 'Latest Blog',
                                    ];
                                    @endphp

                                    @foreach($sections as $key => $label)
                                    @php
                                    $section = $homepageSections[$key] ?? null;
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $label }}</strong></td>

                                        {{-- Title Field --}}
                                        <td>
                                            <input type="text" name="sections[{{ $key }}][title]" class="form-control"
                                                placeholder="Enter section title"
                                                value="{{ old("sections.$key.title", $section['title'] ?? $label) }}">
                                        </td>

                                        {{-- Position Field --}}
                                        <td>
                                            <input type="number" name="sections[{{ $key }}][position]" class="form-control"
                                                placeholder="Position (1, 2, 3...)"
                                                value="{{ old("sections.$key.position", $section['position'] ?? 0) }}">
                                        </td>

                                        {{-- Display Toggle --}}
                                        <td class="text-center">
                                            <label class="switch">
                                                <input type="checkbox" name="sections[{{ $key }}][display]" value="1"
                                                    {{ old("sections.$key.display", $section['display'] ?? 1) ? 'checked' : '' }}>
                                                <span class="slider"></span>
                                            </label>
                                        </td>
                                    </tr>
                                    @endforeach

                                    <tr>
                                        <td colspan="4" class="text-center">
                                            <button class="btn btn-primary btn-lg">Update Homepage Sections</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>

                    </div>
                </form>
            </div>
        </div>
    </main>
    @include('admin/layouts.footer')
</div>

@endsection