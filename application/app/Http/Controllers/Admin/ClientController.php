<?php

namespace App\Http\Controllers\Admin;

use App\Helper\ImageUploadHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRequest;
use App\Services\ClientService;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    protected ClientService $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function index()
    {
        $clients = $this->clientService->index();
        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(ClientRequest $request)
    {
        $validated = $request->validated();
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $validated['logo'] = ImageUploadHelper::upload(
                $request->file('image'),
                'uploads/link',
                'links',
                300,
                null,
            );
        }
        
        $this->clientService->create($validated);
        return redirect()->route('clients.index')->with('success', 'Client created successfully.');
    }

    public function show($id)
    {
        $client = $this->clientService->find($id);
        return view('admin.clients.show', compact('client'));
    }

    public function edit($id)
    {
        $client = $this->clientService->find($id);
        return view('admin.clients.edit', compact('client'));
    }

    public function update(ClientRequest $request, $id)
    {
        $currentClient = $this->clientService->find($id);
        $validated = $request->validated();
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $validated['logo'] = ImageUploadHelper::upload(
                $request->file('image'),
                'uploads/link',
                'links',
                300,
                null,
                $currentClient->image
            );
        }

        $this->clientService->update($id, $validated);
        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    public function destroy($id)
    {
        $this->clientService->delete($id);
        return redirect()->route('clients.index')->with('success', 'Client soft deleted.');
    }

    public function restore($id)
    {
        $this->clientService->restore($id);
        return redirect()->route('clients.index')->with('success', 'Client restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->clientService->forceDelete($id);
        return redirect()->route('clients.index')->with('success', 'Client permanently deleted.');
    }
}
