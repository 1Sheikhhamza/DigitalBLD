@extends('auth.subscribers.layouts.app')

@section('seodetails')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection

@section('title', Auth::guard('subscriber')->user()->name . ' | BLD Profile')
@php
    $currentYear = date('Y');
    $years = range($currentYear, $currentYear - 49); // last 50 years
    $months = [
        1 => 'January',
        2 => 'February',
        3 => 'March',
        4 => 'April',
        5 => 'May',
        6 => 'June',
        7 => 'July',
        8 => 'August',
        9 => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December'
    ];
@endphp
@section('content')
    <style>
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            /* text-sm */
            font-weight: 500;
            /* font-medium */
            color: #4b5563;
            /* text-gray-600 */
        }

        .autocomplete-suggestions {
            border: 1px solid #ddd;
            background: #fff;
            overflow-y: auto;
            overflow-x: hidden;
            position: absolute;
            z-index: 9999;
            max-height: 250px;
            width: 100%;
            left: 0;
            top: 100%;
            border-radius: 0 0 5px 5px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .autocomplete-suggestion {
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
            color: #333;
        }
        .autocomplete-suggestion:last-child {
            border-bottom: none;
        }
        .autocomplete-suggestion:hover {
            background-color: #f8f9fa;
            color: #000;
        }
        .autocomplete-suggestion strong {
            color: #0d6efd;
        }
    </style>
    <div class="container form-container">
        <form method="POST" action="{{ route('subscriber.searchResult') }}">
            {{ csrf_field() }}

            <div class="mb-4 form-group-top">
                <label for="searchKeyword" class="form-label">Write(s)/Sentence(s)</label>
                <input type="text" class="form-control" id="searchKeyword" name="searchKeyword"
                    value="{{ old('searchKeyword', $inputs['searchKeyword'] ?? '') }}">
            </div>

            <!-- First Row -->
            <div class="row g-3 form-group-row">
                <div class="col-md-4 mb-3">
                    <label for="selectDivision" class="form-label">Division</label>
                    <select class="form-select" id="selectDivision" name="division">
                        <option value="" disabled {{ empty(old('division', $inputs['division'] ?? '')) ? 'selected' : '' }}>
                        </option>
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
                    <input type="text" class="form-control" id="writeCaseNumber2" name="case_number"
                        value="{{ old('case_number', $inputs['case_number'] ?? '') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="writeVolumeNumber" class="form-label">Volume number</label>
                    <select name="volume_number"
                        class="select2Data form-select @error('volume_number') is-invalid @enderror">
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
                    <input type="text" class="form-control" id="writePageNumber" name="page_number"
                        value="{{ old('page_number', $inputs['page_number'] ?? '') }}">
                </div>

                <div class="col-md-4 mb-3">
                <label for="writePartiesNames" class="form-label">Parties names</label>
                <div style="position: relative;">
                    <input type="text" class="form-control" id="writePartiesNames" name="parties" value="{{ old('parties', $inputs['parties'] ?? '') }}" autocomplete="off">
                    <div id="parties-suggestions-list" class="autocomplete-suggestions" style="display: none;"></div>
                </div>
            </div>


                <div class="col-md-4 mb-3">
                    <label for="council" class="form-label">Counsel (Petitioner/Respondent) Name</label>
                    <input type="text" class="form-control" id="council" name="council"
                        value="{{ old('council', $inputs['council'] ?? '') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="writeJudgesNames" class="form-label">Judge/Judge(s) Name</label>
                    <input type="text" class="form-control" id="writeJudgesNames" name="judges"
                        value="{{ old('judges', $inputs['judges'] ?? '') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="act_rule_name" class="form-label">Act/Order/Rule Name</label>
                    <div style="position: relative;">
                        <input type="text" class="form-control" id="act_rule_name" name="act_rule_name"
                            value="{{ old('act_rule_name', $inputs['act_rule_name'] ?? '') }}" autocomplete="off">
                        <div id="act-suggestions-list" class="autocomplete-suggestions" style="display: none;"></div>
                    </div>
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
                    <input type="text" class="form-control" id="selectSectionSubSection" name="section_subsection"
                        value="{{ old('section_subsection', $inputs['section_subsection'] ?? '') }}">
                </div>

            </div>

            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('subscriber.leagalSearch', ['new' => 1]) }}" class="btn btn-reset me-4">Reset/Clear</a>
                <button type="submit" class="btn btn-search">Search</button>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Act/Rule Autocomplete
        const input = $('#act_rule_name');
        const suggestionsList = $('#act-suggestions-list');
        let timeout = null;

        input.on('input', function() {
            const term = $(this).val();
            
            if (timeout) clearTimeout(timeout);
            
            if (term.length < 2) {
                suggestionsList.hide();
                return;
            }

            timeout = setTimeout(() => {
                $.ajax({
                    url: "{{ route('subscriber.search.act.suggestions') }}",
                    method: 'GET',
                    data: { term: term },
                    dataType: 'json',
                    success: function(data) {
                        suggestionsList.empty();
                        if (data && data.length > 0) {
                            data.forEach(item => {
                                if (item) {
                                    const regex = new RegExp(`(${term})`, 'gi');
                                    const highlighted = item.replace(regex, '<strong>$1</strong>');
                                    
                                    const div = $('<div>')
                                        .addClass('autocomplete-suggestion')
                                        .html(highlighted)
                                        .on('click', function() {
                                            input.val(item);
                                            suggestionsList.hide();
                                        });
                                    suggestionsList.append(div);
                                }
                            });
                            suggestionsList.show();
                        } else {
                            suggestionsList.hide();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching suggestions:', error);
                    }
                });
            }, 300);
        });

        // Parties Autocomplete
        const partiesInput = $('#writePartiesNames');
        const partiesSuggestionsList = $('#parties-suggestions-list');
        let partiesTimeout = null;

        partiesInput.on('input', function() {
            const term = $(this).val();
            
            if (partiesTimeout) clearTimeout(partiesTimeout);
            
            if (term.length < 2) {
                partiesSuggestionsList.hide();
                return;
            }

            partiesTimeout = setTimeout(() => {
                $.ajax({
                    url: "{{ route('subscriber.search.parties.suggestions') }}",
                    method: 'GET',
                    data: { term: term },
                    dataType: 'json',
                    success: function(data) {
                        partiesSuggestionsList.empty();
                        if (data && data.length > 0) {
                            data.forEach(item => {
                                if (item) {
                                    const regex = new RegExp(`(${term})`, 'gi');
                                    // Escape special characters in regex if needed, but for now simple
                                    const highlighted = item.replace(regex, '<strong>$1</strong>');
                                    
                                    const div = $('<div>')
                                        .addClass('autocomplete-suggestion')
                                        .html(highlighted)
                                        .on('click', function() {
                                            partiesInput.val(item);
                                            partiesSuggestionsList.hide();
                                        });
                                    partiesSuggestionsList.append(div);
                                }
                            });
                            partiesSuggestionsList.show();
                        } else {
                            partiesSuggestionsList.hide();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching parties suggestions:', error);
                    }
                });
            }, 300);
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#act_rule_name, #act-suggestions-list').length) {
                suggestionsList.hide();
            }
            if (!$(e.target).closest('#writePartiesNames, #parties-suggestions-list').length) {
                partiesSuggestionsList.hide();
            }
        });
    });
</script>
@endpush