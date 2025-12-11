@extends('auth.subscribers.layouts.app')
@section('content')
<style>
    .delete-btn {
        position: absolute;
        top: 6px;
        right: 6px;
        background: rgba(255, 0, 0, 0.8);
        color: #fff;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        z-index: 10000;
    }

    .delete-card-item {
        position: relative;
        z-index: 10000;
    }
</style>

<div class="container py-4">
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif


    <h3 class="mb-4">Shared Legal Decision</h3>

    <!-- Files Grid -->
   

        <div class="row g-3">
            @forelse($sharedDecisions as $share)
            <div class="col-6 col-md-4 col-lg-3">

                <div class="delete-card-item">

                    <form action="{{ route('subscriber.sharedDecision.delete', $share->id) }}"
                        method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this shared decision?')"
                        class="position-absolute"
                        style="top: 5px; right: 5px;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete-btn" title="Delete">
                            ✕
                        </button>
                    </form>

                    <!-- Existing clickable card -->
                    <a class="card-item"
                        href="{{ route('subscriber.sharedDecision', Crypt::encrypt($share->id)) }}"
                        style="display:block; padding-top:35px;">

                        @if($share && $share->decision)
                        <h5 class="card-title clamp-3 mb-2">
                            {!! html_entity_decode($share->decision->parties, ENT_QUOTES | ENT_HTML5) !!}
                        </h5>

                        <div class="text-sm text-dark clamp-1 text-center mb-2">
                            {!! $share->decision->case_no !!}
                        </div>

                        <p class="card-text clamp-4 flex-grow-1">
                            {!! strip_tags($share->decision->judgment) ?? 'No description available.' !!}
                        </p>

                        <p class="text-success">
                            Shared by: {{ $share->sender->name ?? null }}
                        </p>
                        @endif

                    </a>

                </div>

            </div>
            @empty
            <p>No decision yet.</p>
            @endforelse
        </div>
</div>
@endsection

@push('scripts')
<script>
    function removeFromFolder(decisionId, folderId) {
        fetch("{{ route('subscriber.remove-decision-from-folder') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    decision_id: decisionId,
                    folder_id: folderId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // alert(data.message);
                    window.location.reload()
                } else {
                    alert(data.message || 'Something went wrong.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to remove decision from folder.');
            });
    }
</script>

@endpush