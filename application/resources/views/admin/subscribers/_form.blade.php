<div class="card card-success card-outline mb-4">
    <div class="card-header">
        <h3 class="card-title">Subscriber Form</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
            </button>
            <button type="button" class="btn btn-tool" data-lte-toggle="card-remove">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="row g-4">
            {{-- First Name --}}
            <div class="col-md-6">
                <label for="first_name" class="form-label fw-semibold">First Name *</label>
                <input type="text" id="first_name" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                    value="{{ old('first_name', $subscriber->first_name ?? '') }}" required>
                @error('first_name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Last Name --}}
            <div class="col-md-6">
                <label for="last_name" class="form-label fw-semibold">Last Name *</label>
                <input type="text" id="last_name" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                    value="{{ old('last_name', $subscriber->last_name ?? '') }}" required>
                @error('last_name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Full Name (optional if you want to keep it separate or remove) --}}
            {{-- 
            <div class="col-md-12">
                <label for="name" class="form-label fw-semibold">Full Name</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $subscriber->name ?? '') }}">
                @error('name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            --}}

            {{-- Email --}}
            <div class="col-md-6">
                <label for="email" class="form-label fw-semibold">Email *</label>
                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email', $subscriber->email ?? '') }}" required>
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Mobile --}}
            <div class="col-md-6">
                <label for="mobile" class="form-label fw-semibold">Mobile</label>
                <input type="text" id="mobile" name="mobile" class="form-control @error('mobile') is-invalid @enderror"
                    value="{{ old('mobile', $subscriber->mobile ?? '') }}">
                @error('mobile')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Registration Type --}}
            <div class="col-md-6">
                <label for="registration_as" class="form-label fw-semibold">Registered as *</label>
                <select id="registration_as" name="registration_as" class="form-select @error('registration_as') is-invalid @enderror">
                    <option value="Judiciary Person" {{ old('registration_as', $subscriber->registration_as ?? '') == 'Judiciary Person' ? 'selected' : '' }}>Judiciary Person</option>
                    <option value="Lawyer" {{ old('registration_as', $subscriber->registration_as ?? '') == 'Lawyer' ? 'selected' : '' }}>Lawyer</option>
                    <option value="Student" {{ old('registration_as', $subscriber->registration_as ?? '') == 'Student' ? 'selected' : '' }}>Student</option>
                    <option value="Other" {{ old('registration_as', $subscriber->registration_as ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('registration_as')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Date of Birth --}}
            <div class="col-md-6">
                <label for="dob" class="form-label fw-semibold">Date of Birth</label>
                <input type="date" id="dob" name="dob" class="form-control @error('dob') is-invalid @enderror"
                    value="{{ old('dob', isset($subscriber->dob) ? \Carbon\Carbon::parse($subscriber->dob)->format('Y-m-d') : '') }}">
                @error('dob')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Gender --}}
            <div class="col-md-6">
                <label for="gender" class="form-label fw-semibold">Gender</label>
                <select id="gender" name="gender" class="form-select @error('gender') is-invalid @enderror">
                    <option value="">Select</option>
                    <option value="Male" {{ old('gender', $subscriber->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('gender', $subscriber->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other" {{ old('gender', $subscriber->gender ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('gender')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Address --}}
            <div class="col-md-6">
                <label for="address" class="form-label fw-semibold">Address</label>
                <textarea id="address" name="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $subscriber->address ?? '') }}</textarea>
                @error('address')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Profile Image --}}
            <div class="col-md-12">
                <label for="profile_image" class="form-label fw-semibold">
                    Profile Image <small class="text-muted">(JPEG, PNG, JPG, Max: 512 KB, Size: 250x250px)</small>
                </label>

                <div class="d-flex align-items-center gap-3">
                    <div class="position-relative profile-photo-wrapper">
                        <img id="profilePreview"
                            src="{{ isset($subscriber->photo) && $subscriber->photo 
                                ? asset('uploads/subscriber/profile/'.$subscriber->photo) 
                                : 'https://placehold.co/120x120/e0e0e0/000000?text=Profile' }}"
                            class="rounded-circle border shadow-sm"  style="width:100px; height:auto"
                            alt="Profile Preview"><br/>
                        <label for="profileImage" class="edit-photo-btn">
                            <i class="bi bi-pencil"></i> Change Image
                        </label>
                    </div>
                    <input type="file" id="profileImage" name="profile_image" class="d-none @error('profile_image') is-invalid @enderror" accept="image/*">
                </div>
                @error('profile_image')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Password fields --}}
            @if(!isset($isEdit) || !$isEdit)
                {{-- Required password fields for create --}}
                <div class="col-md-6">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <input type="password" id="password" name="password" minlength="8" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" minlength="8" class="form-control @error('password_confirmation') is-invalid @enderror" required>
                    @error('password_confirmation')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            @else
                <div class="col-md-6">
                    <label for="password" class="form-label fw-semibold">Password <small class="text-muted">(Leave blank to keep current)</small></label>
                    <input type="password" id="password" name="password" minlength="8" class="form-control @error('password') is-invalid @enderror">
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" minlength="8" class="form-control @error('password_confirmation') is-invalid @enderror">
                    @error('password_confirmation')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            @endif

            {{-- Submit Button --}}
            <div class="col-12 text-end mt-3">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> {{ $buttonText ?? 'Submit' }}
                </button>
            </div>
        </div>
    </div>
</div>


@section('page-script')
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

@endsection