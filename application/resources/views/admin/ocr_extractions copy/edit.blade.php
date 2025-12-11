@extends('admin.layouts.app')
@section('title', 'Edit Decision')
@section('content')
@php
$currentYear = date('Y');
$years = range($currentYear, $currentYear - 49); // last 50 years

$months = [
    'January' => 'January',
    'February' => 'February',
    'March' => 'March',
    'April' => 'April',
    'May' => 'May',
    'June' => 'June',
    'July' => 'July',
    'August' => 'August',
    'September' => 'September',
    'October' => 'October',
    'November' => 'November',
    'December' => 'December'
];

@endphp

<div class="app-wrapper">
    @include('admin.layouts.sidebar')
    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Edit Legal Decision</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="{{ route('ocr_extractions.index') }}" class="btn btn-primary btn-sm">Legal Decision List</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-content">
            <div class="form-container col-sm-8 offset-2">
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <form action="{{ route('ocr_extractions.update', $ocrExtraction->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card card-success card-outline mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Legal Decision</h3>
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
                            <div class="row">
                                <!-- Column 1 -->
                                <div class="col-md-6">
                                    <!-- Volume -->
                                    <div class="mb-3">
                                        <label for="volume_id" class="form-label">Volume</label>
                                        <select name="volume_id" class="form-select" required>
                                            <option value="">-- Select Volume --</option>
                                            @foreach($volumeList as $id => $volumeName)
                                            <option value="{{ $id }}" {{ old('volume_id', $ocrExtraction->volume_id) == $id ? 'selected' : '' }}>
                                                {{ $volumeName }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Published Year -->
                                    <div class="mb-3">
                                        <label for="published_year" class="form-label">Published Year</label>
                                        <input type="text" name="published_year" class="form-control" value="{{ old('published_year', $ocrExtraction->published_year) }}">
                                    </div>

                                    <!-- Decided On -->
                                    <div class="mb-3">
                                        <label for="decided_on" class="form-label">Decided On</label>
                                        <input type="date" name="decided_on" class="form-control" value="{{ old('decided_on', $ocrExtraction->decided_on) }}">
                                    </div>

                                    <!-- Starting Page -->
                                    <div class="mb-3">
                                        <label for="starting_page_no" class="form-label">Starting Page</label>
                                        <input type="number" name="starting_page_no" class="form-control" value="{{ old('starting_page_no', $ocrExtraction->starting_page_no) }}">
                                    </div>

                                    <!-- Judge Name -->
                                    <div class="mb-3">
                                        <label for="judge_name" class="form-label">Judge Name</label>
                                        <input type="text" name="judge_name" class="form-control" value="{{ old('judge_name', $ocrExtraction->judge_name) }}">
                                    </div>

                                    <!-- Parties -->
                                    <div class="mb-3">
                                        <label for="parties" class="form-label">Parties</label>
                                        <input type="text" name="parties" class="form-control" value="{{ old('parties', $ocrExtraction->parties) }}">
                                    </div>

                                    <!-- Respondent -->
                                    <div class="mb-3">
                                        <label for="respondent" class="form-label">Respondent</label>
                                        <input type="text" name="respondent" class="form-control" value="{{ old('respondent', $ocrExtraction->respondent) }}">
                                    </div>

                                    <!-- Jurisdiction -->
                                    <div class="mb-3">
                                        <label for="jurisdiction" class="form-label">Jurisdiction</label>
                                        <input type="text" name="jurisdiction" class="form-control" value="{{ old('jurisdiction', $ocrExtraction->jurisdiction) }}">
                                    </div>

                                    <!-- Case No -->
                                    <div class="mb-3">
                                        <label for="case_no" class="form-label">Case No</label>
                                        <input type="text" name="case_no" class="form-control" value="{{ old('case_no', $ocrExtraction->case_no) }}">
                                    </div>
                                </div>

                                <!-- Column 2 -->
                                <div class="col-md-6">
                                    <!-- Published Month -->
                                    <div class="mb-3">
                                        <label for="published_month" class="form-label">Published Month</label>
                                        <select name="published_month" class="form-select @error('month') is-invalid @enderror" required>
                                            <option value="">-- Select Month --</option>
                                            @foreach($months as $name)
                                            <option value="{{ $name }}" {{ old('month', $name ?? '') == $name ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                            @endforeach
                                        </select>

                                    </div>

                                    <!-- Ending Page -->
                                    <div class="mb-3">
                                        <label for="ending_page_no" class="form-label">Ending Page</label>
                                        <input type="number" name="ending_page_no" class="form-control" value="{{ old('ending_page_no', $ocrExtraction->ending_page_no) }}">
                                    </div>

                                    <!-- Judges -->
                                    <div class="mb-3">
                                        <label for="judges" class="form-label">Judges</label>
                                        <input type="text" name="judges" class="form-control" value="{{ old('judges', $ocrExtraction->judge_name) }}">
                                    </div>

                                    <!-- Petitioners -->
                                    <div class="mb-3">
                                        <label for="petitioners" class="form-label">Petitioners</label>
                                        <input type="text" name="petitioners" class="form-control" value="{{ old('petitioners', $ocrExtraction->petitioners) }}">
                                    </div>

                                    <!-- Related Act/Order -->
                                    <div class="mb-3">
                                        <label for="related_act_order_rule" class="form-label">Related Act/Order</label>
                                        <input type="text" name="related_act_order_rule" class="form-control" value="{{ old('related_act_order_rule', $ocrExtraction->related_act_order_rule) }}">
                                    </div>

                                    <!-- Sections/Subsections -->
                                    <div class="mb-3">
                                        <label for="sections_subsections" class="form-label">Sections/Subsections</label>
                                        <input type="text" name="sections_subsections" class="form-control" value="{{ old('sections_subsections', $ocrExtraction->sections_subsections) }}">
                                    </div>

                                    <!-- Keywords -->
                                    <div class="mb-3">
                                        <label for="key_words" class="form-label">Keywords</label>
                                        <input type="text" name="key_words" class="form-control" value="{{ old('key_words', $ocrExtraction->key_words) }}">
                                    </div>

                                    <!-- Subject -->
                                    <div class="mb-3">
                                        <label for="subject" class="form-label">Subject</label>
                                        <input type="text" name="subject" class="form-control" value="{{ old('subject', $ocrExtraction->subject) }}">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="judgment" class="form-label">Judgment</label>
                                    <textarea name="judgment" class="form-control ckeditor">{{ old('judgment', $ocrExtraction->judgment) }}</textarea>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary mt-3">Update</button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </main>
    @include('admin/layouts.footer')
</div>

@endsection