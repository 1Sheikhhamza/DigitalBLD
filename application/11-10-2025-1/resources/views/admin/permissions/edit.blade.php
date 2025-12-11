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
                        <h3 class="mb-0">Edit Permission</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="{{ route('permission.index') }}" class="btn btn-primary btn-sm">View Permission List</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-content">
            <div class="form-container col-sm-8 offset-2">
                <form method="POST" action="{{ route('permission.update', $permission->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="card card-warning card-outline mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Update Permission</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse"><i data-lte-icon="expand" class="bi bi-plus-lg"></i><i data-lte-icon="collapse" class="bi bi-dash-lg"></i></button>
                                <button type="button" class="btn btn-tool" data-lte-toggle="card-remove"><i class="bi bi-x-lg"></i></button>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="input-group mb-3 row">
                                <label class="col-md-3 col-form-label text-md-right">Module</label>
                                <div class="col-md-8">
                                    <select name="module_id" id="module_id" class="form-select" required>
                                        <option value="">-- Select Module --</option>
                                        @foreach($modules as $module)
                                            <option value="{{ $module->id }}" {{ (old('module_id', $permission->module_id) == $module->id) ? 'selected' : '' }}>
                                                {{ $module->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('module_id') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="input-group mb-3 row">
                                <label class="col-md-3 col-form-label text-md-right">Permission Name</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $permission->name) }}" required>
                                    @error('name') <span style="color:red">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="input-group mb-3 row">
                                <label class="col-md-3 col-form-label text-md-right">Permission Slug</label>
                                <div class="col-md-8">
                                    <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $permission->slug) }}" required>
                                    @error('slug') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="input-group mb-3 row">
                                <label class="col-md-3 col-form-label text-md-right">Status</label>
                                <div class="col-md-8">
                                    <select name="status" class="form-control">
                                        <option value="1" {{ old('status', $permission->status) == 1 ? 'selected' : '' }}>Display</option>
                                        <option value="0" {{ old('status', $permission->status) == 0 ? 'selected' : '' }}>Not Display</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-warning">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
    @include('admin.layouts.footer')
</div>
@endsection
