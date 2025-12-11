<div class="card card-success card-outline mb-4">
    <div class="card-header">
        <h3 class="card-title">Subscription</h3>
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
        {{-- Subscriber --}}
        <div class="mb-3">
            <label for="subscriber_id" class="form-label">Subscriber</label>
            <select name="subscriber_id" class="form-select @error('subscriber_id') is-invalid @enderror" required>
                <option value="">-- Select Subscriber --</option>
                @foreach($subscriberList as $id => $name)
                <option value="{{ $id }}" {{ old('subscriber_id', $subscription->subscriber_id ?? '') == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
                @endforeach
            </select>
            @error('subscriber_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Package --}}
        <div class="mb-3">
            <label for="package_id" class="form-label">Package</label>
            <select name="package_id" class="form-select @error('package_id') is-invalid @enderror" required>
                <option value="">-- Select Package --</option>
                @foreach($packageList as $id => $name)
                <option value="{{ $id }}" {{ old('package_id', $subscription->package_id ?? '') == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
                @endforeach
            </select>
            @error('package_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Subscription Date --}}
        <div class="mb-3">
            <label for="subscription_date" class="form-label">Subscription Date</label>
            <input type="date" name="subscription_date" class="form-control @error('subscription_date') is-invalid @enderror" value="{{ old('subscription_date', $subscription->subscription_date ?? '') }}" required>
            @error('subscription_date')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Expire Date --}}
        <div class="mb-3">
            <label for="expire_date" class="form-label">Expire Date</label>
            <input type="date" name="expire_date" class="form-control @error('expire_date') is-invalid @enderror" value="{{ old('expire_date', $subscription->expire_date ?? '') }}" required>
            @error('expire_date')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Fee --}}
        <div class="mb-3">
            <label for="fee" class="form-label">Fee</label>
            <div class="input-group">
                <span class="input-group-text">&#2547;</span>
                <input type="number" step="0.01" name="fee" class="form-control @error('fee') is-invalid @enderror" value="{{ old('fee', $subscription->fee ?? '') }}" required>
                @error('fee')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Payment Method --}}
        <div class="mb-3">
            <label for="payment_method" class="form-label">Payment Method</label>
            <input type="text" name="payment_method" class="form-control @error('payment_method') is-invalid @enderror" value="{{ old('payment_method', $subscription->payment_method ?? '') }}">
            @error('payment_method')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">e.g., Bkash, Nagad, Card, etc.</small>
        </div>

        {{-- Transaction ID --}}
        <div class="mb-3">
            <label for="transaction_id" class="form-label">Transaction ID</label>
            <input type="text" name="transaction_id" class="form-control @error('transaction_id') is-invalid @enderror" value="{{ old('transaction_id', $subscription->transaction_id ?? '') }}">
            @error('transaction_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Status --}}
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                <option value="1" {{ old('status', $subscription->status ?? '') == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status', $subscription->status ?? '') == 0 ? 'selected' : '' }}>Inactive</option>
                <option value="2" {{ old('status', $subscription->status ?? '') == 2 ? 'selected' : '' }}>Pending</option>
                <option value="3" {{ old('status', $subscription->status ?? '') == 3 ? 'selected' : '' }}>Cancelled</option>
            </select>
            @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Remarks --}}
        <div class="mb-3">
            <label for="remarks" class="form-label">Remarks</label>
            <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" rows="3">{{ old('remarks', $subscription->remarks ?? '') }}</textarea>
            @error('remarks')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>
