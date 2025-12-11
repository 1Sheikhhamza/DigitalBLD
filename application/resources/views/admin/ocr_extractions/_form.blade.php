@php
    $currentYear = date('Y');
    $years = range($currentYear, $currentYear - 49); // last 50 years
@endphp

<div class="card card-success card-outline mb-4">
    <div class="card-header">
        <h3 class="card-title">Legal Decision</h3>
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

        <!-- Volume Dropdown -->
        <div class="mb-3">
            <label for="volume_id" class="form-label">Volume</label>
            <select name="volume_id" class="form-select @error('volume_id') is-invalid @enderror" required>
                <option value="">-- Select Volume --</option>
                @foreach($volumeList as $id => $volumeName)
                <option value="{{ $id }}" {{ old('volume_id', $subscription->volume_id ?? '') == $id ? 'selected' : '' }}>
                    {{ $volumeName }}
                </option>
                @endforeach
            </select>
            @error('volume_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Year Dropdown -->
        <div class="mb-3">
            <label for="year" class="form-label">Year</label>
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
            <label for="month" class="form-label">Documents</label>
            <input type="file" name="document" required class="form-control">
            @error('month')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>
