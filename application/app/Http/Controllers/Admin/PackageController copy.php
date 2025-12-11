<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PackageRequest;
use App\Models\Feature;
use App\Models\Package;
use App\Models\PackageFeature;
use App\Services\PackageService;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    protected PackageService $packageService;

    public function __construct(PackageService $packageService)
    {
        $this->packageService = $packageService;
    }

    public function index()
    {
        $packages = $this->packageService->index();
        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        $features = PackageFeature::with('modules')->orderBy('name')->get();
        return view('admin.packages.create', compact('features'));
    }

    public function store(PackageRequest $request)
    {
        $this->packageService->create($request->validated());
        return redirect()->route('packages.index')->with('success', 'Package created successfully.');
    }

    public function show($id)
    {
        $package = $this->packageService->find($id);
        return view('admin.packages.show', compact('package'));
    }

    public function edit($id)
    {
        // $package = $this->packageService->find($id);
        $package = Package::with(['featuresRelation', 'featureModulesRelation'])->findOrFail($id);
        $features = PackageFeature::with('modules')->orderBy('name')->get();
        return view('admin.packages.edit', compact('package','features'));
    }

    public function update(PackageRequest $request, $id)
    {
        $this->packageService->update($id, $request->validated());
        return redirect()->route('packages.index')->with('success', 'Package updated successfully.');
    }

    public function destroy($id)
    {
        $this->packageService->delete($id);
        return redirect()->route('packages.index')->with('success', 'Package soft deleted.');
    }

    public function restore($id)
    {
        $this->packageService->restore($id);
        return redirect()->route('packages.index')->with('success', 'Package restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->packageService->forceDelete($id);
        return redirect()->route('packages.index')->with('success', 'Package permanently deleted.');
    }
}
