<div class="container">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <div class="top-action-bar mb-3">
        <div class="row align-items-center w-100 g-2">
            <!-- Left: Back Button -->
            <div class="col-md-1 col-12 text-start mb-2 mb-md-0" style="z-index: 100;">
                @if(!$myDecision)
                <a href="{{ url('subscriber/'.$returnParamString) }}" class="action-link">
                    <i class="bi bi-chevron-left"></i> Back
                </a>
                @endif
            </div>

            <!-- Center: Action Buttons -->
            <div class="col-md-9 col-12">
                <div class="text-center d-flex align-items-center justify-content-center">
                    <a href="{{ route('subscriber.legal-search.print', $data->id) }}" class="action-link" target="_blank">
                        <i class="bi bi-printer"></i> Print
                    </a>
                    <a href="{{ route('subscriber.legal-search.downloadPdf', $data->id) }}" class="action-link">
                        <i class="bi bi-download"></i> Download
                    </a>
                    <a href="#" class="action-link">
                        <label class="me-1">Translator:</label>
                        <div id="google_translate_element"></div>
                    </a>
                    @if(!$myDecision)
                    <a href="#" class="action-link" data-bs-toggle="modal" data-bs-target="#copyModal">
                        <i class="bi bi-folder"></i> Copy to My Folder
                    </a>
                    @endif
                    @if($myDecision)
                    @if($myNotes && !$sharedDecision)
                    <a href="{{ route('subscriber.myDecision.editNote', [$myNotes->noteId, $decisionFolderId]) }}" class="action-link">
                        <i class="bi bi-pencil"></i> Edit My Note
                    </a>
                    <!-- <a href="{{ route('subscriber.shared.decisions') }}" class="action-link">
                            <i class="bi bi-share"></i> Shared with Me
                        </a> -->
                    @endif
                    @endif
                    @if(!$myDecision)
                    <a href="#" class="action-link" id="bookmark-btn" data-id="{{ $data->id }}">
                        @if($isBookmarked)
                        <i class="bi bi-bookmark-fill text-danger"></i> Bookmarked
                        @else
                        <i class="bi bi-bookmark"></i> Bookmark
                        @endif
                    </a>
                    <span id="bookmark-message" style="margin-left: 10px; color: green; display: none;"></span>
                    @endif
                </div>
            </div>

            <!-- Right: Previous/Next Navigation -->
            @if(!$myDecision)
            <div class="col-md-2 col-12 justify-content-end nav-arrows mt-2 mt-md-0 d-flex justify-content-between">
                @if($previousDecision)
                <a href="{{ route('subscriber.singleDecision', [$previousDecision->id, Crypt::encrypt($returnParamString)]) }}" class="action-link me-2">
                    <i class="bi bi-chevron-left"></i> Previous
                </a>
                @else
                <a href="#" class="action-link disabled me-2" style="pointer-events: none; opacity: 0.5;">
                    <i class="bi bi-chevron-left"></i> Previous
                </a>
                @endif

                @if($nextDecision)
                <a href="{{ route('subscriber.singleDecision', [$nextDecision->id, Crypt::encrypt($returnParamString)]) }}" class="action-link">
                    Next <i class="bi bi-chevron-right"></i>
                </a>
                @else
                <a href="#" class="action-link disabled" style="pointer-events: none; opacity: 0.5;">
                    Next <i class="bi bi-chevron-right"></i>
                </a>
                @endif
            </div>
            @endif
        </div>
    </div>

</div>
@push('scripts')
<script>
    $(document).ready(function() {
        $('#bookmark-btn').click(function(e) {
            e.preventDefault();

            let $btn = $(this);
            let decisionId = $btn.data('id');
            let $message = $('#bookmark-message');

            $.ajax({
                url: "{{ route('subscriber.bookmark.toggle') }}",
                method: "POST",
                data: {
                    decision_id: decisionId,
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    if (res.status === 'added') {
                        $btn.html('<i class="bi bi-bookmark-fill text-danger"></i> Bookmarked');
                        $message.text('Bookmarked!').css('color', 'green').fadeIn();

                    } else {
                        $btn.html('<i class="bi bi-bookmark"></i> Bookmark');
                        $message.text('Bookmark removed!').css('color', 'red').fadeIn();
                    }

                    // Hide message after 3 sec
                    setTimeout(function() {
                        $message.fadeOut();
                    }, 3000);
                }
            });
        });
    });
</script>

<script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                includedLanguages: 'en,bn,hi,zh-CN,es,ar', // English, Bangla, Hindi, Chinese (Simplified), Spanish, Arabic
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
                autoDisplay: false
            }, 'google_translate_element');
        }

</script>

<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

@endpush