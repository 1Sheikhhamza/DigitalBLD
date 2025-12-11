<div class="card card-success card-outline mb-4">
    <div class="card-header">
        <h3 class="card-title">Team Member</h3>
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

        {{-- Name --}}
        <div class="mb-3">
            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $team->name ?? '') }}" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Designation --}}
        <div class="mb-3">
            <label for="designation" class="form-label">Designation <span class="text-danger">*</span></label>
            <input type="text" name="designation" class="form-control @error('designation') is-invalid @enderror"
                value="{{ old('designation', $team->designation ?? '') }}" required>
            @error('designation') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Photo --}}
        <div class="mb-3">
            <label for="image" class="form-label">Photo</label>
            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
            @if(isset($team->photo))
                <img src="{{ asset('uploads/teams/' . $team->photo) }}" alt="Photo" style="max-width: 150px; margin-top: 5px;">
            @endif
            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Bio --}}
        <div class="mb-3">
            <label for="bio" class="form-label">Bio</label>
            <textarea name="bio" rows="4" class="form-control @error('bio') is-invalid @enderror">{{ old('bio', $team->bio ?? '') }}</textarea>
            @error('bio') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Facebook --}}
        <div class="mb-3">
            <label for="facebook" class="form-label">Facebook URL</label>
            <input type="url" name="facebook" class="form-control @error('facebook') is-invalid @enderror"
                value="{{ old('facebook', $team->facebook ?? '') }}">
            @error('facebook') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Linkedin --}}
        <div class="mb-3">
            <label for="linkedin" class="form-label">LinkedIn URL</label>
            <input type="url" name="linkedin" class="form-control @error('linkedin') is-invalid @enderror"
                value="{{ old('linkedin', $team->linkedin ?? '') }}">
            @error('linkedin') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email', $team->email ?? '') }}">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Status --}}
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                <option value="0" {{ old('status', $team->status ?? 0) == 0 ? 'selected' : '' }}>Inactive</option>
                <option value="1" {{ old('status', $team->status ?? 0) == 1 ? 'selected' : '' }}>Active</option>
            </select>
            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn btn-primary">{{ $buttonText ?? 'Save Team Member' }}</button>
    </div>
</div>
