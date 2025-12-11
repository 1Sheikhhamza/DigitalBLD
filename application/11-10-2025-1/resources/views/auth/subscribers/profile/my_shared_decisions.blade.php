@extends('auth.subscribers.layouts.app')
@section('content')

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
    <form action="{{ route('subscriber.files.downloadMultiple') }}" method="POST">
        @csrf

        <div class="row g-3">
            @forelse($sharedDecisions as $share)
            <div class="col-6 col-md-4 col-lg-3">
                <a class="card-item" href="{{ route('subscriber.sharedDecision', Crypt::encrypt($share->id)) }}">
                    @if($share && $share->decision)
                        <h5 class="card-title clamp-3 mb-2">
                            {!! html_entity_decode($share->decision->parties, ENT_QUOTES | ENT_HTML5) !!}
                        </h5>

                        {{-- Case No (1 line only) --}}
                        <div class="text-sm text-dark clamp-1 text-center mb-2">
                            {!! $share->decision->case_no !!}
                        </div>

                        {{-- Judgment (max 4 lines, fixed height, fills remaining space) --}}
                        <p class="card-text clamp-4 flex-grow-1">
                            {!! strip_tags($share->decision->judgment) ?? 'No description available.' !!}
                        </p>


                        <p class="text-success">Shared by: {{ $share->sender->name ?? null }}</p>
                    @endif
                </a>
            </div>
            @empty
            <p>No decision yet.</p>
            @endforelse
        </div>
    </form>
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