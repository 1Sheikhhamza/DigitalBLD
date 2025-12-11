@extends('auth.subscribers.layouts.app')
@section('title', 'Search Results | Digital BLD')

@push('styles')
    <style>
        :root {
            --primary-color: #0047AB;
            --primary-dark: #002D62;
            --primary-light: #4A90E2;
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --text-color: #334155;
            --text-muted: #64748b;
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        body {
            background: var(--bg-color);
        }

        .results-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        /* Header Section */
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .results-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-color);
        }

        .header-actions {
            display: flex;
            gap: 1rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            border: none;
        }

        .btn-modify {
            background: white;
            color: var(--text-color);
            border: 2px solid var(--border-color);
        }

        .btn-modify:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            transform: translateY(-2px);
        }

        .btn-new {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .btn-new:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(239, 68, 68, 0.3);
        }

        /* Results Grid */
        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        /* Result Card */
        .result-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            border: 2px solid var(--border-color);
            cursor: pointer;
            transition: all 0.3s ease;
            animation: fadeInUp 0.4s ease-out;
            animation-fill-mode: both;
            min-height: 280px;
            max-height: 280px;
            display: flex;
            flex-direction: column;
        }

        .result-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .result-card-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1rem;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            position: relative;
            cursor: pointer;
        }

        .result-card-body {
            margin-bottom: 1rem;
            flex: 1;
            overflow: hidden;
            position: relative;
        }

        .result-card-body p {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }

        .result-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            font-size: 0.9rem;
            margin-top: auto;
        }

        .info-row {
            display: flex;
            gap: 0.5rem;
            align-items: flex-start;
        }

        .info-label {
            font-weight: 600;
            color: var(--text-muted);
            min-width: 90px;
            flex-shrink: 0;
        }

        .info-value {
            color: var(--text-color);
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
            position: relative;
            word-wrap: break-word;
            overflow-wrap: break-word;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            line-height: 1.4;
        }

        /* Loading State */
        .loading-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 4rem 2rem;
        }

        .spinner {
            border: 4px solid rgba(0, 71, 171, 0.1);
            border-top-color: var(--primary-color);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 0.8s linear infinite;
            margin-bottom: 1rem;
        }

        .loading-text {
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
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
            margin-bottom: 1.5rem;
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
            box-shadow: var(--shadow-lg);
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

        .icon-btn {
            background: var(--bg-color);
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

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .results-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .header-actions {
                width: 100%;
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .results-grid {
                grid-template-columns: 1fr;
            }

            .pdf-modal-content {
                width: 95%;
                height: 95vh;
            }
        }

        /* Pagination */
        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .pagination-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.5rem;
            border: 1px solid var(--border-color);
            background: white;
            color: var(--text-color);
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .pagination-btn:hover:not(:disabled) {
            border-color: var(--primary-color);
            color: var(--primary-color);
            background: #f0f7ff;
        }

        .pagination-btn.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f1f5f9;
        }
    </style>
@endpush

@section('content')
    <div class="results-container">
        <!-- Header -->
        <div class="results-header">
            <h1 class="results-title">Search Results</h1>
            <div class="header-actions">
                <a href="{{ route('subscriber.supremeSearch') }}?modify=1" class="btn btn-modify">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    Modify Search
                </a>
                <a href="{{ route('subscriber.supremeSearch') }}" class="btn btn-new">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    New Search
                </a>
            </div>
        </div>

        <!-- Loading State -->
        <div class="loading-container" id="loading-container">
            <div class="spinner"></div>
            <div class="loading-text">Searching judgments...</div>
        </div>

        <!-- Results Grid -->
        <div class="results-grid" id="results-grid" style="display: none;"></div>

        <!-- Pagination -->
        <div class="pagination-container" id="pagination-container" style="display: none;"></div>
    </div>

@endsection

@push('scripts')
    <script>


        const searchParams = @json($searchParams);
        const loadingContainer = document.getElementById('loading-container');
        const resultsGrid = document.getElementById('results-grid');
        const paginationContainer = document.getElementById('pagination-container');
        let currentPage = 1;

        // Perform search on page load
        async function performSearch(page = 1) {
            try {
                currentPage = page;
                loadingContainer.style.display = 'flex';
                resultsGrid.style.display = 'none';
                paginationContainer.style.display = 'none';
                window.scrollTo({ top: 0, behavior: 'smooth' });

                const params = new URLSearchParams();

                for (let [key, value] of Object.entries(searchParams)) {
                    if (value) params.append(key, value);
                }
                params.append('page', page);

                console.log('Searching with params:', params.toString());

                const response = await fetch(`{{ route('subscriber.supremeSearch.proxy') }}?${params.toString()}`);

                console.log('Response status:', response.status);

                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    // If we got HTML (e.g. login page), distinct error
                    const text = await response.text();
                    console.error('Expected JSON, got:', contentType, text.substring(0, 100));
                    throw new Error('Session expired or invalid response. Please refresh the page and login again.');
                }

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                console.log('Response data:', data);

                if (data.success) {
                    displayResults(data.data);
                } else {
                    showError('Error: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Search error:', error);
                showError('Error performing search: ' + error.message);
            }
        }

        function displayResults(judgments) {
            loadingContainer.style.display = 'none';
            resultsGrid.style.display = 'grid';

            if (judgments.length === 0) {
                // If it's not the first page and no results, we might have reached the end.
                // But generally the scraper should handle this.
                // For now, if page 1 has no results, show empty state.
                if (currentPage === 1) {
                    resultsGrid.innerHTML = `
                                    <div class="empty-state" style="grid-column: 1 / -1;">
                                        <div class="empty-state-icon">📄</div>
                                        <div class="empty-state-title">No judgments found</div>
                                        <div class="empty-state-text">Try adjusting your search criteria</div>
                                        <a href="{{ route('subscriber.supremeSearch') }}" class="btn btn-new">New Search</a>
                                    </div>
                                `;
                    paginationContainer.style.display = 'none';
                    return;
                } else {
                    // Empty result on page > 1 usually means end of list
                    resultsGrid.innerHTML = `
                                    <div class="empty-state" style="grid-column: 1 / -1;">
                                         <div class="empty-state-text">No more results found on this page.</div>
                                         <div class="header-actions" style="justify-content:center;">
                                            <button onclick="changePage(${currentPage - 1})" class="btn btn-modify">Go Back</button>
                                         </div>
                                    </div>
                                `;
                    displayPaginationInputs(false); // Show pagination but maybe disable next
                    return;
                }
            }

            resultsGrid.innerHTML = judgments.map((judgment, index) => `
                                        <div class="result-card" onclick="viewPdf('${encodeURIComponent(judgment.pdf_url)}', '${escapeHtml(judgment.case_title)}', '${encodeURIComponent(judgment.result || '')}')" style="animation-delay: ${index * 0.05}s">
                                            <div class="result-card-title" data-full-text="${escapeHtml(judgment.case_title)}">${escapeHtml(judgment.case_title)}</div>
                                            <div class="result-card-body">
                                                <p style="color: var(--text-color); margin-bottom: 1rem; line-height: 1.6;" data-full-text="${escapeHtml(judgment.parties)}">${escapeHtml(judgment.parties)}</p>
                                            </div>
                                            <div class="result-info">
                                                <div class="info-row">
                                                    <span class="info-label">Uploaded on :</span>
                                                    <span class="info-value" data-full-text="${escapeHtml(judgment.upload_date)}" onclick="event.stopPropagation()">${escapeHtml(judgment.upload_date)}</span>
                                                </div>
                                                <div class="info-row">
                                                    <span class="info-label">Result :</span>
                                                    <span class="info-value" data-full-text="${escapeHtml(judgment.result || 'N/A')}" onclick="event.stopPropagation()">${escapeHtml(judgment.result || 'N/A')}</span>
                                                </div>
                                            </div>
                                        </div>
                                    `).join('');

            displayPaginationInputs(true);
        }

        function displayPaginationInputs(hasResults) {
            paginationContainer.style.display = 'flex';
            paginationContainer.innerHTML = '';

            // Previous Button
            const prevBtn = document.createElement('button');
            prevBtn.className = 'pagination-btn';
            prevBtn.innerHTML = '&lt;';
            prevBtn.disabled = currentPage === 1;
            prevBtn.onclick = () => changePage(currentPage - 1);
            paginationContainer.appendChild(prevBtn);

            // Page Numbers (Simple window: Current-2 to Current+2)
            let startPage = Math.max(1, currentPage - 2);
            // We don't know the total pages, so we just show a few ahead
            let endPage = currentPage + 2;

            // If we are on page 1, show up to 5
            if (currentPage === 1) endPage = 5;

            for (let i = startPage; i <= endPage; i++) {
                const pageBtn = document.createElement('button');
                pageBtn.className = `pagination-btn ${i === currentPage ? 'active' : ''}`;
                pageBtn.textContent = i;
                pageBtn.onclick = () => changePage(i);
                paginationContainer.appendChild(pageBtn);
            }

            // Next Button
            const nextBtn = document.createElement('button');
            nextBtn.className = 'pagination-btn';
            nextBtn.innerHTML = '&gt;';
            // We disable next if no results were returned (handled in displayResults somewhat), 
            // but here we just assume there might be more unless we explicitly know
            nextBtn.disabled = !hasResults;
            nextBtn.onclick = () => changePage(currentPage + 1);
            paginationContainer.appendChild(nextBtn);
        }

        function changePage(page) {
            if (page < 1) return;
            performSearch(page);
        }

        function viewPdf(pdfUrl, title, result = '') {
            const url = `{{ route('subscriber.supremeSearch.viewPdf') }}?url=${pdfUrl}&title=${encodeURIComponent(title)}&result=${result}`;
            window.location.href = url;
        }

        function showError(message) {
            loadingContainer.style.display = 'none';
            resultsGrid.style.display = 'grid';
            resultsGrid.innerHTML = `
                                                                            <div class="empty-state" style="grid-column: 1 / -1;">
                                                                                <div class="empty-state-icon">⚠️</div>
                                                                                <div class="empty-state-title">Error</div>
                                                                                <div class="empty-state-text">${escapeHtml(message)}</div>
                                                                                <a href="{{ route('subscriber.supremeSearch') }}" class="btn btn-new">Try Again</a>
                                                                            </div>
                                                                        `;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Perform search on page load
        performSearch();
    </script>
@endpush