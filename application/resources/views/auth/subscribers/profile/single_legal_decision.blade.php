@extends('auth.subscribers.layouts.app')
@section('seodetails')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', Auth::guard('subscriber')->user()->name . ' | BLD Profile')

@section('content')

    @include('auth.subscribers.profile._legal_top_navbar', ['myDecision' => false])
    @include('auth.subscribers.profile._legal_search_pdf')

    <!-- AI Floating Action Button -->
    <!-- AI Floating Action Button -->
    <div id="aiFloatingActionBtn"
        style="position: fixed; bottom: 30px; right: 30px; z-index: 9999; transition: all 0.3s ease-in-out;">
        <button type="button" class="btn btn-primary rounded-circle shadow-lg"
            style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;"
            data-bs-toggle="offcanvas" data-bs-target="#aiAssistantOffcanvas" title="Ask AI about this case">
            <i class="bi bi-robot" style="font-size: 1.5rem;"></i>
        </button>
    </div>

    <!-- Copy to Folder Modal -->
    <div class="modal fade" id="copyModal" tabindex="-1" aria-labelledby="copyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="copyToFolderForm" class="modal-content" method="POST"
                action="{{ route('subscriber.decision.copy.to.folder') }}">
                @csrf
                <input type="hidden" name="decision_id" value="{{ $data->id }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="copyModalLabel">Copy Decision to Your Folder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <select name="folder_id" class="form-select" required>
                        <option value="">Select Folder</option>
                        @foreach($folders as $folder)
                            <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-files"></i> Copy
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- AI Verification Modal -->
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
    <!-- AI Assistant Offcanvas (Right Sidebar) -->
    <div class="offcanvas offcanvas-end" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1"
        id="aiAssistantOffcanvas" aria-labelledby="aiAssistantOffcanvasLabel"
        style="width: 500px; max-width: 90vw; border-left: 1px solid #ddd; box-shadow: -5px 0 15px rgba(0,0,0,0.1); top: 220px !important; height: calc(100vh - 220px);">

        <div class="offcanvas-header bg-light py-3 border-bottom">
            <h5 class="offcanvas-title d-flex align-items-center" id="aiAssistantOffcanvasLabel">
                <span class="me-2">🤖</span> AI Assistant
            </h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0" style="overflow: hidden;">
            <!-- Iframe to Streamlit App with Case ID -->
            <iframe src="http://127.0.0.1:8501/?embed=true&case_id={{ $data->id }}&user_id={{ auth('subscriber')->id() }}"
                style="width: 100%; height: 100%; border: none;" allow="clipboard-write"></iframe>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        let currentUtterance = null;
        let isReading = false;
        let isPaused = false;
        let boundaries = []; // Store boundaries globally for jump logic

        // Helper: Wrap words in spans for highlighting
        function wrapWordsInElement(element, wordIndexRef) {
            const childNodes = Array.from(element.childNodes);

            childNodes.forEach(node => {
                if (node.nodeType === Node.TEXT_NODE) {
                    const text = node.textContent;
                    if (!text.trim()) return;

                    const words = text.split(/(\s+)/); // Keep delimiters
                    const fragment = document.createDocumentFragment();

                    words.forEach(word => {
                        if (word.trim().length > 0) {
                            const span = document.createElement('span');
                            span.textContent = word;
                            span.id = `tts-word-${wordIndexRef.count}`;
                            span.className = 'tts-word-span pointer-cursor'; // Added pointer cursor class if you have CSS, else inline style?
                            span.style.cursor = 'pointer'; // Ensure it looks clickable
                            span.onclick = (e) => handleWordClick(e, wordIndexRef.count); // Direct click handler or delegated
                            fragment.appendChild(span);
                            wordIndexRef.count++;
                        } else {
                            fragment.appendChild(document.createTextNode(word));
                        }
                    });

                    node.replaceWith(fragment);
                } else if (node.nodeType === Node.ELEMENT_NODE) {
                    if (node.classList.contains('tts-word-span')) return;
                    wrapWordsInElement(node, wordIndexRef);
                }
            });
        }

        // Helper: Remove spans (unwrap)
        function unwrapWords(element) {
            const spans = element.querySelectorAll('span.tts-word-span');
            spans.forEach(span => {
                const text = document.createTextNode(span.textContent);
                span.replaceWith(text);
            });
            element.normalize();
        }

        function stopReadAloud(e) {
            if (e) e.preventDefault();
            window.speechSynthesis.cancel();
            resetTTSState();
        }

        function resetTTSState() {
            const btnText = document.getElementById('read-aloud-text');
            const icon = document.querySelector('#read-aloud-btn i');
            const stopBtn = document.getElementById('stop-read-aloud-btn');
            const container = document.getElementById('judgment-content');

            isReading = false;
            isPaused = false;

            if (btnText) btnText.innerText = 'Read Aloud';
            if (icon) icon.className = 'bi bi-play-circle';
            if (stopBtn) stopBtn.style.display = 'none';

            currentUtterance = null;
            if (window.activeHighlightSpanId) {
                const old = document.getElementById(window.activeHighlightSpanId);
                if (old) old.classList.remove('bg-warning', 'text-dark');
                window.activeHighlightSpanId = null;
            }
            // Optional: Unwrap words to clean up DOM, or keep them for future clicks? 
            // Better to unwrap to assume clean state.
            unwrapWords(container);
        }

        function handleWordClick(e, wordIndex) {
            if (!isReading && !isPaused) {
                // If not reading, start reading from here?
                // Or just ignore? User said "option for to start from a specific line"
                // Let's allow starting from here.
                startReadAloud(wordIndex);
            } else {
                // If reading, jumping to this word
                window.speechSynthesis.cancel();
                startReadAloud(wordIndex);
            }
            e.stopPropagation(); // Prevent bubbling
        }

        // Delegated click handler for better performance if needed, 
        // but individual onclicks on spans is fine for reasonable text size.
        // Actually, let's use the startReadAloud logic.

        function startReadAloud(startIndex = 0) {
            const container = document.getElementById('judgment-content');
            const btnText = document.getElementById('read-aloud-text');
            const icon = document.querySelector('#read-aloud-btn i');
            const stopBtn = document.getElementById('stop-read-aloud-btn');

            // 1. Prepare DOM if not already
            const spans = container.querySelectorAll('span.tts-word-span');
            if (spans.length === 0) {
                const wordCounter = { count: 0 };
                wrapWordsInElement(container, wordCounter);
            }

            // Re-query spans
            const domSpans = Array.from(container.querySelectorAll('span.tts-word-span'));
            if (domSpans.length === 0) {
                alert("No text found.");
                return;
            }

            // 2. Build map and text from startIndex
            let textToSpeak = "";
            boundaries = [];
            let cursor = 0;

            // We only want to speak from the startIndex-th span onwards
            // But we need to build the boundaries relative to the NEW textToSpeak string.

            // Find the span at startIndex
            // Actually, we can just iterate all spans, skip until startIndex, and append text.

            let startingSpanFound = false;

            domSpans.forEach((span, index) => {
                // Extract index from ID logic: tts-word-0, tts-word-1...
                // Simpler: just match index of valid spans logic.
                // The wrapWordsInElement counts sequentially. 
                // So domSpans[i] corresponds to word index i.

                if (index >= startIndex) {
                    const text = span.textContent;
                    // We also need to account for spaces between words?
                    // The wrap function kept delimiters in text nodes but only wrapped words.
                    // Accessing textContent of the container *includes* the non-wrapped text (spaces).
                    // THIS IS TRICKY: Constructing text from partial DOM spans misses the spaces in between.

                    // ROBUST STRATEGY: 
                    // 1. Get full textContent of container.
                    // 2. Find the character offset of the span at startIndex.
                    // 3. Slice the textContent from that offset.

                    // Let's find offset of domSpans[startIndex]
                }
            });

            // Better Robust Strategy:
            // Calculate offset of the start span
            let startOffset = 0;
            if (startIndex > 0 && startIndex < domSpans.length) {
                // We need to find the character position of this span in the container.textContent
                // Range API is good for this.
                const range = document.createRange();
                range.setStart(container, 0);
                range.setEndBefore(domSpans[startIndex]);
                startOffset = range.toString().length;
            }

            // Get text from that offset
            const fullText = container.textContent;
            textToSpeak = fullText.slice(startOffset);

            if (!textToSpeak.trim()) return;

            // Stop any current
            window.speechSynthesis.cancel();

            const utterance = new SpeechSynthesisUtterance(textToSpeak);

            // UI Update
            isReading = true;
            isPaused = false;
            btnText.innerText = 'Pause';
            icon.className = 'bi bi-pause-circle';
            stopBtn.style.display = 'inline-block';

            window.activeHighlightSpanId = null;

            // Boundary Mapping
            // We need to map (utterance charIndex + startOffset) -> span in DOM
            // Because utterance starts at 0, but corresponds to document at startOffset.

            // Let's rebuild the global boundaries map for the WHOLE document first?
            // Expensive? No.
            // Let's just build it once if empty? No, DOM might change? 
            // Just build it.

            const allBoundaries = [];
            let globalCursor = 0;
            function buildMap(element) {
                const childNodes = Array.from(element.childNodes);
                childNodes.forEach(node => {
                    if (node.nodeType === Node.TEXT_NODE) {
                        globalCursor += node.textContent.length;
                    } else if (node.nodeType === Node.ELEMENT_NODE) {
                        if (node.classList.contains('tts-word-span')) {
                            const len = node.textContent.length;
                            allBoundaries.push({
                                start: globalCursor,
                                end: globalCursor + len,
                                id: node.id
                            });
                            globalCursor += len;
                        } else {
                            buildMap(node);
                        }
                    }
                });
            }
            globalCursor = 0;
            buildMap(container);
            boundaries = allBoundaries;

            utterance.onboundary = function (event) {
                if (event.name === 'word') {
                    // event.charIndex is relative to textToSpeak
                    // So absolute index is event.charIndex + startOffset
                    const absIndex = event.charIndex + startOffset;

                    const closest = boundaries.find(b => absIndex >= b.start && absIndex < b.end);

                    if (closest && closest.id !== window.activeHighlightSpanId) {
                        if (window.activeHighlightSpanId) {
                            const old = document.getElementById(window.activeHighlightSpanId);
                            if (old) old.classList.remove('bg-warning', 'text-dark');
                        }

                        window.activeHighlightSpanId = closest.id;
                        const newSpan = document.getElementById(window.activeHighlightSpanId);
                        if (newSpan) {
                            newSpan.classList.add('bg-warning', 'text-dark');
                            // newSpan.scrollIntoView({ behavior: 'smooth', block: 'center' }); // Disabled
                        }
                    }
                }
            };

            utterance.onend = function () {
                // Only reset if we naturally ended (not paused)
                // Actually onboundary/onend might fire on pause? No.
                // onend fires on finish.
                resetTTSState();
            };

            utterance.onerror = function (event) {
                console.error("TTS Error", event);
                if (event.error !== 'interrupted') {
                    resetTTSState();
                }
            };

            currentUtterance = utterance;
            window.speechSynthesis.speak(utterance);
        }

        function toggleReadAloud(e) {
            if (e) e.preventDefault();

            const btnText = document.getElementById('read-aloud-text');
            const icon = document.querySelector('#read-aloud-btn i');

            if (isReading) {
                if (isPaused) {
                    // Resume
                    window.speechSynthesis.resume();
                    isPaused = false;
                    btnText.innerText = 'Pause';
                    icon.className = 'bi bi-pause-circle';
                } else {
                    // Pause
                    window.speechSynthesis.pause();
                    isPaused = true;
                    btnText.innerText = 'Resume';
                    icon.className = 'bi bi-play-circle';
                }
            } else {
                // Start New
                startReadAloud(0);
            }
        }

        // Add global click listener for delegated events on the container? 
        // Or reliant on the onclick added in wrapWordsInElement?
        // Using onclick in wrapWordsInElement is safer for specific span targeting.

        // However, we need to ensure the container is wrapped initially if user just clicks a word *before* pressing Read Aloud.
        // We can listen to container click?
        // No, let's just use the toggle button to 'Activate' text-to-speech mode?
        // User asked: "option for to start from a specific line"
        // If I make the text clickable ONLY after "Read Aloud" is pressed, that might be confusing.
        // Better: When "Read Aloud" is pressed, we wrap text.
        // What if they want to click to start?
        // Maybe we wrap on page load? No, intrusive.
        // Let's stick to "Press Read Aloud to Start", then you can Pause/Resume or Click other words to Jump.
        // Wait, "always starts from the begining - that should not be"
        // So they want Pause (resume from where left off) AND Jump.
        // My implementation supports both.
        // I will add a click listener to the container so IF we are in reading mode (spans exist), clicks work.

        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('judgment-content');
            if (container) {
                container.addEventListener('click', (e) => {
                    // Check if target is a tts-word
                    if (e.target.classList.contains('tts-word-span')) {
                        // Parse index from ID: tts-word-123
                        const parts = e.target.id.split('-');
                        const index = parseInt(parts[2]);
                        if (!isNaN(index)) {
                            handleWordClick(e, index);
                        }
                    }
                });
            }
        });

    </script>
    <script>
        $(document).ready(function () {
            $('#commentForm').submit(function (e) {
                e.preventDefault();

                let comment = $('#commentText').val().trim();
                let decisionId = $('#decisionId').val();

                if (!comment) {
                    alert('Comment cannot be empty.');
                    return;
                }

                $.ajax({
                    url: "{{ route('subscriber.add.comment') }}",
                    method: "POST",
                    data: {
                        decision_id: decisionId,
                        comment: comment,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (res) {
                        if (res.comment) {
                            let newComment = `
                                                                                                                            <div class="d-flex mb-3 p-3 border rounded shadow-sm">
                                                                                                                                <img src="${res.comment.user.image ? res.comment.user.image : '{{ asset('assets/img/avater-user.webp') }}'}"
                                                                                                                                    alt="${res.comment.user.name}"
                                                                                                                                    class="rounded-circle me-3" width="50" height="50">

                                                                                                                                <div>
                                                                                                                                    <div class="d-flex align-items-center mb-1">
                                                                                                                                        <h6 class="mb-0 me-2">${res.comment.user.name}</h6>
                                                                                                                                        <small class="text-muted">Just now</small>
                                                                                                                                    </div>
                                                                                                                                    <p class="mb-0">${res.comment.comment}</p>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        `;

                            $('#comments').prepend(newComment);

                            // Clear textarea and hide modal
                            $('#commentText').val('');
                            let modal = bootstrap.Modal.getInstance(document.getElementById('commentModal'));
                            modal.hide();
                        } else {
                            alert('Failed to add comment. Please try again.');
                        }
                    },
                    error: function () {
                        alert('Error submitting comment.');
                    }
                });
            });

            // Edit button click
            $(document).on('click', '.edit-comment-btn', function () {
                let commentItem = $(this).closest('.comment-item');
                let commentId = commentItem.data('id');
                let commentText = commentItem.find('.comment-text').text();

                $('#editCommentId').val(commentId);
                $('#editCommentText').val(commentText.trim());

                let editModal = new bootstrap.Modal(document.getElementById('editCommentModal'));
                editModal.show();
            });

            // Submit update
            $('#editCommentForm').submit(function (e) {
                e.preventDefault();
                let commentId = $('#editCommentId').val();
                let comment = $('#editCommentText').val().trim();

                $.ajax({
                    url: "{{ route('subscriber.comment.update') }}",
                    method: "POST",
                    data: {
                        comment_id: commentId,
                        comment: comment,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (res) {
                        if (res.status === 'updated') {
                            let commentItem = $('.comment-item[data-id="' + commentId + '"]');
                            commentItem.find('.comment-text').text(res.comment.comment);

                            let editModal = bootstrap.Modal.getInstance(document.getElementById('editCommentModal'));
                            editModal.hide();
                        }
                    },
                    error: function () {
                        alert('Failed to update comment.');
                    }
                });
            });

            // Delete button click
            $(document).on('click', '.delete-comment-btn', function () {
                if (!confirm('Are you sure you want to delete this comment?')) return;

                let commentItem = $(this).closest('.comment-item');
                let commentId = commentItem.data('id');

                $.ajax({
                    url: "{{ route('subscriber.comment.delete') }}",
                    method: "POST",
                    data: {
                        comment_id: commentId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (res) {
                        if (res.status === 'deleted') {
                            commentItem.remove();
                        }
                    },
                    error: function () {
                        alert('Failed to delete comment.');
                    }
                });
            });


            $('#shareForm').submit(function (e) {
                e.preventDefault();
                let decisionId = $('#shareDecisionId').val();
                let receiverId = $('#receiverUser').val();

                if (!receiverId) {
                    alert('Please select a user to share with.');
                    return;
                }

                $.ajax({
                    url: "{{ route('subscriber.decision.share') }}",
                    method: "POST",
                    data: {
                        decision_id: decisionId,
                        receiver_id: receiverId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (res) {
                        if (res.status === 'success') {
                            alert('Decision shared successfully!');
                            let modal = bootstrap.Modal.getInstance(document.getElementById('shareModal'));
                            modal.hide();
                        } else {
                            alert('Failed to share decision.');
                        }
                    },
                    error: function () {
                        alert('Error occurred while sharing.');
                    }
                });
            });



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
                            if (xhr.status === 419) msg = 'Session expired (419). Please refresh the page.';
                            if (xhr.status === 500) msg = 'Server Error (500). Please check logs.';
                            if (xhr.status === 404) msg = 'Route not found (404).';

                            // Try to parse detailed error from JSON response
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

            // Handle Layout Shift for AI Sidebar
            const aiOffcanvas = document.getElementById('aiAssistantOffcanvas');
            // Target the specific judgment container ID we found
            const judgmentContent = document.getElementById('judgment-content');
            const aiFloatingBtn = document.getElementById('aiFloatingActionBtn');

            if (aiOffcanvas) {
                aiOffcanvas.addEventListener('show.bs.offcanvas', function () {
                    // Only shift layout on desktop
                    if (window.innerWidth >= 992 && judgmentContent) {
                        judgmentContent.style.transition = 'margin-right 0.3s ease-in-out';
                        judgmentContent.style.marginRight = '500px';
                    }
                    // Move AI Button to left (above Admin Chat)
                    if (aiFloatingBtn) {
                        aiFloatingBtn.style.right = 'auto'; // Clear right
                        aiFloatingBtn.style.left = '30px';
                        aiFloatingBtn.style.bottom = '110px'; // Stack above Admin Chat (30px + 60px + 20px gap)
                    }
                });

                aiOffcanvas.addEventListener('hide.bs.offcanvas', function () {
                    if (judgmentContent) {
                        judgmentContent.style.marginRight = 'auto'; // Reset to centered/auto
                    }
                    // Move AI Button back to right
                    if (aiFloatingBtn) {
                        aiFloatingBtn.style.left = 'auto'; // Clear left
                        aiFloatingBtn.style.right = '30px';
                        aiFloatingBtn.style.bottom = '30px';
                    }
                });
            }

        });
    </script>
@endpush