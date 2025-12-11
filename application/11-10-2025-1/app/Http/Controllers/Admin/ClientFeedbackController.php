<?php

namespace App\Http\Controllers\Admin;

use App\Helper\ImageUploadHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClientFeedbackRequest;
use App\Services\ClientFeedbackService;

class ClientFeedbackController extends Controller
{
    protected ClientFeedbackService $clientFeedbackService;

    public function __construct(ClientFeedbackService $clientFeedbackService)
    {
        $this->clientFeedbackService = $clientFeedbackService;
    }

    public function index()
    {
        $clientFeedbacks = $this->clientFeedbackService->index();
        return view('admin.client-feedbacks.index', compact('clientFeedbacks'));
    }

    public function create()
    {
        return view('admin.client-feedbacks.create');
    }

    public function store(ClientFeedbackRequest $request)
    {
        
        $validated = $request->validated();
        if ($request->hasFile('client_photo') && $request->file('client_photo')->isValid()) {
            $validated['client_photo'] = ImageUploadHelper::upload(
                $request->file('client_photo'),
                'uploads/feedback/image',
                'feedbacks',
                300,
                300,
            );
        }

        $this->clientFeedbackService->create($validated);
        return redirect()->route('client_feedbacks.index')->with('success', 'ClientFeedback created successfully.');
    }

    public function show($id)
    {
        $clientFeedback = $this->clientFeedbackService->find($id);
        return view('admin.client-feedbacks.show', compact('clientFeedback'));
    }

    public function edit($id)
    {
        $feedback = $this->clientFeedbackService->find($id);
        return view('admin.client-feedbacks.edit', compact('feedback'));
    }

    public function update(ClientFeedbackRequest $request, $id)
    {
        $currentFeedback = $this->clientFeedbackService->find($id);
        $validated = $request->validated();
        if ($request->hasFile('client_photo') && $request->file('client_photo')->isValid()) {
            $validated['client_photo'] = ImageUploadHelper::upload(
                $request->file('client_photo'),
                'uploads/feedback/image',
                'feedbacks',
                300,
                300,
                $currentFeedback->client_photo
            );
        }

        $this->clientFeedbackService->update($id, $validated);
        return redirect()->route('client_feedbacks.index')->with('success', 'ClientFeedback updated successfully.');
    }

    public function destroy($id)
    {
        $this->clientFeedbackService->delete($id);
        return redirect()->route('client_feedbacks.index')->with('success', 'ClientFeedback soft deleted.');
    }

    public function restore($id)
    {
        $this->clientFeedbackService->restore($id);
        return redirect()->route('client_feedbacks.index')->with('success', 'ClientFeedback restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->clientFeedbackService->forceDelete($id);
        return redirect()->route('client_feedbacks.index')->with('success', 'ClientFeedback permanently deleted.');
    }
}
