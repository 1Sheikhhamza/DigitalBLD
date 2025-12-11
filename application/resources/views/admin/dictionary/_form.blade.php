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
        <div class="mb-3">
            <label for="word" class="form-label">Word <span class="text-danger">*</span></label>
            <input type="text" name="word" class="form-control @error('word') is-invalid @enderror"
                value="{{ old('word', $service->word ?? '') }}" required>
            @error('word') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Description --}}
        <div class="mb-3">
            <label for="meaning" class="form-label">Meaning</label>
            <textarea name="meaning" class="form-control ckeditor @error('meaning') is-invalid @enderror"
                rows="4">{{ old('meaning', $service->meaning ?? '') }}</textarea>
            @error('meaning') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        

        {{-- Submit --}}
        <button type="submit" class="btn btn-primary">{{ $buttonText ?? 'Save Data' }}</button>
    </div>
</div>