<div class="card card-success card-outline mb-4">
    <div class="card-header">
        <h3 class="card-title">{{ $buttonText }} Feature</h3>
    </div>
    <div class="card-body">
        @csrf
        <div class="mb-3">
            <label class="form-label">Feature Name</label>
            <input type="text" class="form-control" name="name" value="{{ old('name', $packageFeature->name ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Feature description</label>
            <input type="text" class="form-control" name="description" value="{{ old('description', $packageFeature->description ?? '') }}">
        </div>

        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>
