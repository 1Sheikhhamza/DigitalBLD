<?php

namespace App\Services;

use App\Models\PackageFeature;
use App\Repositories\Eloquent\PackageRepository;
use Illuminate\Support\Facades\Storage;

class PackageService
{
    protected PackageRepository $repository;

    public function __construct(PackageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        return $this->repository->index();
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        if (isset($data['icon'])) {
            $data['icon'] = $data['icon']->store('uploads/icons', 'public');
        }

        // Remove features/modules from $data before creating package
        $features = $data['features'] ?? [];
        $modules = $data['modules'] ?? [];
        unset($data['features'], $data['modules']);

        $package = $this->repository->create($data);

        // Sync pivot tables
        $package->features()->sync($features);

        return $package;
    }


    public function update($id, array $data)
    {
        $package = $this->repository->find($id);

        // Handle icon upload
        if (isset($data['icon'])) {
            if ($package->icon) {
                Storage::disk('public')->delete($package->icon);
            }
            $data['icon'] = $data['icon']->store('uploads/icons', 'public');
        }

        // Remove features/modules from $data before updating package
        $features = $data['features'] ?? [];
        unset($data['features'], $data['modules']); // modules are synced via features

        // Update package
        $package = $this->repository->update($id, $data);

        // Sync features
        $package->features()->sync($features);

        // Optional: sync modules for each feature if needed
        if (!empty($data['modules'])) {
            foreach ($features as $featureId) {
                $feature = PackageFeature::find($featureId);
                if ($feature && isset($data['modules'][$featureId])) {
                    $feature->modules()->sync($data['modules'][$featureId]);
                }
            }
        }

        return $package;
    }


    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function restore($id)
    {
        return $this->repository->restore($id);
    }

    public function forceDelete($id)
    {
        $package = $this->repository->find($id);
        if ($package->icon) {
            Storage::disk('public')->delete($package->icon);
        }
        return $this->repository->forceDelete($id);
    }
}
