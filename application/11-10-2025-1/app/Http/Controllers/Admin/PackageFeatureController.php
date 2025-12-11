<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PackageFeatureService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackageFeatureController extends Controller
{
    protected $service;

    public function __construct(PackageFeatureService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $packageFeatures = $this->service->index();
        return view('admin.package_features.index', compact('packageFeatures'));
    }

    public function create()
    {
        return view('admin.package_features.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:package_features,name',
            'description' => 'nullable|string',
        ]);

        $data['slug'] = Str::slug($request->input('name'));
        $this->service->create($data);
        return redirect()->route('package_features.index')->with('success', 'Package Feature created successfully.');
    }

    public function edit($id)
    {
        $packageFeature = $this->service->find($id);
        return view('admin.package_features.edit', compact('packageFeature'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:package_features,name,' . $id,
            'description' => 'nullable|string',
        ]);

        // $data['slug'] = Str::slug($request->input('name'));
        $this->service->update($id, $data);
        return redirect()->route('package_features.index')->with('success', 'Package Feature updated successfully.');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return redirect()->route('package_features.index')->with('success', 'Package Feature deleted successfully.');
    }
}
