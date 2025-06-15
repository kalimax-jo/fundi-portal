@extends('layouts.admin')

@section('title', 'Create User')

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Create New User
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-500">Users</a>
                <span class="mx-2">/</span>
                <span>Create</span>
            </div>
        </div>
    </div>
    <div class="mt-4 flex md:ml-4 md:mt-0">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 010-1.06L10.94 8.25H4a.75.75 0 010-1.5h6.94L7.72 3.53a.75.75 0 011.06-1.06l4.5 4.5a.75.75 0 010 1.06l-4.5 4.5a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
            </svg>
            Back to Users
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-8">
        @csrf

        <!-- Basic Information -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Basic Information</h3>
                    <p class="mt-1 text-sm text-gray-500">User's personal information and contact details.</p>
                </div>
                <div class="mt-5 space-y-6 md:col-span-2 md:mt-0">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- First Name -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                                   placeholder="Enter first name"
                                   class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('first_name') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                                   placeholder="Enter last name"
                                   class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('last_name') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-span-6 sm:col-span-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                   placeholder="Enter email address"
                                   class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('email') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                                   placeholder="e.g., +250788123456"
                                   class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('phone') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                            <select name="status" id="status" required
                                    class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('status') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                <option value="">Select Status</option>
                                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
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
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Password</h3>
                    <p class="mt-1 text-sm text-gray-500">Set a secure password for the user account.</p>
                </div>
                <div class="mt-5 space-y-6 md:col-span-2 md:mt-0">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- Password -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                            <input type="password" name="password" id="password" required
                                   placeholder="Enter password"
                                   class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('password') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Minimum 8 characters</p>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                   placeholder="Confirm password"
                                   class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
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
                    <p class="mt-1 text-sm text-gray-500">Assign roles to determine user permissions and access levels.</p>
                </div>
                <div class="mt-5 md:col-span-2 md:mt-0">
                    @error('roles')
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        </div>
                    @enderror

                    <fieldset class="border border-gray-200 rounded-lg p-4">
                        <legend class="text-sm font-medium text-gray-900 px-2">Select Roles *</legend>
                        <div class="mt-4 space-y-4">
                            @foreach($roles as $role)
                            <div class="flex items-start p-3 border border-gray-100 rounded-lg hover:bg-gray-50">
                                <div class="flex items-center h-5">
                                    <input id="role_{{ $role->id }}" name="roles[]" type="checkbox" value="{{ $role->id }}"
                                           {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}
                                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm flex-1">
                                    <label for="role_{{ $role->id }}" class="font-medium text-gray-700 cursor-pointer">{{ $role->display_name }}</label>
                                    @if($role->description)
                                        <p class="text-gray-500 mt-1">{{ $role->description }}</p>
                                    @endif
                                    
                                    <!-- Show permissions for this role -->
                                    @if($role->permissions && count($role->permissions) > 0)
                                        <div class="mt-2">
                                            <p class="text-xs text-gray-600 font-medium">Permissions:</p>
                                            <div class="mt-1 flex flex-wrap gap-1">
                                                @foreach(array_slice($role->permissions, 0, 5) as $permission)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                                        {{ str_replace('_', ' ', $permission) }}
                                                    </span>
                                                @endforeach
                                                @if(count($role->permissions) > 5)
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
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.users.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Cancel
            </a>
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Create User
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password strength indicator
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    
    // Password match validation
    confirmPasswordInput.addEventListener('input', function() {
        if (this.value !== passwordInput.value) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });
    
    passwordInput.addEventListener('input', function() {
        if (confirmPasswordInput.value !== this.value) {
            confirmPasswordInput.setCustomValidity('Passwords do not match');
        } else {
            confirmPasswordInput.setCustomValidity('');
        }
    });

    // Role selection validation
    const roleCheckboxes = document.querySelectorAll('input[name="roles[]"]');
    const roleFieldset = document.querySelector('fieldset');
    
    function validateRoles() {
        const selectedRoles = document.querySelectorAll('input[name="roles[]"]:checked');
        if (selectedRoles.length === 0) {
            roleCheckboxes.forEach(checkbox => {
                checkbox.setCustomValidity('Please select at least one role');
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
});
</script>
@endpush
@endsection