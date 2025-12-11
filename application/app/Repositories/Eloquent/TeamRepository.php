<?php

namespace App\Repositories\Eloquent;

use App\Models\Team;
use App\Repositories\Contracts\TeamRepositoryInterface;
use Illuminate\Support\Str;

class TeamRepository implements TeamRepositoryInterface
{
    public function index()
    {
        return Team::orderByDesc('id')->paginate(50);
    }

    public function create(array $data)
    {
        return Team::create([
            'name' => $data['name'] ?? '',
            'designation' => $data['designation'] ?? '',
            'photo' => $data['photo'] ?? null,
            'bio' => $data['bio'] ?? null,
            'facebook' => $data['facebook'] ?? null,
            'linkedin' => $data['linkedin'] ?? null,
            'email' => $data['email'] ?? null,
            'status' => $data['status'] ?? 0,
        ]);
    }


    public function find($id)
    {
        return Team::find($id);
    }

    public function update($id, array $data)
    {
        $team = Team::findOrFail($id);

        $team->update([
            'name' => $data['name'] ?? $team->name,
            'designation' => $data['designation'] ?? $team->designation,
            'photo' => $data['photo'] ?? $team->photo,
            'bio' => $data['bio'] ?? $team->bio,
            'facebook' => $data['facebook'] ?? $team->facebook,
            'linkedin' => $data['linkedin'] ?? $team->linkedin,
            'email' => $data['email'] ?? $team->email,
            'status' => $data['status'] ?? $team->status,
        ]);

        return true;
    }



    public function delete($id)
    {
        $package = Team::findOrFail($id);
        return $package->delete();
    }

    public function restore($id)
    {
        $package = Team::onlyTrashed()->findOrFail($id);
        return $package->restore();
    }

    public function forceDelete($id)
    {
        $package = Team::onlyTrashed()->findOrFail($id);
        return $package->forceDelete();
    }
}
