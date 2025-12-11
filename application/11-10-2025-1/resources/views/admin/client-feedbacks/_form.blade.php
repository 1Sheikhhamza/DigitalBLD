<div class="card card-success card-outline mb-4">
    <div class="card-header">
        <h3 class="card-title">Client Feedback</h3>
    </div>
    <div class="card-body">

        {{-- Client Name --}}
        <div class="mb-3">
            <label for="client_name" class="form-label">Client Name <span class="text-danger">*</span></label>
            <input type="text" name="client_name" class="form-control @error('client_name') is-invalid @enderror" value="{{ old('client_name', $feedback->client_name ?? '') }}" required>
            @error('client_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Client Position --}}
        <div class="mb-3">
            <label for="client_position" class="form-label">Client Position</label>
            <input type="text" name="client_position" class="form-control @error('client_position') is-invalid @enderror" value="{{ old('client_position', $feedback->client_position ?? '') }}">
            @error('client_position') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Company --}}
        <div class="mb-3">
            <label for="company" class="form-label">Company</label>
            <input type="text" name="company" class="form-control @error('company') is-invalid @enderror" value="{{ old('company', $feedback->company ?? '') }}">
            @error('company') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Website --}}
        <div class="mb-3">
            <label for="website" class="form-label">Website</label>
            <input type="url" name="website" class="form-control @error('website') is-invalid @enderror" value="{{ old('website', $feedback->website ?? '') }}">
            @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Feedback --}}
        <div class="mb-3">
            <label for="feedback" class="form-label">Feedback <span class="text-danger">*</span></label>
            <textarea name="feedback" rows="4" class="form-control @error('feedback') is-invalid @enderror">{{ old('feedback', $feedback->feedback ?? '') }}</textarea>
            @error('feedback') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Client Photo --}}
        <div class="mb-3">
            <label for="client_photo" class="form-label">Client Photo</label>
            <input type="file" name="client_photo" class="form-control @error('client_photo') is-invalid @enderror">

            @if (!empty($feedback->client_photo))
                <div class="mt-2">
                    <img src="{{ asset('uploads/feedback/image/' . $feedback->client_photo) }}" alt="Website Banner" style="max-height: 100px;">
                </div>
            @endif

            @error('client_photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Rating --}}
        <div class="mb-3">
            <label for="rating" class="form-label">Rating (1 to 5)</label>
            <select name="rating" class="form-select @error('rating') is-invalid @enderror">
                @for ($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}" {{ old('rating', $feedback->rating ?? 5) == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
            @error('rating') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Status --}}
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" class="form-select @error('status') is-invalid @enderror">
                <option value="1" {{ old('status', $feedback->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status', $feedback->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Submit Button --}}
        <button type="submit" class="btn btn-primary">{{ $buttonText ?? 'Save Feedback' }}</button>
    </div>
</div>
