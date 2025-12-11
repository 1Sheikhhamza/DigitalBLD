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
                            <li class="breadcrumb-item"><a href="{{ route('user.index') }}" class="btn btn-primary btn-sm">User List</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-content">
            <div class="form-container col-sm-8 offset-2">

                <form action="{{ route('user.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
                    @csrf
                    @method('PATCH')
                    <div class="card card-success card-outline mb-4">
                        <div class="card-header">
                            <h3 class="card-title">User List</h3>
                            <div class="card-tools"> <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse"> <i data-lte-icon="expand" class="bi bi-plus-lg"></i> <i data-lte-icon="collapse" class="bi bi-dash-lg"></i> </button> <button type="button" class="btn btn-tool" data-lte-toggle="card-remove"> <i class="bi bi-x-lg"></i> </button> </div>
                        </div>
                        <div class="card-body">

                            <div class="input-group mb-3 row">
                                <label for="first_name" class="col-md-3 col-form-label text-md-right">{{ __('First Name') }}</label>
                                <div class="col-md-8">
                                    <input id="first_name" type="text" class="form-control" name="first_name" value="{{ $user->first_name }}" required>
                                    @error('first_name') <span style="color: red;">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="input-group mb-3 row">
                                <label for="last_name" class="col-md-3 col-form-label text-md-right">{{ __('Last Name') }}</label>
                                <div class="col-md-8">
                                    <input id="last_name" type="text" class="form-control" name="last_name" value="{{ $user->last_name }}" required>
                                    @error('last_name') <span style="color: red;">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="input-group mb-3 row">
                                <label for="user_type" class="col-md-3 col-form-label text-md-right">{{ __('User Type') }}</label>
                                <div class="col-md-8">
                                    <select name="user_type" id="user_type" class="form-control">
                                        <option value="{{ $user->user_type }}">{{ $user->user_type }}</option>
                                        <option value="Admin">Admin</option>
                                        <option value="Staff">Staff</option>
                                    </select>
                                    @error('user_type') <span style="color: red;">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="input-group mb-3 row">
                                <label for="user_type" class="col-md-3 col-form-label text-md-right">{{ __('Roles') }}</label>
                                <div class="col-md-8">
                                    @foreach ($roles as $role)
                                    <div>
                                        <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                            {{ $hasRoles->contains($role->id) ? 'checked' : '' }}>
                                        {{ $role->name }}
                                    </div>
                                    @endforeach
                                    @error('roles') <span style="color: red;">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="input-group mb-3 row">
                                <label for="phone_no" class="col-md-3 col-form-label text-md-right">{{ __('Phone No') }}</label>
                                <div class="col-md-8">
                                    <input id="phone_no" type="tel" class="form-control" name="phone_no" value="{{ $user->mobile }}" required>
                                    @error('phone_no') <span style="color: red;">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="input-group mb-3 row">
                                <label for="email" class="col-md-3 col-form-label text-md-right">{{ __('Email') }}</label>
                                <div class="col-md-8">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                                    @error('email') <span style="color: red;">{{ $message }}</span> @enderror
                                </div>
                            </div>


                            <div class="input-group mb-3 row">
                                <label for="gender" class="col-md-3 col-form-label text-md-right">{{ __('Gender') }}</label>
                                <div class="col-md-8">
                                    <select name="gender" class="form-control">
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Not Declare">Not Declare</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    @error('gender') <span style="color: red;">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="input-group mb-3 row">
                                <label for="date_of_birth" class="col-md-3 col-form-label text-md-right">{{ __('Date of Birth') }}</label>
                                <div class="col-md-8">
                                    <input id="date_of_birth" type="date" class="form-control" name="date_of_birth" value="{{ $user->dob }}" required>
                                    @error('date_of_birth') <span style="color: red;">{{ $message }}</span> @enderror
                                </div>
                            </div>


                            <div class="input-group mb-3 row">
                                <label for="date_of_birth" class="col-md-3 col-form-label text-md-right">{{ __('Password') }}</label>
                                <div class="col-md-8">
                                    <input id="password" type="password" class="form-control" name="password" value="{{ $user->password }}" required>
                                    @error('password') <span style="color: red;">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="input-group mb-3 row">
                                <label for="status" class="col-md-3 col-form-label text-md-right">{{ __('Status') }}</label>
                                <div class="col-md-8">
                                    <select name="status" class="form-control">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                    @error('status') <span style="color: red;">{{ $message }}</span> @enderror
                                </div>
                            </div>

                        </div>

                        <div class="card-footer"> <button type="submit" class="btn btn-success">Submit</button> </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
    @include('admin/layouts.footer')
</div>

@endsection