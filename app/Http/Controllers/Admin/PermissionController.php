<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct(protected ActivityLogService $activityLog) {}

    public function index()
    {
        $permissions = Permission::orderBy('module')->paginate(20);
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:permissions,name',
            'module'      => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'min_tier'    => 'nullable|string|max:20', // tier-gated feature access control
        ]);

        $permission = Permission::create($validated);
        $this->activityLog->log('permission.created', $permission, ['new_values' => $validated]);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission created.');
    }

    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:permissions,name,' . $permission->id,
            'module'      => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'min_tier'    => 'nullable|string|max:20',
        ]);

        $old = $permission->toArray();
        $permission->update($validated);

        $this->activityLog->log('permission.updated', $permission, [
            'old_values' => $old, 'new_values' => $validated,
        ]);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated.');
    }

    public function destroy(Permission $permission)
    {
        $this->activityLog->log('permission.deleted', $permission, ['old_values' => $permission->toArray()]);
        $permission->delete();

        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted.');
    }
}
