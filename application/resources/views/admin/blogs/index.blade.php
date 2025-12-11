@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="app-wrapper">
    @include('admin.layouts.sidebar')
    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Blog</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <button type="button" onclick="permissions('blogs','1');" style="color:#000; " class="btn btn-success btn-sm m-2"><i class="fa fa-check"></i> Approved</button>
                            <button type="button" onclick="permissions('blogs','0');" style="color:#000; " class="btn btn-warning btn-sm m-2"><i class="fa fa-times"></i> Disapproved</button>
                            <button type="button" onclick="deletedata('masterdelete','blogs');" style="color:#fff; " class="btn btn-danger btn-sm m-2"><i class="fa fa-times"></i> Multiple Delete</button>
                            <a href="{{ route('blogs.create') }}" style="color:#fff; margin-right:20px" class="btn btn-primary btn-sm m-2"><i class="fa fa-plus"></i> Add New</a>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Blog List</h3>
                        <div class="card-tools"> <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse"> <i data-lte-icon="expand" class="bi bi-plus-lg"></i> <i data-lte-icon="collapse" class="bi bi-dash-lg"></i> </button> <button type="button" class="btn btn-tool" data-lte-toggle="card-remove"> <i class="bi bi-x-lg"></i> </button> </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <form id="form_check">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>SI</th>
                                            <th>Title</th>
                                            <th>Author</th>
                                            <th>Image</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($blogs as $blog)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $blog->title }}</td>
                                            <td>{{ $blog->author }}</td>
                                            <td>
                                                @if($blog->featured_image)
                                                <img src="{{ asset('uploads/blogs/' . $blog->featured_image) }}"
                                                    alt="{{ $blog->title }}"
                                                    style="width: 80px; height: auto; object-fit: cover; border-radius: 4px;">
                                                @else
                                                <em>No image</em>
                                                @endif
                                            </td>
                                            <td>
                                                {!! $blog->status === 'published'
                                                ? '<span class="badge text-bg-success">Published</span>'
                                                : '<span class="badge text-bg-warning">Draft</span>' !!}
                                            </td>
                                            <td>{{ $blog->created_at->format('d M Y') }}</td>
                                            <td>
                                                <a href="{{ route('blogs.edit', $blog->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                                <a href="{{ route('blogs.show', $blog->id) }}" class="btn btn-sm btn-info">View</a>
                                                <form action="{{ route('blogs.destroy', $blog->id) }}" method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this blog?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>



                            </form>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        {{ $blogs->links('pagination::bootstrap-4')}}
                    </div>
                </div>
            </div>
        </div>
    </main>
    @include('admin/layouts.footer')
</div>

@endsection