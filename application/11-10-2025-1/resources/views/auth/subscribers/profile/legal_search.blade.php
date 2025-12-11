@extends('auth.subscribers.layouts.app')

@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection

@section('title', Auth::guard('subscriber')->user()->name.' | BLD Profile')
@php
$currentYear = date('Y');
$years = range($currentYear, $currentYear - 49); // last 50 years
$months = [
1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
];
@endphp
@section('content')
<style>
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.875rem; /* text-sm */
            font-weight: 500; /* font-medium */
            color: #4b5563; /* text-gray-600 */
        }
    </style>
<div class="container form-container">
    <form method="POST" action="{{ route('subscriber.searchResult') }}">
        {{ csrf_field() }}

        <div class="mb-4 form-group-top">
            <label for="searchKeyword" class="form-label">Write(s)/Sentence(s)</label>
            <input type="text" class="form-control" id="searchKeyword" name="searchKeyword" value="{{ old('searchKeyword', $inputs['searchKeyword'] ?? '') }}">
        </div>

        <!-- First Row -->
        <div class="row g-3 form-group-row">
            <div class="col-md-4 mb-3">
                <label for="selectDivision" class="form-label">Division</label>
                <select class="form-select" id="selectDivision" name="division">
                    <option value="" disabled {{ empty(old('division', $inputs['division'] ?? '')) ? 'selected' : '' }}></option>
                    <option value="Appellate Division" {{ (old('division', $inputs['division'] ?? '') == 'Appellate Division') ? 'selected' : '' }}>Appellate Division</option>
                    <option value="High Court Division" {{ (old('division', $inputs['division'] ?? '') == 'High Court Division') ? 'selected' : '' }}>High Court Division</option>
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label for="selectJurisdiction" class="form-label">Jurisdiction</label>
                <select class="form-select" id="selectJurisdiction" name="jurisdiction">
                    <option value="" disabled {{ empty(old('jurisdiction', $inputs['jurisdiction'] ?? '')) ? 'selected' : '' }}></option>
                    @foreach($getJurisdiction as $jurisdiction)
                    <option value="{{ $jurisdiction }}" {{ old('jurisdiction', $inputs['volume_number'] ?? '') == $jurisdiction ? 'selected' : '' }}>
                        {{ $jurisdiction }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label for="selectFillingYear" class="form-label">Filling Year</label>
                <select class="select2Data form-select" id="selectFillingYear" name="filling_year">
                    <option value="" disabled {{ empty(old('filling_year', $inputs['filling_year'] ?? '')) ? 'selected' : '' }}></option>
                    @foreach($fillingYears as $year)
                    <option value="{{ $year }}" {{ old('filling_year', $inputs['filling_year'] ?? '') == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label for="selectJudgmentYear" class="form-label">Year (Judgment)</label>
                <select class="select2Data form-select" id="selectJudgmentYear" name="judgment_year">
                    <option value="" disabled {{ empty(old('judgment_year', $inputs['judgment_year'] ?? '')) ? 'selected' : '' }}></option>
                    @foreach($judgmentYear as $year)
                    <option value="{{ $year }}" {{ old('judgment_year', $inputs['judgment_year'] ?? '') == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label for="selectJudgmentMonth" class="form-label">Month (Judgment Month)</label>
                <select class="select2Data form-select" id="selectJudgmentMonth" name="judgment_month">
                    <option value="" {{ empty(old('judgment_month', $inputs['judgment_month'] ?? '')) ? 'selected' : '' }}></option>
                    @foreach($months as $num => $name)
                    <option value="{{ $name }}" {{ old('judgment_month', $inputs['judgment_month'] ?? '') == $name ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label for="selectPublicationYear" class="form-label">Year (Publication)</label>
                <select class="select2Data form-select" id="selectPublicationYear" name="published_year">
                    <option value="" disabled {{ empty(old('published_year', $inputs['published_year'] ?? '')) ? 'selected' : '' }}></option>
                    @foreach($years as $year)
                    <option value="{{ $year }}" {{ old('published_year', $inputs['published_year'] ?? '') == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label for="writeCaseNumber2" class="form-label">Case number</label>
                <input type="text" class="form-control" id="writeCaseNumber2" name="case_number" value="{{ old('case_number', $inputs['case_number'] ?? '') }}">
            </div>

            <div class="col-md-4 mb-3">
                <label for="writeVolumeNumber" class="form-label">Volume number</label>
                <select name="volume_number" class="select2Data form-select @error('volume_number') is-invalid @enderror">
                    <option value="" disabled {{ empty(old('volume_number', $inputs['volume_number'] ?? '')) ? 'selected' : '' }}></option>
                    @foreach($volumeList as $id => $volumeName)
                    <option value="{{ $id }}" {{ old('volume_number', $inputs['volume_number'] ?? '') == $id ? 'selected' : '' }}>
                        {{ $volumeName }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label for="writePageNumber" class="form-label">Page number</label>
                <input type="text" class="form-control" id="writePageNumber" name="page_number" value="{{ old('page_number', $inputs['page_number'] ?? '') }}">
            </div>

            <div class="col-md-4 mb-3">
                <label for="writePartiesNames" class="form-label">Parties names</label>
                <input type="text" class="form-control" id="writePartiesNames" name="parties" value="{{ old('parties', $inputs['parties'] ?? '') }}">
            </div>

            
            <div class="col-md-4 mb-3">
                <label for="council" class="form-label">Counsel (Petitioner/Respondent) Name</label>
                <input type="text" class="form-control" id="council" name="council" value="{{ old('council', $inputs['council'] ?? '') }}">
            </div>
            
            <div class="col-md-4 mb-3">
                <label for="writeJudgesNames" class="form-label">Judge/Judge(s) Name</label>
                <input type="text" class="form-control" id="writeJudgesNames" name="judges" value="{{ old('judges', $inputs['judges'] ?? '') }}">
            </div>
            
            <div class="col-md-4 mb-3">
                <label for="act_rule_name" class="form-label">Act/Order/Rule Name</label>
                <input type="text" class="form-control" id="act_rule_name" name="act_rule_name" value="{{ old('act_rule_name', $inputs['act_rule_name'] ?? '') }}">
            </div>

            <div class="col-md-4 mb-3">
                <label for="selectSectionSubSection" class="form-label">Legislation Status</label>
                <select class="form-select" id="selectSectionSubSection" name="legislation_status">
                <option value="" disabled {{ empty(old('legislation_status', $inputs['legislation_status'] ?? '')) ? 'selected' : '' }}></option>
                    <option {{ (old('legislation_status', $inputs['legislation_status'] ?? '') == 'Active') ? 'selected' : '' }}>Active</option>
                    <option {{ (old('legislation_status', $inputs['legislation_status'] ?? '') == 'Amended') ? 'selected' : '' }}>Amended</option>
                    <option {{ (old('legislation_status', $inputs['legislation_status'] ?? '') == 'Repealed') ? 'selected' : '' }}>Repealed</option>
                </select>
            </div>


            <div class="col-md-4 mb-3">
                <label for="selectSectionSubSection" class="form-label">Section(s) / Sub Section(s)</label>
                <input type="text" class="form-control" id="selectSectionSubSection" name="section_subsection" value="{{ old('section_subsection', $inputs['section_subsection'] ?? '') }}">
            </div>
            
        </div>

        <div class="d-flex justify-content-end mt-4">
            <a href="{{ route('subscriber.leagalSearch', ['new' => 1]) }}" class="btn btn-reset me-4">Reset/Clear</a>
            <button type="submit" class="btn btn-search">Search</button>
        </div>
    </form>
</div>

@endsection