@extends('admin.layouts.app')
@section('title', 'Decision Details')
@section('content')

<style>
    
    .document-container {
        background-color: white;
        border-radius: 0.75rem;
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
        padding: 2.5rem;
        margin-top: 2rem;
        margin-bottom: 2rem;
        max-width: 900px;
        /* Max width for document content */
        margin-left: auto;
        margin-right: auto;
        font-family: 'Inter', sans-serif;
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
        font-weight: 700;
        color: #343a40;
        margin-bottom: 0.5rem;
    }

    .document-section p,
    .document-section ul {
        xsize: 0.95rem;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .document-section ul {
        list-style: disc;
        padding-left: 1.5rem;
    }

    .document-section ul li {
        margin-bottom: 0.25rem;
    }

    .judgment-heading {
        text-align: center;
        font-size: 1.3rem;
        font-weight: bold;
        margin-top: 2rem;
        margin-bottom: 1.5rem;
        color: #0d6efd;
        display: flex;
        color: black;
        justify-content: center;
    }

    @media (max-width: 767.98px) {
        .top-action-bar {
            flex-direction: column;
            align-items: flex-start;
            padding: 1rem;
        }

        .top-action-bar .action-link {
            margin-right: 1rem;
            margin-bottom: 0.75rem;
        }

        .top-action-bar .nav-arrows {
            width: 100%;
            justify-content: space-between;
            display: flex;
            margin-top: 0.5rem;
        }

        .document-container {
            padding: 1.5rem;
        }

        .document-header h4 {
            font-size: 1.2rem;
        }

        .document-section h6 {
            font-size: 1rem;
        }

        .document-section p,
        .document-section ul {
            font-size: 0.9rem;
        }
    }

    .print-download {
        text-align: center;
        margin-top: 20px;
    }

    .print-download a {
        display: inline-block;
        margin: 0 10px;
        padding: 8px 15px;
        background-color: #003092;
        color: #fff;
        text-decoration: none;
        border-radius: 4px;
        font-weight: bold;
    }

    .judgment p {
        text-align: justify;
        margin-bottom: 1rem;
    }
</style>
<?php
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

?>
<div class="app-wrapper">
    @include('admin.layouts.sidebar')
    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Details Legal Decision</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="{{ route('ocr_extractions.index') }}" class="btn btn-primary btn-sm">Legal Decision List</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-content">
            <div class="container">
                <div class="container mt-5">
                    <div class="container document-container">
                        <div class="document-header text-center">
                            <h4>In the Supreme Court of Bangladesh ({{ $ocrExtraction->division }})</h4>
                            <p>{{ $ocrExtraction->case_no ?? '' }}</p>
                        </div>

                        <div class="document-section">
                            <h6>Published in BLD:</h6>
                            <ul>
                                <li>Volume: {{ $ocrExtraction->volume ? $ocrExtraction->volume->number : '' }}</li>
                                <li>Year: {{ $ocrExtraction->published_year ?? '' }}</li>
                                <li>
                                    Page: {{ $ocrExtraction->starting_page_no ?? '' }}
                                    @if($ocrExtraction->ending_page_no)
                                    to {{ $ocrExtraction->ending_page_no }}
                                    @endif
                                </li>
                            </ul>
                        </div>

                        <div class="document-section">
                            <h6>Decided On:</h6>
                            <p>{{ $ocrExtraction->decided_on ?? '' }}</p>
                        </div>
                        <div class="document-section">
                            <h6>Result:</h6>
                            <p>{{ $ocrExtraction->result ?? '' }}</p>
                        </div>

                        <div class="document-section">
                            <h6>Parties:</h6>
                            <p>{!! $ocrExtraction->parties ?? '' !!}</p>
                        </div>

                        <div class="document-section">
                            <h6>Hon'ble Judge(s):</h6>
                            <p>{!! nl2br(e($ocrExtraction->judge_name ?? '')) !!}</p>
                        </div>

                        <div class="document-section">
                            <h6>Counsels:</h6>
                            <h6>{{ getLabel($petitionerEndings, $ocrExtraction->petitioners) }}</h6>
                            <p>{!! $ocrExtraction->petitioners ?? '' !!}</p>

                            <h6>{{ getLabel($respondentEndings, $ocrExtraction->respondent) }}</h6>
                            <p>{!! $ocrExtraction->respondent ?? '' !!}</p>
                        </div>



                        <div class="document-section">
                            <h6>Subject Matter:</h6>
                            <p>{!! $ocrExtraction->subject ?? '' !!}</p>
                        </div>

                        <div class="document-section">
                            <h6>Jurisdiction:</h6>
                            <p>{{ $ocrExtraction->jurisdiction ?? '' }}</p>
                        </div>

                        <div class="document-section">
                            <h6>Related Acts/Rules/Orders:</h6>
                            <p>{!! nl2br(e($ocrExtraction->related_act_order_rule ?? '')) !!} </p>
                        </div>

                        <div class="document-section">
                            <h6>Key words:</h6>
                            <p>{{ $ocrExtraction->key_words ?? '' }}</p>
                        </div>

                        <h5 class="judgment-heading">JUDGMENT</h5>

                        <div class="document-section" style="text-align: justify; white-space: pre-wrap;">
                            {!! $judgmentTextFormatted !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    @include('admin.layouts.footer')
</div>

@endsection