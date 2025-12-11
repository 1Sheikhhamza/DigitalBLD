@extends('auth.subscribers.layouts.app')
@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', Auth::guard('subscriber')->user()->name.' | BLD Profile')

@section('content')

@include('auth.subscribers.profile._legal_top_navbar', ['myDecision' => true])
@include('auth.subscribers.profile._legal_search_pdf')
@if($checkSharedComment || $myNotes)
    @include('auth.subscribers.profile._decision_comments')
@endif
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        $('#commentForm').submit(function(e) {
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
                success: function(res) {
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
                error: function() {
                    alert('Error submitting comment.');
                }
            });
        });

        // Edit button click
        $(document).on('click', '.edit-comment-btn', function() {
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
        $('#editCommentForm').submit(function(e) {
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
                success: function(res) {
                    if (res.status === 'updated') {
                        let commentItem = $('.comment-item[data-id="' + commentId + '"]');
                        commentItem.find('.comment-text').text(res.comment.comment);

                        let editModal = bootstrap.Modal.getInstance(document.getElementById('editCommentModal'));
                        editModal.hide();
                        window.location.reload();
                    }
                },
                error: function() {
                    alert('Failed to update comment.');
                }
            });
        });

        // Delete button click
        $(document).on('click', '.delete-comment-btn', function() {
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
                success: function(res) {
                    if (res.status === 'deleted') {
                        commentItem.remove();
                    }
                },
                error: function() {
                    alert('Failed to delete comment.');
                }
            });
        });


        $('#shareForm').submit(function(e) {
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
                success: function(res) {
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
                error: function() {
                    alert('Error occurred while sharing.');
                }
            });
        });


    });
</script>
@endpush