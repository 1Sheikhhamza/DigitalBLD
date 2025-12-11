<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriberRequest;
use App\Services\SubscriberService;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    protected SubscriberService $subscriberService;

    public function __construct(SubscriberService $subscriberService)
    {
        $this->subscriberService = $subscriberService;
    }

    public function index()
    {
        $subscribers = $this->subscriberService->index();
        return view('admin.subscribers.index', compact('subscribers'));
    }

    public function create()
    {
        return view('admin.subscribers.create');
    }

    public function store(SubscriberRequest $request)
    {
        $this->subscriberService->create($request->validated());
        return redirect()->route('subscribers.index')->with('success', 'Subscriber created successfully.');
    }

    public function show($id)
    {
        $subscriber = $this->subscriberService->find($id);
        return view('admin.subscribers.show', compact('subscriber'));
    }

    public function edit($id)
    {
        $subscriber = $this->subscriberService->find($id);
        // return view('admin.subscribers.edit', compact('subscriber'));
        return view('admin.subscribers.edit', [
            'subscriber' => $subscriber,
            'isEdit' => true, // this tells the Blade view you're editing
        ]);
    }

    public function update(SubscriberRequest $request, $id)
    {
        $this->subscriberService->update($id, $request->validated());
        return redirect()->route('subscribers.index')->with('success', 'Subscriber updated successfully.');
    }

    public function destroy($id)
    {
        $this->subscriberService->delete($id);
        return redirect()->route('subscribers.index')->with('success', 'Subscriber soft deleted.');
    }

    public function restore($id)
    {
        $this->subscriberService->restore($id);
        return redirect()->route('subscribers.index')->with('success', 'Subscriber restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->subscriberService->forceDelete($id);
        return redirect()->route('subscribers.index')->with('success', 'Subscriber permanently deleted.');
    }
}
