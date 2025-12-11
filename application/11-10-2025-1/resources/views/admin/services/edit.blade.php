@extends('admin.layouts.app')
@section('title', 'Add New Service')
@section('content')
<div class="app-wrapper">
    @include('admin.layouts.sidebar')
    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Service</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="{{ route('services.index') }}" class="btn btn-primary btn-sm">Service List</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-content">
            <div class="form-container col-sm-8 offset-2">
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <form method="POST" action="{{ route('services.update', $service->id)  }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('admin.services._form', ['buttonText' => 'Update'])
                </form>
            </div>
        </div>
    </main>
    @include('admin/layouts.footer')
</div>

@endsection