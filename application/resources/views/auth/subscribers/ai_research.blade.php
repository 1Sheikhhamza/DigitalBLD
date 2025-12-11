@extends('auth.subscribers.layouts.app')

@section('content')
    <div class="content-wrapper container-fluid px-0 h-100">
        <div class="row h-100 g-0">
            <!-- Main Chat Area -->
            <div class="col-md-9 d-flex flex-column h-100 border-end">
                <!-- Header -->
                <div class="p-3 border-bottom bg-white d-flex align-items-center justify-content-between sticky-top">
                    <h5 class="mb-0"><i class="bi bi-robot text-primary me-2"></i>Legal AI Research Assistant</h5>
                    <span class="badge bg-light text-dark">Powered by OpenRouter & Database RAG</span>
                </div>

                <!-- Chat History -->
                <div id="chat-history" class="flex-grow-1 p-4 overflow-auto bg-light">
                    <div class="text-center text-muted mt-5">
                        <i class="bi bi-chat-square-quote display-4 mb-3"></i>
                        <h4>Ask meaningful questions about Case Laws</h4>
                        <p>Examples:</p>
                        <div class="d-inline-flex gap-2">
                            <span class="badge bg-white border text-secondary p-2 pointer"
                                onclick="fillQuery('What constitutes negligence in medical cases?')">Medical
                                Negligence</span>
                            <span class="badge bg-white border text-secondary p-2 pointer"
                                onclick="fillQuery('Precedents regarding land boundary disputes')">Land Disputes</span>
                            <span class="badge bg-white border text-secondary p-2 pointer"
                                onclick="fillQuery('Latest guidelines for bail in non-bailable offenses')">Bail
                                Guidelines</span>
                        </div>
                    </div>
                </div>

                <!-- Input Area -->
                <div class="p-3 bg-white border-top">
                    <form id="ai-research-form" class="position-relative">
                        @csrf
                        <textarea id="user-query" class="form-control pe-5" rows="2"
                            placeholder="Ask your legal question here..." required
                            style="resize: none; padding-right: 50px;"></textarea>
                        <button type="submit" id="send-btn"
                            class="btn btn-primary position-absolute bottom-0 end-0 m-2 rounded-circle"
                            style="width: 40px; height: 40px;">
                            <i class="bi bi-send"></i>
                        </button>
                    </form>
                    <small class="text-muted ms-1">AI responses are generated based on available database records. Always
                        verify with original judgments.</small>
                </div>
            </div>

            <!-- Sidebar: Sources -->
            <div class="col-md-3 bg-white h-100 overflow-auto border-start">
                <div class="p-3 border-bottom sticky-top bg-white">
                    <h6 class="mb-0"><i class="bi bi-journal-bookmark me-2"></i>Cited Sources</h6>
                </div>
                <div id="sources-list" class="p-3">
                    <p class="text-muted small text-center mt-5">Sources used for the answer will appear here.</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Chat Bubble Styles */
        .chat-message {
            max-width: 80%;
            margin-bottom: 20px;
        }

        .chat-user {
            margin-left: auto;
        }

        .chat-user .message-box {
            background-color: #0d6efd;
            color: white;
            border-radius: 15px 15px 0 15px;
            padding: 15px;
        }

        .chat-ai .message-box {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 15px 15px 15px 0;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .source-card {
            transition: all 0.2s;
            cursor: pointer;
        }

        .source-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        }

        .pointer {
            cursor: pointer;
        }

        /* Markdown Styles */
        .markdown-body ul {
            padding-left: 20px;
            margin-bottom: 1rem;
        }

        .markdown-body li {
            margin-bottom: 8px;
        }

        .markdown-body p {
            margin-bottom: 10px;
        }

        .markdown-body strong {
            font-weight: 600;
            color: #2c3e50;
        }

        .markdown-body blockquote {
            border-left: 4px solid #0d6efd;
            padding-left: 15px;
            color: #555;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            font-size: 0.9em;
        }
    </style>

    <!-- Add Marked.js for Markdown Parsing -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    @push('scripts')
        <script>
            function fillQuery(text) {
                document.getElementById('user-query').value = text;
                document.getElementById('user-query').focus();
            }

            $(document).ready(function () {
                function submitQuery() {
                    let query = $('#user-query').val().trim();
                    if (!query) return;

                    // 1. Add User Message
                    $('#chat-history').append(`
                                            <div class="chat-message chat-user d-flex justify-content-end">
                                                <div class="message-box">
                                                    <p class="mb-0">${query}</p>
                                                </div>
                                            </div>
                                        `);
                    $('#user-query').val('');

                    // 2. Add Loading Indicator
                    let loadingId = 'loading-' + Date.now();
                    $('#chat-history').append(`
                                            <div id="${loadingId}" class="chat-message chat-ai d-flex">
                                                <div class="message-box">
                                                   <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                                                   <span>Analyzing database records... This may take up to 2 minutes.</span>
                                                </div>
                                            </div>
                                        `);

                    // Scroll to bottom
                    let chatHistory = document.getElementById('chat-history');
                    chatHistory.scrollTop = chatHistory.scrollHeight;

                    // 3. AJAX Request
                    $.ajax({
                        url: "{{ route('ai.research.ask') }}",
                        method: "POST",
                        data: {
                            query: query,
                            _token: "{{ csrf_token() }}"
                        },
                        timeout: 120000, // 2 mins timeout
                        success: function (res) {
                            $('#' + loadingId).remove();

                            // Show AI Answer
                        if (res.success) {
                            // Parse Markdown to HTML
                            let rawAnswer = res.answer;
                            let htmlAnswer = marked.parse(rawAnswer);

                            $('#chat-history').append(`
                                    <div class="chat-message chat-ai d-flex">
                                        <div class="message-box w-100">
                                           <div class="mb-0 markdown-body">${htmlAnswer}</div>
                                        </div>
                                    </div>
                                `);
                                // Update Sources Sidebar
                                let sourcesHtml = '';
                                if (res.sources && res.sources.length > 0) {
                                    res.sources.forEach(source => {
                                        let viewUrl = "{{ route('subscriber.singleDecision', ':id') }}".replace(':id', source.id);
                                        sourcesHtml += `
                                                                <div class="card mb-2 source-card">
                                                                    <div class="card-body p-2">
                                                                        <h6 class="card-title text-primary small mb-1">
                                                                            <a href="${viewUrl}" target="_blank" class="text-decoration-none stretched-link">
                                                                                ${source.title}
                                                                            </a>
                                                                        </h6>
                                                                        <p class="card-text text-muted x-small mb-0"><i class="bi bi-calendar3"></i> ${source.year}</p>
                                                                    </div>
                                                                </div>
                                                            `;
                                    });
                                } else {
                                    sourcesHtml = '<p class="text-muted small text-center">No specific sources cited for this general answer.</p>';
                                }
                                $('#sources-list').html(sourcesHtml);
                            }
                        },
                        error: function (xhr) {
                            $('#' + loadingId).remove();
                            let msg = "An error occurred.";
                            if (xhr.status === 500) msg = "Server Error (500). Please check if the AI service is reachable.";

                            // Parse JSON error if available
                            try {
                                let responseJSON = JSON.parse(xhr.responseText);
                                if (responseJSON.error) msg = responseJSON.error;
                            } catch (e) { }

                            $('#chat-history').append(`
                                                    <div class="chat-message chat-ai d-flex">
                                                        <div class="message-box text-danger border-danger">
                                                           <i class="bi bi-exclamation-circle me-1"></i> ${msg}
                                                        </div>
                                                    </div>
                                                `);
                        }
                    });
                }

                // Handle Form Submit (prevent default)
                $('#ai-research-form').on('submit', function (e) {
                    e.preventDefault();
                    submitQuery();
                });

                // Handle Button Click explicitly
                $('#send-btn').on('click', function (e) {
                    e.preventDefault();
                    submitQuery();
                });

                // Submit on Enter (without Shift)
                $('#user-query').keydown(function (e) {
                    if (e.keyCode == 13 && !e.shiftKey) {
                        e.preventDefault();
                        submitQuery();
                    }
                });
            });
        </script>
    @endpush
@endsection