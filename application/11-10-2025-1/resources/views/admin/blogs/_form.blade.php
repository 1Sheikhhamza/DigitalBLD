<div class="card card-success card-outline mb-4">
    <div class="card-header">
        <h3 class="card-title">Blog</h3>
    </div>
    <div class="card-body">

        {{-- Title --}}
        <div class="mb-3">
            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $blog->title ?? '') }}" required>
            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Slug --}}
        <!-- <div class="mb-3">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $blog->slug ?? '') }}">
            @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div> -->

        {{-- Author --}}
        <div class="mb-3">
            <label for="author" class="form-label">Author</label>
            <input type="text" name="author" class="form-control @error('author') is-invalid @enderror" value="{{ old('author', $blog->author ?? '') }}">
            @error('author') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Content --}}
        <div class="mb-3">
            <label for="content" class="form-label">Content</label>
            <textarea name="content" class="form-control ckeditor @error('content') is-invalid @enderror">{{ old('content', $blog->content ?? '') }}</textarea>
            @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Featured Image --}}
        <div class="mb-3">
            <label for="featured_image" class="form-label">Featured Image</label>
            <input type="file" name="featured_image" class="form-control @error('featured_image') is-invalid @enderror">
            @if(isset($blog->featured_image))
                <small class="text-muted">Current: {{ $blog->featured_image }}</small>
            @endif
            @error('featured_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Status --}}
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" class="form-select @error('status') is-invalid @enderror">
                <option value="draft" {{ old('status', $blog->status ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ old('status', $blog->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
            </select>
            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Meta Title --}}
        <div class="mb-3">
            <label for="meta_title" class="form-label">Meta Title</label>
            <input type="text" name="meta_title" class="form-control @error('meta_title') is-invalid @enderror" value="{{ old('meta_title', $blog->meta_title ?? '') }}">
            @error('meta_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Meta Description --}}
        <div class="mb-3">
            <label for="meta_description" class="form-label">Meta Description</label>
            <textarea name="meta_description" class="form-control @error('meta_description') is-invalid @enderror">{{ old('meta_description', $blog->meta_description ?? '') }}</textarea>
            @error('meta_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Meta Keywords --}}
        <div class="mb-3">
            <label for="meta_keywords" class="form-label">Meta Keywords</label>
            <input type="text" name="meta_keywords" class="form-control @error('meta_keywords') is-invalid @enderror" value="{{ old('meta_keywords', $blog->meta_keywords ?? '') }}">
            @error('meta_keywords') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn btn-primary">{{ $buttonText ?? 'Save Blog' }}</button>
    </div>
</div>
