@extends('admin.layouts.app')
@section('title', 'Add New Page')

@section('content')
<div class="app-wrapper">
    @include('admin.layouts.sidebar')

    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Page Details</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item">
                                <a href="{{ route('pages.index') }}" class="btn btn-primary btn-sm">Page List</a>
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
                        <h5 class="mb-0">Page Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th scope="row" style="width: 30%;">Name</th>
                                    <td>{{ $page->title }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Slug</th>
                                    <td>{{ $page->slug }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Parent Page</th>
                                    <td>
                                        @if($page->parent_id && $page->parent)
                                        {{ $page->parent->title }}
                                        @else
                                        <em>None</em>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Page Structure</th>
                                    <td>{{ $page->page_structure }}</td>
                                </tr>

                                <tr>
                                    <th scope="row">Connected Page</th>
                                    <td>{{ $page->connected_page }}</td>
                                </tr>

                                <tr>
                                    <th scope="row">External URL</th>
                                    <td>
                                        @if($page->external_url)
                                        <a href="{{ $page->external_url }}" target="_blank">{{ $page->external_url }}</a>
                                        @else
                                        N/A
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">Content</th>
                                    <td>{!! $page->content !!}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Meta Title</th>
                                    <td>{{ $page->meta_title }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Meta Description</th>
                                    <td>{{ $page->meta_description }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Status</th>
                                    <td>
                                        {!! $page->status
                                        ? '<span class="badge text-bg-success">Active</span>'
                                        : '<span class="badge text-bg-danger">Inactive</span>' !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Created At</th>
                                    <td>{{ $page->created_at->format('d M Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Updated At</th>
                                    <td>{{ $page->updated_at->format('d M Y h:i A') }}</td>
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