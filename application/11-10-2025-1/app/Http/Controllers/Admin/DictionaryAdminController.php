<?php

namespace App\Http\Controllers\Admin;

use App\Helper\ImageUploadHelper;
use App\Http\Controllers\Controller;
use App\Services\DictionaryService;
use Illuminate\Http\Request;

class DictionaryAdminController extends Controller
{
    protected DictionaryService $dictionaryService;

    public function __construct(DictionaryService $dictionaryService)
    {
        $this->dictionaryService = $dictionaryService;
    }

    public function index()
    {
        $dictionary = $this->dictionaryService->index();
        return view('admin.dictionary.index', compact('dictionary'));
    }

    public function create()
    {
        return view('admin.dictionary.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'word' => 'required|string|max:255',
            'meaning' => 'required|string',
        ]);
        $this->dictionaryService->create($validated);
        return redirect()->route('dictionary.index')->with('success', 'Service created successfully.');
    }

    public function edit($id)
    {
        $service = $this->dictionaryService->find($id);
        return view('admin.dictionary.edit', compact('service'));
    }

    public function update(Request $request, $id)
    {
        $currentService = $this->dictionaryService->find($id);
        $validated = $request->validate([
            'word' => 'required|string|max:255',
            'meaning' => 'required|string',
        ]);
        
        $this->dictionaryService->update($id, $validated);
        return redirect()->route('dictionary.index')->with('success', 'Service updated successfully.');
    }

    public function destroy($id)
    {
        $this->dictionaryService->delete($id);
        return redirect()->route('dictionary.index')->with('success', 'Service soft deleted.');
    }

    public function restore($id)
    {
        $this->dictionaryService->restore($id);
        return redirect()->route('dictionary.index')->with('success', 'Service restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->dictionaryService->forceDelete($id);
        return redirect()->route('dictionary.index')->with('success', 'Service permanently deleted.');
    }
}
