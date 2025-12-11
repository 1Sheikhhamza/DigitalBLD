@extends('admin.layouts.app') @section('title', 'Dashboard') @section('content') <div class="app-wrapper"> @include('admin.layouts.sidebar') <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Add New FAQ</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="#">Home</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page"> FAQ List </li>
                        </ol>
                    </div>
                    <div class="col-sm-6">
                        <div class="float-sm-end">
                            <a href="{{ route('faq.index') }}" style="color:#fff; margin-right:20px" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> View faq List </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-content">
            <div class="container-fluid">
                <div class="row g-4">
                    <div class="col-md-10 offset-1 mt-5">
                        <div class="card card-primary card-outline mb-4">
                            <div class="card-header">
                                <div class="card-title">faq Information</div>
                            </div>
                            <form method="POST" action="{{ route('faq.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6 mt-4">
                                            <label for="topic" class="form-label">Topic</label>
                                            <input id="topic" type="text" class="form-control" name="topic" value="{{ old('topic') }}" required>
                                            @if ($errors->has('topic'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('topic') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="col-sm-6 mt-4">
                                            <label for="question" class="form-label">Question</label>
                                            <input id="question" type="text" class="form-control" name="question" value="{{ old('question') }}">
                                            @if ($errors->has('question'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('question') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="col-sm-12 mt-4">
                                            <label for="answer" class="form-label">Answer</label>
                                            <textarea id="answer" name="answer" class="form-control ckeditor" required>{{ old('answer') }}</textarea>
                                            @if ($errors->has('answer'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('answer') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="col-sm-6 mt-4">
                                            <label for="sequence" class="form-label">Sequence</label>
                                            <input id="sequence" type="number" class="form-control" name="sequence" value="{{ old('sequence') }}" style="width:30%">
                                            @if ($errors->has('sequence'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('sequence') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="col-sm-6 mt-4">
                                            <label for="status" class="form-label">Status</label>
                                            <select id="status" name="status" class="form-control">
                                                <option value="1">Publish</option>
                                                <option value="0">Not Publish</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div> @endsection