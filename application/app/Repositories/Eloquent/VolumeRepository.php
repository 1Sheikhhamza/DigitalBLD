<?php

namespace App\Repositories\Eloquent;

use App\Models\Volume;
use App\Repositories\Contracts\VolumeRepositoryInterface;

class VolumeRepository implements VolumeRepositoryInterface
{
    public function index()
    {
        return Volume::orderByDesc('id')->paginate(50);
    }

    /* public function create(array $data)
    {
        return Volume::create([
            'number'        => $data['number'] ?? '',
            'year'        => $data['year'] ?? '',
            'image'        => $data['image'] ?? '',
            'index_file'        => $data['index_file'] ?? '',
            'document_path'        => $data['document_path'] ?? '',
            'document_name'        => $data['document_name'] ?? '',
            'document_size'        => $data['document_size'] ?? '',
            'document_mimetype'    => $data['document_mimetype'] ?? '',
            'status'                => 1,
        ]);
    } */

    /* public function create(array $data)
    {
        return Volume::updateOrCreate(
            ['number' => $data['number']],
            [
                'year'             => $data['year'] ?? '',
                'image'            => $data['image'] ?? '',
                'index_file'       => $data['index_file'] ?? '',
                'document_path'    => $data['document_path'] ?? '',
                'document_name'    => $data['document_name'] ?? '',
                'document_size'    => $data['document_size'] ?? '',
                'document_mimetype' => $data['document_mimetype'] ?? '',
                'status'           => 1,
            ]
        );
    } */


    public function create(array $data)
    {
        $volume = Volume::where('number', $data['number'])->first();

        if ($volume) {
            // Replace image if new uploaded
            if (!empty($data['image']) && $volume->image && file_exists(public_path('uploads/volume/'.$volume->image))) {
                unlink(public_path('uploads/volume/'.$volume->image));
            }

            // Replace index_file if new uploaded
            if (!empty($data['index_file']) && $volume->index_file && file_exists(public_path('uploads/volume/pdfs/' . $volume->index_file))) {
                unlink(public_path('uploads/volume/pdfs/' . $volume->index_file));
            }

            // Always update document info with latest
            $volume->update([
                'year'               => $data['year'] ?? $volume->year,
                'image'              => $data['image'] ?? $volume->image,
                'index_file'         => $data['index_file'] ?? $volume->index_file,
                'document_path'      => $data['document_path'] ?? $volume->document_path,
                'document_name'      => $data['document_name'] ?? $volume->document_name,
                'document_size'      => $data['document_size'] ?? $volume->document_size,
                'document_mimetype'  => $data['document_mimetype'] ?? $volume->document_mimetype,
                'status'             => 1,
            ]);

            return $volume;
        }

        // If new, create everything
        return Volume::create([
            'number'             => $data['number'],
            'year'               => $data['year'] ?? '',
            'image'              => $data['image'] ?? '',
            'index_file'         => $data['index_file'] ?? '',
            'document_path'      => $data['document_path'] ?? '',
            'document_name'      => $data['document_name'] ?? '',
            'document_size'      => $data['document_size'] ?? '',
            'document_mimetype'  => $data['document_mimetype'] ?? '',
            'status'             => 1,
        ]);
    }


    public function find($id)
    {
        return Volume::find($id);
    }

    public function update($id, array $data)
    {
        $package = Volume::findOrFail($id);

        $package->update([
            'number'        => $data['number'] ?? $package->number,
            'year'        => $data['year'] ?? $package->year,
            'image'        => $data['image'] ?? $package->image,
            'index_file'        => $data['index_file'] ?? $package->index_file,
            'status'                => 1,
        ]);

        return true;
    }

    public function delete($id)
    {
        $package = Volume::findOrFail($id);
        return $package->delete();
    }

    public function restore($id)
    {
        $package = Volume::onlyTrashed()->findOrFail($id);
        return $package->restore();
    }

    public function forceDelete($id)
    {
        $package = Volume::onlyTrashed()->findOrFail($id);
        return $package->forceDelete();
    }
}
