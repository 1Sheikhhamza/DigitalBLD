@extends('admin.layouts.app')
@section('title', 'Add New Blog')

@section('content')
<div class="app-wrapper">
    @include('admin.layouts.sidebar')

    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Blog Details</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item">
                                <a href="{{ route('projects.index') }}" class="btn btn-primary btn-sm">Blog List</a>
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
                        <h5 class="mb-0">Blog Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th style="width: 25%;">Title</th>
                                    <td>{{ $blog->title }}</td>
                                </tr>
                                <tr>
                                    <th>Slug</th>
                                    <td>{{ $blog->slug }}</td>
                                </tr>
                                <tr>
                                    <th>Author</th>
                                    <td>{{ $blog->author ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Content</th>
                                    <td>{!! $blog->content !!}</td>
                                </tr>
                                <tr>
                                    <th>Featured Image</th>
                                    <td>
                                        @if($blog->featured_image)
                                        <img src="{{ asset('storage/' . $blog->featured_image) }}" alt="Featured Image" style="max-height: 150px;">
                                        @else
                                        <em>No image uploaded</em>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        {!! $blog->status === 'published'
                                        ? '<span class="badge text-bg-success">Published</span>'
                                        : '<span class="badge text-bg-warning">Draft</span>' !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Meta Title</th>
                                    <td>{{ $blog->meta_title ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Meta Description</th>
                                    <td>{{ $blog->meta_description ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Meta Keywords</th>
                                    <td>{{ $blog->meta_keywords ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $blog->created_at->format('d M Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $blog->updated_at->format('d M Y h:i A') }}</td>
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