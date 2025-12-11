<?php

namespace App\Repositories\Eloquent;

use App\Models\Photo;
use App\Repositories\Contracts\PhotoRepositoryInterface;
use Illuminate\Support\Str;

class PhotoRepository implements PhotoRepositoryInterface
{
    public function index()
    {
        return Photo::orderByDesc('id')->paginate(50);
    }

    public function create(array $data)
    {
        return Photo::create([
            'title' => $data['title'] ?? null,
            'image' => $data['image'] ?? null,
            'status' => $data['status'] ?? 0,
        ]);
    }


    public function find($id)
    {
        return Photo::find($id);
    }

    public function update($id, array $data)
    {
        $photo = Photo::findOrFail($id);

        $photo->update([
            'title' => $data['title'] ?? $photo->title,
            'image' => $data['image'] ?? $photo->image,
            'status' => $data['status'] ?? $photo->status,
        ]);

        return true;
    }



    public function delete($id)
    {
        $package = Photo::findOrFail($id);
        return $package->delete();
    }

    public function restore($id)
    {
        $package = Photo::onlyTrashed()->findOrFail($id);
        return $package->restore();
    }

    public function forceDelete($id)
    {
        $package = Photo::onlyTrashed()->findOrFail($id);
        return $package->forceDelete();
    }
}
