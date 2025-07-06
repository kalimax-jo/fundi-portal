@extends('layouts.admin')

@section('title', 'Edit User - ' . $user->full_name)

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Edit User: {{ $user->full_name }}
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-500">Users</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.users.show', $user) }}" class="text-indigo-600 hover:text-indigo-500">{{ $user->full_name }}</a>
                <span class="mx-2">/</span>
                <span>Edit</span>
            </div>
        </div>
    </div>
    <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
        <a href="{{ route('admin.users.show', $user) }}" 
           class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 010-1.06L10.94 8.25H4a.75.75 0 010-1.5h6.94L7.72 3.53a.75.75 0 011.06-1.06l4.5 4.5a.75.75 0 010 1.06l-4.5 4.5a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
            </svg>
            Back to User
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Basic Information</h3>
                    <p class="mt-1 text-sm text-gray-500">Update user's personal information and contact details.</p>
                </div>
                <div class="mt-5 space-y-6 md:col-span-2 md:mt-0">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- First Name -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name *</label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('first_name') border-red-300 @enderror">
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name *</label>
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('last_name') border-red-300 @enderror">
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-span-6 sm:col-span-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address *</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('email') border-red-300 @enderror">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('phone') border-red-300 @enderror">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                            <select name="status" id="status" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('status') border-red-300 @enderror">
                                <option value="">Select Status</option>
                                <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ old('status', $user->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Password Section -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Change Password</h3>
                    <p class="mt-1 text-sm text-gray-500">Leave password fields empty if you don't want to change the current password.</p>
                </div>
                <div class="mt-5 space-y-6 md:col-span-2 md:mt-0">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- Password -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                            <input type="password" name="password" id="password"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('password') border-red-300 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Minimum 8 characters (optional)</p>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Roles Section -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">User Roles</h3>
                    <p class="mt-1 text-sm text-gray-500">Update user roles to determine permissions and access levels.</p>
                    
                    <!-- Current Roles Display -->
                    <div class="mt-4 p-3 bg-gray-50 rounded-md">
                        <p class="text-sm font-medium text-gray-700 mb-2">Current Roles:</p>
                        <div class="space-y-1">
                            @foreach($user->roles as $role)
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $role->display_name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="mt-5 md:col-span-2 md:mt-0">
                    @error('roles')
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        </div>
                    @enderror

                    <fieldset>
                        <legend class="text-sm font-medium text-gray-900">Update Roles *</legend>
                        <div class="mt-4 space-y-4">
                            @foreach($roles as $role)
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="role_{{ $role->id }}" name="roles[]" type="checkbox" value="{{ $role->id }}"
                                           {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }}
                                           @if($user->id === auth()->id() && $role->name === 'admin') disabled @endif
                                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="role_{{ $role->id }}" class="font-medium text-gray-700">
                                        {{ $role->display_name }}
                                        @if($user->id === auth()->id() && $role->name === 'admin')
                                            <span class="text-xs text-gray-500">(Cannot remove your own admin role)</span>
                                        @endif
                                    </label>
                                    @if($role->description)
                                        <p class="text-gray-500">{{ $role->description }}</p>
                                    @endif
                                    
                                    <!-- Show permissions for this role -->
                                    @if(is_array($role->permissions) && count($role->permissions) > 0)
                                        <div class="mt-2">
                                            <p class="text-xs text-gray-600 font-medium">Permissions:</p>
                                            <div class="mt-1 flex flex-wrap gap-1">
                                                @foreach(array_slice($role->permissions, 0, 5) as $permission)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ str_replace('_', ' ', $permission) }}
                                                    </span>
                                                @endforeach
                                                @if(is_array($role->permissions) && count($role->permissions) > 5)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                        +{{ count($role->permissions) - 5 }} more
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach

                            <!-- Hidden input to ensure admin role stays if it's the current user -->
                            @if($user->id === auth()->id() && $user->hasRole('admin'))
                                <input type="hidden" name="roles[]" value="{{ $roles->where('name', 'admin')->first()->id }}">
                            @endif
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>

        <!-- Account Status Warning -->
        @if($user->id === auth()->id())
        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">You are editing your own account</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>Be careful when changing your own account settings. Some restrictions apply to prevent you from locking yourself out:</p>
                        <ul class="list-disc pl-5 mt-2">
                            <li>Your admin role cannot be removed</li>
                            <li>Your account status will remain as is if you try to deactivate it</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.users.show', $user) }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Cancel
            </a>
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Update User
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password confirmation validation
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    
    function validatePasswords() {
        if (passwordInput.value && confirmPasswordInput.value) {
            if (passwordInput.value !== confirmPasswordInput.value) {
                confirmPasswordInput.setCustomValidity('Passwords do not match');
            } else {
                confirmPasswordInput.setCustomValidity('');
            }
        } else {
            confirmPasswordInput.setCustomValidity('');
        }
    }
    
    passwordInput.addEventListener('input', validatePasswords);
    confirmPasswordInput.addEventListener('input', validatePasswords);

    // Role selection validation
    const roleCheckboxes = document.querySelectorAll('input[name="roles[]"]:not([disabled])');
    
    function validateRoles() {
        const selectedRoles = document.querySelectorAll('input[name="roles[]"]:checked');
        if (selectedRoles.length === 0) {
            roleCheckboxes.forEach(checkbox => {
                if (!checkbox.disabled) {
                    checkbox.setCustomValidity('Please select at least one role');
                }
            });
        } else {
            roleCheckboxes.forEach(checkbox => {
                checkbox.setCustomValidity('');
            });
        }
    }
    
    roleCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', validateRoles);
    });
    
    // Initial validation
    validateRoles();

    // Confirm form submission for sensitive changes
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const currentUserId = {{ auth()->id() }};
        const editingUserId = {{ $user->id }};
        
        if (currentUserId === editingUserId) {
            const statusSelect = document.getElementById('status');
            if (statusSelect.value !== 'active') {
                if (!confirm('You are about to change your own account status. This might affect your access. Are you sure?')) {
                    e.preventDefault();
                    return false;
                }
            }
        }
        
        return true;
    });
});
</script>
@endpush
@endsection