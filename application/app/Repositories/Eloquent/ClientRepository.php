<?php

namespace App\Repositories\Eloquent;

use App\Models\Client;
use App\Repositories\Contracts\ClientRepositoryInterface;
use Illuminate\Support\Str;

class ClientRepository implements ClientRepositoryInterface
{
    public function index()
    {
        return Client::orderByDesc('id')->paginate(50);
    }

    public function create(array $data)
    {
        return Client::create([
            'name'             => $data['name'] ?? '',
            'logo'             => $data['logo'] ?? null,
            'testimonial'      => $data['testimonial'] ?? null,
            'website'          => $data['website'] ?? null,
            'meta_title'       => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'meta_keywords'    => $data['meta_keywords'] ?? null,
            'status'           => $data['status'] ?? 0,
        ]);
    }


    public function find($id)
    {
        return Client::find($id);
    }

    public function update($id, array $data)
    {
        $client = Client::findOrFail($id);

        $client->update([
            'name'             => $data['name'] ?? $client->name,
            'logo'             => $data['logo'] ?? $client->logo,
            'testimonial'      => $data['testimonial'] ?? $client->testimonial,
            'website'          => $data['website'] ?? $client->website,
            'meta_title'       => $data['meta_title'] ?? $client->meta_title,
            'meta_description' => $data['meta_description'] ?? $client->meta_description,
            'meta_keywords'    => $data['meta_keywords'] ?? $client->meta_keywords,
            'status'           => $data['status'] ?? $client->status,
        ]);

        return true;
    }



    public function delete($id)
    {
        $package = Client::findOrFail($id);
        return $package->delete();
    }

    public function restore($id)
    {
        $package = Client::onlyTrashed()->findOrFail($id);
        return $package->restore();
    }

    public function forceDelete($id)
    {
        $package = Client::onlyTrashed()->findOrFail($id);
        return $package->forceDelete();
    }
}
