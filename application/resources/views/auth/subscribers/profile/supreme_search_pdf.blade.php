@extends('auth.subscribers.layouts.app')
@section('title', 'View Judgment | Digital BLD')

@push('styles')
    <style>
        :root {
            --primary-color: #0047AB;
            --primary-dark: #002D62;
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --text-color: #334155;
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        body {
            background: white;
            margin: 0;
            padding: 0;
        }

        .pdf-viewer-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
            background: white;
        }

        .pdf-header {
            background: white;
            border-bottom: 2px solid var(--border-color);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-md);
            top: 0;
            z-index: 100;
            position: relative;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-back:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 71, 171, 0.3);
        }

        .case-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-color);
            flex: 1;
            text-align: center;
            margin: 0 1.5rem;
            min-width: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: white;
            color: var(--text-color);
            border: 2px solid var(--border-color);
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            transform: translateY(-2px);
        }

        .pdf-content {
            flex: 1;
            overflow: hidden;
            padding: 1rem;
            background: white;
        }

        .pdf-frame {
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 0.5rem;
            background: white;
            box-shadow: var(--shadow-md);
        }

        @media (max-width: 768px) {
            .pdf-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .header-left {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }

            .header-actions {
                width: 100%;
                flex-direction: column;
            }

            .btn-action {
                width: 100%;
                justify-content: center;
            }
        }

        .btn-action {
            width: 100%;
            justify-content: center;
        }
        }

        .description-wrapper {
            position: relative;
            display: inline-flex;
            padding-bottom: 20px;
            /* Invisible bridge area */
            margin-bottom: -20px;
            /* Compensate layout */
        }

        .description-tooltip {
            visibility: hidden;
            width: 350px;
            max-height: 400px;
            /* Limit height */
            overflow-y: auto;
            /* Enable vertical scrolling */
            background-color: white;
            color: var(--text-color);
            text-align: left;
            border-radius: 0.5rem;
            padding: 1rem;
            position: absolute;
            z-index: 1000;
            top: 100%;
            /* Move closer to button gap is problematic for hover */
            margin-top: 0.5rem;
            right: 0;
            box-shadow: var(--shadow-lg);
            opacity: 0;
            transition: opacity 0.2s ease-in-out;
            border: 1px solid var(--border-color);
            font-size: 0.9rem;
            line-height: 1.5;
            white-space: normal;
        }

        .description-tooltip strong {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
            font-size: 1rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 0.25rem;
        }

        .description-wrapper:hover .description-tooltip {
            visibility: visible;
            opacity: 1;
        }
    </style>
@endpush

@section('content')
    <div class="pdf-viewer-container">
        <!-- Header -->
        <div class="pdf-header">
            <div class="header-left">
                <a href="javascript:history.back()" class="btn-back">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Back to Results
                </a>
            </div>

            <div class="case-title">{{ $caseTitle }}</div>

            <div class="header-actions">
                @if($result)
                    <div class="description-wrapper">
                        <button class="btn-action">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                            Description
                        </button>
                        <div class="description-tooltip">
                            <strong>Judgment Description</strong>
                            {{ $result }}
                        </div>
                    </div>
                @endif
                <button class="btn-action" onclick="printPdf()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    Print
                </button>
                <button class="btn-action" onclick="downloadPdf()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Download
                </button>
            </div>
        </div>

        <!-- PDF Content -->
        <div class="pdf-content">
            <iframe id="pdf-frame" class="pdf-frame" src="{{ $pdfUrl }}"></iframe>
        </div>


    </div>
@endsection

@push('scripts')
    <script>
        function printPdf() {
            const iframe = document.getElementById('pdf-frame');
            iframe.contentWindow.print();
        }

        function downloadPdf() {
            window.open('{{ $pdfUrl }}', '_blank');
        }


    </script>
@endpush