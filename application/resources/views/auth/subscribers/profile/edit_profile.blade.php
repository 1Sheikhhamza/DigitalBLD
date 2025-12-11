@extends('auth.subscribers.layouts.app')
@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', Auth::guard('subscriber')->user()->name.' | BLD Profile')

@section('content')


<style>
    /* Wrapper for the profile photo */
    .profile-photo-wrapper {
        display: inline-block;
        position: relative;
    }

    /* Image styling */
    .profile-photo-wrapper img {
        width: 130px;
        height: 130px;
        object-fit: cover;
        transition: all 0.3s ease;
    }

    /* Zoom + shadow on hover */
    .profile-photo-wrapper:hover img {
        transform: scale(1.05);
        box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
    }

    /* Edit button styling */
    .edit-photo-btn {
        position: absolute;
        bottom: 8px;
        right: 8px;
        background: linear-gradient(135deg, #0d6efd, #3b8cff);
        color: white;
        border-radius: 50%;
        padding: 6px 9px;
        cursor: pointer;
        font-size: 14px;
        border: 2px solid #fff;
        transition: all 0.3s ease;
    }

    /* Button hover effect */
    .edit-photo-btn:hover {
        background: linear-gradient(135deg, #3b8cff, #0d6efd);
        transform: scale(1.1);
    }
</style>

<div class="container mt-5 mb-5">
    <div class="row g-4">
        <!-- Main Content -->
        <main class="col-lg-8">
            <div class="search-summary">
                <h4 class="fw-bold">Edit Profile</h4>
            </div>

            <div class="card profile-card shadow-sm p-4">

                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('subscriber.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="row g-4">
                                {{-- First Name --}}
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label fw-semibold">First Name *</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                        name="first_name" value="{{ old('first_name', $userProfile->first_name) }}" required>
                                    @error('first_name')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Last Name --}}
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label fw-semibold">Last Name *</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                        name="last_name" value="{{ old('last_name', $userProfile->last_name) }}" required>
                                    @error('last_name')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold">Email *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        name="email" value="{{ old('email', $userProfile->email) }}" required>
                                    @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Mobile --}}
                                <div class="col-md-6">
                                    <label for="mobile" class="form-label fw-semibold">Mobile</label>
                                    <input type="text" class="form-control @error('mobile') is-invalid @enderror"
                                        name="mobile" value="{{ old('mobile', $userProfile->mobile) }}">
                                    @error('mobile')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Registration Type --}}
                                <div class="col-md-6">
                                    <label for="registeredAs" class="form-label fw-semibold">Registered as *</label>
                                    <select class="form-select @error('registration_as') is-invalid @enderror"
                                        name="registration_as">
                                        <option value="Judiciary Person" {{ old('registration_as', $userProfile->registration_as) == 'Judiciary Person' ? 'selected' : '' }}>Judiciary Person</option>
                                        <option value="Lawyer" {{ old('registration_as', $userProfile->registration_as) == 'Lawyer' ? 'selected' : '' }}>Lawyer</option>
                                        <option value="Student" {{ old('registration_as', $userProfile->registration_as) == 'Student' ? 'selected' : '' }}>Student</option>
                                        <option value="Other" {{ old('registration_as', $userProfile->registration_as) == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('registration_as')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Date of Birth --}}
                                <div class="col-md-6">
                                    <label for="dob" class="form-label fw-semibold">Date of Birth</label>
                                    <input type="date" class="form-control @error('dob') is-invalid @enderror"
                                        name="dob" value="{{ old('dob', $userProfile->dob ? \Carbon\Carbon::parse($userProfile->dob)->format('Y-m-d') : '') }}">
                                    @error('dob')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Gender --}}
                                <div class="col-md-6">
                                    <label for="gender" class="form-label fw-semibold">Gender</label>
                                    <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                        <option value="">Select</option>
                                        <option value="Male" {{ old('gender', $userProfile->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ old('gender', $userProfile->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ old('gender', $userProfile->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Address --}}
                                <div class="col-md-6">
                                    <label for="address" class="form-label fw-semibold">Address</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror"
                                        name="address" rows="3">{{ old('address', $userProfile->address) }}</textarea>
                                    @error('address')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Profile Image --}}
                                <div class="col-md-12">
                                    <label for="profileImage" class="form-label fw-semibold">
                                        Profile Image <small class="text-muted">(JPEG, PNG, JPG, Max: 512 KB, Size: 250x250px)</small>
                                    </label>

                                    <div class="d-flex align-items-center gap-3">
                                        <div class="position-relative profile-photo-wrapper">
                                            <img id="profilePreview"
                                                src="{{ $userProfile && $userProfile->photo  
                                                ? asset('uploads/subscriber/profile/'.$userProfile->photo) 
                                                : 'https://placehold.co/120x120/e0e0e0/000000?text=Profile' }}"
                                                class="rounded-circle border shadow-sm"
                                                alt="Profile Preview">
                                            <label for="profileImage" class="edit-photo-btn">
                                                <i class="bi bi-pencil"></i>
                                            </label>
                                        </div>
                                        <input type="file" id="profileImage" name="profile_image"
                                            class="d-none @error('profile_image') is-invalid @enderror" accept="image/*">
                                    </div>
                                    @error('profile_image')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Submit Button --}}
                                <div class="col-12 text-end mt-3">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="bi bi-save me-1"></i> Update Profile
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>

                </form>
            </div>
        </main>

        @include('auth.subscribers.profile._my_account_nav')
    </div>
</div>

@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('profileImage').addEventListener('change', function(e) {
            previewImage(e, 'profilePreview');
        });

        function previewImage(e, previewId) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    const preview = document.getElementById(previewId);
                    preview.src = ev.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        }
    });
</script>

@endpush