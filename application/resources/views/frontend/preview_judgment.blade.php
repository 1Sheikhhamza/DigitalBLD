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
            margin: 0 10px;
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
    <div class="container mt-4">
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
                    <div class="text-center d-flex align-items-center justify-content-center flex-wrap">
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

                        <div class="action-link d-flex align-items-center">
                            <label class="me-1 mb-0">Translator:</label>
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
        <div class="container document-container">

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