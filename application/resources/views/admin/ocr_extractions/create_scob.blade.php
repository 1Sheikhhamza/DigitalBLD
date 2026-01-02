@extends('admin.layouts.app')
@section('title', 'Add New SCOB Judgment')
@section('content')

    @php
        $currentYear = date('Y');
        $years = range($currentYear, $currentYear - 49); // last 50 years

        $months = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];
    @endphp

    <div class="app-wrapper">
        @include('admin.layouts.sidebar')
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Add New SCOB Judgment</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="{{ route('ocr_extractions.index') }}"
                                        class="btn btn-primary btn-sm">Legal Decision List</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="form-container col-sm-10 offset-1">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('ocr_extractions.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="card card-success card-outline mb-4">
                            <div class="card-header">
                                <h3 class="card-title">SCOB Judgment Details</h3>
                            </div>

                            <!-- Auto-Fill Section -->
                            <div class="card-body border-bottom bg-light">
                                <div class="row align-items-end">
                                    <div class="col-md-8">
                                        <label class="form-label fw-bold text-success"><i class="bi bi-magic"></i> Auto-Fill
                                            from SCOB PDF</label>
                                        <input type="file" id="scobPdfInput" class="form-control" accept="application/pdf">
                                        <small class="text-muted">Upload a SCOB judgment PDF to automatically extract text
                                            and metadata.</small>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" id="btnAutoFill" class="btn btn-success w-100">
                                            <span id="btnAutoFillText">Upload & Auto-Fill</span>
                                            <span id="btnAutoFillLoading" class="spinner-border spinner-border-sm d-none"
                                                role="status" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                    <div id="uploadMessage" class="col-12 mt-2"></div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <!-- Column 1 -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Division</label>
                                            <select name="division" class="form-select" required>
                                                <option value="">-- Select Division --</option>
                                                <option value="SCOB" {{ old('division') == 'SCOB' ? 'selected' : '' }}>SCOB
                                                    (Generic)</option>
                                                <option value="SCOB - Appellate Division" {{ old('division') == 'SCOB - Appellate Division' ? 'selected' : '' }}>SCOB - Appellate Division
                                                </option>
                                                <option value="SCOB - High Court Division" {{ old('division') == 'SCOB - High Court Division' ? 'selected' : '' }}>SCOB - High Court Division</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="row">
                                                <div class="col-sm-6"><label class="form-label">Volume (Year)</label></div>
                                                <div class="col-sm-6 text-right d-flex justify-content-end">
                                                    <a href="javascript:void(0)" class="text-primary text-right"
                                                        data-bs-toggle="modal" data-bs-target="#exampleModal">
                                                        Volume List
                                                    </a>
                                                </div>
                                            </div>
                                            <input type="number" name="volume_id" id="selectedVolume" required
                                                class="form-control" value="{{ old('volume_id') }}">
                                            <small class="text-muted">For SCOB, assume Volume ID = Year.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Volume Index File (PDF Only)</label>
                                            <input type="file" name="index_file" class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Published Year</label>
                                            <input type="text" name="published_year" class="form-control"
                                                value="{{ old('published_year') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Starting Page</label>
                                            <input type="number" name="starting_page_no" class="form-control"
                                                value="{{ old('starting_page_no') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Ending Page</label>
                                            <input type="number" name="ending_page_no" class="form-control"
                                                value="{{ old('ending_page_no') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Case No</label>
                                            <input type="text" name="case_no" class="form-control"
                                                value="{{ old('case_no') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Judge Name</label>
                                            <input type="text" name="judge_name" class="form-control"
                                                value="{{ old('judge_name') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Subject Matter</label>
                                            <textarea name="subject"
                                                class="form-control ckeditor">{{ old('subject') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Parties</label>
                                            <textarea name="parties"
                                                class="form-control ckeditor">{{ old('parties') }}</textarea>
                                        </div>
                                    </div>



                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Petitioners</label>
                                            <textarea name="petitioners"
                                                class="form-control ckeditor">{{ old('petitioners') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Respondent</label>
                                            <textarea name="respondent"
                                                class="form-control ckeditor">{{ old('respondent') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Decided On / Date of Judgment</label>
                                            <input type="text" name="decided_on" class="form-control datepicker"
                                                placeholder="The {{ date('jS F, Y') }}" value="{{ old('decided_on') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Result</label>
                                            <input type="text" name="result" class="form-control"
                                                value="{{ old('result') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Act/Order/Rule Name</label>
                                            <input type="text" name="related_act_order_rule" class="form-control"
                                                value="{{ old('related_act_order_rule') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Sections / Subsections</label>
                                            <input type="text" name="sections_subsections" class="form-control"
                                                value="{{ old('sections_subsections') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Jurisdiction</label>
                                            <input type="text" name="jurisdiction" class="form-control"
                                                value="{{ old('jurisdiction') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Keywords</label>
                                            <input type="text" name="key_words" class="form-control"
                                                value="{{ old('key_words') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3 col-12">
                                    <label class="form-label">Judgment</label>
                                    <textarea name="judgment" class="form-control ckeditor">{{ old('judgment') }}</textarea>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary mt-3">Save</button>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="file_path" id="hiddenFilePath">
                    </form>


                </div>
            </div>
        </main>

        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">📚 Volume List</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-2">
                            @foreach($volumeList as $id => $volumeNumber)
                                <div class="col-md-3">
                                    <div class="card shadow-sm border-0 p-2 volume-card" data-volume="{{ $volumeNumber }}"
                                        style="cursor: pointer;">
                                        <div class="card-body p-2 text-center">
                                            <span class="fw-bold">Volume</span>
                                            <span class="badge bg-primary ms-1">{{ $volumeNumber }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('admin.layouts.footer')
    </div>

@endsection
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".volume-card").forEach(function (card) {
            card.addEventListener("click", function () {
                let volume = this.getAttribute("data-volume");
                document.getElementById("selectedVolume").value = volume;
                // Close modal after selection
                var modal = bootstrap.Modal.getInstance(document.getElementById('exampleModal'));
                modal.hide();
            });
        });

        // SCOB Auto-Fill Logic
        document.getElementById('btnAutoFill').addEventListener('click', function () {
            const fileInput = document.getElementById('scobPdfInput');
            const file = fileInput.files[0];
            const msgDiv = document.getElementById('uploadMessage');
            const btnText = document.getElementById('btnAutoFillText');
            const btnLoading = document.getElementById('btnAutoFillLoading');

            if (!file) {
                alert("Please select a PDF file first.");
                return;
            }

            // Show Loading
            btnText.classList.add('d-none');
            btnLoading.classList.remove('d-none');
            msgDiv.innerHTML = '<div class="alert alert-info">Processing PDF... This may take a few seconds.</div>';

            const formData = new FormData();
            formData.append('file', file);
            // Append CSRF Token
            formData.append('_token', '{{ csrf_token() }}');

            fetch("{{ route('ocr_extractions.upload_scob_pdf') }}", {
                method: "POST",
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    // Hide Loading
                    btnText.classList.remove('d-none');
                    btnLoading.classList.add('d-none');

                    if (data.success) {
                        msgDiv.innerHTML = '<div class="alert alert-success">Successfully extracted data!</div>';

                        // Fill Fields

                        // Division -> AI or default SCOB
                        const divisionSelect = document.querySelector('select[name="division"]');
                        if (divisionSelect) {
                            if (data.metadata.division) {
                                const divLower = data.metadata.division.toLowerCase();
                                if (divLower.includes('appellate')) divisionSelect.value = 'SCOB - Appellate Division';
                                else if (divLower.includes('high court')) divisionSelect.value = 'SCOB - High Court Division';
                                else divisionSelect.value = 'SCOB';
                            } else {
                                divisionSelect.value = 'SCOB';
                            }
                        }

                        // File Path
                        if (data.file_path) document.getElementById('hiddenFilePath').value = data.file_path;

                        // Text Inputs
                        const safeSet = (name, val) => {
                            const el = document.querySelector(`input[name="${name}"]`);
                            if (el && val) el.value = val;
                        };

                        safeSet('case_no', data.metadata.case_no);
                        safeSet('published_year', data.metadata.published_year);
                        safeSet('decided_on', data.metadata.decision_date);
                        safeSet('judge_name', data.metadata.judges);
                        safeSet('result', data.metadata.result);
                        safeSet('related_act_order_rule', data.metadata.related_act_order_rule);
                        safeSet('key_words', data.metadata.key_words);

                        // CKEditor / Textarea Fields
                        const setEditor = (name, val) => {
                            if (!val) return;
                            try {
                                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances[name]) {
                                    CKEDITOR.instances[name].setData(val);
                                } else {
                                    const el = document.querySelector(`textarea[name="${name}"]`);
                                    if (el) el.value = val;
                                }
                            } catch (e) {
                                const el = document.querySelector(`textarea[name="${name}"]`);
                                if (el) el.value = val;
                            }
                        };

                        setEditor('parties', data.metadata.parties);
                        setEditor('subject', data.metadata.subject);
                        setEditor('petitioners', data.metadata.petitioners);
                        setEditor('respondent', data.metadata.respondent);

                        // Judgment Text
                        if (data.judgment_text) setEditor('judgment', data.judgment_text);

                    } else {
                        msgDiv.innerHTML = '<div class="alert alert-danger">Error: ' + data.message + '</div>';
                    }
                })
                .catch(error => {
                    btnText.classList.remove('d-none');
                    btnLoading.classList.add('d-none');
                    msgDiv.innerHTML = '<div class="alert alert-danger">Upload failed: ' + error + '</div>';
                });
        });
    });
</script>