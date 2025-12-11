<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InquiryRequest;
use App\Services\InquiryService;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    protected InquiryService $inquiryService;

    public function __construct(InquiryService $inquiryService)
    {
        $this->inquiryService = $inquiryService;
    }

    public function index()
    {
        $inquiries = $this->inquiryService->index();
        return view('admin.inquiries.index', compact('inquiries'));
    }

    public function create()
    {
        return view('admin.inquiries.create');
    }

    public function store(InquiryRequest $request)
    {
        $this->inquiryService->create($request->validated());
        return redirect()->route('inquiries.index')->with('success', 'Inquiry created successfully.');
    }

    public function show($id)
    {
        $inquiry = $this->inquiryService->find($id);
        return view('admin.inquiries.show', compact('inquiry'));
    }

    public function edit($id)
    {
        $inquiry = $this->inquiryService->find($id);
        return view('admin.inquiries.edit', compact('inquiry'));
    }

    public function update(InquiryRequest $request, $id)
    {
        $this->inquiryService->update($id, $request->validated());
        return redirect()->route('inquiries.index')->with('success', 'Inquiry updated successfully.');
    }

    public function destroy($id)
    {
        $this->inquiryService->delete($id);
        return redirect()->route('inquiries.index')->with('success', 'Inquiry soft deleted.');
    }

    public function restore($id)
    {
        $this->inquiryService->restore($id);
        return redirect()->route('inquiries.index')->with('success', 'Inquiry restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->inquiryService->forceDelete($id);
        return redirect()->route('inquiries.index')->with('success', 'Inquiry permanently deleted.');
    }
}
