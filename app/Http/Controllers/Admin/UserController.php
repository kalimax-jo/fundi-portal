<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Remove the problematic constructor or make it simple
    public function __construct()
    {
        // Don't add middleware here since it's already in routes
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::with('roles');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $users = $query->paginate(15)->withQueryString();

        $roles = Role::all();
        $statuses = ['active', 'inactive', 'suspended'];

        return view('admin.users.index', compact('users', 'roles', 'statuses'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:active,inactive,suspended',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Create the user
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'status' => $request->status,
            ]);

            // Assign roles
            foreach ($request->roles as $roleId) {
                $user->roles()->attach($roleId, [
                    'assigned_at' => now(),
                    'assigned_by' => auth()->id(),
                ]);
            }

            return redirect()->route('admin.users.index')
                ->with('success', 'User created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load(['roles', 'businessPartners', 'inspector']);

        // Get user statistics
        $stats = [
            'total_inspection_requests' => 0, // Will implement when we have the tables
            'completed_inspections' => 0,
            'pending_inspections' => 0,
            'total_payments' => 0,
            'last_login' => $user->last_login_at,
            'account_age' => $user->created_at->diffForHumans(),
        ];

        return view('admin.users.show', compact('user', 'stats'));
    }

    /**
     * Show the form for editing the user
     */
    public function edit(User $user)
    {
        $user->load('roles');
        $roles = Role::all();
        
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'required|in:active,inactive,suspended',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Update user data
            $updateData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => $request->status,
            ];

            // Update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            // Update roles - detach all and reattach selected ones
            $user->roles()->detach();
            foreach ($request->roles as $roleId) {
                $user->roles()->attach($roleId, [
                    'assigned_at' => now(),
                    'assigned_by' => auth()->id(),
                ]);
            }

            return redirect()->route('admin.users.show', $user)
                ->with('success', 'User updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        try {
            // Prevent deleting the current admin user
            if ($user->id === auth()->id()) {
                return redirect()->back()
                    ->with('error', 'You cannot delete your own account.');
            }

            // Detach roles first
            $user->roles()->detach();

            // Delete the user
            $user->delete();

            return redirect()->route('admin.users.index')
                ->with('success', 'User deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user status (active/inactive/suspended)
     */
    public function toggleStatus(User $user)
    {
        try {
            // Prevent disabling the current admin user
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot change your own status.'
                ], 400);
            }

            $newStatus = $user->status === 'active' ? 'inactive' : 'active';
            $user->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => "User status changed to {$newStatus}.",
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign a role to user
     */
    public function assignRole(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid role selected.'
            ], 400);
        }

        try {
            // Check if user already has this role
            if ($user->roles()->where('role_id', $request->role_id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User already has this role.'
                ], 400);
            }

            $user->roles()->attach($request->role_id, [
                'assigned_at' => now(),
                'assigned_by' => auth()->id(),
            ]);

            $role = Role::find($request->role_id);

            return response()->json([
                'success' => true,
                'message' => "Role '{$role->display_name}' assigned successfully.",
                'role' => $role
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove a role from user
     */
    public function removeRole(User $user, Role $role)
    {
        try {
            // Prevent removing the last role
            if ($user->roles()->count() <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'User must have at least one role.'
                ], 400);
            }

            // Prevent removing admin role from current user
            if ($user->id === auth()->id() && $role->name === 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot remove admin role from your own account.'
                ], 400);
            }

            $user->roles()->detach($role->id);

            return response()->json([
                'success' => true,
                'message' => "Role '{$role->display_name}' removed successfully."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove role: ' . $e->getMessage()
            ], 500);
        }
    }
}