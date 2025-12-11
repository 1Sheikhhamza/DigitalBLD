@extends('auth.subscribers.layouts.app')

@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection

@section('title', Auth::guard('subscriber')->user()->name.' | Legal Terminology Dictionary')

@section('content')
<div class="container my-5">
    <!-- Page Header -->
    <div class="text-center mb-4">
        <h1 class="display-5 text-primary">Legal Terminology</h1>
        <p class="text-muted">Search and explore legal terms with clear explanations.</p>
    </div>

    <!-- Search Bar -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-8">
            <form method="GET" action="{{ route('subscriber.dictionary.index') }}">
                <div class="input-group shadow-sm rounded">
                    <input type="text" id="dictionarySearch" name="q" class="form-control" placeholder="Search a legal term..." value="{{ $query }}">
                    <button class="btn btn-primary">Search</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Word List -->
    <div class="row">
        <div class="col-md-8 offset-md-2">
            @if($words->count() > 0)
                <div class="list-group">
                    @foreach($words as $word)
                        <div class="card dictionary-item mb-3 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong class="word-title h5" data-original="{{ $word->word }}">{{ $word->word }}</strong>
                                        <p class="meaning-preview text-muted mb-0"
                                           data-full="{{ $word->meaning }}"
                                           data-original="{{ $word->meaning }}">
                                            {!! $word->meaning !!}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-3 d-flex justify-content-center">
                    {{ $words->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="alert alert-info text-center">No terms found matching your search.</div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.dictionary-item {
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer;
}
.dictionary-item:hover {
    transform: scale(1.02);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}
.word-title {
    font-size: 1.25rem;
}
.meaning-preview {
    font-size: 1rem;
    line-height: 1.5;
}
.input-group input {
    border-right: 0;
}
.input-group button {
    border-left: 0;
}
.card-body {
    padding: 1rem 1.25rem;
}
mark {
    background-color: #ffe58f;
    color: #000;
    padding: 0 2px;
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const query = "{{ $query }}".trim();
    let keywords = [];
    if(query){
        keywords = query.split(/\s+/).filter(Boolean);
        const regex = new RegExp(`(${keywords.join('|')})`, 'gi');

        // Highlight words and preview meanings
        document.querySelectorAll('.dictionary-item .word-title, .dictionary-item .meaning-preview').forEach(function(el){
            el.innerHTML = el.dataset.original.replace(regex, '<mark>$1</mark>');
        });
    }

    // Expand/Collapse full meaning on click
    document.querySelectorAll('.dictionary-item').forEach(function(card) {
        card.addEventListener('click', function() {
            const meaning = card.querySelector('.meaning-preview');
            if (meaning.classList.contains('expanded')){
                // Collapse
                meaning.textContent = meaning.dataset.preview;
                meaning.classList.remove('expanded');
            } else {
                // Expand
                if (!meaning.dataset.preview) meaning.dataset.preview = meaning.textContent;
                meaning.textContent = meaning.dataset.full;
                meaning.classList.add('expanded');
            }

            // Re-apply highlighting after toggle
            if(keywords.length > 0){
                const regex = new RegExp(`(${keywords.join('|')})`, 'gi');
                meaning.innerHTML = meaning.textContent.replace(regex, '<mark>$1</mark>');
            }
        });
    });
});
</script>
@endsection
