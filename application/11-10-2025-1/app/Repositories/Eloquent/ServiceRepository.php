<?php

namespace App\Repositories\Eloquent;

use App\Models\Service;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use Illuminate\Support\Str;

class ServiceRepository implements ServiceRepositoryInterface
{
    public function index()
    {
        return Service::orderByDesc('id')->paginate(50);
    }

    public function create(array $data)
    {
        return Service::create([
            'name'             => $data['name'] ?? '',
            'slug'             => Str::slug($data['name'] ?? ''),
            'description'      => $data['description'] ?? null,
            'icon'             => $data['icon'] ?? null,
            'image'            => $data['image'] ?? null,
            'banner'           => $data['banner'] ?? null,
            'meta_title'       => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'meta_keywords'    => $data['meta_keywords'] ?? null,
        ]);
    }


    public function find($id)
    {
        return Service::find($id);
    }

    public function update($id, array $data)
    {
        $service = Service::findOrFail($id);

        $service->update([
            'name'             => $data['name'] ?? $service->name,
            'slug'             => Str::slug($data['name'] ?? $service->name),
            'description'      => $data['description'] ?? $service->description,
            'icon'              => $data['icon'] ?? $service->icon,
            'image'             => $data['image'] ?? $service->image,
            'banner'             => $data['banner'] ?? $service->banner,
            'meta_title'       => $data['meta_title'] ?? $service->meta_title,
            'meta_description' => $data['meta_description'] ?? $service->meta_description,
            'meta_keywords'    => $data['meta_keywords'] ?? $service->meta_keywords,
        ]);

        return true;
    }



    public function delete($id)
    {
        $package = Service::findOrFail($id);
        return $package->delete();
    }

    public function restore($id)
    {
        $package = Service::onlyTrashed()->findOrFail($id);
        return $package->restore();
    }

    public function forceDelete($id)
    {
        $package = Service::onlyTrashed()->findOrFail($id);
        return $package->forceDelete();
    }
}
