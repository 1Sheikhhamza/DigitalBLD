<?php

namespace App\Repositories\Eloquent;

use App\Models\Module;
use App\Models\Page;
use App\Repositories\Contracts\PageRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PageRepository implements PageRepositoryInterface
{
    public function index()
    {
        return Page::with('parent')->orderByDesc('id')->paginate(50);
    }

    public function getAvailableParentPages($exceptId = null)
    {
        return Page::whereNull('parent_id')
            // ->whereDoesntHave('children')
            ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->get();
    }

    public function getPageModule()
    {
        $excludedModules = ['Admin', 'Role', 'Permission', 'User', 'Configuration', 'Page', 'Banner', 'Subscriber', 'ProjectOwner', 'Investment'];
        $query = Module::query();
        $query->whereNotIn('name', $excludedModules);

        return $query->get();
    }


    public function updateSequence(int $pageId, int $newSequence): bool
    {
        return DB::transaction(function () use ($pageId, $newSequence) {
            $page = Page::findOrFail($pageId);
            $old = $page->sequence;

            if ($newSequence == $old) return true;

            if ($newSequence < $old) {
                Page::where('sequence', '>=', $newSequence)
                    ->where('sequence', '<', $old)
                    ->increment('sequence');
            } else {
                Page::where('sequence', '<=', $newSequence)
                    ->where('sequence', '>', $old)
                    ->decrement('sequence');
            }

            $page->sequence = $newSequence;
            $page->save();

            return true;
        });
    }


    public function create(array $data)
    {

        $max = Page::max('sequence') ?? 0;
        return Page::create([
            'title'             => $data['title'] ?? '',
            'parent_id'         => $data['parent_id'] ?? null,
            'slug'              => $data['slug'] ?? '',
            'content'           => $data['content'] ?? null,
            'icon'              => $data['icon'] ?? null,
            'image'             => $data['image'] ?? null,
            'homepage_display'  => $data['homepage_display'] ?? 0,
            'why_choose'        => $data['why_choose'] ?? 0,
            'sequence'          => $max + 1,
            'meta_title'        => $data['meta_title'] ?? null,
            'meta_description'  => $data['meta_description'] ?? null,
            'page_structure'    => $data['page_structure'] ?? 'Text',
            'menu_type'         => $data['menu_type'] ?? 'Main Menu',
            'connected_page'    => $data['connected_page'] ?? null,
            'external_url'      => $data['external_url'] ?? null,
        ]);
    }

    public function find($id)
    {
        return Page::with('parent')->find($id);
    }

    public function update($id, array $data)
    {
        $page = Page::findOrFail($id);

        $page->update([
            'title'             => $data['title'] ?? $page->title,
            'parent_id'         => $data['parent_id'] ?? $page->parent_id,
            'slug'              => $data['slug'] ?? '',
            'content'           => $data['content'] ?? $page->content,
            'icon'              => $data['icon'] ?? $page->icon,
            'image'             => $data['image'] ?? $page->image,
            'homepage_display'  => $data['homepage_display'] ?? 0,
            'why_choose'        => $data['why_choose'] ?? 0,
            'sequence'          => $data['sequence'] ?? $page->sequence,
            'meta_title'        => $data['meta_title'] ?? $page->meta_title,
            'meta_description'  => $data['meta_description'] ?? $page->meta_description,
            'page_structure'    => $data['page_structure'] ?? $page->page_structure,
            'menu_type'         => $data['menu_type'] ?? $page->menu_type,
            'connected_page'    => $data['connected_page'] ?? $page->connected_page,
            'external_url'      => $data['external_url'] ?? $page->external_url,
        ]);


        return true;
    }

    public function delete($id)
    {
        $page = Page::findOrFail($id);
        return $page->delete();
    }

    public function restore($id)
    {
        $page = Page::onlyTrashed()->findOrFail($id);
        return $page->restore();
    }

    public function forceDelete($id)
    {
        $page = Page::onlyTrashed()->findOrFail($id);
        return $page->forceDelete();
    }
}
