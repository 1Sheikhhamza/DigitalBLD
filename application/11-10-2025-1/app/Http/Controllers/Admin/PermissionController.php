<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    // Show all permissions
    public function index()
    {
        $permissions = Permission::paginate(15);
        return view('admin.permissions.index', compact('permissions'));
    }

    // Show create form
    public function create()
    {
        $modules = Module::all();
        return view('admin.permissions.create', compact('modules'));
    }

    // Store permission
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:permissions,slug',
            'module_id' => 'required|exists:modules,id',
        ]);

        Permission::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'module_id' => $request->module_id,
        ]);

        return redirect()->route('permission.index')->with('success', 'Permission created successfully.');
    }

    // Show edit form
    public function edit(Permission $permission)
    {
        $modules = Module::all();
        return view('admin.permissions.edit', compact('permission', 'modules'));
    }

    // Update permission
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:permissions,slug,' . $permission->id,
            'module_id' => 'required|exists:modules,id',
        ]);

        $permission->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'module_id' => $request->module_id,
        ]);

        return redirect()->route('permission.index')->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('permission.index')->with('success', 'Permission deleted successfully.');
    }
}
