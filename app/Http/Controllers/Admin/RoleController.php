<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display a listing of roles
     */
    public function index(Request $request)
    {
        $query = Role::withCount('users');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $roles = $query->paginate(10)->withQueryString();

        // Get all available permissions
        $availablePermissions = Role::getPermissionLabels();
        $groupedPermissions = Role::getGroupedPermissions();

        return view('admin.roles.index', compact('roles', 'availablePermissions', 'groupedPermissions'));
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        $availablePermissions = Role::getPermissionLabels();
        $groupedPermissions = Role::getGroupedPermissions();
        
        return view('admin.roles.create', compact('availablePermissions', 'groupedPermissions'));
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:roles|alpha_dash',
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Create the role
            $role = Role::create([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
                'permissions' => $request->permissions ?? [],
            ]);

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create role: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified role
     */
    public function show(Role $role)
    {
        $role->loadCount('users');
        $role->load('users');
        
        $availablePermissions = Role::getPermissionLabels();
        $groupedPermissions = Role::getGroupedPermissions();

        return view('admin.roles.show', compact('role', 'availablePermissions', 'groupedPermissions'));
    }

    /**
     * Show the form for editing the role
     */
    public function edit(Role $role)
    {
        $availablePermissions = Role::getPermissionLabels();
        $groupedPermissions = Role::getGroupedPermissions();
        
        return view('admin.roles.edit', compact('role', 'availablePermissions', 'groupedPermissions'));
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('roles')->ignore($role->id)],
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Update role data
            $role->update([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
                'permissions' => $request->permissions ?? [],
            ]);

            return redirect()->route('admin.roles.show', $role)
                ->with('success', 'Role updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update role: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified role
     */
    public function destroy(Role $role)
    {
        try {
            // Prevent deleting admin role
            if ($role->name === 'admin') {
                return redirect()->back()
                    ->with('error', 'Cannot delete the admin role.');
            }

            // Check if role has users
            if ($role->users()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete role that has users assigned. Please reassign users first.');
            }

            // Delete the role
            $role->delete();

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete role: ' . $e->getMessage());
        }
    }

    /**
     * Assign a permission to role
     */
    public function assignPermission(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'permission' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid permission.'
            ], 400);
        }

        try {
            $permissions = $role->permissions ?? [];
            
            if (!in_array($request->permission, $permissions)) {
                $permissions[] = $request->permission;
                $role->update(['permissions' => $permissions]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Permission assigned successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign permission: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove a permission from role
     */
    public function removePermission(Role $role, string $permission)
    {
        try {
            $permissions = $role->permissions ?? [];
            
            if (($key = array_search($permission, $permissions)) !== false) {
                unset($permissions[$key]);
                $role->update(['permissions' => array_values($permissions)]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Permission removed successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove permission: ' . $e->getMessage()
            ], 500);
        }
    }
}