<div class="card card-success card-outline mb-4">
    <div class="card-header">
        <h3 class="card-title">Video</h3>
    </div>
    <div class="card-body">

        {{-- Title --}}
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                value="{{ old('title', $video->title ?? '') }}">
            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Video URL --}}
        <div class="mb-3">
            <label for="video_url" class="form-label">YouTube Video URL <span class="text-danger">*</span></label>
            <input type="url" name="video_url" class="form-control @error('video_url') is-invalid @enderror"
                value="{{ old('video_url', $video->video_url ?? '') }}" required>
            @error('video_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    

        {{-- Status --}}
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                <option value="0" {{ old('status', $video->status ?? 0) == 0 ? 'selected' : '' }}>Inactive</option>
                <option value="1" {{ old('status', $video->status ?? 0) == 1 ? 'selected' : '' }}>Active</option>
            </select>
            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary">{{ $buttonText ?? 'Save Video' }}</button>
    </div>
</div>
