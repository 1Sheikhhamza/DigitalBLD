<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CareerRequest;
use App\Services\CareerService;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    protected CareerService $careerService;

    public function __construct(CareerService $careerService)
    {
        $this->careerService = $careerService;
    }

    public function index()
    {
        $careers = $this->careerService->index();
        return view('admin.careers.index', compact('careers'));
    }

    public function create()
    {
        return view('admin.careers.create');
    }

    public function store(CareerRequest $request)
    {
        $this->careerService->create($request->validated());
        return redirect()->route('careers.index')->with('success', 'Career created successfully.');
    }

    public function show($id)
    {
        $career = $this->careerService->find($id);
        return view('admin.careers.show', compact('career'));
    }

    public function edit($id)
    {
        $career = $this->careerService->find($id);
        return view('admin.careers.edit', compact('career'));
    }

    public function update(CareerRequest $request, $id)
    {
        $this->careerService->update($id, $request->validated());
        return redirect()->route('careers.index')->with('success', 'Career updated successfully.');
    }

    public function destroy($id)
    {
        $this->careerService->delete($id);
        return redirect()->route('careers.index')->with('success', 'Career soft deleted.');
    }

    public function restore($id)
    {
        $this->careerService->restore($id);
        return redirect()->route('careers.index')->with('success', 'Career restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->careerService->forceDelete($id);
        return redirect()->route('careers.index')->with('success', 'Career permanently deleted.');
    }
}
