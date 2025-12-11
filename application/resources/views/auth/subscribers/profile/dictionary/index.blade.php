@extends('auth.subscribers.layouts.app')

@section('seodetails')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection

@section('title', Auth::guard('subscriber')->user()->name . ' | Legal Terminology Dictionary')

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
                <form method="GET" action="{{ route('subscriber.dictionary.index') }}" id="searchForm">
                    <div class="input-group shadow-sm rounded" style="position: relative;">
                        <input type="text" id="dictionarySearch" name="q" class="form-control"
                            placeholder="Search a legal term..." value="{{ $query }}" autocomplete="off">
                        <button class="btn btn-primary" type="submit">Search</button>
                        <!-- Autocomplete Dropdown -->
                        <div id="autocompleteDropdown" class="autocomplete-dropdown"></div>
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
                                            <strong class="word-title h5"
                                                data-original="{{ $word->word }}">{{ $word->word }}</strong>
                                            <p class="meaning-preview text-muted mb-0" data-full="{{ $word->meaning }}"
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
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
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

        /* Autocomplete Dropdown */
        .autocomplete-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #dee2e6;
            border-top: none;
            border-radius: 0 0 0.375rem 0.375rem;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .autocomplete-dropdown.show {
            display: block;
        }

        .autocomplete-item {
            padding: 0.75rem 1rem;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s ease;
        }

        .autocomplete-item:last-child {
            border-bottom: none;
        }

        .autocomplete-item:hover,
        .autocomplete-item.active {
            background-color: #f8f9fa;
        }

        .autocomplete-item.active {
            background-color: #e9ecef;
        }

        .autocomplete-item strong {
            color: #0d6efd;
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('dictionarySearch');
            const dropdown = document.getElementById('autocompleteDropdown');
            const searchForm = document.getElementById('searchForm');
            let debounceTimer;
            let currentFocus = -1;

            // Autocomplete functionality
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                const query = this.value.trim();

                if (query.length < 2) {
                    dropdown.classList.remove('show');
                    return;
                }

                debounceTimer = setTimeout(() => {
                    console.log('Fetching autocomplete for:', query);

                    fetch(`{{ route('subscriber.dictionary.autocomplete') }}?q=${encodeURIComponent(query)}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin'
                    })
                        .then(response => {
                            console.log('Response status:', response.status);
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Autocomplete data:', data);
                            if (data.length > 0) {
                                dropdown.innerHTML = data.map(word =>
                                    `<div class="autocomplete-item">${highlightMatch(word, query)}</div>`
                                ).join('');
                                dropdown.classList.add('show');
                                attachItemClickHandlers();
                            } else {
                                dropdown.classList.remove('show');
                            }
                        })
                        .catch(error => {
                            console.error('Autocomplete error:', error);
                            dropdown.classList.remove('show');
                        });
                }, 300);
            });

            // Keyboard navigation
            searchInput.addEventListener('keydown', function (e) {
                const items = dropdown.querySelectorAll('.autocomplete-item');

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    currentFocus++;
                    if (currentFocus >= items.length) currentFocus = 0;
                    setActive(items);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    currentFocus--;
                    if (currentFocus < 0) currentFocus = items.length - 1;
                    setActive(items);
                } else if (e.key === 'Enter') {
                    if (currentFocus > -1 && items[currentFocus]) {
                        e.preventDefault();
                        items[currentFocus].click();
                    }
                } else if (e.key === 'Escape') {
                    dropdown.classList.remove('show');
                    currentFocus = -1;
                }
            });

            function setActive(items) {
                items.forEach((item, index) => {
                    item.classList.remove('active');
                    if (index === currentFocus) {
                        item.classList.add('active');
                        item.scrollIntoView({ block: 'nearest' });
                    }
                });
            }

            function attachItemClickHandlers() {
                dropdown.querySelectorAll('.autocomplete-item').forEach(item => {
                    item.addEventListener('click', function () {
                        const text = this.textContent;
                        searchInput.value = text;
                        dropdown.classList.remove('show');
                        searchForm.submit();
                    });
                });
            }

            function highlightMatch(word, query) {
                const regex = new RegExp(`(${query})`, 'gi');
                return word.replace(regex, '<strong>$1</strong>');
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function (e) {
                if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.remove('show');
                    currentFocus = -1;
                }
            });

            // Existing code for highlighting search results
            const query = "{{ $query }}".trim();
            let keywords = [];
            if (query) {
                keywords = query.split(/\s+/).filter(Boolean);
                const regex = new RegExp(`(${keywords.join('|')})`, 'gi');

                // Highlight words and preview meanings
                document.querySelectorAll('.dictionary-item .word-title, .dictionary-item .meaning-preview').forEach(function (el) {
                    el.innerHTML = el.dataset.original.replace(regex, '<mark>$1</mark>');
                });
            }

            // Expand/Collapse full meaning on click
            document.querySelectorAll('.dictionary-item').forEach(function (card) {
                card.addEventListener('click', function () {
                    const meaning = card.querySelector('.meaning-preview');
                    if (meaning.classList.contains('expanded')) {
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
                    if (keywords.length > 0) {
                        const regex = new RegExp(`(${keywords.join('|')})`, 'gi');
                        meaning.innerHTML = meaning.textContent.replace(regex, '<mark>$1</mark>');
                    }
                });
            });
        });
    </script>
@endsection