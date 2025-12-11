<div class="card card-success card-outline mb-4">
    <div class="card-header">
        <h3 class="card-title">Service</h3>
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
            <label for="name" class="form-label">Service Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $service->name ?? '') }}" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Description --}}
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" class="form-control ckeditor @error('description') is-invalid @enderror"
                rows="4">{{ old('description', $service->description ?? '') }}</textarea>
            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="icon" class="form-label">Icon <small class="text-muted mt-1">(Size: 100 X 100)</small></label>
            <input type="file" name="icon" class="form-control @error('icon') is-invalid @enderror">
            @error('icon')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if (!empty($service->icon))
            <div class="mt-2">
                <img src="{{ asset('uploads/services/icon/' . $service->icon) }}" alt="Service Banner" style="max-height: 100px;">
            </div>
            @endif
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Image <small class="text-muted mt-1">(Size: 720 X 330)</small></label>
            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">

            <p class="text-muted mt-1">Size: 720 X 330</p>
            @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if (!empty($service->image))
            <div class="mt-2">
                <img src="{{ asset('uploads/services/image/' . $service->image) }}" alt="Service Icon" style="max-height: 100px;">
            </div>
            @endif
        </div>

        <div class="mb-3">
            <label for="banner" class="form-label">Banner <small class="text-muted mt-1">(Size: 720 X 330)</small></label>
            <input type="file" name="banner" class="form-control @error('banner') is-invalid @enderror">

            <p class="text-muted mt-1">Size: 300 X 500</p>
            @error('banner')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if (!empty($service->banner))
            <div class="mt-2">
                <img src="{{ asset('uploads/services/banner/' . $service->banner) }}" alt="Service Icon" style="max-height: 100px;">
            </div>
            @endif
        </div>

        {{-- Meta Title --}}
        <div class="mb-3">
            <label for="meta_title" class="form-label">Meta Title</label>
            <input type="text" name="meta_title" class="form-control @error('meta_title') is-invalid @enderror"
                value="{{ old('meta_title', $service->meta_title ?? '') }}">
            @error('meta_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Meta Description --}}
        <div class="mb-3">
            <label for="meta_description" class="form-label">Meta Description</label>
            <textarea name="meta_description" class="form-control @error('meta_description') is-invalid @enderror"
                rows="2">{{ old('meta_description', $service->meta_description ?? '') }}</textarea>
            @error('meta_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Meta Keywords --}}
        <div class="mb-3">
            <label for="meta_keywords" class="form-label">Meta Keywords</label>
            <input type="text" name="meta_keywords" class="form-control @error('meta_keywords') is-invalid @enderror"
                value="{{ old('meta_keywords', $service->meta_keywords ?? '') }}">
            @error('meta_keywords') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn btn-primary">{{ $buttonText ?? 'Save Service' }}</button>
    </div>
</div>