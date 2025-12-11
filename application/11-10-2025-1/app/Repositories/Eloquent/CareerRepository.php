<?php

namespace App\Repositories\Eloquent;

use App\Models\Career;
use App\Repositories\Contracts\CareerRepositoryInterface;
use Illuminate\Support\Str;

class CareerRepository implements CareerRepositoryInterface
{
    public function index()
    {
        return Career::orderByDesc('id')->paginate(50);
    }

    public function create(array $data)
    {
        return Career::create([
            'title'            => $data['title'] ?? '',
            'slug'             => Str::slug($data['title'] ?? ''),
            'department'       => $data['department'] ?? null,
            'job_type'         => $data['job_type'] ?? 'full-time',
            'job_level'        => $data['job_level'] ?? null,
            'vacancy'          => $data['vacancy'] ?? null,
            'description'      => $data['description'] ?? '',
            'responsibilities' => $data['responsibilities'] ?? null,
            'requirements'     => $data['requirements'] ?? null,
            'education'        => $data['education'] ?? null,
            'location'         => $data['location'] ?? null,
            'salary'           => $data['salary'] ?? null,
            'apply_email'      => $data['apply_email'] ?? null,
            'apply_url'        => $data['apply_url'] ?? null,
            'deadline'         => $data['deadline'] ?? null,
            'published_at'     => $data['published_at'] ?? null,
            'job_status'       => $data['job_status'] ?? 'published',
            'status'           => $data['status'] ?? 0,
        ]);
    }

    public function find($id)
    {
        return Career::find($id);
    }

    public function update($id, array $data)
    {
        $career = Career::findOrFail($id);

        $career->update([
            'title'            => $data['title'] ?? $career->title,
            'slug'             => Str::slug($data['title'] ?? $career->title),
            'department'       => $data['department'] ?? $career->department,
            'job_type'         => $data['job_type'] ?? $career->job_type,
            'job_level'        => $data['job_level'] ?? $career->job_level,
            'vacancy'          => $data['vacancy'] ?? $career->vacancy,
            'description'      => $data['description'] ?? $career->description,
            'responsibilities' => $data['responsibilities'] ?? $career->responsibilities,
            'requirements'     => $data['requirements'] ?? $career->requirements,
            'education'        => $data['education'] ?? $career->education,
            'location'         => $data['location'] ?? $career->location,
            'salary'           => $data['salary'] ?? $career->salary,
            'apply_email'      => $data['apply_email'] ?? $career->apply_email,
            'apply_url'        => $data['apply_url'] ?? $career->apply_url,
            'deadline'         => $data['deadline'] ?? $career->deadline,
            'published_at'     => $data['published_at'] ?? $career->published_at,
            'job_status'       => $data['job_status'] ?? $career->job_status,
            'status'           => $data['status'] ?? $career->status,
        ]);

        return true;
    }



    public function delete($id)
    {
        $package = Career::findOrFail($id);
        return $package->delete();
    }

    public function restore($id)
    {
        $package = Career::onlyTrashed()->findOrFail($id);
        return $package->restore();
    }

    public function forceDelete($id)
    {
        $package = Career::onlyTrashed()->findOrFail($id);
        return $package->forceDelete();
    }
}
