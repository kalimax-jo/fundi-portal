<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        try {
            // Validate registration data
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'required|string|max:20|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|string|in:individual_client,business_partner'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create the user
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'status' => 'active'
            ]);

            // Assign role to user
            $role = Role::where('name', $request->role)->first();
            if ($role) {
                $user->roles()->attach($role->id, [
                    'assigned_at' => now(),
                    'assigned_by' => $user->id // Self-assigned during registration
                ]);
            }

            // Generate API token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'full_name' => $user->full_name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'status' => $user->status,
                        'roles' => $user->roles->pluck('name'),
                        'created_at' => $user->created_at
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * User login
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {
            // Validate login data
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Find user by email
            $user = User::where('email', $request->email)->first();

            // Check if user exists and password is correct
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            // Check if user account is active
            if (!$user->isActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account is inactive. Please contact administrator.'
                ], 403);
            }

            // Update last login time
            $user->updateLastLogin();

            // Generate API token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Load user relationships
            $user->load('roles', 'businessPartners', 'inspector');

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'full_name' => $user->full_name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'status' => $user->status,
                        'profile_photo' => $user->profile_photo,
                        'last_login_at' => $user->last_login_at,
                        'roles' => $user->roles->pluck('name'),
                        'permissions' => $this->getUserPermissions($user),
                        'business_partners' => $user->businessPartners->map(function ($partner) {
                            return [
                                'id' => $partner->id,
                                'name' => $partner->name,
                                'type' => $partner->type,
                                'access_level' => $partner->pivot->access_level,
                                'is_primary_contact' => $partner->pivot->is_primary_contact
                            ];
                        }),
                        'inspector_profile' => $user->inspector ? [
                            'id' => $user->inspector->id,
                            'inspector_code' => $user->inspector->inspector_code,
                            'certification_level' => $user->inspector->certification_level,
                            'specializations' => $user->inspector->specializations,
                            'availability_status' => $user->inspector->availability_status,
                            'rating' => $user->inspector->rating
                        ] : null
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current authenticated user
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $user->load('roles', 'businessPartners', 'inspector');

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'full_name' => $user->full_name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'status' => $user->status,
                        'profile_photo' => $user->profile_photo,
                        'last_login_at' => $user->last_login_at,
                        'roles' => $user->roles->pluck('name'),
                        'permissions' => $this->getUserPermissions($user),
                        'business_partners' => $user->businessPartners->map(function ($partner) {
                            return [
                                'id' => $partner->id,
                                'name' => $partner->name,
                                'type' => $partner->type,
                                'access_level' => $partner->pivot->access_level,
                                'is_primary_contact' => $partner->pivot->is_primary_contact
                            ];
                        }),
                        'inspector_profile' => $user->inspector ? [
                            'id' => $user->inspector->id,
                            'inspector_code' => $user->inspector->inspector_code,
                            'certification_level' => $user->inspector->certification_level,
                            'specializations' => $user->inspector->specializations,
                            'availability_status' => $user->inspector->availability_status,
                            'rating' => $user->inspector->rating,
                            'current_workload' => $user->inspector->getCurrentWorkload()
                        ] : null,
                        'created_at' => $user->created_at
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get user data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user profile
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Validate update data
            $validator = Validator::make($request->all(), [
                'first_name' => 'sometimes|required|string|max:100',
                'last_name' => 'sometimes|required|string|max:100',
                'phone' => 'sometimes|required|string|max:20|unique:users,phone,' . $user->id,
                'profile_photo' => 'sometimes|nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update user data
            $user->update($request->only(['first_name', 'last_name', 'phone', 'profile_photo']));

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'full_name' => $user->full_name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'profile_photo' => $user->profile_photo,
                        'updated_at' => $user->updated_at
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Profile update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change user password
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function changePassword(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Validate password change data
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 400);
            }

            // Update password
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            // Revoke all existing tokens (force re-login)
            $user->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully. Please login again.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Password change failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * User logout
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout from all devices
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function logoutAll(Request $request): JsonResponse
    {
        try {
            // Revoke all tokens
            $request->user()->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logged out from all devices successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available roles for registration
     * 
     * @return JsonResponse
     */
    public function getAvailableRoles(): JsonResponse
    {
        try {
            $roles = Role::whereIn('name', ['individual_client', 'business_partner'])
                ->get(['name', 'display_name', 'description']);

            return response()->json([
                'success' => true,
                'data' => [
                    'roles' => $roles
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get roles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user permissions from roles
     * 
     * @param User $user
     * @return array
     */
    private function getUserPermissions(User $user): array
    {
        $permissions = [];
        
        foreach ($user->roles as $role) {
            if ($role->permissions) {
                $permissions = array_merge($permissions, $role->permissions);
            }
        }

        return array_unique($permissions);
    }
