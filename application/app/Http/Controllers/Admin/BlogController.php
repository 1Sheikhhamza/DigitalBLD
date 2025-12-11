<?php

namespace App\Http\Controllers\Admin;

use App\Helper\ImageUploadHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\BlogRequest;
use App\Services\BlogService;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    protected BlogService $blogService;

    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }

    public function index()
    {
        $blogs = $this->blogService->index();
        return view('admin.blogs.index', compact('blogs'));
    }

    public function create()
    {
        return view('admin.blogs.create');
    }

    public function store(BlogRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('featured_image') && $request->file('featured_image')->isValid()) {
            $validated['featured_image'] = ImageUploadHelper::upload(
                $request->file('featured_image'),
                'uploads/blogs',
                'blog',
                720,
                330,
            );
        }

        $this->blogService->create($validated);
        return redirect()->route('blogs.index')->with('success', 'Blog created successfully.');
    }

    public function show($id)
    {
        $blog = $this->blogService->find($id);
        return view('admin.blogs.show', compact('blog'));
    }

    public function edit($id)
    {
        $blog = $this->blogService->find($id);
        return view('admin.blogs.edit', compact('blog'));
    }

    public function update(BlogRequest $request, $id)
    {
        $blog = $this->blogService->find($id);
        $validated = $request->validated();

        if ($request->hasFile('featured_image') && $request->file('featured_image')->isValid()) {
            $validated['featured_image'] = ImageUploadHelper::upload(
                $request->file('featured_image'),
                'uploads/blogs',
                'blog',
                720,
                330,
                $blog->featured_image
            );
        }

        $blog->update($validated);

        return redirect()->route('blogs.index')->with('success', 'Blog updated successfully.');
    }

    public function destroy($id)
    {
        $this->blogService->delete($id);
        return redirect()->route('blogs.index')->with('success', 'Blog soft deleted.');
    }

    public function restore($id)
    {
        $this->blogService->restore($id);
        return redirect()->route('blogs.index')->with('success', 'Blog restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->blogService->forceDelete($id);
        return redirect()->route('blogs.index')->with('success', 'Blog permanently deleted.');
    }
}
