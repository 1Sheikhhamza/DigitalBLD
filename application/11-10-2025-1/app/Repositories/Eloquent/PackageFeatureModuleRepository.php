<?php

namespace App\Repositories\Eloquent;

use App\Models\PackageFeatureModule;
use App\Repositories\Contracts\PackageFeatureModuleRepositoryInterface;
use Illuminate\Support\Str;

class PackageFeatureModuleRepository implements PackageFeatureModuleRepositoryInterface
{
    public function index()
    {
        return PackageFeatureModule::latest()->paginate(50);
    }

    public function find($id)
    {
        return PackageFeatureModule::findOrFail($id);
    }

    public function create(array $data)
    {
        return PackageFeatureModule::create($data);
    }

    public function update($id, array $data)
    {
        $module = $this->find($id);
        $module->update($data);
        return $module;
    }

    public function delete($id)
    {
        $module = $this->find($id);
        return $module->delete();
    }
}
