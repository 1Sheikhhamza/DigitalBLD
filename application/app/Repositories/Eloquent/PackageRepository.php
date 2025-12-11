<?php

namespace App\Repositories\Eloquent;

use App\Models\FeatureModule;
use App\Models\Package;
use App\Repositories\Contracts\PackageRepositoryInterface;
use Illuminate\Support\Str;

class PackageRepository implements PackageRepositoryInterface
{
    protected Package $model;

    public function __construct(Package $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        return $this->model->with(['features', 'features.modules'])->orderBy('id', 'desc')->paginate(50);
    }

    public function find($id)
    {
        return $this->model->with(['features', 'features.modules'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $package = $this->model->findOrFail($id);
        $package->update($data);
        return $package;
    }

    public function delete($id)
    {
        $package = $this->model->findOrFail($id);
        return $package->delete();
    }

    public function restore($id)
    {
        $package = $this->model->withTrashed()->findOrFail($id);
        return $package->restore();
    }

    public function forceDelete($id)
    {
        $package = $this->model->withTrashed()->findOrFail($id);
        return $package->forceDelete();
    }
}
