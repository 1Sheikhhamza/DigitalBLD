@extends('admin.layouts.app')
@section('title', 'Add New Volume')
@section('content')
@php
$currentYear = date('Y');
$years = range($currentYear, $currentYear - 49); // last 50 years
@endphp
<div class="app-wrapper">
    @include('admin.layouts.sidebar')
    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Volume</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="{{ route('volumes.index') }}" class="btn btn-primary btn-sm">Volume List</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-content">
            <div class="form-container col-sm-8 offset-2">

                <!-- Validation errors container -->
                <div id="validationErrors" class="alert alert-danger" style="display:none;">
                    <ul id="validationErrorsList"></ul>
                </div>
                <!-- id="ocrForm" -->
                <form enctype="multipart/form-data" method="POST" action="{{ route('volumes.store') }}" id="ocrForm">
                    @csrf
                    <div class="card card-success card-outline mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Volume</h3>
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

                            <div class="mb-3">
                                <label for="number" class="form-label">Volume Number</label>
                                <input type="number" name="number" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="year" class="form-label">Published Year</label>
                                <select name="year" class="form-select" required>
                                    <option value="">-- Select Year --</option>
                                    @foreach($years as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Icon/Image</label>
                                <input type="file" name="image" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label for="index_file" class="form-label">Index (PDF Only)</label>
                                <input type="file" name="index_file" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label for="document" class="form-label">Documents (required)</label>
                                <input type="file" name="document" required class="form-control">
                            </div>

                            <button type="submit" id="submitBtn" class="btn btn-primary">Submit</button>

                            <!-- Progress Bar -->
                            <div id="progressContainer" style="display:none; margin-top: 15px;">
                                <p>Uploading: <span id="progressPercent">0%</span></p>
                                <progress id="uploadProgress" value="0" max="100" style="width: 100%;"></progress>
                            </div>

                            <!-- Result message -->
                            <div id="resultMessage" style="margin-top: 10px;"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
    @include('admin.layouts.footer')


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#ocrForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                // Reset UI
                $('#submitBtn').prop('disabled', true);
                $('#submitBtn').removeClass('btn btn-primary');
                $('#submitBtn').addClass('btn btn-default');
                $('#validationErrors').hide();
                $('#validationErrorsList').empty();
                $('#resultMessage').html('');
                $('#progressContainer').show();
                $('#uploadProgress').val(0);
                $('#progressPercent').text('0%');

                $.ajax({
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = Math.round((evt.loaded / evt.total) * 100);
                                $('#uploadProgress').val(percentComplete);
                                $('#progressPercent').text(percentComplete + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log(response);
                        $('#resultMessage').html('<p style="color:green;">' + response.message + '</p>');

                        // Ensure final value is 100% if upload really finished
                        $('#uploadProgress').val(100);
                        $('#progressPercent').text('100%');

                        // Redirect after 2 seconds (optional)
                        setTimeout(function() {
                            $('#submitBtn').prop('disabled', false);
                            $('#submitBtn').removeClass('btn btn-default');
                            $('#submitBtn').addClass('btn btn-primary');
                            window.location.href = "{{ route('volumes.index') }}";
                        }, 2000);
                    },
                    error: function(xhr) {
                        $('#progressContainer').hide();
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $('#validationErrorsList').empty();
                            $.each(errors, function(key, messages) {
                                $.each(messages, function(index, message) {
                                    $('#validationErrorsList').append('<li>' + message + '</li>');
                                });
                            });
                            $('#validationErrors').show();
                        } else {
                            $('#resultMessage').html('<p style="color:red;">An unexpected error occurred. Please try again.</p>');
                        }


                        $('#submitBtn').prop('disabled', false);
                        $('#submitBtn').removeClass('btn btn-default');
                        $('#submitBtn').addClass('btn btn-primary');
                    },
                    complete: function() {
                        // Re-enable the submit button if needed
                        $('#submitBtn').prop('disabled', false);
                        $('#submitBtn').removeClass('btn btn-default');
                        $('#submitBtn').addClass('btn btn-primary');
                    }
                });
            });
        });
    </script>


</div>
@endsection