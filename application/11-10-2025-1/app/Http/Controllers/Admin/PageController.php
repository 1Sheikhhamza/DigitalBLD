<?php

namespace App\Http\Controllers\Admin;

use App\Helper\ImageUploadHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\PageRequest;
use App\Models\Page;
use App\Services\PageService;
use Illuminate\Http\Request;

class PageController extends Controller
{
    protected PageService $pageService;

    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    public function index()
    {
        $pages = $this->pageService->index();
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        $allPages = $this->pageService->getAvailableParentPages();
        $getModuleExceptPage = $this->pageService->getPageModule();
        return view('admin.pages.create', compact('allPages','getModuleExceptPage'));
    }

    public function updateSequence(Request $request)
    {
        $request->validate([
            'id'       => 'required|integer|exists:pages,id',
            'sequence' => 'required|integer|min:1',
        ]);

        $pageId = $request->input('id');
        $newSequence = $request->input('sequence');

        $result = $this->pageService->updateSequence($pageId, $newSequence);

        if ($result) {
            return response()->json(['message' => 'Sequence updated successfully']);
        }

        return response()->json(['message' => 'Failed to update sequence'], 500);
    }



    public function store(PageRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('icon') && $request->file('icon')->isValid()) {
            $validated['icon'] = ImageUploadHelper::upload(
                $request->file('icon'),
                'uploads/pages/icon',
                'pages',
                100,
                100,
            );
        }

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $validated['image'] = ImageUploadHelper::upload(
                $request->file('image'),
                'uploads/pages/image',
                'pages',
                720,
                330,
            );
        }

        $validated['slug'] = Page::generateUniqueSlug($validated['title']);
        $this->pageService->create($validated);
        return redirect()->route('pages.index')->with('success', 'Page created successfully.');
    }

    public function show($id)
    {
        $page = $this->pageService->find($id);
        return view('admin.pages.show', compact('page'));
    }

    public function edit($id)
    {
        $page = $this->pageService->find($id);
        $allPages = $this->pageService->getAvailableParentPages($id);
        $getModuleExceptPage = $this->pageService->getPageModule();
        return view('admin.pages.edit', compact('page', 'allPages','getModuleExceptPage'));
    }

    public function update(PageRequest $request, $id)
    {
        $currentPage = $this->pageService->find($id);
        $validated = $request->validated();
        if ($request->hasFile('icon') && $request->file('icon')->isValid()) {
            $validated['icon'] = ImageUploadHelper::upload(
                $request->file('icon'),
                'uploads/pages/icon',
                'pages',
                100,
                100,
                $currentPage->icon
            );
        }
        
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $validated['image'] = ImageUploadHelper::upload(
                $request->file('image'),
                'uploads/pages/image',
                'pages',
                720,
                330,
                $currentPage->image
            );
        }
        $validated['slug'] = Page::generateUniqueSlug($validated['title'], $currentPage->id);
        $this->pageService->update($id, $validated);
        return redirect()->route('pages.index')->with('success', 'Page updated successfully.');
    }

    public function destroy($id)
    {
        $this->pageService->delete($id);
        return redirect()->route('pages.index')->with('success', 'Page soft deleted.');
    }

    public function restore($id)
    {
        $this->pageService->restore($id);
        return redirect()->route('pages.index')->with('success', 'Page restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->pageService->forceDelete($id);
        return redirect()->route('pages.index')->with('success', 'Page permanently deleted.');
    }
}
