<?php

namespace App\Repositories\Eloquent;

use App\Models\FeatureModule;
use App\Models\Package;
use App\Repositories\Contracts\PackageRepositoryInterface;
use Illuminate\Support\Str;

class PackageRepository implements PackageRepositoryInterface
{
    public function index()
    {
        return Package::orderByDesc('id')->paginate(50);
    }

    /* public function create(array $data)
    {
        return Package::create([
            'name'            => $data['name'] ?? '',
            'slug'            => Str::slug($data['name'] ?? ''),
            'description'     => $data['description'] ?? null,
            'price'           => $data['price'] ?? 0,
            'duration_type'   => $data['duration_type'] ?? 'monthly',
            'duration_in_days' => $data['duration_in_days'] ?? 30,
            'status'          => $data['status'] ?? 1,
            'is_featured'     => $data['is_featured'] ?? 0,
            'features'        => $data['features'] ?? null,
            'button_text'     => $data['button_text'] ?? 'Sign up Now',
            'currency'        => $data['currency'] ?? '৳',
            'highlight_badge' => $data['highlight_badge'] ?? null,
            'icon'            => $data['icon'] ?? null,
            'order'           => $data['order'] ?? 0,
        ]);
    } */

    public function create(array $data)
    {
        $package = Package::create([
            'name'            => $data['name'] ?? '',
            'slug'            => Str::slug($data['name'] ?? ''),
            'description'     => $data['description'] ?? null,
            'price'           => $data['price'] ?? 0,
            'duration_type'   => $data['duration_type'] ?? 'monthly',
            'duration_in_days' => $data['duration_in_days'] ?? 30,
            'status'          => $data['status'] ?? 1,
            'is_featured'     => $data['is_featured'] ?? 0,
            'button_text'     => $data['button_text'] ?? 'Sign up Now',
            'currency'        => $data['currency'] ?? '৳',
            'highlight_badge' => $data['highlight_badge'] ?? null,
            'icon'            => $data['icon'] ?? null,
            'order'           => $data['order'] ?? 0,
        ]);

        // Attach features
        if (!empty($data['features'])) {
            $package->features()->sync($data['features']);
        }

        // Attach modules according to selected features
        if (!empty($data['modules'])) {
            foreach ($data['modules'] as $featureId => $moduleIds) {
                foreach ($moduleIds as $moduleId) {
                    FeatureModule::firstOrCreate([
                        'feature_id' => $featureId,
                        'module_id' => $moduleId,
                    ]);
                }
            }
        }

        return $package;
    }

    public function find($id)
    {
        return Package::find($id);
    }

    public function update($id, array $data)
    {
        $package = Package::findOrFail($id);

        $package->update([
            'name'            => $data['name'] ?? $package->name,
            'slug'            => isset($data['name']) ? Str::slug($data['name']) : $package->slug,
            'description'     => $data['description'] ?? $package->description,
            'price'           => $data['price'] ?? $package->price,
            'duration_type'   => $data['duration_type'] ?? $package->duration_type,
            'duration_in_days' => $data['duration_in_days'] ?? $package->duration_in_days,
            'status'          => $data['status'] ?? $package->status,
            'is_featured'     => $data['is_featured'] ?? $package->is_featured,
            'features'        => $data['features'] ?? $package->features,
            'button_text'     => $data['button_text'] ?? $package->button_text,
            'currency'        => $data['currency'] ?? $package->currency,
            'highlight_badge' => $data['highlight_badge'] ?? $package->highlight_badge,
            'icon'            => $data['icon'] ?? $package->icon,
            'order'           => $data['order'] ?? $package->order,
        ]);

        return true;
    }

    public function delete($id)
    {
        $package = Package::findOrFail($id);
        return $package->delete();
    }

    public function restore($id)
    {
        $package = Package::onlyTrashed()->findOrFail($id);
        return $package->restore();
    }

    public function forceDelete($id)
    {
        $package = Package::onlyTrashed()->findOrFail($id);
        return $package->forceDelete();
    }
}
