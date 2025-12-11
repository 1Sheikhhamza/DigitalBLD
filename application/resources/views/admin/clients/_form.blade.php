<div class="card card-success card-outline mb-4">
    <div class="card-header">
        <h3 class="card-title">Client</h3>
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
            <label for="name" class="form-label">Client Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                value="{{ old('name', $client->name ?? '') }}" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Logo --}}
        <div class="mb-3">
            <label for="image" class="form-label">Logo</label>
            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">

            @if (!empty($client->logo))
                <div class="mt-2">
                    <img src="{{ asset('uploads/link/' . $client->logo) }}" alt="Website Banner" style="max-height: 100px;">
                </div>
            @endif

            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Testimonial --}}
        <div class="mb-3">
            <label for="testimonial" class="form-label">Testimonial</label>
            <textarea name="testimonial" class="form-control @error('testimonial') is-invalid @enderror" rows="3">{{ old('testimonial', $client->testimonial ?? '') }}</textarea>
            @error('testimonial') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Website --}}
        <div class="mb-3">
            <label for="website" class="form-label">Website URL</label>
            <input type="url" name="website" class="form-control @error('website') is-invalid @enderror" 
                value="{{ old('website', $client->website ?? '') }}">
            @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Meta Title --}}
        <div class="mb-3">
            <label for="meta_title" class="form-label">Meta Title</label>
            <input type="text" name="meta_title" class="form-control @error('meta_title') is-invalid @enderror" 
                value="{{ old('meta_title', $client->meta_title ?? '') }}">
            @error('meta_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Meta Description --}}
        <div class="mb-3">
            <label for="meta_description" class="form-label">Meta Description</label>
            <textarea name="meta_description" class="form-control @error('meta_description') is-invalid @enderror" rows="2">{{ old('meta_description', $client->meta_description ?? '') }}</textarea>
            @error('meta_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Meta Keywords --}}
        <div class="mb-3">
            <label for="meta_keywords" class="form-label">Meta Keywords</label>
            <input type="text" name="meta_keywords" class="form-control @error('meta_keywords') is-invalid @enderror" 
                value="{{ old('meta_keywords', $client->meta_keywords ?? '') }}">
            @error('meta_keywords') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Status --}}
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                <option value="1" {{ old('status', $client->status ?? 0) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status', $client->status ?? 0) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn btn-primary">{{ $buttonText ?? 'Save Client' }}</button>
    </div>
</div>
