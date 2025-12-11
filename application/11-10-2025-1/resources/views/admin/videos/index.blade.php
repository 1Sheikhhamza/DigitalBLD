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
                        <h3 class="mb-0">Video</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <button type="button" onclick="permissions('videos','1');" style="color:#000; " class="btn btn-success btn-sm m-2"><i class="fa fa-check"></i> Approved</button>
                            <button type="button" onclick="permissions('videos','0');" style="color:#000; " class="btn btn-warning btn-sm m-2"><i class="fa fa-times"></i> Disapproved</button>
                            <button type="button" onclick="deletedata('masterdelete','videos');" style="color:#fff; " class="btn btn-danger btn-sm m-2"><i class="fa fa-times"></i> Multiple Delete</button>
                            <a href="{{ route('videos.create') }}" style="color:#fff; margin-right:20px" class="btn btn-primary btn-sm m-2"><i class="fa fa-plus"></i> Add New</a>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Video List</h3>
                        <div class="card-tools"> <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse"> <i data-lte-icon="expand" class="bi bi-plus-lg"></i> <i data-lte-icon="collapse" class="bi bi-dash-lg"></i> </button> <button type="button" class="btn btn-tool" data-lte-toggle="card-remove"> <i class="bi bi-x-lg"></i> </button> </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <form id="form_check">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th><input name="checkbox" onclick="checkedAll();" type="checkbox" readonly="readonly" /></th>
                                            <th>SI</th>
                                            <th>Title</th>
                                            <th>Video</th>
                                            <th>Project</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($videos as $video)
                                        <tr id="tablerow{{ $video->id }}" class="tablerow">
                                            <td><input type="checkbox" name="summe_code[]" id="summe_code{{ $video->id }}" value="{{ $video->id }}" /></td>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $video->title ?? '-' }}</td>
                                            <td>
                                                @if($video->video_url)
                                                <a href="{{ $video->video_url }}" target="_blank">Watch Video</a>
                                                @else
                                                -
                                                @endif
                                            </td>
                                            <td>{{ $video->project?->title ?? '-' }}</td>
                                            <td>{!! $video->status
                                                ? '<span class="badge text-bg-success">Active</span>'
                                                : '<span class="badge text-bg-danger">Inactive</span>' !!}
                                            </td>
                                            <td>
                                                <a href="{{ route('videos.edit', $video->id) }}" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></a>
                                                <a href="{{ route('videos.show', $video->id) }}" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></a>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteSingle('{{ $video->id }}','masterdelete','videos')"><i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>


                            </form>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        {{ $videos->links('pagination::bootstrap-4')}}
                    </div>
                </div>
            </div>
        </div>
    </main>
    @include('admin/layouts.footer')
</div>

@endsection