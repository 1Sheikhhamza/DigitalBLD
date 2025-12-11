@extends('layouts.app')
@section('seodetails')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', "Welcome to Digital Bangladesh Legal Decisions")

@section('content')

    <section class="banner-inner-sec" style="background-image:url('/frontend/assets/img/service-page-bg.jpg')">
        <div class="banner-table">
            <div class="banner-table-cell">
                <div class="container">
                    <div class="banner-inner-content">
                        <h2 class="banner-inner-title">{{ $blog->title }}</h2>
                        <!-- <ul class="xs-breadcumb">
                                            <li><a href="{{ route('blog') }}"> Home / </a> Blogs</li>
                                        </ul> -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="service-inner-sec single-service-sec section-padding">
        <div class="container">
            <!-- Toggle Button -->
            <div class="row mb-3">
                <div class="col-12 text-start">
                    <button class="btn btn-sm btn-outline-secondary" onclick="toggleSidebar()" id="sidebar-toggle-btn">
                        <i class="fa fa-bars"></i> Show Related Blogs
                    </button>
                </div>
            </div>

            <div class="row">
                <!-- Sidebar (Hidden by default) -->
                <div class="col-lg-3 col-md-4 d-none" id="sidebar-col">
                    <div class="service-sidebar">

                        <!-- Related Blogs Section -->
                        <div class="widgets">
                            <h3 class="widget-title"><span>Related</span> Blogs</h3>
                            <div class="related-blogs">
                                @if($allBlogs != '' && count($allBlogs) > 0)
                                    @foreach($allBlogs as $otherBlog)
                                        <div class="related-blog-card {{ $currentSlug == $otherBlog->slug ? 'active' : '' }}">
                                            <a href="{{ url('blog/' . $otherBlog->slug) }}">
                                                <div class="blog-thumb">
                                                    <img src="{{ asset('uploads/blogs/' . $otherBlog->featured_image) }}"
                                                        alt="{{ $otherBlog->title }}">
                                                </div>
                                                <div class="blog-info">
                                                    <h5>{{ \Illuminate\Support\Str::limit($otherBlog->title, 50) }}</h5>
                                                    <span class="blog-date">
                                                        <i class="fa fa-calendar"></i>
                                                        {{ \Carbon\Carbon::parse($otherBlog->created_at)->format('M d, Y') }}
                                                    </span>
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div><!-- service sidebar -->
                </div>

                <!-- Main Content (Full width by default) -->
                <div class="col-lg-12 col-md-12" id="content-col">
                    <div class="main-single-blog-content">
                        <div class="single-blog-post-content">
                            <img src="{{ asset('uploads/blogs/' . ($blog->featured_image ?? '')) }}"
                                alt="{{ $blog->title }}">
                            <p>{!! $blog->content!!}</p>

                            <!-- Social Sharing Buttons -->
                            <div class="social-sharing mb-4 mt-4">
                                <style>
                                    .social-share-btn {
                                        width: 40px;
                                        height: 40px;
                                        display: inline-flex;
                                        align-items: center;
                                        justify-content: center;
                                        margin-right: 8px;
                                        transition: transform 0.2s;
                                    }
                                    .social-share-btn:hover {
                                        transform: scale(1.1);
                                    }
                                    .social-share-btn img {
                                        width: 100%;
                                        height: 100%;
                                    }
                                    /* Keep styles for non-image buttons if any */
                                    .btn-email { background-color: #7f7f7f; border-radius: 5px; color: white !important; }
                                    .btn-share { background-color: #84c637; border-radius: 5px; color: white !important; }
                                </style>
                                <div class="d-flex align-items-center">
                                    <span class="me-3 fw-bold">Share:</span>

                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="social-share-btn">
                                        <img src="{{ asset('assets/images/icons/icons8-facebook-50.svg') }}" alt="Facebook">
                                    </a>

                                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($blog->title) }}" target="_blank" class="social-share-btn">
                                        <img src="{{ asset('assets/images/icons/icons8-x-50.svg') }}" alt="X">
                                    </a>

                                    <a href="https://wa.me/?text={{ urlencode($blog->title . ' - ' . url()->current()) }}" target="_blank" class="social-share-btn">
                                        <img src="{{ asset('assets/images/icons/icons8-whatsapp-50.svg') }}" alt="WhatsApp">
                                    </a>

                                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank" class="social-share-btn">
                                        <img src="{{ asset('assets/images/icons/icons8-linkedin-50.svg') }}" alt="LinkedIn">
                                    </a>

                                    <!-- Added Instagram (Though typically not directly shareable via link, added as requested/available icon) -->
                                    <a href="https://www.instagram.com/" target="_blank" class="social-share-btn">
                                        <img src="{{ asset('assets/images/icons/icons8-instagram-50.svg') }}" alt="Instagram">
                                    </a>

                                    <a href="mailto:?subject={{ urlencode($blog->title) }}&body={{ urlencode(url()->current()) }}"
                                        class="social-share-btn btn-email">
                                        <i class="fa fa-envelope"></i>
                                    </a>

                                    <button onclick="nativeShare()" class="social-share-btn btn-share border-0">
                                        <i class="fa fa-share-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Blog Interactions -->
                        <div class="blog-interactions mt-5">
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="mb-0">Interactions</h4>
                                <div class="interaction-buttons">
                                    @php
                                        $isLiked = Auth::guard('subscriber')->check() ? $blog->isLikedBy(Auth::guard('subscriber')->id()) : false;
                                        $isDisliked = Auth::guard('subscriber')->check() ? $blog->isDislikedBy(Auth::guard('subscriber')->id()) : false;
                                    @endphp
                                    <button class="btn {{ $isLiked ? 'btn-primary' : 'btn-outline-primary' }} me-2"
                                        onclick="toggleInteraction('like')" id="like-btn">
                                        <i class="fa fa-thumbs-up"></i> <span
                                            id="like-count">{{ $blog->likes_count }}</span>
                                    </button>
                                    <button class="btn {{ $isDisliked ? 'btn-danger' : 'btn-outline-danger' }}"
                                        onclick="toggleInteraction('dislike')" id="dislike-btn">
                                        <i class="fa fa-thumbs-down"></i> <span
                                            id="dislike-count">{{ $blog->dislikes_count }}</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Comments Section -->
                            <div class="comments-section">
                                <h4 class="mb-3">Comments ({{ $blog->comments->count() }})</h4>

                                <!-- Comment Form -->
                                @if(Auth::guard('subscriber')->check())
                                    <form action="{{ route('subscriber.blog.comment', $blog->id) }}" method="POST" class="mb-5">
                                        @csrf
                                        <div class="form-group">
                                            <textarea name="comment" class="form-control" rows="3"
                                                placeholder="Write a comment..." required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary mt-2">Post Comment</button>
                                    </form>
                                @else
                                    <div class="alert alert-info mb-4">
                                        Please <a href="{{ route('subscriber.login') }}">login</a> to comment or react.
                                    </div>
                                @endif

                                <!-- Comment List -->
                                <div class="comment-list">
                                    @php
                                        // Group comments by parent_id for easier threading handling in view without N+1 if loaded properly, 
                                        // or just iterating roots and using relationship if lazy loading is acceptable (for now it is).
                                        $rootComments = $blog->comments->whereNull('parent_id');
                                    @endphp

                                    @forelse($rootComments as $comment)
                                        @include('frontend.blogs.comment_item', ['comment' => $comment, 'blog' => $blog])
                                    @empty
                                        <p class="text-muted">No comments yet. Be the first to share your thoughts!</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <script>
                            function toggleSidebar() {
                                const sidebar = document.getElementById('sidebar-col');
                                const content = document.getElementById('content-col');
                                const btn = document.getElementById('sidebar-toggle-btn');

                                if (sidebar.classList.contains('d-none')) {
                                    // Show Sidebar
                                    sidebar.classList.remove('d-none');
                                    content.classList.remove('col-lg-12', 'col-md-12');
                                    content.classList.add('col-lg-9', 'col-md-8');
                                    btn.innerHTML = '<i class="fa fa-times"></i> Hide Related Blogs';
                                } else {
                                    // Hide Sidebar
                                    sidebar.classList.add('d-none');
                                    content.classList.remove('col-lg-9', 'col-md-8');
                                    content.classList.add('col-lg-12', 'col-md-12');
                                    btn.innerHTML = '<i class="fa fa-bars"></i> Show Related Blogs';
                                }
                            }

                            function toggleInteraction(action) {
                                @if(!Auth::guard('subscriber')->check())
                                    window.location.href = "{{ route('subscriber.login') }}";
                                    return;
                                @endif

                                fetch("{{ route('subscriber.blog.like', $blog->id) }}", {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({ action: action })
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.status === 'success') {
                                            document.getElementById('like-count').innerText = data.likes;
                                            document.getElementById('dislike-count').innerText = data.dislikes;

                                            // Update button styles
                                            const likeBtn = document.getElementById('like-btn');
                                            const dislikeBtn = document.getElementById('dislike-btn');

                                            if (action === 'like') {
                                                if (data.action === 'removed') {
                                                    likeBtn.classList.remove('btn-primary');
                                                    likeBtn.classList.add('btn-outline-primary');
                                                } else { // created or updated
                                                    likeBtn.classList.add('btn-primary');
                                                    likeBtn.classList.remove('btn-outline-primary');
                                                    dislikeBtn.classList.remove('btn-danger');
                                                    dislikeBtn.classList.add('btn-outline-danger');
                                                }
                                            } else {
                                                if (data.action === 'removed') {
                                                    dislikeBtn.classList.remove('btn-danger');
                                                    dislikeBtn.classList.add('btn-outline-danger');
                                                } else { // created or updated
                                                    dislikeBtn.classList.add('btn-danger');
                                                    dislikeBtn.classList.remove('btn-outline-danger');
                                                    likeBtn.classList.remove('btn-primary');
                                                    likeBtn.classList.add('btn-outline-primary');
                                                }
                                            }
                                        }
                                    })
                                    .catch(error => console.error('Error:', error));
                            }

                            function toggleReplyForm(commentId) {
                                const form = document.getElementById('reply-form-' + commentId);
                                if (form.classList.contains('d-none')) {
                                    form.classList.remove('d-none');
                                } else {
                                    form.classList.add('d-none');
                                }
                            }

                            function nativeShare() {
                                if (navigator.share) {
                                    navigator.share({
                                        title: '{{ $blog->title }}',
                                        text: 'check out this blog post!',
                                        url: '{{ url()->current() }}'
                                    })
                                        .then(() => console.log('Successful share'))
                                        .catch((error) => console.log('Error sharing', error));
                                } else {
                                    alert('Web Share API is not supported in your browser.');
                                }
                            }
                        </script>

                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection
@section('footer')

    @parent
@endsection