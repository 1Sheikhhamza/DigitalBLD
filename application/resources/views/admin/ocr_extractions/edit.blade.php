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
                <form action="{{ route('ocr_extractions.update', $ocrExtraction->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card card-success card-outline mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Legal Decision</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Division</label>
                                        <select name="division" class="form-select" required>
                                            <option value="{{ $ocrExtraction->division}}">{{ $ocrExtraction->division}}</option>
                                            <option value="Appellate Division">Appellate Division</option>
                                            <option value="High Court Division">High Court Division</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <div class="col-sm-6"><label class="form-label">Volume</label></div>
                                            <div class="col-sm-6 text-right d-flex justify-content-end">
                                                <a href="javascript:void(0)" class="text-primary text-right" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                                    Volume List
                                                </a>
                                            </div>
                                        </div>
                                        <input type="number" name="volume_id" id="selectedVolume" value="{{ $ocrExtraction->volume ? $ocrExtraction->volume->number : '' }}" required class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <div class="col-sm-7"><label class="form-label">Volume Index File (PDF Only)</label></div>
                                            <div class="col-sm-5 text-right d-flex justify-content-end">
                                                @if (!empty($ocrExtraction->volume->index_file))

                                                <a href="{{ asset('uploads/volume/pdfs/' . $ocrExtraction->volume->index_file) }}" target="_blank" download>
                                                    Download Index
                                                </a>
                                                @endif
                                            </div>
                                        </div>

                                        <input type="file" name="index_file" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Published Year</label>
                                        <input type="text" name="published_year" class="form-control" value="{{ old('published_year', $ocrExtraction->published_year) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Starting Page</label>
                                        <input type="number" name="starting_page_no" class="form-control" value="{{ old('starting_page_no', $ocrExtraction->starting_page_no) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Ending Page</label>
                                        <input type="number" name="ending_page_no" class="form-control" value="{{ old('ending_page_no', $ocrExtraction->ending_page_no) }}">
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Case No</label>
                                        <input type="text" name="case_no" class="form-control" value="{{ old('case_no', $ocrExtraction->case_no) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Judge Name</label>
                                        <input type="text" name="judge_name" class="form-control" value="{{ old('judge_name', $ocrExtraction->judge_name) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Subject Matter</label>
                                        <textarea  name="subject" class="form-control ckeditor">{{ old('subject', $ocrExtraction->subject) }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Parties</label>
                                        <textarea  name="parties" class="form-control ckeditor">{{ old('parties', $ocrExtraction->parties) }}</textarea>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Petitioners</label>
                                        <textarea  name="petitioners" class="form-control ckeditor">{{ old('petitioners', $ocrExtraction->petitioners) }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Respondent</label>
                                        <textarea  name="respondent" class="form-control ckeditor">{{ old('respondent', $ocrExtraction->respondent) }}</textarea>
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Decided On / Date of Judgment</label>
                                        <input type="text" name="decided_on" class="form-control datepicker" placeholder="The {{ date('jS F, Y') }}" value="{{ old('decided_on', $ocrExtraction->decided_on) }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Result</label>
                                        <input type="text" name="result" class="form-control" value="{{ old('result', $ocrExtraction->result) }}">
                                    </div>
                                </div>



                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Act/Order/Rule Name</label>
                                        <input type="text" name="related_act_order_rule" class="form-control" value="{{ old('related_act_order_rule', $ocrExtraction->related_act_order_rule) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Sections / Subsections</label>
                                        <input type="text" name="sections_subsections" class="form-control" value="{{ old('sections_subsections', $ocrExtraction->sections_subsections) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Jurisdiction</label>
                                        <input type="text" name="jurisdiction" class="form-control" value="{{ old('jurisdiction', $ocrExtraction->jurisdiction) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Keywords</label>
                                        <input type="text" name="key_words" class="form-control" value="{{ old('key_words', $ocrExtraction->key_words) }}">
                                    </div>
                                </div>



                                <div class="mb-3 col-12">
                                    <label class="form-label">Judgment</label>
                                    <textarea name="judgment" class="form-control ckeditor">{{ old('judgment', $ocrExtraction->judgment) }}</textarea>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary mt-3">Update</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>


            </div>
        </div>
    </main>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">📚 Volume List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-2">
                        @foreach($volumeList as $id => $volumeNumber)
                        <div class="col-md-3">
                            <div class="card shadow-sm border-0 p-2 volume-card"
                                data-volume="{{ $volumeNumber }}" style="cursor: pointer;">
                                <div class="card-body p-2 text-center">
                                    <span class="fw-bold">Volume</span>
                                    <span class="badge bg-primary ms-1">{{ $volumeNumber }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin/layouts.footer')
</div>

@endsection

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".volume-card").forEach(function(card) {
            card.addEventListener("click", function() {
                let volume = this.getAttribute("data-volume");
                document.getElementById("selectedVolume").value = volume;
                // Close modal after selection
                var modal = bootstrap.Modal.getInstance(document.getElementById('exampleModal'));
                modal.hide();
            });
        });
    });
</script>