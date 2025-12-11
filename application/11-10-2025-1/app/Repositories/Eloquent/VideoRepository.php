<?php

namespace App\Repositories\Eloquent;

use App\Models\Video;
use App\Repositories\Contracts\VideoRepositoryInterface;
use Illuminate\Support\Str;

class VideoRepository implements VideoRepositoryInterface
{
    public function index()
    {
        return Video::orderByDesc('id')->paginate(50);
    }

    public function create(array $data)
    {
        return Video::create([
            'title' => $data['title'] ?? null,
            'video_url' => $data['video_url'] ?? null,
            'project_id' => $data['project_id'] ?? null,
            'status' => $data['status'] ?? 0,
        ]);
    }

    public function find($id)
    {
        return Video::find($id);
    }

    public function update($id, array $data)
    {
        $video = Video::findOrFail($id);

        $video->update([
            'title' => $data['title'] ?? $video->title,
            'video_url' => $data['video_url'] ?? $video->video_url,
            'project_id' => $data['project_id'] ?? $video->project_id,
            'status' => $data['status'] ?? $video->status,
        ]);

        return true;
    }



    public function delete($id)
    {
        $package = Video::findOrFail($id);
        return $package->delete();
    }

    public function restore($id)
    {
        $package = Video::onlyTrashed()->findOrFail($id);
        return $package->restore();
    }

    public function forceDelete($id)
    {
        $package = Video::onlyTrashed()->findOrFail($id);
        return $package->forceDelete();
    }
}
