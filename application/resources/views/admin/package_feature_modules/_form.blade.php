<div class="card card-success card-outline mb-4">
    <div class="card-header">
        <h3 class="card-title">{{ $buttonText }} Feature Module</h3>
    </div>
    <div class="card-body">
        @csrf



        <div class="mb-3">
            <label class="form-label">Feature</label>
            <select name="feature_id" class="form-select" required>
                <option value="">Select Feature</option>
                @foreach($features as $feature)
                <option value="{{ $feature->id }}" {{ old('feature_id') == $feature->id ? 'selected' : '' }}>{{ $feature->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Module Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $module->name ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Slug</label>
            <input type="text" name="slug" class="form-control" value="{{ old('slug', $module->name ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Route Name</label>
            <input type="text" name="route_name" class="form-control" value="{{ old('route_name', $module->name ?? '') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $module->name ?? '') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>






        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>