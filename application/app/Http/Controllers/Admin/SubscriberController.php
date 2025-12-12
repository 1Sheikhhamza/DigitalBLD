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

    public function index(Request $request)
    {
        $subscribers = $this->subscriberService->index($request->all());
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
    public function suggestions(Request $request)
    {
        $field = $request->query('field');
        $term = $request->query('term');

        if (!$field || !$term) {
            return response()->json([]);
        }

        $suggestions = $this->subscriberService->getSuggestions($field, $term);
        return response()->json($suggestions);
    }

    public function export(Request $request)
    {
        $subscribers = $this->subscriberService->export($request->all());

        $callback = function () use ($subscribers) {
            $file = fopen('php://output', 'w');

            // Add BOM for Excel compatibility
            fputs($file, "\xEF\xBB\xBF");

            $columns = ['SL', 'Name', 'Email', 'Mobile', 'Registered Date', 'Status', 'Current Package', 'Expire Date'];
            fputcsv($file, $columns);

            $sl = 1;
            foreach ($subscribers as $subscriber) {
                $pkgName = $subscriber->latestSubscription && $subscriber->latestSubscription->package ? $subscriber->latestSubscription->package->title : 'N/A';
                $expireDate = $subscriber->latestSubscription && $subscriber->latestSubscription->end_date ? $subscriber->latestSubscription->end_date->format('d M, Y') : 'N/A';

                $row = [
                    $sl++,
                    $subscriber->name,
                    $subscriber->email,
                    $subscriber->mobile,
                    $subscriber->created_at->format('d M, Y h:i A'),
                    $subscriber->status ? 'Active' : 'Inactive',
                    $pkgName,
                    $expireDate
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->streamDownload($callback, 'subscribers-' . date('d-m-Y') . '.csv');
    }
}
