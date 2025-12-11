@extends('admin.layouts.app')
@section('title', 'Add New Video')

@section('content')
<div class="app-wrapper">
    @include('admin.layouts.sidebar')

    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Video Details</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item">
                                <a href="{{ route('videos.index') }}" class="btn btn-primary btn-sm">Video List</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-content py-4">
            <div class="container col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Video Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th style="width: 30%;">Title</th>
                                    <td>{{ $video->title ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Video URL</th>
                                    <td>
                                        @if($video->video_url)
                                        <a href="{{ $video->video_url }}" target="_blank">{{ $video->video_url }}</a>
                                        @else
                                        -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Project</th>
                                    <td>{{ $video->project?->title ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>{!! $video->status
                                        ? '<span class="badge text-bg-success">Active</span>'
                                        : '<span class="badge text-bg-danger">Inactive</span>' !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $video->created_at->format('d M Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $video->updated_at->format('d M Y h:i A') }}</td>
                                </tr>
                            </tbody>
                        </table>


                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('admin.layouts.footer')
</div>
@endsection