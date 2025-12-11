@php
    $currentYear = date('Y');
    $years = range($currentYear, $currentYear - 49); // last 50 years
@endphp
<div class="card card-success card-outline mb-4">
    <div class="card-header">
        <h3 class="card-title">Volume</h3>
        <div class="card-tools"> <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse"> <i data-lte-icon="expand" class="bi bi-plus-lg"></i> <i data-lte-icon="collapse" class="bi bi-dash-lg"></i> </button> <button type="button" class="btn btn-tool" data-lte-toggle="card-remove"> <i class="bi bi-x-lg"></i> </button> </div>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <label for="number" class="form-label">Volume Number</label>
            <input type="number" name="number" class="form-control @error('number') is-invalid @enderror" value="{{ old('number', $volume->number ?? '') }}" required>
            @error('number')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>



        <div class="mb-3">
            <label for="year" class="form-label">Published Year</label>
            <select name="year" class="form-select @error('year') is-invalid @enderror" required>
                <option value="">-- Select Year --</option>
                @foreach($years as $year)
                <option value="{{ $year }}" {{ old('year', $subscription->year ?? '') == $year ? 'selected' : '' }}>
                    {{ $year }}
                </option>
                @endforeach
            </select>
            @error('year')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Icon/Image</label>
            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">

            @if (!empty($volume->image))
                <div class="mt-2">
                    <img src="{{ asset('uploads/volume/' . $volume->image) }}" alt="Website Banner" style="max-height: 100px;">
                </div>
            @endif

            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="index_file" class="form-label">Index (PDF Only)</label>
            <input type="file" name="index_file" class="form-control @error('index_file') is-invalid @enderror">

            @if (!empty($volume->index_file))
                <div class="mt-2">
                    <img src="{{ asset('uploads/volume/' . $volume->index_file) }}" alt="Website Banner" style="max-height: 100px;">
                </div>
            @endif

            @error('index_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        
        <div class="mb-3">
            <label for="month" class="form-label">Documents</label>
            <input type="file" name="document" required class="form-control">
            @error('month')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>

    </div>