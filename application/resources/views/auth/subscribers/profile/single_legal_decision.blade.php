@extends('auth.subscribers.layouts.app')
@section('seodetails')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', Auth::guard('subscriber')->user()->name . ' | BLD Profile')

@section('content')

    @include('auth.subscribers.profile._legal_top_navbar', ['myDecision' => false])
    @include('auth.subscribers.profile._legal_search_pdf')

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
@endsection
@push('scripts')
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

        });
    </script>
@endpush