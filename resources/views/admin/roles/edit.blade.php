@extends('layouts.admin')

@section('title', 'Edit Role - ' . $role->display_name)

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Edit Role: {{ $role->display_name }}
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <a href="{{ route('admin.roles.index') }}" class="text-indigo-600 hover:text-indigo-500">Roles & Permissions</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.roles.show', $role) }}" class="text-indigo-600 hover:text-indigo-500">{{ $role->display_name }}</a>
                <span class="mx-2">/</span>
                <span>Edit</span>
            </div>
        </div>
    </div>
    <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
        <a href="{{ route('admin.roles.show', $role) }}" 
           class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 010-1.06L10.94 8.25H4a.75.75 0 010-1.5h6.94L7.72 3.53a.75.75 0 011.06-1.06l4.5 4.5a.75.75 0 010 1.06l-4.5 4.5a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
            </svg>
            Back to Role
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.roles.update', $role) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Basic Information</h3>
                    <p class="mt-1 text-sm text-gray-500">Update the role name, display name, and description.</p>
                    
                    <!-- Current Role Info -->
                    <div class="mt-4 p-3 bg-gray-50 rounded-md">
                        <p class="text-sm font-medium text-gray-700 mb-1">Current Info:</p>
                        <p class="text-xs text-gray-600">Name: <code>{{ $role->name }}</code></p>
                        <p class="text-xs text-gray-600">Display: {{ $role->display_name }}</p>
                        <p class="text-xs text-gray-600">Users: {{ $role->users_count }}</p>
                    </div>
                </div>
                <div class="mt-5 space-y-6 md:col-span-2 md:mt-0">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- Role Name -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Role Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" required
                                   @if($role->name === 'admin') readonly @endif
                                   placeholder="e.g., manager, supervisor"
                                   class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('name') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror @if($role->name === 'admin') bg-gray-100 @endif">
                            @if($role->name === 'admin')
                                <p class="mt-1 text-xs text-yellow-600">Admin role name cannot be changed</p>
                            @else
                                <p class="mt-1 text-xs text-gray-500">Lowercase, no spaces. Use underscores or hyphens.</p>
                            @endif
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Display Name -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="display_name" class="block text-sm font-medium text-gray-700 mb-1">Display Name *</label>
                            <input type="text" name="display_name" id="display_name" value="{{ old('display_name', $role->display_name) }}" required
                                   placeholder="e.g., Project Manager, Site Supervisor"
                                   class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('display_name') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            @error('display_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="col-span-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" id="description" rows="3"
                                      placeholder="Describe the role's purpose and responsibilities..."
                                      class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('description') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">{{ old('description', $role->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions Section -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Permissions</h3>
                    <p class="mt-1 text-sm text-gray-500">Update the permissions that users with this role will have.</p>
                    
                    <!-- Current Permissions Summary -->
                    <div class="mt-4 p-3 bg-blue-50 rounded-md">
                        <p class="text-sm font-medium text-blue-800 mb-2">Current Permissions:</p>
                        <p class="text-xs text-blue-600">{{ count($role->permissions ?? []) }} permissions assigned</p>
                        @if($role->users_count > 0)
                            <p class="text-xs text-blue-600 mt-1">
                                Affects {{ $role->users_count }} {{ Str::plural('user', $role->users_count) }}
                            </p>
                        @endif
                    </div>
                    
                    <!-- Select All/None Controls -->
                    <div class="mt-4 space-y-2">
                        <button type="button" onclick="selectAllPermissions()" class="text-sm text-indigo-600 hover:text-indigo-500">Select All</button>
                        <br>
                        <button type="button" onclick="selectNoPermissions()" class="text-sm text-gray-600 hover:text-gray-500">Select None</button>
                    </div>
                </div>
                <div class="mt-5 md:col-span-2 md:mt-0">
                    @error('permissions')
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        </div>
                    @enderror

                    <div class="space-y-6">
                        @foreach($groupedPermissions as $group => $permissions)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-medium text-gray-900">{{ $group }}</h4>
                                <div class="space-x-2">
                                    <button type="button" onclick="selectGroupPermissions('{{ $group }}')" class="text-xs text-indigo-600 hover:text-indigo-500">Select All</button>
                                    <button type="button" onclick="deselectGroupPermissions('{{ $group }}')" class="text-xs text-gray-600 hover:text-gray-500">None</button>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                @foreach($permissions as $permission)
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="permission_{{ $permission }}" name="permissions[]" type="checkbox" value="{{ $permission }}"
                                               {{ in_array($permission, old('permissions', $role->permissions ?? [])) ? 'checked' : '' }}
                                               data-group="{{ $group }}"
                                               class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="permission_{{ $permission }}" class="font-medium text-gray-700 cursor-pointer">
                                            {{ $availablePermissions[$permission] ?? str_replace('_', ' ', $permission) }}
                                        </label>
                                        <p class="text-gray-500 text-xs mt-1">
                                            <code class="bg-gray-100 px-1 py-0.5 rounded">{{ $permission }}</code>
                                        </p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Impact Warning -->
        @if($role->users_count > 0)
        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Permission Changes Will Affect Existing Users</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>This role is currently assigned to {{ $role->users_count }} {{ Str::plural('user', $role->users_count) }}. Any permission changes will immediately affect their access to the system.</p>
                        <ul class="list-disc pl-5 mt-2">
                            <li>Adding permissions will grant new access to all users with this role</li>
                            <li>Removing permissions will revoke access for all users with this role</li>
                            <li>Users will need to log in again to see permission changes</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Preview Section -->
        <div class="bg-gray-50 shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Updated Role Preview</h3>
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-900" id="preview-display-name">{{ $role->display_name }}</p>
                        <p class="text-sm text-gray-500" id="preview-name">{{ $role->name }}</p>
                        <p class="text-sm text-gray-600 mt-1" id="preview-description">{{ $role->description ?: 'No description provided' }}</p>
                        <div class="mt-2">
                            <span class="text-xs text-gray-500">Permissions: </span>
                            <span id="preview-permissions-count" class="text-xs font-medium text-indigo-600">{{ count($role->permissions ?? []) }} selected</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.roles.show', $role) }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Cancel
            </a>
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Update Role
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Live preview functionality
    const nameInput = document.getElementById('name');
    const displayNameInput = document.getElementById('display_name');
    const descriptionInput = document.getElementById('description');
    const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');
    
    function updatePreview() {
        // Update display name
        const displayName = displayNameInput.value || '{{ $role->display_name }}';
        document.getElementById('preview-display-name').textContent = displayName;
        
        // Update role name
        const roleName = nameInput.value || '{{ $role->name }}';
        document.getElementById('preview-name').textContent = roleName;
        
        // Update description
        const description = descriptionInput.value || 'No description provided';
        document.getElementById('preview-description').textContent = description;
        
        // Update permissions count
        const checkedPermissions = document.querySelectorAll('input[name="permissions[]"]:checked');
        const count = checkedPermissions.length;
        document.getElementById('preview-permissions-count').textContent = 
            count + (count === 1 ? ' permission selected' : ' permissions selected');
    }
    
    // Add event listeners for live preview
    nameInput.addEventListener('input', updatePreview);
    displayNameInput.addEventListener('input', updatePreview);
    descriptionInput.addEventListener('input', updatePreview);
    
    permissionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updatePreview);
    });
    
    // Initial preview update
    updatePreview();
});

function selectAllPermissions() {
    const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    updatePermissionsCount();
}

function selectNoPermissions() {
    const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    updatePermissionsCount();
}

function selectGroupPermissions(group) {
    const checkboxes = document.querySelectorAll(`input[data-group="${group}"]`);
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    updatePermissionsCount();
}

function deselectGroupPermissions(group) {
    const checkboxes = document.querySelectorAll(`input[data-group="${group}"]`);
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    updatePermissionsCount();
}

function updatePermissionsCount() {
    const checkedPermissions = document.querySelectorAll('input[name="permissions[]"]:checked');
    const count = checkedPermissions.length;
    document.getElementById('preview-permissions-count').textContent = 
        count + (count === 1 ? ' permission selected' : ' permissions selected');
}
</script>
@endpush
@endsection