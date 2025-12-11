<div class="card card-success card-outline mb-4">
    <div class="card-header">
        <h3 class="card-title">Photo</h3>
    </div>
    <div class="card-body">
        {{-- Title --}}
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                value="{{ old('title', $photo->title ?? '') }}">
            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Image --}}
        <div class="mb-3">
            <label for="image" class="form-label">Image <span class="text-danger">*</span></label>
            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" {{ isset($photo) ? '' : 'required' }}>
            @if(isset($photo->image))
                <img src="{{ asset('uploads/photos/' . $photo->image) }}" alt="Photo" style="max-width: 150px; margin-top: 5px;">
            @endif
            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Status --}}
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                <option value="0" {{ old('status', $photo->status ?? 0) == 0 ? 'selected' : '' }}>Inactive</option>
                <option value="1" {{ old('status', $photo->status ?? 0) == 1 ? 'selected' : '' }}>Active</option>
            </select>
            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary">{{ $buttonText ?? 'Save Photo' }}</button>
    </div>
</div>
