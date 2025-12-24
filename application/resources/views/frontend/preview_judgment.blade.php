@extends('layouts.app')

@section('title', $data->case_no . ' | Preview Judgment')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/custom/dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/custom/watermark.css') }}">
    <style>
        .document-container {
            background-color: white;
            border-radius: 0.75rem;
            padding: 2.5rem;
            margin-top: 2rem;
            margin-bottom: 2rem;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
            position: relative;
            z-index: 1;
            background-image: url("{{ asset('frontend/assets/img/watermark.png') }}");
            background-repeat: no-repeat;
            background-position: center center;
            background-size: 90%;
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15);
        }

        .document-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .document-header h4 {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #343a40;
        }

        .document-header p {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 0;
        }

        .document-section {
            margin-bottom: 1.5rem;
        }

        .document-section h6 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 0.5rem;
        }

        .document-section p,
        .document-section ul {
            font-size: 0.95rem;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .document-section ul {
            list-style: disc;
            padding-left: 1.5rem;
        }

        .judgment-heading {
            text-align: center;
            font-size: 1.3rem;
            font-weight: bold;
            margin-top: 2rem;
            margin-bottom: 1.5rem;
            color: black;
            display: flex;
            justify-content: center;
        }

        .action-link {
            text-decoration: none;
            color: #333;
            margin: 0 5px;
            font-size: 0.9rem;
            white-space: nowrap;
        }

        .action-link:hover {
            color: #0d6efd;
        }

        .action-link i {
            margin-right: 5px;
        }

        .action-link.disabled {
            color: #ccc;
            pointer-events: none;
            cursor: not-allowed;
        }

        /* Top Action Bar matching _legal_top_navbar */
        .top-action-bar {
            background: #fff;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        /* Translator styling */
        .goog-te-gadget-simple {
            background-color: transparent !important;
            border: none !important;
            padding: 0 !important;
            font-size: 13px !important;
            font-family: inherit !important;
        }

        .goog-te-gadget-simple .goog-te-menu-value {
            color: transparent !important;
            font-size: 0 !important;
        }

        .goog-te-gadget-simple .goog-te-menu-value span {
            display: none !important;
        }

        .goog-te-gadget-simple .goog-te-menu-value img {
            display: inline-block !important;
        }

        .goog-te-gadget-icon {
            display: none !important;
        }

        /* Force header to be static (scroll with page) to match subscriber view */
        #header.sticky-top {
            position: relative !important;
            z-index: 1 !important;
            /* Reset z-index as well if needed, though static ignores it */
        }
    </style>
@endpush

@php
    $petitionerEndings = [
        'Plaintiff-Appellants',
        'Defendant Appellants',
        'Plaintiff-Petitioner',
        'Accused Applicant',
        'Accused Petitioner',
        'Condemned Petitioner',
        'Appellants',
        'Appellant',
        'Petitioners',
        'petitioner',
        'State',
        'Defd-Appellant'
    ];

    $respondentEndings = [
        'Plaintiff-Respondents',
        'Defendant Opposite Part',
        'Opposite Parties',
        'Condemned Prisoner',
        'Opposite Party',
        'Respondents',
        'Respondent',
        'the Respondent',
        'State'
    ];

    function getLabel($array, $string)
    {
        $found = null;
        foreach ($array as $ending) {
            if (preg_match('/\b' . preg_quote($ending, '/') . '\b\.?/i', $string)) {
                $found = $ending;
                break;
            }
        }
        return $found ? 'For the ' . $found . ':' : null;
    }
@endphp

@section('content')

    <!-- Top Action Bar -->
    <div class="container-fluid mt-4">
        <div class="top-action-bar mb-3">
            <div class="row align-items-center w-100 g-2">
                <!-- Left: Back Button -->
                <div class="col-md-1 col-12 text-start mb-2 mb-md-0 d-flex align-items-center">
                    <a href="{{ route('home') }}" class="action-link fw-bold">
                        <i class="bi bi-chevron-left"></i> Back
                    </a>
                </div>

                <!-- Center: Action Buttons -->
                <div class="col-md-9 col-12">
                    <div class="text-center d-flex align-items-center justify-content-center flex-nowrap">
                        <a href="{{ route('preview.judgment.print', [$data->id, 'print']) }}" class="action-link"
                            target="_blank">
                            <i class="bi bi-printer"></i> Print
                        </a>
                        <a href="{{ route('preview.judgment.print', [$data->id, 'download']) }}" class="action-link">
                            <i class="bi bi-download"></i> Download
                        </a>

                        <a href="#" class="action-link" data-bs-toggle="modal" data-bs-target="#aiSummaryModal"
                            data-id="{{ $data->id }}">
                            <i class="bi bi-stars"></i> Summarize
                        </a>

                        <!-- Read Aloud Button -->
                        <a href="#" class="action-link" id="read-aloud-btn" onclick="toggleReadAloud(event)">
                            <i class="bi bi-play-circle"></i> <span id="read-aloud-text">Read Aloud</span>
                        </a>
                        <a href="#" class="action-link text-danger" id="stop-read-aloud-btn" onclick="stopReadAloud(event)"
                            style="display: none;">
                            <i class="bi bi-stop-circle"></i> Stop
                        </a>

                        <div class="action-link d-flex align-items-center">
                            <span class="me-1">Translator</span>
                            <div id="google_translate_element"></div>
                        </div>

                        <!-- Disabled/Tooltip Features for Guests -->
                        <a href="#" class="action-link disabled" title="Login to copy to folder">
                            <i class="bi bi-folder"></i> Copy to My Folder
                        </a>

                        <a href="#" class="action-link disabled" title="Login to bookmark">
                            <i class="bi bi-bookmark"></i> Bookmark
                        </a>
                    </div>
                </div>

                <!-- Right: Previous/Next (Disabled/Hidden for Preview) -->
                <div class="col-md-2 col-12 justify-content-end nav-arrows mt-2 mt-md-0 d-flex justify-content-between">
                    <a href="#" class="action-link disabled me-2" style="opacity: 0.5;">
                        <i class="bi bi-chevron-left"></i> Previous
                    </a>
                    <a href="#" class="action-link disabled" style="opacity: 0.5;">
                        Next <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Document Content -->
    <div class="container mt-5">
        <div class="container document-container" id="judgment-content">

            <div class="document-header text-center">
                <h4>In the Supreme Court of Bangladesh ({{ $data->division }})</h4>
                <p>{{ $data->case_no ?? '' }}</p>
            </div>

            <div class="document-section">
                <h6>Published in BLD:</h6>
                <ul>
                    <li>Volume: {{ $data->volume ? $data->volume->number : '' }}</li>
                    <li>Year: {{ $data->published_year ?? '' }}</li>
                    <li>
                        Page: {{ $data->starting_page_no ?? '' }}
                        @if($data->ending_page_no)
                            to {{ $data->ending_page_no }}
                        @endif
                    </li>
                </ul>
            </div>

            <div class="document-section">
                <h6>Decided On:</h6>
                <p>{{ $data->decided_on ?? '' }}</p>
            </div>
            <div class="document-section">
                <h6>Result:</h6>
                <p>{{ $data->result ?? '' }}</p>
            </div>

            <div class="document-section">
                <h6>Parties:</h6>
                <p>{!! html_entity_decode($data->parties, ENT_QUOTES | ENT_HTML5) !!}</p>
            </div>

            <div class="document-section">
                <h6>Hon'ble Judge(s):</h6>
                <p>{!! nl2br(e($data->judge_name ?? '')) !!}</p>
            </div>

            <div class="document-section">
                <h6>Counsels:</h6>
                <h6>{{ getLabel($petitionerEndings, $data->petitioners) }}</h6>
                <p>{!! html_entity_decode($data->petitioners, ENT_QUOTES | ENT_HTML5) !!}</p>

                <h6>{{ getLabel($respondentEndings, $data->respondent) }}</h6>
                <p>{!! html_entity_decode($data->respondent, ENT_QUOTES | ENT_HTML5) !!}</p>
            </div>

            <div class="document-section">
                <h6>Subject Matter:</h6>
                <p>{!! $data->subject ?? '' !!}</p>
            </div>

            <div class="document-section">
                <h6>Jurisdiction:</h6>
                <p>{{ $data->jurisdiction ?? '' }}</p>
            </div>

            <div class="document-section">
                <h6>Related Acts/Rules/Orders:</h6>
                <p>{!! nl2br(e($data->related_act_order_rule ?? '')) !!} </p>
            </div>

            <div class="document-section">
                <h6>Key words:</h6>
                <p>{{ $data->key_words ?? '' }}</p>
            </div>

            <h5 class="judgment-heading">JUDGMENT</h5>

            @php
                $paragraphs = preg_split("/\r\n|\n|\r/", trim($judgmentTextFormatted));
            @endphp

            <div class="document-section" style="text-align: justify; white-space: normal;">
                @foreach($paragraphs as $para)
                    <p style="margin-bottom: 10px;">
                        {!! $para !!}
                    </p>
                @endforeach
            </div>

        </div>
    </div>

    <!-- AI Summary Modal -->
    <div class="modal fade" id="aiSummaryModal" tabindex="-1" aria-labelledby="aiSummaryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="aiSummaryModalLabel">
                        <i class="bi bi-stars text-primary"></i> Judgment Summary
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="aiSummaryContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Analyzing judgment.<br>This may take a few seconds.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="copyToClipboard()">
                        <i class="bi bi-clipboard"></i> Copy Summary
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentUtterance = null;
        let isReading = false;
        let isPaused = false;
        let boundaries = [];

        function wrapWordsInElement(element, wordIndexRef) {
            const childNodes = Array.from(element.childNodes);
            childNodes.forEach(node => {
                if (node.nodeType === Node.TEXT_NODE) {
                    const text = node.textContent;
                    if (!text.trim()) return;
                    const words = text.split(/(\s+)/);
                    const fragment = document.createDocumentFragment();
                    words.forEach(word => {
                        if (word.trim().length > 0) {
                            const span = document.createElement('span');
                            span.textContent = word;
                            span.id = `tts-word-${wordIndexRef.count}`;
                            span.className = 'tts-word-span pointer-cursor';
                            span.style.cursor = 'pointer';
                            span.onclick = (e) => handleWordClick(e, wordIndexRef.count);
                            fragment.appendChild(span);
                            wordIndexRef.count++;
                        } else {
                            fragment.appendChild(document.createTextNode(word));
                        }
                    });
                    node.replaceWith(fragment);
                } else if (node.nodeType === Node.ELEMENT_NODE) {
                    if (node.classList.contains('tts-word-span')) return;
                    wrapWordsInElement(node, wordIndexRef);
                }
            });
        }

        function unwrapWords(element) {
            const spans = element.querySelectorAll('span.tts-word-span');
            spans.forEach(span => {
                const text = document.createTextNode(span.textContent);
                span.replaceWith(text);
            });
            element.normalize();
        }

        function stopReadAloud(e) {
            if (e) e.preventDefault();
            window.speechSynthesis.cancel();
            resetTTSState();
        }

        function resetTTSState() {
            const btnText = document.getElementById('read-aloud-text');
            const icon = document.querySelector('#read-aloud-btn i');
            const stopBtn = document.getElementById('stop-read-aloud-btn');
            const container = document.getElementById('judgment-content');

            isReading = false;
            isPaused = false;

            if (btnText) btnText.innerText = 'Read Aloud';
            if (icon) icon.className = 'bi bi-play-circle';
            if (stopBtn) stopBtn.style.display = 'none';

            currentUtterance = null;
            if (window.activeHighlightSpanId) {
                const old = document.getElementById(window.activeHighlightSpanId);
                if (old) old.classList.remove('bg-warning', 'text-dark');
                window.activeHighlightSpanId = null;
            }
            unwrapWords(container);
        }

        function handleWordClick(e, wordIndex) {
            if (!isReading && !isPaused) {
                startReadAloud(wordIndex);
            } else {
                window.speechSynthesis.cancel();
                startReadAloud(wordIndex);
            }
            e.stopPropagation();
        }

        function startReadAloud(startIndex = 0) {
            const container = document.getElementById('judgment-content');
            const btnText = document.getElementById('read-aloud-text');
            const icon = document.querySelector('#read-aloud-btn i');
            const stopBtn = document.getElementById('stop-read-aloud-btn');

            const spans = container.querySelectorAll('span.tts-word-span');
            if (spans.length === 0) {
                const wordCounter = { count: 0 };
                wrapWordsInElement(container, wordCounter);
            }

            const domSpans = Array.from(container.querySelectorAll('span.tts-word-span'));
            if (domSpans.length === 0) {
                alert("No text found.");
                return;
            }

            let startOffset = 0;
            if (startIndex > 0 && startIndex < domSpans.length) {
                const range = document.createRange();
                range.setStart(container, 0);
                range.setEndBefore(domSpans[startIndex]);
                startOffset = range.toString().length;
            }

            const fullText = container.textContent;
            const textToSpeak = fullText.slice(startOffset);

            if (!textToSpeak.trim()) return;

            window.speechSynthesis.cancel();

            const utterance = new SpeechSynthesisUtterance(textToSpeak);
            isReading = true;
            isPaused = false;
            btnText.innerText = 'Pause';
            icon.className = 'bi bi-pause-circle';
            stopBtn.style.display = 'inline-block';

            window.activeHighlightSpanId = null;

            const allBoundaries = [];
            let globalCursor = 0;
            function buildMap(element) {
                const childNodes = Array.from(element.childNodes);
                childNodes.forEach(node => {
                    if (node.nodeType === Node.TEXT_NODE) {
                        globalCursor += node.textContent.length;
                    } else if (node.nodeType === Node.ELEMENT_NODE) {
                        if (node.classList.contains('tts-word-span')) {
                            const len = node.textContent.length;
                            allBoundaries.push({
                                start: globalCursor,
                                end: globalCursor + len,
                                id: node.id
                            });
                            globalCursor += len;
                        } else {
                            buildMap(node);
                        }
                    }
                });
            }
            globalCursor = 0;
            buildMap(container);
            boundaries = allBoundaries;

            utterance.onboundary = function (event) {
                if (event.name === 'word') {
                    const absIndex = event.charIndex + startOffset;
                    const closest = boundaries.find(b => absIndex >= b.start && absIndex < b.end);

                    if (closest && closest.id !== window.activeHighlightSpanId) {
                        if (window.activeHighlightSpanId) {
                            const old = document.getElementById(window.activeHighlightSpanId);
                            if (old) old.classList.remove('bg-warning', 'text-dark');
                        }

                        window.activeHighlightSpanId = closest.id;
                        const newSpan = document.getElementById(window.activeHighlightSpanId);
                        if (newSpan) {
                            newSpan.classList.add('bg-warning', 'text-dark');
                        }
                    }
                }
            };

            utterance.onend = function () {
                resetTTSState();
            };

            utterance.onerror = function (event) {
                if (event.error !== 'interrupted') {
                    resetTTSState();
                }
            };

            currentUtterance = utterance;
            window.speechSynthesis.speak(utterance);
        }

        function toggleReadAloud(e) {
            if (e) e.preventDefault();

            const btnText = document.getElementById('read-aloud-text');
            const icon = document.querySelector('#read-aloud-btn i');

            if (isReading) {
                if (isPaused) {
                    window.speechSynthesis.resume();
                    isPaused = false;
                    btnText.innerText = 'Pause';
                    icon.className = 'bi bi-pause-circle';
                } else {
                    window.speechSynthesis.pause();
                    isPaused = true;
                    btnText.innerText = 'Resume';
                    icon.className = 'bi bi-play-circle';
                }
            } else {
                startReadAloud(0);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('judgment-content');
            if (container) {
                container.addEventListener('click', (e) => {
                    if (e.target.classList.contains('tts-word-span')) {
                        const parts = e.target.id.split('-');
                        const index = parseInt(parts[2]);
                        if (!isNaN(index)) {
                            handleWordClick(e, index);
                        }
                    }
                });
            }
        });
    </script>
    <script>
        $(document).ready(function () {
            // AI Summarize Logic
            let summaryLoaded = false;
            const aiModal = document.getElementById('aiSummaryModal');
            if (aiModal) {
                aiModal.addEventListener('show.bs.modal', function (event) {
                    if (summaryLoaded) return;

                    const button = event.relatedTarget;
                    const decisionId = button.getAttribute('data-id');
                    const contentDiv = document.getElementById('aiSummaryContent');

                    $.ajax({
                        url: "{{ route('ai.summarize') }}",
                        method: "POST",
                        data: {
                            id: decisionId,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (res) {
                            if (res.success) {
                                let formatted = res.summary
                                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                                    .replace(/\r\n/g, '<br>')
                                    .replace(/\n/g, '<br>');

                                contentDiv.innerHTML = `<div class="text-start" style="font-size: 1.1em; line-height: 1.6;">${formatted}</div>`;
                                summaryLoaded = true;
                            } else {
                                contentDiv.innerHTML = `<div class="text-center text-danger"><i class="bi bi-exclamation-triangle"></i> Failed to generate summary: ${res.error || 'Unknown error'}</div>`;
                            }
                        },
                        error: function (xhr, status, error) {
                            let msg = 'Error connecting to AI service.';
                            try {
                                let responseJSON = JSON.parse(xhr.responseText);
                                if (responseJSON.error) msg += ' ' + responseJSON.error;
                            } catch (e) { }

                            contentDiv.innerHTML = `<div class="text-center text-danger"><i class="bi bi-exclamation-triangle"></i> ${msg}</div>`;
                        }
                    });
                });
            }

            window.copyToClipboard = function () {
                const content = document.getElementById('aiSummaryContent').innerText;
                navigator.clipboard.writeText(content).then(() => {
                    alert('Summary copied to clipboard!');
                }).catch(err => {
                    console.error('Failed to copy: ', err);
                });
            }
        });

        // Google Translate Init
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                includedLanguages: 'en,bn,hi,zh-CN,es,ar',
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
                autoDisplay: false
            }, 'google_translate_element');
        }
    </script>
    <script type="text/javascript"
        src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
@endpush