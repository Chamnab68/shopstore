<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Role, Permission};
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(protected ActivityLogService $activityLog) {}

    public function index()
    {
        $roles = Role::with('permissions')->paginate(15);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy('module');
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:50|unique:roles,name',
            'display_name' => 'required|string|max:100',
            'description'  => 'nullable|string|max:255',
            'permissions'  => 'array',
            'permissions.*'=> 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name'         => $validated['name'],
            'display_name' => $validated['display_name'],
            'description'  => $validated['description'] ?? null,
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        $this->activityLog->log('role.created', $role, ['new_values' => $validated]);

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy('module');
        $assignedIds = $role->permissions()->pluck('permissions.id')->toArray();
        return view('admin.roles.edit', compact('role', 'permissions', 'assignedIds'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->is_system) {
            return back()->with('error', 'System roles cannot be modified.');
        }

        $validated = $request->validate([
            'display_name' => 'required|string|max:100',
            'description'  => 'nullable|string|max:255',
            'permissions'  => 'array',
            'permissions.*'=> 'exists:permissions,id',
        ]);

        $old = $role->only(['display_name', 'description']);
        $role->update($validated);
        $role->permissions()->sync($validated['permissions'] ?? []);

        $this->activityLog->log('role.updated', $role, ['old_values' => $old, 'new_values' => $validated]);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return back()->with('error', 'System roles cannot be deleted.');
        }

        $this->activityLog->log('role.deleted', $role, ['old_values' => $role->toArray()]);
        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }

    // ── Assign / revoke permission directly (granular endpoint) ─────────────
    public function assignPermission(Request $request, Role $role)
    {
        $request->validate(['permission_id' => 'required|exists:permissions,id']);
        $role->permissions()->syncWithoutDetaching([$request->permission_id]);

        $this->activityLog->log('permission.assigned', $role, [
            'metadata' => ['permission_id' => $request->permission_id],
        ]);

        return back()->with('success', 'Permission assigned.');
    }

    public function revokePermission(Request $request, Role $role)
    {
        $request->validate(['permission_id' => 'required|exists:permissions,id']);
        $role->permissions()->detach($request->permission_id);

        $this->activityLog->log('permission.revoked', $role, [
            'metadata' => ['permission_id' => $request->permission_id],
        ]);

        return back()->with('success', 'Permission revoked.');
    }
}
