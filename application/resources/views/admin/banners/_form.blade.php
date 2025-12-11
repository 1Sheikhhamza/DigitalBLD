<div class="card card-success card-outline mb-4">
    <div class="card-header">
        <h3 class="card-title">Banner</h3>
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
            <label for="title" class="form-label">Name</label>
            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                   value="{{ old('title', $banner->title ?? '') }}" required>
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="link" class="form-label">External URL (Optional)</label>
            <input type="url" name="link" class="form-control @error('link') is-invalid @enderror"
                   value="{{ old('link', $banner->link ?? '') }}">
            @error('link')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Description --}}
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" class="form-control ckeditor @error('description') is-invalid @enderror">{{ old('description', $banner->description ?? '') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Image --}}
        <div class="mb-3">
            <label for="image" class="form-label">Banner Image</label>
            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
            <small>Sizee: 1920 X 995</small>
            @error('image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if (!empty($banner->image))
                <div class="mt-2">
                    <img src="{{ asset('uploads/banners/' . $banner->image) }}" alt="Website Banner" style="max-height: 100px;">
                </div>
            @endif
        </div>

        {{-- Submit Button --}}
        <button type="submit" class="btn btn-primary">{{ $buttonText ?? 'Save' }}</button>
    </div>
</div>
