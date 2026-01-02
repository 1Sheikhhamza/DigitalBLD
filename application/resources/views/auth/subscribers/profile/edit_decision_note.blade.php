@extends('auth.subscribers.layouts.app')
@section('title', 'Edit My Judgment Note')

@section('content')
  <style>
    /* Custom card with gradient and smooth shadow */
    .custom-card {
      background: linear-gradient(135deg, #c5d6ea 0%, #a1c7eb 100%);
      color: #fff;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(53, 122, 189, 0.4);
    }

    .custom-card label {
      font-weight: 600;
      font-size: 1.1rem;
      color: #003092;
    }

    .custom-textarea {
      border-radius: 8px;
      border: none;
      padding: 1rem;
      font-size: 1.05rem;
      min-height: 300px;
      resize: vertical;
      box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .custom-textarea:focus {
      outline: none;
      box-shadow: 0 0 8px 3px rgba(255, 255, 255, 0.7);
      background-color: rgba(255, 255, 255, 0.1);
      color: #fff;
    }

    .btn-cancel {
      background: transparent;
      border: 2px solid #003092;
      color: #003092;
      transition: all 0.3s ease;
    }

    .btn-cancel:hover {
      background: #003092;
      color: #FFF;
    }

    .btn-save {
      background: #ffce00;
      border: none;
      color: #222;
      font-weight: 600;
      box-shadow: 0 5px 15px rgba(255, 206, 0, 0.6);
      transition: all 0.3s ease;
    }

    .btn-save:hover {
      background: #e6b800;
      box-shadow: 0 8px 20px rgba(230, 184, 0, 0.8);
      color: #111;
    }

    /* Increase Icon Size */
    .cke_button_icon {
      transform: scale(1.1);
    }

    .cke_button {
      padding: 4px !important;
    }

    /* Ensure Editor is Full Width & Frameless */
    .cke_chrome {
      border: none !important;
      width: 100% !important;
      box-shadow: none !important;
    }

    /* Fix container overflow */
    .container-fluid {
      overflow-x: hidden;
    }
  </style>
  <div class="container-fluid p-0 m-0">
    <div class="row m-0">
      <div class="col-12 p-0">
        <div class="p-0">
          <form action="{{ route('subscriber.myDecision.updateNote', $myNotes->id) }}" method="POST">
            @csrf
            <div class="mb-2">
              <textarea name="notes" id="editor" class="form-control custom-textarea"
                placeholder="Write your notes here...">{!! old('notes', $myNotes->notes) !!}</textarea>
            </div>

            <input type="hidden" name="decision_id" value="{{ $myNotes->decision_id }}">
            <input type="hidden" name="folderId" value="{{ $folderId }}">

            <div class="d-flex justify-content-between">
              <a href="{{ route('subscriber.myDecision', Crypt::encrypt($myNotes->id)) }}"
                class="btn btn-cancel px-3 py-1">
                Cancel
              </a>
              <button type="submit" class="btn btn-save px-3 py-1">
                <i class="bi bi-pencil-square me-2"></i> Save My Note
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
  <script>
    if (typeof CKEDITOR !== 'undefined') {
      CKEDITOR.replace('editor', {
        extraPlugins: 'font',
        height: 800,
        toolbar: [
          // Line 1: Common editing tools
          { name: 'document', items: ['Source', '-', 'Print'] },
          { name: 'clipboard', items: ['Undo', 'Redo', '-', 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord'] },
          { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll'] },
          { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'] },
          { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
          // Line 2: Inserts, Links, Styles (Now merged into Line 1 logic by removing break)
          { name: 'links', items: ['Link', 'Unlink'] },
          { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar'] },
          { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
          { name: 'colors', items: ['TextColor', 'BGColor'] },
          { name: 'tools', items: ['Maximize', 'ShowBlocks'] }
        ]
      });
    }
  </script>
@endpush