@extends('admin.layouts.app')
@section('title', 'Subscriber Details')

@section('content')
<div class="app-wrapper">
    @include('admin.layouts.sidebar')

    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Subscription Details</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item">
                                <a href="{{ route('subscriptions.index') }}" class="btn btn-primary btn-sm">Subscription List</a>
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
                        <h5 class="mb-0">Subscription Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th scope="row">First Name</th>
                                    <td>{{ $subscriber->first_name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Last Name</th>
                                    <td>{{ $subscriber->last_name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Full Name</th>
                                    <td>{{ $subscriber->name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Email</th>
                                    <td>{{ $subscriber->email }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Phone</th>
                                    <td>{{ $subscriber->mobile }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Address</th>
                                    <td>{{ $subscriber->address }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Registration As</th>
                                    <td>{{ $subscriber->registration_as }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Gender</th>
                                    <td>{{ $subscriber->gender }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Date of Birth</th>
                                    <td>{{ $subscriber->dob }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Photo</th>
                                    <td>
                                    <img src="{{ isset($subscriber->photo) && $subscriber->photo 
                                ? asset('uploads/subscriber/profile/'.$subscriber->photo) 
                                : 'https://placehold.co/120x120/e0e0e0/000000?text=Profile' }}"
                                class="rounded-circle border shadow-sm"  style="width:100px; height:auto"
                                alt="Profile Preview">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Status</th>
                                    <td>{{ $subscriber->status }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Created At</th>
                                    <td>{{ $subscriber->created_at }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Updated At</th>
                                    <td>{{ $subscriber->updated_at }}</td>
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