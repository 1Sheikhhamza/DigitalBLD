<div class="card card-primary card-outline mb-4">
    <div class="card-body">
        <div class="mb-3">
            <label for="message" class="form-label">Announcement Message <span class="text-danger">*</span></label>
            <textarea name="message" class="form-control" rows="3"
                required>{{ old('message', $announcement->message ?? '') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="target_type" class="form-label">Target Audience <span class="text-danger">*</span></label>
            <select name="target_type" id="target_type" class="form-control" onchange="toggleUserSelect()">
                <option value="all" {{ old('target_type', $announcement->target_type ?? '') == 'all' ? 'selected' : '' }}>
                    All Subscribers</option>
                <option value="individual" {{ old('target_type', $announcement->target_type ?? '') == 'individual' ? 'selected' : '' }}>Individual Subscriber</option>
            </select>
        </div>

        <div class="mb-3" id="user_select_wrapper" style="display: none;">
            <label for="user_id" class="form-label">Select Subscriber</label>
            <select name="user_id" id="user_id" class="form-control select2Data">
                <option value="">Select Subscriber</option>
                @foreach($subscribers as $subscriber)
                    <option value="{{ $subscriber->id }}" {{ old('user_id', $announcement->user_id ?? '') == $subscriber->id ? 'selected' : '' }}>
                        {{ $subscriber->name }} ({{ $subscriber->email }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="is_active" class="form-label">Status</label>
            <select name="is_active" class="form-control">
                <option value="1" {{ old('is_active', $announcement->is_active ?? '') == 1 ? 'selected' : '' }}>Active
                </option>
                <option value="0" {{ old('is_active', $announcement->is_active ?? '') == 0 ? 'selected' : '' }}>Inactive
                </option>
            </select>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>

<script>
    function toggleUserSelect() {
        var targetType = document.getElementById('target_type').value;
        var userWrapper = document.getElementById('user_select_wrapper');
        if (targetType === 'individual') {
            userWrapper.style.display = 'block';
        } else {
            userWrapper.style.display = 'none';
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        toggleUserSelect();
    });
</script>