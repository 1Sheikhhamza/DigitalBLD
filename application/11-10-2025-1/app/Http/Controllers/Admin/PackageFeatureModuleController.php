<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PackageFeature;
use App\Services\PackageFeatureModuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackageFeatureModuleController extends Controller
{
    protected $service;

    public function __construct(PackageFeatureModuleService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $modules = $this->service->index();
        return view('admin.package_feature_modules.index', compact('modules'));
    }

    public function create()
    {
        $features = PackageFeature::all();
        return view('admin.package_feature_modules.create', compact('features'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'feature_id'   => 'required|exists:package_features,id',
            'name'         => 'required|string|max:255|unique:package_feature_modules,name',
            // 'slug'         => 'nullable|string|max:255|unique:package_feature_modules,slug',
            'route_name'   => 'nullable|string|max:255',
            'description'  => 'nullable|string',
            'status'       => 'required|in:active,inactive',
        ]);

        // Generate slug if not provided
        if (!empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $this->service->create($data);

        return redirect()->route('package_feature_modules.index')
            ->with('success', 'Module created successfully.');
    }


    public function edit($id)
    {
        $module = $this->service->find($id);
        $features = PackageFeature::all();
        return view('admin.package_feature_modules.edit', compact('module', 'features'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            // 'slug' => 'nullable|string|max:100|unique:package_feature_modules,slug,' . $id,
            'route_name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        if (!empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $this->service->update($id, $data);

        return redirect()->route('package_feature_modules.index')->with('success', 'Module updated successfully.');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return redirect()->route('package_feature_modules.index')->with('success', 'Module deleted successfully.');
    }
}
