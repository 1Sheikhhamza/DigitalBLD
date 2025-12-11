@extends('auth.subscribers.layouts.app')
@section('title', 'Supreme Search | Digital BLD')

@push('styles')
    <style>
        :root {
            --primary-color: #0047AB;
            --primary-dark: #002D62;
            --primary-light: #4A90E2;
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --input-bg: #f8fafc;
            --border-color: #e2e8f0;
            --text-color: #334155;
            --text-muted: #64748b;
            --success-color: #10b981;
            --error-color: #ef4444;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        .page-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        /* Header */
        .header-section {
            text-align: center;
            margin-bottom: 2.5rem;
            animation: fadeInDown 0.6s ease-out;
        }

        .header-section h2 {
            color: var(--primary-color);
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header-section p {
            color: var(--text-muted);
            font-size: 1.125rem;
            margin: 0;
        }

        /* Search Card */
        .search-card {
            background: var(--card-bg);
            border-radius: 1.25rem;
            padding: 2.5rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
            margin-bottom: 2rem;
            animation: fadeInUp 0.6s ease-out;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .search-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-xl);
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            font-weight: 600;
            color: var(--text-color);
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }

        input,
        select {
            width: 100%;
            padding: 0.875rem 1rem;
            border-radius: 0.625rem;
            border: 2px solid var(--border-color);
            background-color: var(--input-bg);
            color: var(--text-color);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(0, 71, 171, 0.1);
            background-color: #fff;
        }

        input::placeholder {
            color: #94a3b8;
        }

        /* Combobox */
        .combobox-wrapper {
            position: relative;
        }

        .combobox-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            /* border: 2px solid var(--primary-color); */
            border-radius: 0.625rem;
            /* margin-top: 0.5rem; */
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: var(--shadow-lg);
            display: none;
        }

        .combobox-dropdown.active {
            display: block;
            animation: slideDown 0.2s ease-out;
        }

        .combobox-option {
            padding: 0.875rem 1rem;
            cursor: pointer;
            transition: background-color 0.15s ease;
            border-bottom: 1px solid #f1f5f9;
        }

        .combobox-option:last-child {
            border-bottom: none;
        }

        .combobox-option:hover,
        .combobox-option.selected {
            background-color: #f0f7ff;
            color: var(--primary-color);
        }

        .combobox-option.highlighted {
            background-color: #e0f2fe;
        }

        /* Buttons */
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            border: none;
            padding: 1rem 1.5rem;
            border-radius: 0.625rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-search {
            flex: 2;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 71, 171, 0.3);
        }

        .btn-search:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-reset {
            flex: 1;
            background-color: #f1f5f9;
            color: #475569;
            border: 2px solid #e2e8f0;
        }

        .btn-reset:hover {
            background-color: #e2e8f0;
            transform: translateY(-2px);
        }

        /* Loading Spinner */
        .spinner {
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 0.8s linear infinite;
        }

        /* Results Section */
        .results-section {
            animation: fadeInUp 0.6s ease-out;
        }

        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .results-count {
            font-size: 1.125rem;
            color: var(--text-color);
            font-weight: 600;
        }

        .results-grid {
            display: grid;
            gap: 1.5rem;
        }

        /* Result Card */
        .result-card {
            background: white;
            border-radius: 1rem;
            padding: 1.75rem;
            border: 2px solid var(--border-color);
            cursor: pointer;
            transition: all 0.3s ease;
            animation: fadeInUp 0.4s ease-out;
            animation-fill-mode: both;
        }

        .result-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .result-card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1rem;
            line-height: 1.4;
        }

        .result-card-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .meta-item {
            display: flex;
            align-items: start;
            gap: 0.5rem;
        }

        .meta-label {
            font-weight: 600;
            color: var(--text-muted);
            font-size: 0.875rem;
            min-width: 80px;
        }

        .meta-value {
            color: var(--text-color);
            font-size: 0.875rem;
            flex: 1;
        }

        .result-card-footer {
            display: flex;
            justify-content: flex-end;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .view-pdf-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-color);
            font-weight: 600;
            font-size: 0.95rem;
            transition: gap 0.3s ease;
        }

        .result-card:hover .view-pdf-btn {
            gap: 0.75rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            animation: fadeIn 0.6s ease-out;
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        .empty-state-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }

        .empty-state-text {
            color: var(--text-muted);
            font-size: 1rem;
        }

        /* PDF Modal */
        .pdf-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            z-index: 9999;
            animation: fadeIn 0.3s ease-out;
        }

        .pdf-modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pdf-modal-content {
            background: white;
            border-radius: 1rem;
            width: 90%;
            max-width: 1200px;
            height: 90vh;
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow-xl);
            animation: scaleIn 0.3s ease-out;
        }

        .pdf-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            border-bottom: 2px solid var(--border-color);
        }

        .pdf-modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-color);
        }

        .pdf-modal-actions {
            display: flex;
            gap: 0.75rem;
        }

        .icon-btn {
            background: var(--input-bg);
            border: 2px solid var(--border-color);
            border-radius: 0.5rem;
            padding: 0.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-btn:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .pdf-modal-body {
            flex: 1;
            overflow: hidden;
            padding: 1rem;
        }

        .pdf-viewer {
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 0.5rem;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .search-card {
                padding: 1.5rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .header-section h2 {
                font-size: 2rem;
            }

            .pdf-modal-content {
                width: 95%;
                height: 95vh;
            }

            .result-card-meta {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('title', 'SCOB Search | Digital BLD')

@section('content')
    <div class="page-container">
        <!-- Header -->
        <div class="header-section">
            <h2>SCOB Search</h2>
        </div>

        <!-- Search Card -->
        <div class="search-card">
            <form id="supreme-search-form">
                <!-- Division Selection -->
                <div class="form-group">
                    <label for="div_id">Division</label>
                    <select id="div_id" name="div_id" onchange="handleDivisionChange()">
                        <option value="2" selected>High Court Division</option>
                        <option value="1">Appellate Division</option>
                    </select>
                </div>

                <!-- Case Type -->
                <div class="form-group">
                    <label for="case_type">Case Type</label>
                    <div class="combobox-wrapper">
                        <input type="text" id="case_type" placeholder="Type to search case type..." autocomplete="off">
                        <input type="hidden" id="case_type_id" name="case_type_id">
                        <div class="combobox-dropdown" id="case_type_dropdown"></div>
                    </div>
                </div>

                <!-- Case Number & Year -->
                <div class="form-row">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="case_number">Case/Tender Number</label>
                        <input type="text" id="case_number" name="case_number" placeholder="e.g. 1234">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="year">Year</label>
                        <input type="text" id="year" name="year" placeholder="e.g. 2024" maxlength="4">
                    </div>
                </div>

                <!-- Parties -->
                <div class="form-group">
                    <label for="parties">Parties</label>
                    <input type="text" id="parties" name="parties" placeholder="e.g. Name of petitioner or respondent">
                </div>

                <!-- Short Description -->
                <div class="form-group">
                    <label for="description">Short Description</label>
                    <input type="text" id="description" name="description" placeholder="Keywords from the judgment">
                </div>

                <!-- Buttons -->
                <div class="action-buttons">
                    <button type="submit" class="btn btn-search" id="search-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <span id="search-btn-text">Search Records</span>
                    </button>
                    <button type="button" class="btn btn-reset" id="reset-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="1 4 1 10 7 10"></polyline>
                            <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                        </svg>
                        Reset
                    </button>
                </div>
            </form>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        // Case Type Dataset - will be loaded dynamically
        let caseTypes = [];

        // DOM Elements
        const caseTypeInput = document.getElementById('case_type');
        const caseTypeIdInput = document.getElementById('case_type_id');
        const caseTypeDropdown = document.getElementById('case_type_dropdown');
        const searchForm = document.getElementById('supreme-search-form');
        const searchBtn = document.getElementById('search-btn');
        const searchBtnText = document.getElementById('search-btn-text');
        const resetBtn = document.getElementById('reset-btn');
        const resultsSection = document.getElementById('results-section');
        const resultsCount = document.getElementById('results-count');
        const resultsGrid = document.getElementById('results-grid');
        const pdfModal = document.getElementById('pdf-modal');
        const pdfViewer = document.getElementById('pdf-viewer');
        const pdfModalTitle = document.getElementById('pdf-modal-title');
        const closePdfBtn = document.getElementById('close-pdf-btn');

        let highlightedIndex = -1;
        let filteredCaseTypes = [];

        // Load case types from backend
        async function loadCaseTypes() {
            try {
                const divId = document.getElementById('div_id').value;
                const response = await fetch(`{{ route('subscriber.supremeSearch.caseTypes') }}?div_id=${divId}`);
                const data = await response.json();

                if (data.success) {
                    caseTypes = data.data;
                } else {
                    console.error('Error loading case types:', data.error);
                    // Use fallback list
                    useFallbackCaseTypes();
                }
            } catch (error) {
                console.error('Error fetching case types:', error);
                // Use fallback list
                useFallbackCaseTypes();
            }
        }

        function useFallbackCaseTypes() {
            caseTypes = [
                { id: '1', name: 'Civil Appeal' },
                { id: '2', name: 'Civil Petition' },
                { id: '3', name: 'Civil Revision' },
                { id: '4', name: 'Civil Review Petition' },
                { id: '5', name: 'Criminal Appeal' },
                { id: '6', name: 'Criminal Petition' },
                { id: '7', name: 'Criminal Revision' },
                { id: '8', name: 'Criminal Review Petition' },
                { id: '9', name: 'Death Reference' },
                { id: '10', name: 'Jail Appeal' },
                { id: '11', name: 'Jail Petition' },
                { id: '12', name: 'Miscellaneous Case' },
                { id: '13', name: 'Writ Petition' },
                { id: '14', name: 'Company Matter' },
                { id: '15', name: 'Contempt Petition' },
                { id: '16', name: 'Reference' },
                { id: '17', name: 'Special Case' },
                { id: '18', name: 'Criminal Misc (TN)' },
            ];
        }

        // Initialize - load case types on page load
        loadCaseTypes();

        // Combobox functionality
        caseTypeInput.addEventListener('input', function () {
            const query = this.value.toLowerCase();
            filteredCaseTypes = caseTypes.filter(ct => ct.name.toLowerCase().includes(query));
            renderDropdown(filteredCaseTypes);
            highlightedIndex = -1;
        });

        caseTypeInput.addEventListener('focus', function () {
            filteredCaseTypes = caseTypes;
            renderDropdown(filteredCaseTypes);
        });

        caseTypeInput.addEventListener('keydown', function (e) {
            if (!caseTypeDropdown.classList.contains('active')) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                highlightedIndex = Math.min(highlightedIndex + 1, filteredCaseTypes.length - 1);
                updateHighlight();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                highlightedIndex = Math.max(highlightedIndex - 1, 0);
                updateHighlight();
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (highlightedIndex >= 0) {
                    selectCaseType(filteredCaseTypes[highlightedIndex]);
                }
            } else if (e.key === 'Escape') {
                caseTypeDropdown.classList.remove('active');
            }
        });

        document.addEventListener('click', function (e) {
            if (!caseTypeInput.contains(e.target) && !caseTypeDropdown.contains(e.target)) {
                caseTypeDropdown.classList.remove('active');
            }
        });

        function renderDropdown(items) {
            if (items.length === 0) {
                caseTypeDropdown.innerHTML = '<div class="combobox-option" style="color: #94a3b8;">No results found</div>';
            } else {
                caseTypeDropdown.innerHTML = items.map((item, index) =>
                    `<div class="combobox-option" data-id="${item.id}" data-name="${item.name}" data-index="${index}">${item.name}</div>`
                ).join('');

                caseTypeDropdown.querySelectorAll('.combobox-option').forEach(option => {
                    option.addEventListener('click', function () {
                        selectCaseType({ id: this.dataset.id, name: this.dataset.name });
                    });
                });
            }
            caseTypeDropdown.classList.add('active');
        }

        function updateHighlight() {
            const options = caseTypeDropdown.querySelectorAll('.combobox-option');
            options.forEach((opt, idx) => {
                opt.classList.toggle('highlighted', idx === highlightedIndex);
            });
            if (options[highlightedIndex]) {
                options[highlightedIndex].scrollIntoView({ block: 'nearest' });
            }
        }

        function selectCaseType(caseType) {
            caseTypeInput.value = caseType.name;
            caseTypeIdInput.value = caseType.id;
            caseTypeDropdown.classList.remove('active');
        }


        // Search functionality - redirect to results page
        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const params = new URLSearchParams();

            for (let [key, value] of formData.entries()) {
                if (value) params.append(key, value);
            }

            // Redirect to results page with search parameters
            window.location.href = `{{ route('subscriber.supremeSearch.results') }}?${params.toString()}`;
        });

        // Reset functionality
        resetBtn.addEventListener('click', function () {
            searchForm.reset();
            caseTypeIdInput.value = '';
        });

        // Handle Division Change
        function handleDivisionChange() {
            caseTypeInput.value = '';
            caseTypeIdInput.value = '';
            loadCaseTypes();
        }

        // PDF viewer functions (kept for potential use)
        function openPdf(pdfUrl, title) {
            const proxyUrl = `{{ route('subscriber.supremeSearch.proxyPdf') }}?url=${pdfUrl}`;
            pdfViewer.src = proxyUrl;
            pdfModalTitle.textContent = decodeURIComponent(title);
            pdfModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        closePdfBtn.addEventListener('click', closePdfModal);
        pdfModal.addEventListener('click', function (e) {
            if (e.target === pdfModal) {
                closePdfModal();
            }
        });

        function closePdfModal() {
            pdfModal.classList.remove('active');
            pdfViewer.src = '';
            document.body.style.overflow = '';
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && pdfModal.classList.contains('active')) {
                closePdfModal();
            }
        });

    </script>
@endpush