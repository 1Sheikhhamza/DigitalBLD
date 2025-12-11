<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BannerRequest;
use App\Services\BannerService;
use Intervention\Image\Facades\Image;
use App\Helper\ImageUploadHelper;

class BannerController extends Controller
{
    protected BannerService $bannerService;

    public function __construct(BannerService $bannerService)
    {
        $this->bannerService = $bannerService;
    }

    public function index()
    {
        $banners = $this->bannerService->index();
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }



    public function store(BannerRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $validated['image'] = ImageUploadHelper::upload(
                $request->file('image'),
                'uploads/banners',
                'banner',
                1920,
                995,
            );
        }

        $this->bannerService->create($validated);

        return redirect()->route('banners.index')->with('success', 'Banner created successfully.');
    }


    public function show($id)
    {
        $banner = $this->bannerService->find($id);
        return view('admin.banners.show', compact('banner'));
    }

    public function edit($id)
    {
        $banner = $this->bannerService->find($id);
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(BannerRequest $request, $id)
    {
        $banner = $this->bannerService->find($id);
        $validated = $request->validated();

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $validated['image'] = ImageUploadHelper::upload(
                $request->file('image'),
                'uploads/banners',
                'banner',
                1920,
                995,
                $banner->image
            );
        }

        $banner->update($validated);

        return redirect()->route('banners.index')->with('success', 'Banner updated successfully.');
    }


    public function destroy($id)
    {
        $this->bannerService->delete($id);
        return redirect()->route('banners.index')->with('success', 'Banner soft deleted.');
    }

    public function restore($id)
    {
        $this->bannerService->restore($id);
        return redirect()->route('banners.index')->with('success', 'Banner restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->bannerService->forceDelete($id);
        return redirect()->route('banners.index')->with('success', 'Banner permanently deleted.');
    }
}
