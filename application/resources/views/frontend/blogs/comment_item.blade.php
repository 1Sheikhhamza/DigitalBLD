<div class="comment-item border-bottom py-3 ps-{{ $comment->parent_id ? '4' : '0' }}"
    style="{{ $comment->parent_id ? 'background-color: #f9f9f9;' : '' }}">
    <div class="d-flex justify-content-between">
        <div>
            <strong>{{ $comment->subscriber->name ?? 'User' }}</strong>
            <small class="text-muted ms-2">{{ $comment->created_at->diffForHumans() }}</small>
        </div>
        <div>
            @if(Auth::guard('subscriber')->check())
                <button class="btn btn-sm btn-link text-decoration-none"
                    onclick="toggleReplyForm({{ $comment->id }})">Reply</button>
                @if($comment->subscriber_id == Auth::guard('subscriber')->id())
                    <form action="{{ route('subscriber.blog.comment.delete', $comment->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Are you sure you want to delete this comment?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-link text-danger text-decoration-none">Delete</button>
                    </form>
                @endif
            @endif
        </div>
    </div>
    <p class="mb-2 mt-1">{{ $comment->comment }}</p>

    <!-- Reply Form -->
    <div id="reply-form-{{ $comment->id }}" class="reply-form d-none mt-2 mb-3 ps-3 border-start">
        <form action="{{ route('subscriber.blog.comment', $blog->id) }}" method="POST">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
            <div class="form-group">
                <textarea name="comment" class="form-control form-control-sm" rows="2" placeholder="Write a reply..."
                    required></textarea>
            </div>
            <button type="submit" class="btn btn-sm btn-primary mt-2">Post Reply</button>
        </form>
    </div>

    <!-- Nested Replies -->
    @if($comment->replies->count() > 0)
        <div class="replies ms-4 border-start ps-2">
            @foreach($comment->replies as $reply)
                @include('frontend.blogs.comment_item', ['comment' => $reply, 'blog' => $blog])
            @endforeach
        </div>
    @endif
</div>