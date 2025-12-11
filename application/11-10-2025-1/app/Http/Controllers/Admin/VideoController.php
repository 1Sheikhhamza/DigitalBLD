<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VideoRequest;
use App\Services\VideoService;
use App\Services\CommonService;

class VideoController extends Controller
{
    protected VideoService $videoService;
    protected CommonService $commonService;

    public function __construct(VideoService $videoService, CommonService $commonService)
    {
        $this->videoService = $videoService;
        $this->commonService = $commonService;
    }

    public function index()
    {
        $videos = $this->videoService->index();
        return view('admin.videos.index', compact('videos'));
    }

    public function create()
    {
        return view('admin.videos.create');
    }

    public function store(VideoRequest $request)
    {
        $this->videoService->create($request->validated());
        return redirect()->route('videos.index')->with('success', 'Video created successfully.');
    }

    public function show($id)
    {
        $video = $this->videoService->find($id);
        return view('admin.videos.show', compact('video'));
    }

    public function edit($id)
    {
        $video = $this->videoService->find($id);
        return view('admin.videos.edit', compact('video'));
    }

    public function update(VideoRequest $request, $id)
    {
        $this->videoService->update($id, $request->validated());
        return redirect()->route('videos.index')->with('success', 'Video updated successfully.');
    }

    public function destroy($id)
    {
        $this->videoService->delete($id);
        return redirect()->route('videos.index')->with('success', 'Video soft deleted.');
    }

    public function restore($id)
    {
        $this->videoService->restore($id);
        return redirect()->route('videos.index')->with('success', 'Video restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->videoService->forceDelete($id);
        return redirect()->route('videos.index')->with('success', 'Video permanently deleted.');
    }
}
