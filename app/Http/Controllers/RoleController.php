<?php
// app/Http/Controllers/RoleController.php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
{
    /**
     * All Role actions are Admin-only.
     * Gate check is applied per method via the 'admin-only' gate defined in AuthServiceProvider.
     */

    public function index()
    {
        Gate::authorize('admin-only');

        $roles = Role::withCount('users')->get();

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        Gate::authorize('admin-only');

        return view('roles.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('admin-only');

        $validated = $request->validate([
            'role_name' => ['required', 'string', 'max:50', 'unique:roles,role_name'],
        ]);

        Role::create($validated);

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function show(Role $role)
    {
        Gate::authorize('admin-only');

        $role->load('users');

        return view('roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        Gate::authorize('admin-only');

        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        Gate::authorize('admin-only');

        $validated = $request->validate([
            'role_name' => ['required', 'string', 'max:50', 'unique:roles,role_name,' . $role->role_id . ',role_id'],
        ]);

        $role->update($validated);

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        Gate::authorize('admin-only');

        // Prevent deletion of core system roles
        $coreRoles = [Role::ADMIN, Role::STAFF, Role::CUSTOMER];
        if (in_array($role->role_name, $coreRoles)) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete core system roles (Admin, Staff, Customer).');
        }

        // Prevent deleting a role that has users attached (DB also enforces ON DELETE RESTRICT)
        if ($role->users()->exists()) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete a role that is assigned to users.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}