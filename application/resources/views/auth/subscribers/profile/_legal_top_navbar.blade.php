<div class="container-fluid">
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
                    <a href="{{ url('subscriber/' . $returnParamString) }}" class="action-link">
                        <i class="bi bi-chevron-left"></i> Back
                    </a>
                @endif
            </div>

            <!-- Center: Action Buttons -->
            <div class="col-md col-12">
                <div class="text-center d-flex align-items-center justify-content-center flex-nowrap gap-1">
                    <a href="{{ route('subscriber.legal-search.print', [$data->id, 'print']) }}" class="action-link"
                        target="_blank">
                        <i class="bi bi-printer"></i> Print
                    </a>
                    <a href="{{ route('subscriber.legal-search.print', [$data->id, 'download']) }}" class="action-link">
                        <i class="bi bi-download"></i> Download
                    </a>

                    @if($data->file_path)
                        <a href="{{ asset($data->file_path) }}" class="action-link" target="_blank"
                            title="View Original PDF">
                            <i class="bi bi-file-earmark-pdf text-danger"></i> View PDF
                        </a>
                    @endif

                    @if(auth('subscriber')->user()->canAccessModule('ai.summary'))
                        <a href="#" class="action-link" id="summarize-btn" data-id="{{ $data->id }}" data-bs-toggle="modal"
                            data-bs-target="#aiSummaryModal">
                            <i class="bi bi-stars"></i> Summarize
                        </a>
                    @else
                        <a href="{{ route('subscriber.upgrade.plan') }}" class="action-link">
                            <i class="bi bi-stars"></i> Summarize
                        </a>
                    @endif

                    @if(auth('subscriber')->user()->canAccessModule('read.aloud'))
                        <a href="#" class="action-link" id="read-aloud-btn" onclick="toggleReadAloud(event)">
                            <i class="bi bi-play-circle"></i> <span id="read-aloud-text">Read Aloud</span>
                        </a>
                        <a href="#" class="action-link text-danger" id="stop-read-aloud-btn" onclick="stopReadAloud(event)"
                            style="display: none;">
                            <i class="bi bi-stop-circle"></i> Stop
                        </a>
                    @else
                        <a href="{{ route('subscriber.upgrade.plan') }}" class="action-link">
                            <i class="bi bi-volume-up"></i> <span id="read-aloud-text">Read Aloud</span>
                        </a>
                    @endif
                    <!-- <a href="{{ route('subscriber.legal-search.downloadPdf', $data->id) }}" class="action-link">
                        <i class="bi bi-download"></i> Download
                    </a> -->
                    <div class="action-link d-flex align-items-center">
                        <span class="me-1">Translator</span>
                        <div id="google_translate_element"></div>
                    </div>
                    @if(!$myDecision)
                        <a href="#" class="action-link" data-bs-toggle="modal" data-bs-target="#copyModal">
                            <i class="bi bi-folder"></i> Copy to My Folder
                        </a>
                    @endif
                    @if($myDecision)
                        @if($myNotes && !$sharedDecision)
                            <a href="{{ route('subscriber.myDecision.editNote', [$myNotes->noteId, $decisionFolderId]) }}"
                                class="action-link">
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
                        <a href="{{ route('subscriber.singleDecision', [$previousDecision->id, Crypt::encrypt($returnParamString)]) }}"
                            class="action-link me-2">
                            <i class="bi bi-chevron-left"></i> Previous
                        </a>
                    @else
                        <a href="#" class="action-link disabled me-2" style="pointer-events: none; opacity: 0.5;">
                            <i class="bi bi-chevron-left"></i> Previous
                        </a>
                    @endif

                    @if($nextDecision)
                        <a href="{{ route('subscriber.singleDecision', [$nextDecision->id, Crypt::encrypt($returnParamString)]) }}"
                            class="action-link">
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

<style>
    .goog-te-gadget-simple {
        background-color: transparent !important;
        border: none !important;
        padding: 0 !important;
        font-size: 13px !important;
    }

    .goog-te-gadget-simple .goog-te-menu-value {
        color: transparent !important;
        font-size: 0 !important;
    }

    .goog-te-gadget-simple .goog-te-menu-value span {
        display: none !important;
    }

    /* Force dropdown arrow (img) to show */
    .goog-te-gadget-simple .goog-te-menu-value img {
        display: inline-block !important;
    }

    .goog-te-gadget-simple {
        font-family: inherit !important;
    }

    .goog-te-gadget-icon {
        display: none !important;
    }

    /* Fix alignment */
    #google_translate_element {
        display: inline-block;
        vertical-align: middle;
    }
</style>
@push('scripts')
    <script>
        $(document).ready(function () {
            $('#bookmark-btn').click(function (e) {
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
                    success: function (res) {
                        if (res.status === 'added') {
                            $btn.html('<i class="bi bi-bookmark-fill text-danger"></i> Bookmarked');
                            $message.text('Bookmarked!').css('color', 'green').fadeIn();

                        } else {
                            $btn.html('<i class="bi bi-bookmark"></i> Bookmark');
                            $message.text('Bookmark removed!').css('color', 'red').fadeIn();
                        }

                        // Hide message after 3 sec
                        setTimeout(function () {
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

    <script type="text/javascript"
        src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

@endpush