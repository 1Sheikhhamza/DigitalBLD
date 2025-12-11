<?php

namespace App\Http\Controllers\Admin;

use App\Helper\ImageUploadHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Services\ServiceService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    protected ServiceService $serviceService;

    public function __construct(ServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }

    public function index()
    {
        $services = $this->serviceService->index();
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(ServiceRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('icon') && $request->file('icon')->isValid()) {
            $validated['icon'] = ImageUploadHelper::upload(
                $request->file('icon'),
                'uploads/services/icon',
                'services',
                100,
                100,
            );
        }

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $validated['image'] = ImageUploadHelper::upload(
                $request->file('image'),
                'uploads/services/image',
                'services',
                300,
                500,
            );
        }

        if ($request->hasFile('banner') && $request->file('banner')->isValid()) {
            $validated['banner'] = ImageUploadHelper::upload(
                $request->file('banner'),
                'uploads/services/banner',
                'services',
                720,
                330,
            );
        }

        $this->serviceService->create($validated);
        return redirect()->route('services.index')->with('success', 'Service created successfully.');
    }

    public function show($id)
    {
        $service = $this->serviceService->find($id);
        return view('admin.services.show', compact('service'));
    }

    public function edit($id)
    {
        $service = $this->serviceService->find($id);
        return view('admin.services.edit', compact('service'));
    }

    public function update(ServiceRequest $request, $id)
    {
        $currentService = $this->serviceService->find($id);
        $validated = $request->validated();
        if ($request->hasFile('icon') && $request->file('icon')->isValid()) {
            $validated['icon'] = ImageUploadHelper::upload(
                $request->file('icon'),
                'uploads/services/icon',
                'services',
                100,
                100,
                $currentService->icon
            );
        }

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $validated['image'] = ImageUploadHelper::upload(
                $request->file('image'),
                'uploads/services/image',
                'services',
                300,
                500,
                $currentService->image
            );
        }

        if ($request->hasFile('banner') && $request->file('banner')->isValid()) {
            $validated['banner'] = ImageUploadHelper::upload(
                $request->file('banner'),
                'uploads/services/banner',
                'services',
                720,
                330,
                $currentService->banner
            );
        }
        $this->serviceService->update($id, $validated);
        return redirect()->route('services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy($id)
    {
        $this->serviceService->delete($id);
        return redirect()->route('services.index')->with('success', 'Service soft deleted.');
    }

    public function restore($id)
    {
        $this->serviceService->restore($id);
        return redirect()->route('services.index')->with('success', 'Service restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->serviceService->forceDelete($id);
        return redirect()->route('services.index')->with('success', 'Service permanently deleted.');
    }
}
