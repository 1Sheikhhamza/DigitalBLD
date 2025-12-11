<?php

namespace App\Repositories\Eloquent;

use App\Models\Banner;
use App\Repositories\Contracts\BannerRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BannerRepository implements BannerRepositoryInterface
{
    ////////////////// Admin ////////
    public function index()
    {
        return Banner::orderBy('id', 'desc')->paginate(50);
    }

    public function create(array $data)
    {
        return Banner::create([
            'title'          => $data['title'] ?? '',
            'description'   => $data['description'] ?? '',
            'link'          => $data['link'] ?? '',
            'image'         => $data['image'] ?? null, // assuming image filename is already saved
        ]);
    }

    public function find($id)
    {
        return Banner::find($id);
    }

    public function update($id, array $data)
    {
        $banner = Banner::findOrFail($id);

        $updateData = [
            'title'          => $data['title'] ?? $banner->title,
            'description'   => $data['description'] ?? $banner->description,
            'link'          => $data['link'] ?? $banner->link,
            'updated_at'    => now(),
            'image'         => $data['image'] ?? $banner->image,
        ];

        $banner->update($updateData);

        return true;
    }



    public function delete($id)
    {
        $patientData = Banner::find($id);

        if (!$patientData) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        return $patientData->delete(); // Soft delete the record
    }



    public function restore($id)
    {
        $patientData = Banner::onlyTrashed()->find($id);

        if (!$patientData) {
            return response()->json(['message' => 'No deleted record found'], 404);
        }

        $patientData->restore(); // Restore the record

        return response()->json(['message' => 'Banner member restored successfully', 'data' => $patientData]);
    }

    public function forceDelete($id)
    {
        $patientData = Banner::onlyTrashed()->find($id);

        if (!$patientData) {
            return response()->json(['message' => 'No deleted record found'], 404);
        }

        $patientData->forceDelete(); // Permanently delete the record

        return response()->json(['message' => 'Banner member permanently deleted']);
    }
}
