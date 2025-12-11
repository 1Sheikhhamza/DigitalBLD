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
                            <h3 class="mb-0">User</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <a href="javascript:void()" onclick="permissions('users','1');" style="color:#000; " class="btn btn-success btn-sm m-2"><i class="fa fa-check"></i> Approved</a>
                                <a href="javascript:void()" onclick="permissions('users','0');" style="color:#000; " class="btn btn-warning btn-sm m-2"><i class="fa fa-times"></i> Disapproved</a>
                                <a href="javascript:void()" onclick="deletedata('masterdelete','users');" style="color:#fff; " class="btn btn-danger btn-sm m-2"><i class="fa fa-times"></i> Multiple Delete</a>
                                <a href="{{ route('user.create') }}" style="color:#fff; margin-right:20px" class="btn btn-primary btn-sm m-2"><i class="fa fa-plus"></i> Add New</a>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="app-content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">User List</h3>
                            <div class="card-tools"> <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse"> <i data-lte-icon="expand" class="bi bi-plus-lg"></i> <i data-lte-icon="collapse" class="bi bi-dash-lg"></i> </button> <button type="button" class="btn btn-tool" data-lte-toggle="card-remove"> <i class="bi bi-x-lg"></i> </button> </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <form id="form_check">
                                    <table class="table m-0">
                                        <thead>
                                            <tr>
                                                <th width="4%"><input name="checkbox" onclick='checkedAll();' type="checkbox" readonly="readonly" /></th>
                                                <th>SI</th>
                                                <th>Name</th>
                                                <th>Phone</th>
                                                <th>Email</th>
                                                <th>Gender</th>
                                                <th>DOB</th>
                                                <th>Role</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($listData as $data)
                                            <tr id="tablerow<?php echo $data->id; ?>" class="tablerow">
                                                <td><input type="checkbox" name="summe_code[]" id="summe_code<?php echo $data->id; ?>" value="{{ $data->id }}" /></td>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $data->first_name.' '.$data->last_name }}</td>
                                                <td>{{ $data->mobile }}</td>
                                                <td>{{ $data->email }}</td>
                                                <td>{{ $data->gender }}</td>
                                                <td>{{ $data->dob }}</td>
                                                <td>{{ $data->getRoleNames()->implode(', ') }}</td>
                                                <td>
                                                    {!! $data->status ? '<span class="badge text-bg-success">Active</span>' : '<span class="badge text-bg-danger">Inactive</span>' !!}
                                                </td>
                                                <td align="right">
                                                    <div style="width:50%; float:left">
                                                        <a href="{{ route('user.edit', $data->id) }}" class="btn btn-warning" style="font-size: 12px; float:left; padding:3px 5px"><i class="fa fa-edit"></i></a>
                                                    </div>
                                                    <div style="width:50%; float:left">
                                                        <button type="button" class="btn btn-danger" style="font-size: 12px; float:left; padding:3px 5px"
                                                            onclick="deleteSingle('<?php echo $data->id; ?>','masterdelete','users')"><i class="fa fa-trash"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                        <div class="card-footer clearfix">
                            {{ $listData->links('pagination::bootstrap-4')}}
                        </div>
                    </div>
                </div>
            </div>
        </main>
        @include('admin/layouts.footer')
    </div>

    @endsection