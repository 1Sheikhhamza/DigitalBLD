<?php

namespace App\Http\Controllers\Admin;

use App\Helper\ImageUploadHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\TeamRequest;
use App\Services\TeamService;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    protected TeamService $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    public function index()
    {
        $teams = $this->teamService->index();
        return view('admin.teams.index', compact('teams'));
    }

    public function create()
    {
        return view('admin.teams.create');
    }

    public function store(TeamRequest $request)
    {
        $validated = $request->validated();
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $validated['photo'] = ImageUploadHelper::upload(
                $request->file('image'),
                'uploads/teams',
                'teams',
                300,
                null,
            );
        }

        $this->teamService->create($validated);

        return redirect()->route('teams.index')->with('success', 'Team created successfully.');
    }

    public function show($id)
    {
        $team = $this->teamService->find($id);
        return view('admin.teams.show', compact('team'));
    }

    public function edit($id)
    {
        $team = $this->teamService->find($id);
        return view('admin.teams.edit', compact('team'));
    }

    public function update(TeamRequest $request, $id)
    {
        $team = $this->teamService->find($id);
        $validated = $request->validated();
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $validated['photo'] = ImageUploadHelper::upload(
                $request->file('image'),
                'uploads/teams',
                'teams',
                300,
                null,
                $team->image
            );
        }

        $this->teamService->update($id, $validated);
        return redirect()->route('teams.index')->with('success', 'Team updated successfully.');
    }

    public function destroy($id)
    {
        $this->teamService->delete($id);
        return redirect()->route('teams.index')->with('success', 'Team soft deleted.');
    }

    public function restore($id)
    {
        $this->teamService->restore($id);
        return redirect()->route('teams.index')->with('success', 'Team restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->teamService->forceDelete($id);
        return redirect()->route('teams.index')->with('success', 'Team permanently deleted.');
    }
}
