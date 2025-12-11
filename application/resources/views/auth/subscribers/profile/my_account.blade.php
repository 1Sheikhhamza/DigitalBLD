@extends('auth.subscribers.layouts.app')
@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', Auth::guard('subscriber')->user()->name.' | BLD Profile')

@section('content')
<div class="container mt-5 mb-5">
  <div class="row g-4">
    <!-- Main Content -->
    <main class="col-lg-8">
      <div class="search-summary">
        <h4 class="fw-bold">My Profile</h4>
      </div>

      <div class="card profile-card shadow-sm p-4">
        <div class="profile-header">
          <img src="{{ $userProfile && $userProfile->photo  
        ? asset('uploads/subscriber/profile/'.$userProfile->photo) 
        : 'https://placehold.co/140x140/e0e0e0/000000?text=Profile' }}"
            alt="Profile Photo" class="profile-photo">

          <h2>{{ $userProfile->first_name . ' ' . $userProfile->last_name }}</h2>
          <p>{{ $userProfile->registration_as ?? 'N/A' }}</p>
        </div>

        <table class="profile-table w-100">
          <tbody>
            <tr>
              <th scope="row">First Name</th>
              <td>{{ $userProfile->first_name ?? 'N/A' }}</td>
            </tr>
            <tr>
              <th scope="row">Last Name</th>
              <td>{{ $userProfile->last_name ?? 'N/A' }}</td>
            </tr>
            <tr>
              <th scope="row">Email</th>
              <td>{{ $userProfile->email ?? 'N/A' }}</td>
            </tr>
            <tr>
              <th scope="row">Phone</th>
              <td>{{ $userProfile->mobile ?? 'N/A' }}</td>
            </tr>
            <tr>
              <th scope="row">Address</th>
              <td>{{ $userProfile->address ?? 'N/A' }}</td>
            </tr>
            <tr>
              <th scope="row">Date of Birth</th>
              <td>{{ format_date($userProfile->dob) }}</td>
            </tr>
            <tr>
              <th scope="row">Registration As</th>
              <td>{{ $userProfile->registration_as ?? 'N/A' }}</td>
            </tr>
            <tr>
              <th scope="row">Gender</th>
              <td>{{ $userProfile->gender ?? 'N/A' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>

    @include('auth.subscribers.profile._my_account_nav')
  </div>
</div>




@endsection