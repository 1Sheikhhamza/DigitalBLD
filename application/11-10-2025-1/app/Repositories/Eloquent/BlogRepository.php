<?php

namespace App\Repositories\Eloquent;

use App\Models\Blog;
use App\Repositories\Contracts\BlogRepositoryInterface;
use Illuminate\Support\Str;

class BlogRepository implements BlogRepositoryInterface
{
    public function index()
    {
        return Blog::orderByDesc('id')->paginate(10);
    }

    public function create(array $data)
    {
        return Blog::create([
            'title'             => $data['title'] ?? '',
            'slug'              => Str::slug($data['title']),
            'author'            => $data['author'] ?? null,
            'content'           => $data['content'] ?? '',
            'featured_image'    => $data['featured_image'] ?? null,
            'status'            => $data['status'] ?? 'draft',
            'meta_title'        => $data['meta_title'] ?? null,
            'meta_description'  => $data['meta_description'] ?? null,
            'meta_keywords'     => $data['meta_keywords'] ?? null,
        ]);
    }



    public function find($id)
    {
        return Blog::find($id);
    }

    public function update($id, array $data)
    {
        $blog = Blog::findOrFail($id);

        $blog->update([
            'title'             => $data['title'] ?? $blog->title,
            'slug'              => Str::slug($data['title'] ?? $blog->title),
            'author'            => $data['author'] ?? $blog->author,
            'content'           => $data['content'] ?? $blog->content,
            'featured_image'    => $data['featured_image'] ?? $blog->featured_image,
            'status'            => $data['status'] ?? $blog->status,
            'meta_title'        => $data['meta_title'] ?? $blog->meta_title,
            'meta_description'  => $data['meta_description'] ?? $blog->meta_description,
            'meta_keywords'     => $data['meta_keywords'] ?? $blog->meta_keywords,
        ]);

        return true;
    }




    public function delete($id)
    {
        $package = Blog::findOrFail($id);
        return $package->delete();
    }

    public function restore($id)
    {
        $package = Blog::onlyTrashed()->findOrFail($id);
        return $package->restore();
    }

    public function forceDelete($id)
    {
        $package = Blog::onlyTrashed()->findOrFail($id);
        return $package->forceDelete();
    }
}
