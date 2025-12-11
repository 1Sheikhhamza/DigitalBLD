<?php

namespace App\Repositories\Eloquent;

use App\Models\Dictionary;
use App\Repositories\Contracts\DictionaryRepositoryInterface;
use Illuminate\Support\Str;

class DictionaryRepository implements DictionaryRepositoryInterface
{
    public function index()
    {
        return Dictionary::orderByDesc('id')->paginate(50);
    }

    public function create(array $data)
    {
        return Dictionary::create([
            'word'             => $data['word'] ?? '',
            'meaning'      => $data['meaning'] ?? null
        ]);
    }


    public function find($id)
    {
        return Dictionary::find($id);
    }

    public function update($id, array $data)
    {
        $service = Dictionary::findOrFail($id);

        $service->update([
            'word'             => $data['word'] ?? $service->word,
            'meaning'      => $data['meaning'] ?? $service->description
        ]);

        return true;
    }



    public function delete($id)
    {
        $package = Dictionary::findOrFail($id);
        return $package->delete();
    }

    public function restore($id)
    {
        $package = Dictionary::onlyTrashed()->findOrFail($id);
        return $package->restore();
    }

    public function forceDelete($id)
    {
        $package = Dictionary::onlyTrashed()->findOrFail($id);
        return $package->forceDelete();
    }
}
