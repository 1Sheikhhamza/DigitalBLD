<div class="card card-success card-outline mb-4">
    <div class="card-header">
        <h3 class="card-title">Career</h3>
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

        {{-- Title --}}
        <div class="mb-3">
            <label for="title" class="form-label">Job Title <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                value="{{ old('title', $career->title ?? '') }}" required>
            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Department --}}
        <div class="mb-3">
            <label for="department" class="form-label">Department</label>
            <input type="text" name="department" class="form-control @error('department') is-invalid @enderror"
                value="{{ old('department', $career->department ?? '') }}">
            @error('department') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Job Type --}}
        <div class="mb-3">
            <label for="job_type" class="form-label">Job Type <span class="text-danger">*</span></label>
            <select name="job_type" class="form-control @error('job_type') is-invalid @enderror" required>
                @php
                    $types = ['full-time' => 'Full-Time', 'part-time' => 'Part-Time', 'internship' => 'Internship', 'contract' => 'Contract'];
                    $selectedType = old('job_type', $career->job_type ?? '');
                @endphp
                <option value="">-- Select Job Type --</option>
                @foreach($types as $key => $label)
                    <option value="{{ $key }}" {{ $selectedType === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('job_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Job Level --}}
        <div class="mb-3">
            <label for="job_level" class="form-label">Job Level</label>
            <select name="job_level" class="form-control @error('job_level') is-invalid @enderror">
                @php
                    $levels = ['entry' => 'Entry', 'mid' => 'Mid', 'senior' => 'Senior', 'manager' => 'Manager'];
                    $selectedLevel = old('job_level', $career->job_level ?? '');
                @endphp
                <option value="">-- Select Job Level --</option>
                @foreach($levels as $key => $label)
                    <option value="{{ $key }}" {{ $selectedLevel === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('job_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Vacancy --}}
        <div class="mb-3">
            <label for="vacancy" class="form-label">Vacancy</label>
            <input type="number" name="vacancy" class="form-control @error('vacancy') is-invalid @enderror"
                value="{{ old('vacancy', $career->vacancy ?? '') }}" min="0" step="1">
            @error('vacancy') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Description --}}
        <div class="mb-3">
            <label for="description" class="form-label">Job Description <span class="text-danger">*</span></label>
            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="5" required>{{ old('description', $career->description ?? '') }}</textarea>
            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Responsibilities --}}
        <div class="mb-3">
            <label for="responsibilities" class="form-label">Responsibilities</label>
            <textarea name="responsibilities" class="form-control @error('responsibilities') is-invalid @enderror" rows="4">{{ old('responsibilities', $career->responsibilities ?? '') }}</textarea>
            @error('responsibilities') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Requirements --}}
        <div class="mb-3">
            <label for="requirements" class="form-label">Requirements</label>
            <textarea name="requirements" class="form-control @error('requirements') is-invalid @enderror" rows="4">{{ old('requirements', $career->requirements ?? '') }}</textarea>
            @error('requirements') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Education --}}
        <div class="mb-3">
            <label for="education" class="form-label">Education</label>
            <textarea name="education" class="form-control @error('education') is-invalid @enderror" rows="3">{{ old('education', $career->education ?? '') }}</textarea>
            @error('education') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Location --}}
        <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" name="location" class="form-control @error('location') is-invalid @enderror"
                value="{{ old('location', $career->location ?? '') }}">
            @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Salary --}}
        <div class="mb-3">
            <label for="salary" class="form-label">Salary</label>
            <input type="text" name="salary" class="form-control @error('salary') is-invalid @enderror"
                value="{{ old('salary', $career->salary ?? '') }}">
            @error('salary') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Apply Email --}}
        <div class="mb-3">
            <label for="apply_email" class="form-label">Apply Email</label>
            <input type="email" name="apply_email" class="form-control @error('apply_email') is-invalid @enderror"
                value="{{ old('apply_email', $career->apply_email ?? '') }}">
            @error('apply_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Apply URL --}}
        <div class="mb-3">
            <label for="apply_url" class="form-label">Apply URL</label>
            <input type="url" name="apply_url" class="form-control @error('apply_url') is-invalid @enderror"
                value="{{ old('apply_url', $career->apply_url ?? '') }}">
            @error('apply_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Deadline --}}
        <div class="mb-3">
            <label for="deadline" class="form-label">Deadline</label>
            <input type="date" name="deadline" class="form-control @error('deadline') is-invalid @enderror"
                value="{{ old('deadline', $career->deadline ?? '') }}">
            @error('deadline') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Published At --}}
        <div class="mb-3">
            <label for="published_at" class="form-label">Published At</label>
            <input type="datetime-local" name="published_at" class="form-control @error('published_at') is-invalid @enderror"
                value="{{ old('published_at', isset($career->published_at) ? $career->published_at->format('Y-m-d\TH:i') : '') }}">
            @error('published_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Job Status --}}
        <div class="mb-3">
            <label for="job_status" class="form-label">Job Status</label>
            <select name="job_status" class="form-control @error('job_status') is-invalid @enderror" required>
                @php
                    $jobStatuses = ['published' => 'Published', 'unpublished' => 'Unpublished'];
                    $selectedJobStatus = old('job_status', $career->job_status ?? 'published');
                @endphp
                @foreach($jobStatuses as $key => $label)
                    <option value="{{ $key }}" {{ $selectedJobStatus === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('job_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Status --}}
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                <option value="1" {{ old('status', $career->status ?? 0) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status', $career->status ?? 0) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn btn-primary">{{ $buttonText ?? 'Save Career' }}</button>
    </div>
</div>
