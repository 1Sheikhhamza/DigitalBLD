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


    <h3 class="mb-4">Legal Decision in "{{ $folder->name }}"</h3>

    <!-- Files Grid -->
    <form action="{{ route('subscriber.files.downloadMultiple') }}" method="POST">
        @csrf

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @forelse($copiedDecisions as $decision)
            @if(isset($decision->decision))
            <div class="col-6 col-md-4 col-lg-4">
                <div class="card text-start position-relative shadow-sm h-100">

                    {{-- Dropdown menu (top-right) --}}
                    <div class="position-absolute top-0 end-0 m-1 dropdown">
                        <button class="btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a href="{{ route('subscriber.legal-search.downloadPdf', $decision->decision->id) }}" class="dropdown-item">
                                    <i class="bi bi-download me-1"></i> Download
                                </a>
                            </li>
                            <li>
                                <a href="#" class="dropdown-item text-danger"
                                    onclick="event.preventDefault(); if(confirm('Remove this decision from the folder?')) { removeFromFolder({{ $decision->decision->id }}, {{ $folder->id }}); }">
                                    <i class="bi bi-trash me-1"></i> Remove from folder
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- Card Body with link --}}
                    <a class="card-item d-flex flex-column h-100 w-100 p-3 border rounded shadow-sm text-decoration-none"
                        href="{{ route('subscriber.myDecision', Crypt::encrypt($decision->id)) }}">

                        {{-- Parties (3 lines max) --}}
                        <h5 class="card-title clamp-3 mb-2">
                            {!! $decision->decision->parties !!}
                        </h5>

                        {{-- Case No (1 line max) --}}
                        <div class="text-sm text-dark clamp-1 text-center mb-2">
                            {!! $decision->decision->case_no !!}
                        </div>

                        {{-- Judgment (4 lines max) --}}
                        <p class="card-text clamp-4 flex-grow-1">
                            {!! strip_tags($decision->decision->judgment) ?? 'No description available.' !!}
                        </p>
                    </a>
                </div>
            </div>

            @endif

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