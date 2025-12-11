    <div class="container comment-container">
        @if(!empty($decisionComment))
        <div id="comments" class="mb-4">
            @foreach($decisionComment as $comment)
            <div class="d-flex mb-3 p-3 border rounded shadow-sm bg-white comment-item" data-id="{{ $comment->id }}">
                <img src="{{ $comment->user->photo ? asset('uploads/subscriber/profile/'.$comment->user->photo) : asset('assets/img/avater-user.webp') }}"
                    alt="{{ $comment->user ? $comment->user->name : '' }}"
                    class="rounded-circle me-3" width="50" height="50">

                <div>
                    <div class="d-flex align-items-center mb-1">
                        <h6 class="mb-0 me-2">{{ $comment->user ? $comment->user->name : '' }}</h6>
                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                        @if(auth('subscriber')->id() === $comment->user_id)
                        <button class="btn btn-sm btn-link text-primary ms-5 edit-comment-btn" title="Edit your comment">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-link text-danger delete-comment-btn" title="Delete this comment">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                        @endif
                    </div>
                    <p class="mb-0 comment-text">{{ $comment->comment }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <div class="d-flex justify-content-between mb-4">
            <!-- Left side: Add Comment -->
            <button type="button" class="book-now-btn" data-bs-toggle="modal" data-bs-target="#commentModal">
                <i class="bi bi-chat"></i> Add Comment
            </button>

            <!-- Right side: Share -->
            <button type="button" class="book-now-btn" data-bs-toggle="modal" data-bs-target="#shareModal">
                <i class="bi bi-share"></i> Share
            </button>
        </div>



        <!-- Add Coment Modal -->
        <div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="commentForm" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="commentModalLabel">Add a Comment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <textarea id="commentText" class="form-control" rows="4" placeholder="Write your comment here..." required></textarea>
                        <input type="hidden" id="decisionId" value="{{ $data->id }}">
                        <input type="hidden" id="requesrtId" value="{{ $myNotes->id }}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="disable-btn mr-20 text-danger" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="book-now-btn">Post Comment</button>
                    </div>
                </form>
            </div>
        </div>


        <!-- Edit Comment Modal -->
        <div class="modal fade" id="editCommentModal" tabindex="-1" aria-labelledby="editCommentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="editCommentForm" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCommentModalLabel">Edit Comment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <textarea id="editCommentText" class="form-control" rows="4" required></textarea>
                        <input type="hidden" id="editCommentId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="disable-btn mr-20 text-danger" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="book-now-btn">Update Comment</button>


                    </div>
                </form>
            </div>
        </div>


        <!-- Share Decision Modal -->
        <div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="shareForm" method="POST" action="{{ route('subscriber.decision.share') }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="shareModalLabel">Share Decision</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <label for="receiverUser" class="form-label">Select user to share with</label>
                            <div class="col-sm-12">
                                <select id="receiverUser" name="receiver_id" class="form-select mb-3 select2Data" required>
                                    <option value="">-- Select user --</option>
                                    @foreach($allUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} [ {{ $user->email }} ] </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="decision_id" id="shareDecisionId" value="{{ $data->id }}">
                                <input type="hidden" name="reqeust_id" id="shareRequestId" value="{{ $myNotes->id }}">
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Share</button>
                        </div>
                    </form>

                    {{-- Shared users list outside main form --}}
                    <div class="p-3 border-top">
                        @if($getAllSharedUser->isNotEmpty())
                        <h6 class="mb-3">Already Shared With:</h6>
                        <ul class="list-group">
                            @foreach($getAllSharedUser as $shared)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $shared->receiver->name }}</strong><br>
                                    <small class="text-muted">{{ $shared->receiver->email }}</small>
                                </div>
                                <form action="{{ route('subscriber.decision.share.revoke', $shared->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to revoke sharing for this user?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Revoke</button>
                                </form>
                            </li>
                            @endforeach
                        </ul>
                        @else
                        <div class="text-muted">Not shared with anyone yet.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>


    </div>