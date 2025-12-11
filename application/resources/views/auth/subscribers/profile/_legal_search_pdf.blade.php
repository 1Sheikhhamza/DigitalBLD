<link rel="stylesheet" href="{{ asset('frontend/assets/css/custom/dashboard.css') }}">
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
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
    z-index: 1000;
    background-image: url("{{ asset('frontend/assets/img/watermark.png') }}");
    background-repeat: no-repeat;
    background-position: center center;
    background-size: 90%;
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



  .print-footer {
    bottom: 0;
    left: 0;
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 11px;
    box-sizing: border-box;
    line-height: 1.2;
    padding-top: 10px;
    white-space: nowrap;
    overflow: hidden;
  }

  .print-footer div {
    flex-shrink: 1;
  }

  .print-footer .center {
    text-align: center;
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .print-footer .right {
    text-align: right;
    margin-left: 20px;
  }

  .avoid-break-inside {
    page-break-inside: avoid !important;
    break-inside: avoid !important;
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
      <p>{!! html_entity_decode($data->respondent, ENT_QUOTES | ENT_HTML5) !!}
      </p>
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
      <p class="avoid-break-inside" style="margin-bottom: 10px;">
        {!! $para !!}
      </p>
      @endforeach
    </div>

  </div>
</div>