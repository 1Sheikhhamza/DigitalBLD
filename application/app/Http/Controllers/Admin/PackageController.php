<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PackageRequest;
use App\Models\PackageFeature;
use App\Services\PackageService;

class PackageController extends Controller
{
    protected PackageService $packageService;

    public function __construct(PackageService $packageService)
    {
        $this->packageService = $packageService;
    }

    // List all packages
    public function index()
    {
        $packages = $this->packageService->index();
        return view('admin.packages.index', compact('packages'));
    }

    // Show create form
    public function create()
    {
        $features = PackageFeature::with('modules')->orderBy('name')->get();
        return view('admin.packages.create', compact('features'));
    }

    // Store new package
    public function store(PackageRequest $request)
    {
        $this->packageService->create($request->validated());
        return redirect()->route('packages.index')->with('success', 'Package created successfully.');
    }

    // Show single package
    public function show($id)
    {
        $package = $this->packageService->find($id);
        return view('admin.packages.show', compact('package'));
    }

    // Show edit form
    public function edit($id)
    {
        $package = $this->packageService->find($id);
        $features = PackageFeature::with('modules')->orderBy('name')->get();
        return view('admin.packages.edit', compact('package', 'features'));
    }

    // Update package
    public function update(PackageRequest $request, $id)
    {
        $this->packageService->update($id, $request->validated());
        return redirect()->route('packages.index')->with('success', 'Package updated successfully.');
    }

    // Soft delete package
    public function destroy($id)
    {
        $this->packageService->delete($id);
        return redirect()->route('packages.index')->with('success', 'Package soft deleted.');
    }

    // Restore soft deleted package
    public function restore($id)
    {
        $this->packageService->restore($id);
        return redirect()->route('packages.index')->with('success', 'Package restored successfully.');
    }

    // Permanently delete package
    public function forceDelete($id)
    {
        $this->packageService->forceDelete($id);
        return redirect()->route('packages.index')->with('success', 'Package permanently deleted.');
    }
}
