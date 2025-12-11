<?php

namespace App\Http\Controllers\Admin;

use App\Helper\ImageUploadHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\PhotoRequest;
use App\Services\PhotoService;
use App\Services\CommonService;

class PhotoController extends Controller
{
    protected PhotoService $photoService;
    protected CommonService $commonService;

    public function __construct(PhotoService $photoService, CommonService $commonService)
    {
        $this->photoService = $photoService;
        $this->commonService = $commonService;
    }

    public function index()
    {
        $photos = $this->photoService->index();
        return view('admin.photos.index', compact('photos'));
    }

    public function create()
    {
        $projects = $this->commonService->getPackage();
        return view('admin.photos.create',compact('projects'));
    }

    public function store(PhotoRequest $request)
    {
        $validated = $request->validated();
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $validated['image'] = ImageUploadHelper::upload(
                $request->file('image'),
                'uploads/photos',
                'photos',
                720,
                null,
            );
        }

        $this->photoService->create($validated);

        return redirect()->route('photos.index')->with('success', 'Photo created successfully.');
    }

    public function show($id)
    {
        $photo = $this->photoService->find($id);
        return view('admin.photos.show', compact('photo'));
    }

    public function edit($id)
    {
        $photo = $this->photoService->find($id);
        $projects = $this->commonService->getPackage();
        return view('admin.photos.edit', compact('photo','projects'));
    }

    public function update(PhotoRequest $request, $id)
    {
        $photo = $this->photoService->find($id);
        $validated = $request->validated();
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $validated['image'] = ImageUploadHelper::upload(
                $request->file('image'),
                'uploads/photos',
                'photos',
                720,
                null,
                $photo->image
            );
        }

        $this->photoService->update($id, $validated);
        return redirect()->route('photos.index')->with('success', 'Photo updated successfully.');
    }

    public function destroy($id)
    {
        $this->photoService->delete($id);
        return redirect()->route('photos.index')->with('success', 'Photo soft deleted.');
    }

    public function restore($id)
    {
        $this->photoService->restore($id);
        return redirect()->route('photos.index')->with('success', 'Photo restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->photoService->forceDelete($id);
        return redirect()->route('photos.index')->with('success', 'Photo permanently deleted.');
    }
}
