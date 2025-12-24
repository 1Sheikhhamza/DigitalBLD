@extends('auth.subscribers.layouts.app')
@section('seodetails')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', Auth::guard('subscriber')->user()->name . ' | BLD Profile')

@section('content')

    @include('auth.subscribers.profile._legal_top_navbar', ['myDecision' => true])
    @include('auth.subscribers.profile._legal_search_pdf')
    @if($checkSharedComment || $myNotes)
        @include('auth.subscribers.profile._decision_comments')
    @endif

    <!-- AI Floating Action Button -->
    <div id="aiFloatingActionBtn"
        style="position: fixed; bottom: 30px; right: 30px; z-index: 9999; transition: all 0.3s ease-in-out;">
        <button type="button" class="btn btn-primary rounded-circle shadow-lg"
            style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;"
            data-bs-toggle="offcanvas" data-bs-target="#aiAssistantOffcanvas" title="Ask AI about this case">
            <i class="bi bi-robot" style="font-size: 1.5rem;"></i>
        </button>
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
        let boundaries = [];

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
                            span.className = 'tts-word-span pointer-cursor';
                            span.style.cursor = 'pointer';
                            span.onclick = (e) => handleWordClick(e, wordIndexRef.count);
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
            unwrapWords(container);
        }

        function handleWordClick(e, wordIndex) {
            if (!isReading && !isPaused) {
                startReadAloud(wordIndex);
            } else {
                window.speechSynthesis.cancel();
                startReadAloud(wordIndex);
            }
            e.stopPropagation();
        }


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

            // Calculate offset of the start span
            let startOffset = 0;
            if (startIndex > 0 && startIndex < domSpans.length) {
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
                        }
                    }
                }
            };

            utterance.onend = function () {
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

        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('judgment-content');
            if (container) {
                container.addEventListener('click', (e) => {
                    if (e.target.classList.contains('tts-word-span')) {
                        const parts = e.target.id.split('-');
                        const index = parseInt(parts[2]);
                        if (!isNaN(index)) {
                            handleWordClick(e, index);
                        }
                    }
                });
            }

            // Handle Layout Shift for AI Sidebar
            const aiOffcanvas = document.getElementById('aiAssistantOffcanvas');
            const judgmentContent = document.getElementById('judgment-content');
            const aiFloatingBtn = document.getElementById('aiFloatingActionBtn');

            if (aiOffcanvas) {
                aiOffcanvas.addEventListener('show.bs.offcanvas', function () {
                    if (window.innerWidth >= 992 && judgmentContent) {
                        judgmentContent.style.transition = 'margin-right 0.3s ease-in-out';
                        judgmentContent.style.marginRight = '500px';
                    }
                    if (aiFloatingBtn) {
                        aiFloatingBtn.style.right = 'auto';
                        aiFloatingBtn.style.left = '30px';
                        aiFloatingBtn.style.bottom = '110px';
                    }
                });

                aiOffcanvas.addEventListener('hide.bs.offcanvas', function () {
                    if (judgmentContent) {
                        judgmentContent.style.marginRight = 'auto';
                    }
                    if (aiFloatingBtn) {
                        aiFloatingBtn.style.left = 'auto';
                        aiFloatingBtn.style.right = '30px';
                        aiFloatingBtn.style.bottom = '30px';
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
                let requesrtId = $('#requesrtId').val();

                if (!comment) {
                    alert('Comment cannot be empty.');
                    return;
                }

                $.ajax({
                    url: "{{ route('subscriber.add.comment') }}",
                    method: "POST",
                    data: {
                        decision_id: decisionId,
                        request_id: requesrtId,
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
                            window.location.reload();
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
                // window.location.reload();
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
                            window.location.reload();
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
                let shareRequestId = $('#shareRequestId').val();
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
                        request_id: shareRequestId,
                        receiver_id: receiverId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (res) {
                        if (res.status === 'success') {
                            alert('Decision shared successfully!');
                            let modal = bootstrap.Modal.getInstance(document.getElementById('shareModal'));
                            modal.hide();
                            window.location.reload();
                        } else {
                            alert(res.message);
                            let modal = bootstrap.Modal.getInstance(document.getElementById('shareModal'));
                            modal.hide();
                            window.location.reload();
                        }
                    },
                    error: function () {
                        alert('Error occurred while sharing.');
                    }
                });
            });


        });
    </script>
@endpush