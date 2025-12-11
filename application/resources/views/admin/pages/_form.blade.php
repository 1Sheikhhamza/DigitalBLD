<div class="card card-success card-outline mb-4">
    <div class="card-header">
        <h3 class="card-title">Page</h3>
        <div class="card-tools"> <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse"> <i data-lte-icon="expand" class="bi bi-plus-lg"></i> <i data-lte-icon="collapse" class="bi bi-dash-lg"></i> </button> <button type="button" class="btn btn-tool" data-lte-toggle="card-remove"> <i class="bi bi-x-lg"></i> </button> </div>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <label for="parent_id">Parent Page (optional)</label>
            <select name="parent_id" class="form-select">
                <option value="">-- No Parent (Top Level Page) --</option>
                @foreach($allPages as $p)
                <option value="{{ $p->id }}" {{ old('parent_id', $page->parent_id ?? '') == $p->id ? 'selected' : '' }}>
                    {{ $p->title }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="title" class="form-label">Page Title</label>
            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $page->title ?? '') }}" required>
            @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Content</label>
            <textarea name="content" class="form-control ckeditor @error('content') is-invalid @enderror">{{ old('content', $page->content ?? '') }}</textarea>
            @error('content')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <div class="mb-3">
            <label for="menu_type">Menu Type</label>
            <select name="menu_type" id="menu_type" class="form-control" required>
                <option value="Main Menu" {{ (old('menu_type', $page->menu_type ?? '') == 'Main Menu') ? 'selected' : '' }}>Main Menu</option>
                <option value="Footer Menu" {{ (old('menu_type', $page->menu_type ?? '') == 'Footer Menu') ? 'selected' : '' }}>Footer Menu</option>
                <option value="Without Menu" {{ (old('menu_type', $page->menu_type ?? '') == 'Without Menu') ? 'selected' : '' }}>Without Menu</option>
            </select>
        </div>
        
        <div class="mb-3">
            <label for="page_structure">Page Structure</label>
            <select name="page_structure" id="page_structure" class="form-control" required>
                <option value="Text" {{ (old('page_structure', $page->page_structure ?? '') == 'Text') ? 'selected' : '' }}>Text</option>
                <option value="Page" {{ (old('page_structure', $page->page_structure ?? '') == 'Page') ? 'selected' : '' }}>Page</option>
                <option value="URL" {{ (old('page_structure', $page->page_structure ?? '') == 'URL') ? 'selected' : '' }}>URL</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="connected_page">Pages</label>
            <select name="connected_page" class="form-select" id="connected_page">
                <option value="">-- No Parent (Top Level Page) --</option>
                @foreach($getModuleExceptPage as $connectPage)
                <option value="{{ $connectPage->slug }}" {{ old('connected_page', $page->connected_page ?? '') }}>
                    {{ $connectPage->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="external_url">External URL (only if "URL")</label>
            <input type="url" name="external_url" id="external_url" class="form-control" value="{{ old('external_url', $page->external_url ?? '') }}">
        </div>

        <div class="mb-3">
            <label for="icon" class="form-label">Icon <small class="text-muted mt-1">(Size: 100 X 100)</small></label>
            <input type="file" name="icon" class="form-control @error('icon') is-invalid @enderror">
            @error('icon')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if (!empty($page->icon))
            <div class="mt-2">
                <img src="{{ asset('uploads/pages/icon/' . $page->icon) }}" alt="Website Banner" style="max-height: 100px;">
            </div>
            @endif
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Image <small class="text-muted mt-1">(Size: 720 X 330)</small></label>
            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">

            <p class="text-muted mt-1">Size: 720 X 330</p>
            @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if (!empty($page->image))
            <div class="mt-2">
                <img src="{{ asset('uploads/pages/image/' . $page->image) }}" alt="Website Banner" style="max-height: 100px;">
            </div>
            @endif
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="homepage_display" id="homepage_display"
                value="1" {{ old('homepage_display', $page->homepage_display ?? 0) ? 'checked' : '' }}>
            <label class="form-check-label" for="homepage_display">
                Display on Homepage
            </label>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="why_choose" id="why_choose"
                value="1" {{ old('why_choose', $page->why_choose ?? 0) ? 'checked' : '' }}>
            <label class="form-check-label" for="why_choose">
                Include in 'Why Choose Us'
            </label>
        </div>

        <div class="mb-3">
            <label for="meta_title" class="form-label">Meta Title</label>
            <input type="text" name="meta_title" class="form-control @error('meta_title') is-invalid @enderror" step="0.01" value="{{ old('meta_title', $page->meta_title ?? '') }}">
            @error('meta_title')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="meta_description" class="form-label">Meta Description</label>
            <textarea name="meta_description" class="form-control @error('meta_description') is-invalid @enderror">{{ old('meta_description', $page->meta_description ?? '') }}</textarea>
            @error('meta_description')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>

    </div>

    <script>
        document.getElementById('page_structure').addEventListener('change', function() {
            const structure = this.value;
            document.getElementById('connected_page').closest('.mb-3').style.display = (structure === 'Page') ? 'block' : 'none';
            document.getElementById('external_url').closest('.mb-3').style.display = (structure === 'URL') ? 'block' : 'none';
        });
        document.getElementById('page_structure').dispatchEvent(new Event('change'));
    </script>