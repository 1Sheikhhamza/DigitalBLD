<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionRequest;
use App\Services\CommonService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    protected SubscriptionService $subscriptionService;
    protected CommonService $commonService;

    public function __construct(SubscriptionService $subscriptionService, CommonService $commonService)
    {
        $this->subscriptionService = $subscriptionService;
        $this->commonService = $commonService;
    }

    public function index()
    {
        $subscriptions = $this->subscriptionService->index();
        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function create()
    {
        $subscriberList = $this->commonService->getSubscriber();
        $packageList = $this->commonService->getPackage();
        return view('admin.subscriptions.create', compact('subscriberList','packageList'));
    }

    public function store(SubscriptionRequest $request)
    {
        $this->subscriptionService->create($request->validated());
        return redirect()->route('subscriptions.index')->with('success', 'Subscription created successfully.');
    }

    public function show($id)
    {
        $subscription = $this->subscriptionService->find($id);
        return view('admin.subscriptions.show', compact('subscription'));
    }

    public function edit($id)
    {
        $subscription = $this->subscriptionService->find($id);
        $subscriberList = $this->commonService->getSubscriber();
        $packageList = $this->commonService->getPackages();
        return view('admin.subscriptions.edit', compact('subscription','subscriberList','packageList'));
    }

    public function update(SubscriptionRequest $request, $id)
    {
        $this->subscriptionService->update($id, $request->validated());
        return redirect()->route('subscriptions.index')->with('success', 'Subscription updated successfully.');
    }

    public function destroy($id)
    {
        $this->subscriptionService->delete($id);
        return redirect()->route('subscriptions.index')->with('success', 'Subscription soft deleted.');
    }

    public function restore($id)
    {
        $this->subscriptionService->restore($id);
        return redirect()->route('subscriptions.index')->with('success', 'Subscription restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->subscriptionService->forceDelete($id);
        return redirect()->route('subscriptions.index')->with('success', 'Subscription permanently deleted.');
    }
}
