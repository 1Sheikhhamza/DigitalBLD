<?php

namespace App\Repositories\Eloquent;

use App\Models\PackageFeature;
use App\Repositories\Contracts\PackageFeatureRepositoryInterface;

class PackageFeatureRepository implements PackageFeatureRepositoryInterface
{
    public function index()
    {
        return PackageFeature::with(['packages', 'modules'])->paginate(50);
    }

    public function find($id)
    {
        return PackageFeature::with(['packages', 'modules'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return PackageFeature::create($data);
    }

    public function update($id, array $data)
    {
        $packageFeature = $this->find($id);
        $packageFeature->update($data);
        return $packageFeature;
    }

    public function delete($id)
    {
        return PackageFeature::destroy($id);
    }
}
